<?php

use App\Mail\CatalogueRequested;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

test('a visitor receives the uploaded catalogue by email', function () {
    Storage::fake('public');
    Mail::fake();

    $cataloguePath = UploadedFile::fake()
        ->create('catalogue.pdf', 500, 'application/pdf')
        ->store('catalogues', 'public');

    Setting::current()->update(['catalogue_path' => $cataloguePath]);

    $response = $this->from('/')->post(route('catalogue-requests.store'), [
        'email' => 'customer@example.com',
    ]);

    $response->assertRedirect('/');

    Mail::assertSent(CatalogueRequested::class, function (CatalogueRequested $mail) use ($cataloguePath): bool {
        return $mail->hasTo('customer@example.com')
            && $mail->cataloguePath === $cataloguePath;
    });
});

test('a visitor cannot request a catalogue until an admin uploads one', function () {
    Mail::fake();

    $response = $this->from('/')->post(route('catalogue-requests.store'), [
        'email' => 'customer@example.com',
    ]);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors('email');
    Mail::assertNothingSent();
});

test('a catalogue request requires a valid email address', function () {
    Mail::fake();

    $response = $this->from('/')->post(route('catalogue-requests.store'), [
        'email' => 'invalid-email',
    ]);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors('email');
    Mail::assertNothingSent();
});
