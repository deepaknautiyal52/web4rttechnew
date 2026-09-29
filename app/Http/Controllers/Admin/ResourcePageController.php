<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Lookups;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Server-rendered list + add/edit form for an admin CRUD module.
 *
 * Reading and writing are delegated to the matching API controller
 * ($api), so validation rules, filters, numbering and side effects are
 * exactly the same as the JSON API. The page opens its form with
 * ?create=1 (optionally ?prefill[field]=value) or ?edit={id}.
 */
abstract class ResourcePageController extends Controller
{
    /** @var class-string API controller that does the actual work. */
    protected string $api;

    protected string $view;

    /** Filters applied when the query string does not mention them at all. */
    protected array $defaultFilters = [];

    protected bool $needsLookups = false;

    /** Blank form values for a new record. */
    abstract protected function emptyForm(): array;

    /** Form values for editing an existing record. */
    protected function toForm(Model $record): array
    {
        return collect($this->emptyForm())->mapWithKeys(function ($default, $key) use ($record) {
            $value = $record->{$key} ?? $default;

            return [$key => $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value];
        })->all();
    }

    /** Extra view data (fields, filters, summary panels...). */
    protected function extra(Request $request, array $lookups): array
    {
        return [];
    }

    protected function api()
    {
        return app($this->api);
    }

    public function index(Request $request)
    {
        $state = self::takeState($request);

        foreach ($this->defaultFilters as $key => $value) {
            if (! $request->query->has($key)) {
                $request->query->set($key, $value);
            }
        }

        $page = $this->api()->index($request);
        $page->withPath($request->url());

        $editing = ! empty($state['edit']) ? $this->api()->show($state['edit']) : null;
        $formOpen = (bool) ($editing || ! empty($state['create']));

        $values = $editing
            ? $this->toForm($editing)
            : array_merge($this->emptyForm(), array_intersect_key((array) ($state['prefill'] ?? []), $this->emptyForm()));

        $lookups = $this->needsLookups ? Lookups::all() : [];

        return view($this->view, [
            'page' => $page,
            'editing' => $editing,
            'formOpen' => $formOpen,
            'values' => $values,
            'lookups' => $lookups,
            'openId' => $state['open'] ?? old('_open'),
            'listUrl' => self::listUrl($request),
            'filtered' => collect($request->query())->except('page')->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty(),
        ] + $this->extra($request, $lookups));
    }

    /**
     * Pull the page-state parameters (form open, prefill, expanded row) out
     * of the query so they are not repeated in pagination links.
     */
    public static function takeState(Request $request): array
    {
        $state = [];
        foreach (['create', 'edit', 'prefill', 'open'] as $key) {
            if ($request->query->has($key)) {
                $state[$key] = $request->query->all()[$key];
                $request->query->remove($key);
            }
        }

        return $state;
    }

    public function store(Request $request)
    {
        return $this->done($request, $this->api()->store($request));
    }

    public function update(Request $request, $id)
    {
        return $this->done($request, $this->api()->update($request, $id));
    }

    public function destroy(Request $request, $id)
    {
        $this->api()->destroy($id);

        return $this->backToList($request);
    }

    protected function done(Request $request, JsonResponse $response)
    {
        return $this->backToList($request)->with('notice', $response->getData(true)['message'] ?? 'Saved.');
    }

    /**
     * Back to the list the user came from (keeping search, filters and
     * page), with the add/edit form closed.
     */
    protected function backToList(Request $request, array $extra = [])
    {
        $back = (string) $request->input('_back', '');
        $base = url('/admin');
        if (! str_starts_with($back, $base)) {
            $back = url()->previous();
        }

        $parts = parse_url($back);
        parse_str($parts['query'] ?? '', $query);
        unset($query['create'], $query['edit'], $query['prefill'], $query['open']);
        $query = array_merge($query, $extra);

        $url = strtok($back, '?').($query ? '?'.http_build_query($query) : '');

        return redirect()->to($url);
    }

    /** The current list URL (search, filters, page) without the form-opening parameters. */
    public static function listUrl(Request $request): string
    {
        $query = $request->query();
        unset($query['create'], $query['edit'], $query['prefill'], $query['open']);

        return $request->url().($query ? '?'.http_build_query($query) : '');
    }
}
