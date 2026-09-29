<?php

namespace App\Support;

use App\Models\Article;
use Illuminate\Support\Collection;

/**
 * Read access to the static site content in config/site.php.
 */
class Site
{
    const NAME = 'Web4rtTech';

    const URL = 'https://web4rttech.com';

    public static function categories(): array
    {
        return config('site.categories');
    }

    public static function services(): array
    {
        return config('site.services');
    }

    public static function servicesIn(string $categoryId): array
    {
        return array_values(array_filter(self::services(), fn ($s) => $s['category'] === $categoryId));
    }

    public static function service(?string $id): ?array
    {
        foreach (self::services() as $service) {
            if ($service['id'] === $id) {
                return $service;
            }
        }

        return null;
    }

    public static function category(?string $id): ?array
    {
        foreach (self::categories() as $category) {
            if ($category['id'] === $id) {
                return $category;
            }
        }

        return null;
    }

    public static function serviceTitle(?string $id): string
    {
        if (! $id) {
            return '—';
        }
        if ($id === 'other') {
            return 'Other';
        }

        return self::service($id)['title'] ?? $id;
    }

    public static function ventures(): array
    {
        return config('site.ventures');
    }

    /**
     * Published newsroom articles, falling back to the built-in copies if
     * the database cannot be reached so the Newsroom never renders empty.
     */
    public static function articles(): Collection
    {
        try {
            return Article::where('is_published', true)
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get(['id', 'slug', 'title', 'excerpt', 'published_at']);
        } catch (\Throwable $e) {
            report($e);

            return collect(config('site.fallback_articles'))->map(fn ($a) => (object) $a);
        }
    }

    /**
     * A published article by slug, or null. Falls back to the built-in
     * copies if the database cannot be reached.
     */
    public static function article(string $slug): ?object
    {
        try {
            return Article::where('is_published', true)->where('slug', $slug)->first();
        } catch (\Throwable $e) {
            report($e);
            $found = collect(config('site.fallback_articles'))->firstWhere('slug', $slug);

            return $found ? (object) $found : null;
        }
    }

    /**
     * Pricing-guide packages for the quotation/invoice line-item picker.
     * "₹41,500" / "$500" are parsed to numbers; "Custom Quote" becomes 0.
     */
    public static function packages(): array
    {
        $parse = fn ($label) => (float) preg_replace('/[^0-9.]/', '', (string) $label);
        $packages = [];

        foreach (self::services() as $s) {
            foreach ($s['pricing'] ?? [] as $tier) {
                $packages[] = [
                    'key' => $s['id'].':'.$tier['name'],
                    'label' => $s['title'].': '.$tier['name'],
                    'description' => $s['title'].': '.$tier['name'].' package ('.implode(', ', array_slice($tier['features'], 0, 3)).')',
                    'INR' => $parse($tier['priceINR']),
                    'USD' => $parse($tier['priceUSD']),
                ];
            }
        }

        return $packages;
    }
}
