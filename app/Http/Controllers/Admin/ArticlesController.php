<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;

class ArticlesController extends ResourcePageController
{
    protected string $api = ArticleController::class;

    protected string $view = 'admin.articles';

    protected function emptyForm(): array
    {
        return ['title' => '', 'slug' => '', 'published_at' => today()->toDateString(), 'excerpt' => '', 'body' => '', 'is_published' => true];
    }

    protected function extra(Request $request, array $lookups): array
    {
        return ['fields' => [
            ['name' => 'title', 'label' => 'Title', 'required' => true, 'full' => true],
            ['name' => 'slug', 'label' => 'URL slug', 'placeholder' => 'auto-generated-from-title', 'help' => 'Lowercase words separated by hyphens. The article lives at /news/<slug>.'],
            ['name' => 'published_at', 'label' => 'Publish date', 'type' => 'date'],
            ['name' => 'excerpt', 'label' => 'Summary', 'type' => 'textarea', 'full' => true, 'rows' => 2, 'help' => 'Shown on the Newsroom card and in search/social previews.'],
            ['name' => 'body', 'label' => 'Article text', 'type' => 'textarea', 'full' => true, 'rows' => 12, 'required' => true, 'help' => 'Separate paragraphs with a blank line.'],
            ['name' => 'is_published', 'label' => 'Published', 'type' => 'checkbox', 'checkboxLabel' => 'Published (visible on the public site)'],
        ]];
    }
}
