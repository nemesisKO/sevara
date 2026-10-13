<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivateLabelProject;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivateLabelProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/PrivateLabel/Projects', [
            'projects' => PrivateLabelProject::latest('id')->get(),
        ]);
    }

    public function download(PrivateLabelProject $project, string $group, int $index): StreamedResponse
    {
        $file = $project->files[$group][$index] ?? null;

        abort_unless(
            in_array($group, ['product_files', 'brand_files', 'packaging_files', 'compliance_files'], true)
                && is_array($file)
                && isset($file['path'], $file['name'])
                && Storage::disk('local')->exists($file['path']),
            404,
        );

        return Storage::disk('local')->download($file['path'], $file['name']);
    }
}
