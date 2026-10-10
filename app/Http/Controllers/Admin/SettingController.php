<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Http\Requests\Admin\UpdateSocialLinksRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('admin/Settings/Index', [
            'setting' => Setting::current(),
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $setting = Setting::current();
        $updates = [];

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            $updates['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        if ($request->hasFile('catalogue')) {
            if ($setting->catalogue_path) {
                Storage::disk('public')->delete($setting->catalogue_path);
            }

            $updates['catalogue_path'] = $request->file('catalogue')->store('catalogues', 'public');
        }

        $setting->update($updates);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Settings updated.']);

        return to_route('admin.settings.edit');
    }

    public function updateSocialLinks(UpdateSocialLinksRequest $request): RedirectResponse
    {
        Setting::current()->update([
            'social_links' => $request->validated('social_links'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Social links updated.']);

        return to_route('admin.settings.edit');
    }
}
