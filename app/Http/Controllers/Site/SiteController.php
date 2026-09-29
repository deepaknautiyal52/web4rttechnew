<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Controller;
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

        return view('pages.contact', [
            'preselectedService' => Site::service($service) ? $service : '',
        ]);
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
