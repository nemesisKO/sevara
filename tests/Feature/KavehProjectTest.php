<?php

use App\Enums\UserRole;
use App\Models\PrivateLabelInquiry;
use App\Models\PrivateLabelProject;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function validKavehProject(): array
{
    return [
        'full_name' => 'Amina Yusuf',
        'job_title' => 'Buyer',
        'company_name' => 'Yusuf Foods',
        'email' => 'amina@example.com',
        'company_country' => 'Qatar',
        'buyer_type' => ['Importer / Distributor'],
        'target_markets' => 'Qatar',
        'sales_channel' => ['Supermarkets'],
        'project_type' => 'Launching a new brand',
        'development_type' => 'custom',
        'product_interest' => 'Dark chocolate dates',
        'packaging_format' => 'Stand-up Pouch',
        'net_weight' => '250 g',
        'skus' => [['product' => 'Dark chocolate dates', 'pack_size' => '250 g', 'quantity' => '1000']],
        'artwork_status' => 'No artwork yet',
        'has_compliance_requirements' => 'no',
        'initial_order' => '5',
        'order_unit' => 'MT',
        'delivery_country' => 'Qatar',
        'sample_request' => 'Yes',
        'accuracy_confirmation' => true,
        'privacy_consent' => true,
    ];
}

function projectInquiry(): PrivateLabelInquiry
{
    return PrivateLabelInquiry::factory()->create([
        'intent' => 'private_label',
        'project_access_token' => 'test-personal-project-token',
        'full_name' => 'Amina Yusuf',
        'company_name' => 'Yusuf Foods',
        'email' => 'amina@example.com',
        'country' => 'Qatar',
        'product_name' => 'Dark chocolate dates',
        'product_format' => 'Premium Gift Box',
        'estimated_quantity' => '20,000 boxes',
        'message' => 'Please suggest a luxury finish.',
    ]);
}

test('only a generated personal link opens the prefilled project page', function () {
    $inquiry = projectInquiry();

    $this->get('/kaveh')->assertNotFound();
    $this->get(route('kaveh', 'invalid-token'))->assertNotFound();
    $this->get(route('kaveh', $inquiry->project_access_token))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('kaveh')
            ->where('projectReference', null)
            ->where('prefill.full_name', 'Amina Yusuf')
            ->where('prefill.company_name', 'Yusuf Foods')
            ->where('prefill.email', 'amina@example.com')
            ->where('prefill.company_country', 'Qatar')
            ->where('prefill.product_interest', 'Dark chocolate dates')
            ->where('prefill.packaging_format', 'Premium Gift Box')
            ->where('prefill.delivery_country', 'Qatar')
            ->where('prefill.comments', "Estimated quantity from initial inquiry: 20,000 boxes\n\nPlease suggest a luxury finish.")
        );
});

test('a project and its uploads are saved privately and show a reference', function () {
    Storage::fake('local');
    $inquiry = projectInquiry();

    $response = $this->from(route('kaveh', $inquiry->project_access_token))->post(route('kaveh.projects.store', $inquiry->project_access_token), [
        ...validKavehProject(),
        'product_files' => [UploadedFile::fake()->image('date-reference.jpg')],
    ]);

    $response->assertRedirect(route('kaveh', $inquiry->project_access_token));

    $project = PrivateLabelProject::sole();
    expect($project->full_name)->toBe('Amina Yusuf')
        ->and($project->private_label_inquiry_id)->toBe($inquiry->id)
        ->and($project->details['buyer_type'])->toBe(['Importer / Distributor'])
        ->and($project->details['skus'][0]['product'])->toBe('Dark chocolate dates')
        ->and($project->files['product_files'][0]['name'])->toBe('date-reference.jpg')
        ->and($project->consented_at)->not->toBeNull();

    Storage::disk('local')->assertExists($project->files['product_files'][0]['path']);

    $this->get(route('kaveh', $inquiry->project_access_token))
        ->assertInertia(fn ($page) => $page
            ->component('kaveh')
            ->where('projectReference', $project->reference)
        );
});

test('required project information and consents are validated', function () {
    $inquiry = projectInquiry();
    $this->from(route('kaveh', $inquiry->project_access_token))->post(route('kaveh.projects.store', $inquiry->project_access_token), [
        ...validKavehProject(),
        'buyer_type' => [],
        'sales_channel' => ['Invalid'],
        'accuracy_confirmation' => false,
        'privacy_consent' => false,
    ])->assertRedirect(route('kaveh', $inquiry->project_access_token))
        ->assertSessionHasErrors(['buyer_type', 'sales_channel.0', 'accuracy_confirmation', 'privacy_consent']);

    expect(PrivateLabelProject::count())->toBe(0);
});

test('unsupported or oversized project uploads are rejected', function () {
    Storage::fake('local');
    $inquiry = projectInquiry();

    $this->post(route('kaveh.projects.store', $inquiry->project_access_token), [
        ...validKavehProject(),
        'product_files' => [UploadedFile::fake()->create('malware.exe', 10)],
        'brand_files' => [UploadedFile::fake()->create('oversized.pdf', 25601, 'application/pdf')],
    ])->assertSessionHasErrors(['product_files.0', 'brand_files.0']);

    expect(PrivateLabelProject::count())->toBe(0);
});

test('a personal link accepts one project submission only', function () {
    $inquiry = projectInquiry();

    $this->post(route('kaveh.projects.store', $inquiry->project_access_token), validKavehProject())
        ->assertRedirect(route('kaveh', $inquiry->project_access_token));
    $this->post(route('kaveh.projects.store', $inquiry->project_access_token), validKavehProject())
        ->assertConflict();

    expect(PrivateLabelProject::count())->toBe(1);

    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]))
        ->get(route('admin.private-label.inquiries.index'))
        ->assertInertia(fn ($page) => $page->where('inquiries.0.project_exists', true));
});

test('unissued and unrelated links cannot submit a project', function () {
    $inquiry = PrivateLabelInquiry::factory()->create(['intent' => 'sample_request', 'project_access_token' => 'other-form-token']);

    $this->post(route('kaveh.projects.store', 'unknown-token'), validKavehProject())->assertNotFound();
    $this->get(route('kaveh', $inquiry->project_access_token))->assertNotFound();
    $this->post(route('kaveh.projects.store', $inquiry->project_access_token), validKavehProject())->assertNotFound();

    expect(PrivateLabelProject::count())->toBe(0);
});

test('only admins can view projects and download their files', function () {
    Storage::fake('local');
    Storage::disk('local')->put('private-label-projects/sample/reference.pdf', 'sample content');
    $project = PrivateLabelProject::factory()->create([
        'company_name' => 'Yusuf Foods',
        'files' => [
            'product_files' => [[
                'name' => 'reference.pdf',
                'path' => 'private-label-projects/sample/reference.pdf',
                'size' => 14,
            ]],
        ],
    ]);
    $download = route('admin.private-label.projects.files.download', [$project, 'product_files', 0]);

    $this->get(route('admin.private-label.projects.index'))->assertRedirect(route('login'));
    $this->get($download)->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get(route('admin.private-label.projects.index'))
        ->assertForbidden();
    $this->get($download)->assertForbidden();

    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]))
        ->get(route('admin.private-label.projects.index'))
        ->assertInertia(fn ($page) => $page
            ->component('admin/PrivateLabel/Projects')
            ->has('projects', 1)
            ->where('projects.0.company_name', 'Yusuf Foods')
        );
    $this->get($download)->assertOk()
        ->assertDownload('reference.pdf');
    $this->get(route('admin.private-label.projects.files.download', [$project, 'product_files', 9]))
        ->assertNotFound();
});
