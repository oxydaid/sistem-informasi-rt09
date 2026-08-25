<?php

use App\Models\CashAccount;
use App\Models\CashCategory;
use App\Models\Content;
use App\Models\Media;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\OrganizationPosition;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\FinanceService;
use App\Services\TiptapContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    Storage::fake('public');
});

test('public pages are accessible without authentication', function () {
    $this->get(route('home'))->assertOk();
    $this->get(route('public.rt.profile'))->assertOk();
    $this->get(route('public.kt.profile'))->assertOk();
    $this->get(route('public.structure'))->assertOk();
    $this->get(route('public.announcements.index'))->assertOk();
    $this->get(route('public.articles.index'))->assertOk();
    $this->get(route('public.events.index'))->assertOk();
    $this->get(route('public.activities.index'))->assertOk();
    $this->get(route('public.gallery'))->assertOk();
    $this->get(route('public.finance'))->assertOk();
});

test('admin can log in and view dashboard', function () {
    $admin = User::where('email', 'admin.rt@rt09.id')->first();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

test('member position assignment works across periods and validates max members', function () {
    $kt = Organization::where('type', 'karang_taruna')->first();
    $period = $kt->activePeriod;

    expect($period)->not->toBeNull();

    // Position with max_members = 2 (Sekretaris Karang Taruna)
    $pos = OrganizationPosition::where('organization_id', $kt->id)
        ->where('name', 'Sekretaris Karang Taruna')
        ->first();

    expect($pos)->not->toBeNull();
    expect($pos->max_members)->toBe(2);

    $member3 = OrganizationMember::create([
        'organization_id' => $kt->id,
        'name' => 'Pemuda C',
        'status' => 'active',
    ]);

    $admin = User::where('email', 'superadmin@rt09.id')->first();

    // Already 2 members seeded in DatabaseSeeder for this position in active period ($memberSekreKt1 & $memberSekreKt2)
    // So assigning a 3rd member should fail validation
    $this->actingAs($admin)->post(route('admin.organization.members.assign', $member3->id), [
        'period_id' => $period->id,
        'position_id' => $pos->id,
        'notes' => 'Sekretaris III',
    ])->assertSessionHasErrors('position_id');
});

test('finance service records and voids transactions atomically', function () {
    $account = CashAccount::first();
    $initialBalance = (float) $account->current_balance;
    $category = CashCategory::where('organization_id', $account->organization_id)
        ->where('type', 'income')
        ->first();
    $user = User::first();

    $financeService = app(FinanceService::class);

    // Record income of 150,000
    $trx = $financeService->recordTransaction([
        'cash_account_id' => $account->id,
        'cash_category_id' => $category->id,
        'type' => 'income',
        'amount' => 150000,
        'transaction_date' => now()->toDateString(),
        'description' => 'Test Donasi Warga',
    ], $user->id);

    $account->refresh();
    expect((float) $account->current_balance)->toBe($initialBalance + 150000.0);

    // Void the transaction
    $financeService->voidTransaction($trx->id, $user->id);
    $account->refresh();
    expect((float) $account->current_balance)->toBe($initialBalance);
});

test('file upload service handles file replacement and deletion from disk', function () {
    $uploadService = app(FileUploadService::class);

    $file1 = UploadedFile::fake()->image('avatar1.jpg');
    $path1 = $uploadService->uploadAvatar($file1);

    Storage::disk('public')->assertExists($path1);

    // Upload file 2 replacing file 1
    $file2 = UploadedFile::fake()->image('avatar2.jpg');
    $path2 = $uploadService->uploadAvatar($file2, $path1);

    Storage::disk('public')->assertMissing($path1);
    Storage::disk('public')->assertExists($path2);
});

test('tiptap content service detects and deletes orphaned media when removed from editor', function () {
    $tiptapService = app(TiptapContentService::class);

    // Create 2 media items
    $file1 = UploadedFile::fake()->image('embedded1.jpg');
    $path1 = $file1->store('media', 'public');
    $media1 = Media::create([
        'file_name' => 'embedded1.jpg',
        'file_path' => $path1,
        'original_name' => 'embedded1.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'file_size' => 1000,
    ]);

    $file2 = UploadedFile::fake()->image('embedded2.jpg');
    $path2 = $file2->store('media', 'public');
    $media2 = Media::create([
        'file_name' => 'embedded2.jpg',
        'file_path' => $path2,
        'original_name' => 'embedded2.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'file_size' => 1000,
    ]);

    $oldContent = [
        'type' => 'doc',
        'content' => [
            ['type' => 'image', 'attrs' => ['src' => $media1->url, 'mediaId' => $media1->id]],
            ['type' => 'image', 'attrs' => ['src' => $media2->url, 'mediaId' => $media2->id]],
        ],
    ];

    // In new content, media2 was deleted by user
    $newContent = [
        'type' => 'doc',
        'content' => [
            ['type' => 'image', 'attrs' => ['src' => $media1->url, 'mediaId' => $media1->id]],
        ],
    ];

    $tiptapService->cleanupOrphanedMedia($oldContent, $newContent);

    // media1 still exists
    expect(Media::find($media1->id))->not->toBeNull();
    Storage::disk('public')->assertExists($path1);

    // media2 should be deleted from DB and disk
    expect(Media::find($media2->id))->toBeNull();
    Storage::disk('public')->assertMissing($path2);
});
