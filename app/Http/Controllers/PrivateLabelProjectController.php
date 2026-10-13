<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrivateLabelProjectRequest;
use App\Models\PrivateLabelInquiry;
use App\Models\PrivateLabelProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Throwable;

class PrivateLabelProjectController extends Controller
{
    public function create(string $token): Response
    {
        $inquiry = $this->inquiryForToken($token);
        $project = $inquiry->project;

        return Inertia::render('kaveh', [
            'projectReference' => $project?->reference,
            'projectToken' => $token,
            'prefill' => [
                'full_name' => $inquiry->full_name,
                'company_name' => $inquiry->company_name ?? '',
                'email' => $inquiry->email,
                'phone' => $inquiry->phone ?? '',
                'company_country' => $inquiry->country ?? '',
                'target_markets' => $inquiry->country ?? '',
                'product_interest' => $inquiry->product_name ?? '',
                'packaging_format' => $inquiry->product_format ?? '',
                'delivery_country' => $inquiry->country ?? '',
                'comments' => collect([
                    $inquiry->estimated_quantity ? 'Estimated quantity from initial inquiry: '.$inquiry->estimated_quantity : null,
                    $inquiry->message,
                ])->filter()->implode("\n\n"),
            ],
        ]);
    }

    public function store(StorePrivateLabelProjectRequest $request, string $token): RedirectResponse
    {
        $inquiry = $this->inquiryForToken($token);
        abort_if($inquiry->project()->exists(), 409);

        $validated = $request->validated();
        $fileGroups = ['product_files', 'brand_files', 'packaging_files', 'compliance_files'];
        $directory = 'private-label-projects/'.Str::uuid();
        $files = [];

        try {
            foreach ($fileGroups as $group) {
                foreach ($request->file($group, []) as $file) {
                    $path = $file->store($directory.'/'.$group, 'local');

                    if ($path === false) {
                        throw new RuntimeException('Unable to store a private label project file.');
                    }

                    $files[$group][] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                    ];
                }
            }

            PrivateLabelProject::create([
                'private_label_inquiry_id' => $inquiry->id,
                'full_name' => $validated['full_name'],
                'company_name' => $validated['company_name'],
                'email' => $validated['email'],
                'details' => Arr::except($validated, [
                    'full_name', 'company_name', 'email',
                    'accuracy_confirmation', 'privacy_consent',
                    ...$fileGroups,
                ]),
                'files' => $files,
                'consented_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->deleteDirectory($directory);

            throw $exception;
        }

        return to_route('kaveh', $token);
    }

    private function inquiryForToken(string $token): PrivateLabelInquiry
    {
        return PrivateLabelInquiry::where('project_access_token', $token)
            ->where('intent', 'private_label')
            ->firstOrFail();
    }
}
