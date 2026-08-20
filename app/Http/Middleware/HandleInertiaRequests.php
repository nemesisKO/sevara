<?php

namespace App\Http\Middleware;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'settings' => [
                'catalogue_url' => Setting::current()->catalogue_url,
                'logo_url' => Setting::current()->logo_url,
            ],
            'seo' => [
                'url' => config('seo.url'),
                'name' => config('seo.name'),
                'title' => config('seo.title'),
                'description' => config('seo.description'),
                'image' => config('seo.url').config('seo.image'),
            ],
            'navProducts' => $request->routeIs('admin.*') ? [] : $this->navProducts(),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * Products shown in the public site's "Products" navigation dropdown.
     *
     * @return array<int, array{title: string, href: string, image_url: string|null}>
     */
    private function navProducts(): array
    {
        return Product::query()
            ->select(['id', 'slug', 'title_part1', 'title_part2'])
            ->with(['images' => fn ($query) => $query->select(['id', 'product_id', 'image_path'])])
            ->orderBy('title_part1')
            ->get()
            ->map(fn (Product $product): array => [
                'title' => "{$product->title_part1} {$product->title_part2}",
                'href' => route('products.show', $product),
                'image_url' => $product->images->first()?->image_url,
            ])
            ->all();
    }
}
