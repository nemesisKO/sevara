<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivateLabelInquiry;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PrivateLabelInquiryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/PrivateLabel/Inquiries', [
            'inquiries' => PrivateLabelInquiry::latest('id')->get(),
        ]);
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
