<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivateLabelInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PrivateLabelInquiryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/PrivateLabel/Inquiries', [
            'inquiries' => PrivateLabelInquiry::withExists('project')->latest('id')->get()
                ->map(fn (PrivateLabelInquiry $inquiry): array => [
                    ...$inquiry->toArray(),
                    'project_url' => $inquiry->project_access_token ? route('kaveh', $inquiry->project_access_token) : null,
                ]),
        ]);
    }

    public function createProjectLink(PrivateLabelInquiry $inquiry): RedirectResponse
    {
        abort_unless($inquiry->intent === 'private_label', 404);

        if ($inquiry->project_access_token === null) {
            PrivateLabelInquiry::whereKey($inquiry->id)
                ->whereNull('project_access_token')
                ->update(['project_access_token' => Str::random(48)]);
        }

        return to_route('admin.private-label.inquiries.index');
    }

    public function markAsRead(PrivateLabelInquiry $inquiry): RedirectResponse
    {
        $inquiry->update(['read_at' => $inquiry->read_at ? null : now()]);

        return to_route('admin.private-label.inquiries.index');
    }

    public function destroy(PrivateLabelInquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Inquiry deleted.']);

        return to_route('admin.private-label.inquiries.index');
    }
}
