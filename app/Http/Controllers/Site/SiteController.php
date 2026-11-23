<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Controller;
use App\Support\DomainChecker;
use App\Support\Site;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SiteController extends Controller
{
    public function service(string $id)
    {
        $service = Site::service($id);

        if (! $service) {
            return response()->view('pages.not-found', [
                'heading' => 'Service not found',
                'message' => 'The requested service does not exist.',
                'backUrl' => url('/services'),
                'backLabel' => 'Back to Services',
            ], 404);
        }

        return view('pages.service-detail', [
            'service' => $service,
            'category' => Site::category($service['category']),
            'related' => array_values(array_filter(
                Site::servicesIn($service['category']),
                fn ($s) => $s['id'] !== $service['id']
            )),
        ]);
    }

    public function news()
    {
        return view('pages.news', ['articles' => Site::articles()]);
    }

    public function article(string $slug)
    {
        $article = Site::article($slug);

        if (! $article) {
            return response()->view('pages.not-found', [
                'heading' => 'Article not found',
                'message' => null,
                'backUrl' => url('/news'),
                'backLabel' => 'Back to News',
            ], 404);
        }

        return view('pages.news-article', ['article' => $article, 'slug' => $slug]);
    }

    public function contact(Request $request)
    {
        // Pre-select the service when arriving from a "Get a Quote" link, e.g. /contact?service=seo
        $service = (string) $request->query('service', '');
        // From the domain search: /contact?service=domain-registration&domain=mybrand.in
        $parsed = DomainChecker::parse((string) $request->query('domain', ''));

        return view('pages.contact', [
            'preselectedService' => Site::service($service) ? $service : '',
            'domain' => $parsed ? implode('', $parsed) : null,
        ]);
    }

    /**
     * Domain search: availability of the searched name plus the same name on
     * the other popular extensions. GET /domain-check?domain=mybrand&ext=.in
     */
    public function checkDomain(Request $request)
    {
        $parsed = DomainChecker::parse((string) $request->query('domain', ''), (string) $request->query('ext', '.com'));
        if (!$parsed) {
            return response()->json(['error' => 'Please enter a valid domain name, using letters, numbers and hyphens only (e.g. mybusiness or mybusiness.in).'], 422);
        }

        [$name, $ext] = $parsed;
        $extensions = DomainChecker::extensions();
        $suggest = array_diff(array_slice(array_keys($extensions), 0, 5), [$ext]);
        $domains = array_map(fn ($e) => $name.$e, array_merge([$ext], $suggest));
        $statuses = DomainChecker::check($domains);

        $results = [];
        foreach ($domains as $i => $domain) {
            $e = $i === 0 ? $ext : array_values($suggest)[$i - 1];
            $results[] = [
                'domain' => $domain,
                'extension' => $e,
                'status' => $statuses[$domain],
                'price' => $extensions[$e]['INR'],
                'register_url' => url('/contact').'?service=domain-registration&domain='.urlencode($domain),
            ];
        }

        return response()->json(['query' => $results[0], 'suggestions' => array_slice($results, 1)]);
    }

    public function submitContact(Request $request)
    {
        try {
            app(ContactController::class)->store($request);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('contact_error', 'Unable to send your message right now. Please try again later.');
        }

        return redirect()->route('contact')->with('contact_success', true);
    }
}
