<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSlideRequest;
use App\Http\Requests\Admin\UpdateSlideRequest;
use App\Models\Slide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SlideController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Slides/Index', [
            'slides' => Slide::orderBy('id')->get(),
        ]);
    }

    public function store(StoreSlideRequest $request): RedirectResponse
    {
        Slide::create([
            ...$request->safe()->except(['image', 'mobile_image']),
            'image_path' => $request->file('image')->store('slides', 'public'),
            'mobile_image_path' => $request->file('mobile_image')?->store('slides', 'public'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide added.']);

        return to_route('admin.slides.index');
    }

    public function update(UpdateSlideRequest $request, Slide $slide): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'mobile_image']);

        if ($request->hasFile('image')) {
            $oldImagePath = $slide->image_path;
            $data['image_path'] = $request->file('image')->store('slides', 'public');
        }

        if ($request->hasFile('mobile_image')) {
            $oldMobileImagePath = $slide->mobile_image_path;
            $data['mobile_image_path'] = $request->file('mobile_image')->store('slides', 'public');
        }

        $slide->update($data);

        if (isset($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }

        if (isset($oldMobileImagePath)) {
            Storage::disk('public')->delete($oldMobileImagePath);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide updated.']);

        return to_route('admin.slides.index');
    }

    public function destroy(Slide $slide): RedirectResponse
    {
        Storage::disk('public')->delete($slide->image_path);

        if ($slide->mobile_image_path) {
            Storage::disk('public')->delete($slide->mobile_image_path);
        }

        $slide->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide deleted.']);

        return to_route('admin.slides.index');
    }
}
