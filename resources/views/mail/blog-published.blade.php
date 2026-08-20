<x-mail::message>
@if ($imageUrl)
<img src="{{ $imageUrl }}" alt="{{ $blog->title }}" style="display: block; width: 100%; max-width: 640px; height: auto; margin: 0 auto 24px;">
@endif

# {{ $blog->title }}

{{ $blog->mini_review ?? 'Discover the latest from Sevara.' }}

<x-mail::button :url="$blogUrl">
Read more
</x-mail::button>

Kind regards,<br>
{{ config('app.name') }}
</x-mail::message>
