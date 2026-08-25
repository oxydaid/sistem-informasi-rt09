<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\Content;
use App\Models\Event;
use App\Models\Media;
use App\Models\Organization;
use App\Models\OrganizationPeriod;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    public function __construct(
        protected SettingsService $settingsService
    ) {}

    public function index(): Response
    {
        $rtOrg = Organization::where('type', 'rt')->first();
        $ktOrg = Organization::where('type', 'karang_taruna')->first();

        $latestAnnouncements = Content::published()
            ->where('type', 'announcement')
            ->latest('published_at')
            ->take(4)
            ->get();

        $upcomingEvents = Content::published()
            ->where('type', 'event')
            ->whereHas('event', function ($query) {
                $query->where('start_at', '>=', now()->startOfDay());
            })
            ->with('event')
            ->orderBy(
                Event::select('start_at')
                    ->whereColumn('events.content_id', 'contents.id')
            )
            ->take(3)
            ->get();

        $latestActivities = Content::published()
            ->where('type', 'activity')
            ->with(['activity.gallery.media'])
            ->latest('published_at')
            ->take(4)
            ->get();

        $latestArticles = Content::published()
            ->where('type', 'article')
            ->latest('published_at')
            ->take(3)
            ->get();

        return Inertia::render('public/Home', [
            'rt' => $rtOrg,
            'karangTaruna' => $ktOrg,
            'latestAnnouncements' => $latestAnnouncements,
            'upcomingEvents' => $upcomingEvents,
            'latestActivities' => $latestActivities,
            'latestArticles' => $latestArticles,
        ]);
    }

    public function profileRt(): Response
    {
        $rt = Organization::where('type', 'rt')
            ->with([
                'activePeriod.memberPositions' => function ($query) {
                    $query->select('position_member_periods.*')
                        ->with(['member', 'position'])
                        ->join('organization_positions', 'position_member_periods.organization_position_id', '=', 'organization_positions.id')
                        ->orderBy('organization_positions.sort_order');
                },
            ])
            ->firstOrFail();

        return Inertia::render('public/ProfileRT', [
            'rt' => $rt,
        ]);
    }

    public function profileKarangTaruna(): Response
    {
        $kt = Organization::where('type', 'karang_taruna')
            ->with([
                'activePeriod.memberPositions' => function ($query) {
                    $query->select('position_member_periods.*')
                        ->with(['member', 'position'])
                        ->join('organization_positions', 'position_member_periods.organization_position_id', '=', 'organization_positions.id')
                        ->orderBy('organization_positions.sort_order');
                },
                'members' => function ($query) {
                    $query->where('status', 'active')->orderBy('name');
                },
            ])
            ->firstOrFail();

        return Inertia::render('public/ProfileKarangTaruna', [
            'karangTaruna' => $kt,
        ]);
    }

    public function structure(Request $request): Response
    {
        $orgType = $request->query('org', 'rt');
        $org = Organization::where('type', $orgType)->first() ?? Organization::first();

        $periods = OrganizationPeriod::where('organization_id', $org->id)
            ->orderByDesc('start_year')
            ->get();

        $selectedPeriodId = $request->query('period')
            ? (int) $request->query('period')
            : ($org->activePeriod?->id ?? $periods->first()?->id);

        $selectedPeriod = OrganizationPeriod::with([
            'memberPositions' => function ($query) {
                $query->select('position_member_periods.*')
                    ->with(['member', 'position'])
                    ->join('organization_positions', 'position_member_periods.organization_position_id', '=', 'organization_positions.id')
                    ->orderBy('organization_positions.sort_order');
            },
        ])->find($selectedPeriodId);

        return Inertia::render('public/Structure', [
            'currentOrg' => $org,
            'orgType' => $orgType,
            'periods' => $periods,
            'selectedPeriod' => $selectedPeriod,
        ]);
    }

    public function announcements(Request $request): Response
    {
        $search = $request->query('search');

        $announcements = Content::published()
            ->where('type', 'announcement')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('excerpt', 'like', "%{$search}%"))
            ->with('author')
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('public/Announcements/Index', [
            'announcements' => $announcements,
            'search' => $search,
        ]);
    }

    public function announcementShow(string $slug): Response
    {
        $content = Content::published()
            ->where('type', 'announcement')
            ->where('slug', $slug)
            ->with(['author', 'organization', 'attachments.media'])
            ->firstOrFail();

        $content->increment('view_count');

        $related = Content::published()
            ->where('type', 'announcement')
            ->where('id', '!=', $content->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return Inertia::render('public/Announcements/Show', [
            'announcement' => $content,
            'related' => $related,
        ]);
    }

    public function articles(Request $request): Response
    {
        $search = $request->query('search');

        $articles = Content::published()
            ->where('type', 'article')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('excerpt', 'like', "%{$search}%"))
            ->with('author')
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('public/Articles/Index', [
            'articles' => $articles,
            'search' => $search,
        ]);
    }

    public function articleShow(string $slug): Response
    {
        $content = Content::published()
            ->where('type', 'article')
            ->where('slug', $slug)
            ->with(['author', 'organization', 'attachments.media'])
            ->firstOrFail();

        $content->increment('view_count');

        $related = Content::published()
            ->where('type', 'article')
            ->where('id', '!=', $content->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return Inertia::render('public/Articles/Show', [
            'article' => $content,
            'related' => $related,
        ]);
    }

    public function events(Request $request): Response
    {
        $filter = $request->query('filter', 'upcoming'); // upcoming, past, all

        $events = Content::published()
            ->where('type', 'event')
            ->with(['event', 'organization'])
            ->when($filter === 'upcoming', function ($q) {
                $q->whereHas('event', fn ($eq) => $eq->where('start_at', '>=', now()->startOfDay()))
                    ->orderBy(Event::select('start_at')->whereColumn('events.content_id', 'contents.id'));
            })
            ->when($filter === 'past', function ($q) {
                $q->whereHas('event', fn ($eq) => $eq->where('start_at', '<', now()->startOfDay()))
                    ->orderByDesc(Event::select('start_at')->whereColumn('events.content_id', 'contents.id'));
            })
            ->when($filter === 'all', function ($q) {
                $q->latest('published_at');
            })
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('public/Events/Index', [
            'events' => $events,
            'filter' => $filter,
        ]);
    }

    public function eventShow(string $slug): Response
    {
        $content = Content::published()
            ->where('type', 'event')
            ->where('slug', $slug)
            ->with(['event', 'author', 'organization', 'attachments.media'])
            ->firstOrFail();

        $content->increment('view_count');

        return Inertia::render('public/Events/Show', [
            'event' => $content,
        ]);
    }

    public function activities(Request $request): Response
    {
        $activities = Content::published()
            ->where('type', 'activity')
            ->with(['activity.gallery.media', 'organization'])
            ->latest('published_at')
            ->paginate(9);

        return Inertia::render('public/Activities/Index', [
            'activities' => $activities,
        ]);
    }

    public function activityShow(string $slug): Response
    {
        $content = Content::published()
            ->where('type', 'activity')
            ->where('slug', $slug)
            ->with(['activity.gallery.media', 'author', 'organization', 'attachments.media'])
            ->firstOrFail();

        $content->increment('view_count');

        return Inertia::render('public/Activities/Show', [
            'activity' => $content,
        ]);
    }

    public function gallery(): Response
    {
        $photos = Media::where('mime_type', 'like', 'image/%')
            ->latest()
            ->paginate(16);

        return Inertia::render('public/Gallery/Index', [
            'photos' => $photos,
        ]);
    }

    public function finance(Request $request): Response
    {
        $publicFinance = (bool) $this->settingsService->get('public_finance', true);

        if (! $publicFinance) {
            return Inertia::render('public/Finance', [
                'publicFinance' => false,
                'accounts' => [],
                'transactions' => null,
                'summary' => null,
            ]);
        }

        $orgType = $request->query('org', 'all'); // all, rt, karang_taruna
        $selectedMonth = $request->query('month', date('m'));
        $selectedYear = $request->query('year', date('Y'));

        $accountsQuery = CashAccount::with('organization')->where('is_active', true);
        if ($orgType === 'rt') {
            $accountsQuery->whereHas('organization', fn ($q) => $q->where('type', 'rt'));
        } elseif ($orgType === 'karang_taruna') {
            $accountsQuery->whereHas('organization', fn ($q) => $q->where('type', 'karang_taruna'));
        }
        $accounts = $accountsQuery->get();
        $accountIds = $accounts->pluck('id')->toArray();

        $transactions = CashTransaction::whereIn('cash_account_id', $accountIds)
            ->where('status', 'confirmed')
            ->when($selectedMonth && $selectedYear, function ($q) use ($selectedMonth, $selectedYear) {
                $q->whereYear('transaction_date', $selectedYear)
                    ->whereMonth('transaction_date', $selectedMonth);
            })
            ->with(['account.organization', 'category'])
            ->latest('transaction_date')
            ->paginate(15)
            ->withQueryString();

        $totalIncome = CashTransaction::whereIn('cash_account_id', $accountIds)
            ->where('status', 'confirmed')
            ->where('type', 'income')
            ->when($selectedMonth && $selectedYear, function ($q) use ($selectedMonth, $selectedYear) {
                $q->whereYear('transaction_date', $selectedYear)
                    ->whereMonth('transaction_date', $selectedMonth);
            })
            ->sum('amount');

        $totalExpense = CashTransaction::whereIn('cash_account_id', $accountIds)
            ->where('status', 'confirmed')
            ->where('type', 'expense')
            ->when($selectedMonth && $selectedYear, function ($q) use ($selectedMonth, $selectedYear) {
                $q->whereYear('transaction_date', $selectedYear)
                    ->whereMonth('transaction_date', $selectedMonth);
            })
            ->sum('amount');

        $totalCurrentBalance = $accounts->sum('current_balance');

        return Inertia::render('public/Finance', [
            'publicFinance' => true,
            'orgType' => $orgType,
            'selectedMonth' => (int) $selectedMonth,
            'selectedYear' => (int) $selectedYear,
            'accounts' => $accounts,
            'transactions' => $transactions,
            'summary' => [
                'total_balance' => (float) $totalCurrentBalance,
                'period_income' => (float) $totalIncome,
                'period_expense' => (float) $totalExpense,
            ],
        ]);
    }

    public function page(string $slug): Response
    {
        $page = Content::published()
            ->where('type', 'page')
            ->where('slug', $slug)
            ->with(['author', 'organization', 'attachments.media'])
            ->firstOrFail();

        $page->increment('view_count');

        return Inertia::render('public/Page', [
            'page' => $page,
        ]);
    }
}
