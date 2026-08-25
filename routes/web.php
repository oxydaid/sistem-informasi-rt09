<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\PeriodController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/profil-rt', [PublicController::class, 'profileRt'])->name('public.rt.profile');
Route::get('/karang-taruna', [PublicController::class, 'profileKarangTaruna'])->name('public.kt.profile');
Route::get('/struktur-organisasi', [PublicController::class, 'structure'])->name('public.structure');

Route::get('/pengumuman', [PublicController::class, 'announcements'])->name('public.announcements.index');
Route::get('/pengumuman/{slug}', [PublicController::class, 'announcementShow'])->name('public.announcements.show');

Route::get('/artikel', [PublicController::class, 'articles'])->name('public.articles.index');
Route::get('/artikel/{slug}', [PublicController::class, 'articleShow'])->name('public.articles.show');

Route::get('/agenda', [PublicController::class, 'events'])->name('public.events.index');
Route::get('/agenda/{slug}', [PublicController::class, 'eventShow'])->name('public.events.show');

Route::get('/kegiatan', [PublicController::class, 'activities'])->name('public.activities.index');
Route::get('/kegiatan/{slug}', [PublicController::class, 'activityShow'])->name('public.activities.show');

Route::get('/galeri', [PublicController::class, 'gallery'])->name('public.gallery');
Route::get('/transparansi-keuangan', [PublicController::class, 'finance'])->name('public.finance');
Route::get('/page/{slug}', [PublicController::class, 'page'])->name('public.page');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Organization Profile
        Route::get('/organization/profile/{type?}', [OrganizationController::class, 'profile'])->name('organization.profile');
        Route::put('/organization/{organization}', [OrganizationController::class, 'update'])->name('organization.update');

        // Organization Periods
        Route::get('/organization/periods', [PeriodController::class, 'index'])->name('organization.periods.index');
        Route::post('/organization/periods', [PeriodController::class, 'store'])->name('organization.periods.store');
        Route::put('/organization/periods/{period}', [PeriodController::class, 'update'])->name('organization.periods.update');
        Route::delete('/organization/periods/{period}', [PeriodController::class, 'destroy'])->name('organization.periods.destroy');

        // Organization Positions
        Route::get('/organization/positions', [PositionController::class, 'index'])->name('organization.positions.index');
        Route::post('/organization/positions', [PositionController::class, 'store'])->name('organization.positions.store');
        Route::put('/organization/positions/{position}', [PositionController::class, 'update'])->name('organization.positions.update');
        Route::delete('/organization/positions/{position}', [PositionController::class, 'destroy'])->name('organization.positions.destroy');

        // Organization Members
        Route::get('/organization/members', [MemberController::class, 'index'])->name('organization.members.index');
        Route::post('/organization/members', [MemberController::class, 'store'])->name('organization.members.store');
        Route::put('/organization/members/{member}', [MemberController::class, 'update'])->name('organization.members.update');
        Route::delete('/organization/members/{member}', [MemberController::class, 'destroy'])->name('organization.members.destroy');
        Route::post('/organization/members/{member}/assign-position', [MemberController::class, 'assignPositionRoute'])->name('organization.members.assign');
        Route::delete('/organization/members/assignments/{assignment}', [MemberController::class, 'removePositionAssignment'])->name('organization.members.remove_assignment');

        // Finance
        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/finance/transactions', [FinanceController::class, 'storeTransaction'])->name('finance.transactions.store');
        Route::post('/finance/transactions/{transaction}/void', [FinanceController::class, 'voidTransaction'])->name('finance.transactions.void');

        Route::get('/finance/categories', [FinanceController::class, 'categories'])->name('finance.categories.index');
        Route::post('/finance/categories', [FinanceController::class, 'storeCategory'])->name('finance.categories.store');
        Route::put('/finance/categories/{category}', [FinanceController::class, 'updateCategory'])->name('finance.categories.update');
        Route::delete('/finance/categories/{category}', [FinanceController::class, 'destroyCategory'])->name('finance.categories.destroy');

        // Content
        Route::get('/contents', [ContentController::class, 'index'])->name('contents.index');
        Route::get('/contents/create', [ContentController::class, 'create'])->name('contents.create');
        Route::post('/contents', [ContentController::class, 'store'])->name('contents.store');
        Route::get('/contents/{content}/edit', [ContentController::class, 'edit'])->name('contents.edit');
        Route::put('/contents/{content}', [ContentController::class, 'update'])->name('contents.update');
        Route::delete('/contents/{content}', [ContentController::class, 'destroy'])->name('contents.destroy');

        Route::post('/contents/editor-media', [ContentController::class, 'uploadEditorMedia'])->name('contents.editor_media');
        Route::post('/contents/{content}/gallery', [ContentController::class, 'uploadGallery'])->name('contents.upload_gallery');
        Route::delete('/contents/gallery/{activityMedia}', [ContentController::class, 'deleteGalleryPhoto'])->name('contents.delete_gallery');
        Route::post('/contents/{content}/attachments', [ContentController::class, 'uploadAttachment'])->name('contents.upload_attachment');
        Route::delete('/contents/attachments/{attachment}', [ContentController::class, 'deleteAttachment'])->name('contents.delete_attachment');
        Route::post('/contents/import-markdown', [ContentController::class, 'importMarkdown'])->name('contents.import_markdown');

        // Media Library
        Route::get('/media', [MediaController::class, 'index'])->name('media.index');
        Route::post('/media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('/media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');

        // Super Admin Only Area
        Route::middleware('role:super_admin')->group(function () {
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');
        });
    });
});
