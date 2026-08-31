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
            'image_path' => $request->file('image')->store('slides', 'public'),
            ...$request->safe()->except('image'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide added.']);

        return to_route('admin.slides.index');
    }

    public function update(UpdateSlideRequest $request, Slide $slide): RedirectResponse
    {
        $slide->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide updated.']);

        return to_route('admin.slides.index');
    }

    public function destroy(Slide $slide): RedirectResponse
    {
        Storage::disk('public')->delete($slide->image_path);

        $slide->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide deleted.']);

        return to_route('admin.slides.index');
    }
}
