<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCatalogueRequestRequest;
use App\Mail\CatalogueRequested;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class CatalogueRequestController extends Controller
{
    public function __invoke(StoreCatalogueRequestRequest $request): RedirectResponse
    {
        $cataloguePath = Setting::current()->catalogue_path;

        if (! $cataloguePath) {
            return back()->withErrors([
                'email' => 'The catalogue is not available yet. Please contact us directly.',
            ]);
        }

        Mail::to($request->validated('email'))->send(new CatalogueRequested($cataloguePath));

        return back();
    }
}
