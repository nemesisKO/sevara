<?php

namespace App\Actions;

use App\Mail\BlogPublished;
use App\Models\Blog;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBlogAnnouncement
{
    public function __invoke(Blog $blog): void
    {
        $subscriberEmails = NewsletterSubscriber::query()->pluck('email')->all();

        if ($subscriberEmails === []) {
            return;
        }

        try {
            Mail::to(config('mail.from.address'))
                ->bcc($subscriberEmails)
                ->send(new BlogPublished($blog));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
