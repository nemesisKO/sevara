<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsletterSubscriberRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class NewsletterSubscriptionController extends Controller
{
    public function store(StoreNewsletterSubscriberRequest $request): RedirectResponse
    {
        NewsletterSubscriber::firstOrCreate([
            'email' => $request->validated('email'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'You are subscribed to Sevara updates.',
            'variant' => 'newsletter',
        ]);

        return back();
    }
}
