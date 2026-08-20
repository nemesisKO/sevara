<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class NewsletterSubscriberController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/NewsletterSubscribers/Index', [
            'subscribers' => NewsletterSubscriber::latest('id')->get(),
        ]);
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Subscriber removed.']);

        return to_route('admin.newsletter-subscribers.index');
    }
}
