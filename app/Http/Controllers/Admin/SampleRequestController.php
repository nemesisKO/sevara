<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SampleRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SampleRequestController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/SampleRequests/Index', [
            'sampleRequests' => SampleRequest::latest('id')->get(),
        ]);
    }

    public function markAsRead(SampleRequest $sampleRequest): RedirectResponse
    {
        $sampleRequest->update(['read_at' => $sampleRequest->read_at ? null : now()]);

        return to_route('admin.sample-requests.index');
    }

    public function destroy(SampleRequest $sampleRequest): RedirectResponse
    {
        $sampleRequest->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Sample request deleted.']);

        return to_route('admin.sample-requests.index');
    }
}
