<?php

namespace App\Support;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Domain availability via RDAP, the registries' public lookup service:
 * 404 = not registered (available), 200 = registered (taken).
 * Extensions without an RDAP server fall back to a DNS check, which can only
 * prove a domain is taken, so a domain with no DNS records is reported "unknown".
 */
class DomainChecker
{
    const BOOTSTRAP_URL = 'https://data.iana.org/rdap/dns.json';

    /** Extensions on sale, e.g. ['.com' => ['INR' => '₹1,099', 'USD' => '$13']]. */
    public static function extensions(): array
    {
        return config('site.domain_extensions');
    }

    /**
     * Split what the visitor typed into [name, extension]. Accepts "mybrand",
     * "mybrand.in" or a pasted URL; the extension falls back to $defaultExt.
     * Returns null when the name is not a valid domain label.
     */
    public static function parse(string $input, string $defaultExt = '.com'): ?array
    {
        $input = strtolower(trim($input));
        $input = preg_replace('#^[a-z]+://#', '', $input);
        $input = preg_replace('#^www\.#', '', explode('/', $input)[0]);

        $ext = array_key_exists($defaultExt, self::extensions()) ? $defaultExt : '.com';
        // Longest match first, so "brand.co.in" is .co.in and not .in.
        $known = array_keys(self::extensions());
        usort($known, fn ($a, $b) => strlen($b) - strlen($a));
        foreach ($known as $candidate) {
            if (str_ends_with($input, $candidate) && strlen($input) > strlen($candidate)) {
                $ext = $candidate;
                $input = substr($input, 0, -strlen($candidate));
                break;
            }
        }

        // A single label: letters, digits and inner hyphens, 1-63 characters.
        if (!preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $input)) {
            return null;
        }

        return [$input, $ext];
    }

    /**
     * Check several full domain names at once.
     * Returns [domain => 'available' | 'taken' | 'unknown'].
     */
    public static function check(array $domains): array
    {
        $results = [];
        $pending = [];
        foreach ($domains as $domain) {
            $cached = Cache::get('domain-check:'.$domain);
            if ($cached) {
                $results[$domain] = $cached;
            } elseif ($server = self::rdapServer($domain)) {
                $pending[$domain] = rtrim($server, '/').'/domain/'.$domain;
            } else {
                $results[$domain] = self::dnsCheck($domain);
            }
        }

        if ($pending) {
            $responses = Http::pool(fn (Pool $pool) => array_map(
                fn ($domain, $url) => $pool->as($domain)->timeout(8)->accept('application/rdap+json')->get($url),
                array_keys($pending),
                $pending,
            ));
            foreach ($pending as $domain => $url) {
                $response = $responses[$domain] ?? null;
                $status = $response instanceof \Illuminate\Http\Client\Response ? $response->status() : 0;
                $results[$domain] = match (true) {
                    $status === 404 => 'available',
                    $status >= 200 && $status < 300 => 'taken',
                    // Rate-limited or registry down: DNS can still show it is taken.
                    default => self::dnsCheck($domain),
                };
            }
        }

        foreach ($results as $domain => $status) {
            if ($status !== 'unknown') {
                Cache::put('domain-check:'.$domain, $status, now()->addMinutes(10));
            }
        }

        return array_merge(array_flip($domains), $results);
    }

    /** RDAP server for the domain's top-level extension, from IANA's list (cached a week). */
    protected static function rdapServer(string $domain): ?string
    {
        $map = Cache::remember('rdap-bootstrap', now()->addWeek(), function () {
            try {
                $services = Http::timeout(10)->get(self::BOOTSTRAP_URL)->json('services') ?? [];
            } catch (\Throwable $e) {
                report($e);
                return null; // not cached as an empty map below
            }
            $map = [];
            foreach ($services as [$tlds, $urls]) {
                foreach ($tlds as $tld) {
                    $map[$tld] = $urls[0];
                }
            }
            return $map;
        });
        if ($map === null) {
            Cache::forget('rdap-bootstrap');
        }

        return $map[substr(strrchr($domain, '.'), 1)] ?? null;
    }

    protected static function dnsCheck(string $domain): string
    {
        return @checkdnsrr($domain, 'NS') || @checkdnsrr($domain, 'A') ? 'taken' : 'unknown';
    }
}
