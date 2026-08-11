<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
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

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            $setting->update(['logo_path' => $request->file('logo')->store('branding', 'public')]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Logo updated.']);

        return to_route('admin.settings.edit');
    }
}
