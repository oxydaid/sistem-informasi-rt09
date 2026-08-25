<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AuditLog;
use App\Models\Content;
use App\Models\Event;
use App\Models\Media;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $orgId = $user->organization_id;

        $rtOrg = Organization::where('type', 'rt')->with('activePeriod')->first();
        $ktOrg = Organization::where('type', 'karang_taruna')->with('activePeriod')->first();

        // Member stats
        $rtMembersCount = OrganizationMember::where('organization_id', $rtOrg?->id)
            ->where('status', 'active')
            ->count();

        $ktMembersCount = OrganizationMember::where('organization_id', $ktOrg?->id)
            ->where('status', 'active')
            ->count();

        // Content counts
        $totalAnnouncements = Content::where('type', 'announcement')->count();
        $totalEvents = Content::where('type', 'event')->count();
        $totalActivities = Content::where('type', 'activity')->count();
        $totalArticles = Content::where('type', 'article')->count();
        $totalMedia = Media::count();

        $upcomingEventsCount = Event::where('start_at', '>=', now()->startOfDay())->count();

        // Recent contents
        $recentContents = Content::with(['organization', 'author'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Upcoming events
        $upcomingEvents = Content::where('type', 'event')
            ->whereHas('event', fn ($q) => $q->where('start_at', '>=', now()->startOfDay()))
            ->with(['event', 'organization'])
            ->orderBy(Event::select('start_at')->whereColumn('events.content_id', 'contents.id'))
            ->take(4)
            ->get();

        // Recent activity documentations
        $recentActivities = Content::where('type', 'activity')
            ->with(['activity.gallery.media', 'organization'])
            ->latest('published_at')
            ->take(4)
            ->get();

        // Recent members
        $recentMembers = OrganizationMember::with([
            'organization',
            'positions.position',
            'positions.period',
        ])
            ->latest('created_at')
            ->take(5)
            ->get();

        $recentLogs = $isSuperAdmin
            ? AuditLog::with('user')->latest('created_at')->take(6)->get()
            : [];

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'rt_members_count' => $rtMembersCount,
                'kt_members_count' => $ktMembersCount,
                'total_members' => $rtMembersCount + $ktMembersCount,
                'total_announcements' => $totalAnnouncements,
                'total_events' => $totalEvents,
                'upcoming_events_count' => $upcomingEventsCount,
                'total_activities' => $totalActivities,
                'total_articles' => $totalArticles,
                'total_media' => $totalMedia,
                'total_users' => $isSuperAdmin ? User::count() : null,
                'rt_active_period' => $rtOrg?->activePeriod?->name ?? 'Belum Diatur',
                'kt_active_period' => $ktOrg?->activePeriod?->name ?? 'Belum Diatur',
            ],
            'rt' => $rtOrg,
            'karangTaruna' => $ktOrg,
            'recentContents' => $recentContents,
            'upcomingEvents' => $upcomingEvents,
            'recentActivities' => $recentActivities,
            'recentMembers' => $recentMembers,
            'recentLogs' => $recentLogs,
        ]);
    }
}
