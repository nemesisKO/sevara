<?php

namespace App\Mail;

use App\Models\Blog;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BlogPublished extends Mailable
{
    public function __construct(public Blog $blog) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New from Sevara: '.$this->blog->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.blog-published',
            with: [
                'blogUrl' => route('blog.show', ['blog' => $this->blog->slug]),
                'imageUrl' => $this->blog->image_url ? url($this->blog->image_url) : null,
            ],
        );
    }
}
