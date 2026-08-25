<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import type { ContentItem, AuditLog, Organization, OrganizationMember } from '@/types';
import {
    Megaphone, Calendar, Activity, Users, PlusCircle, ArrowRight,
    Building, Sparkles, Image, FileText, Clock, MapPin, ShieldCheck, History, UserCheck
} from 'lucide-vue-next';

interface Stats {
    rt_members_count: number;
    kt_members_count: number;
    total_members: number;
    total_announcements: number;
    total_events: number;
    upcoming_events_count: number;
    total_activities: number;
    total_articles: number;
    total_media: number;
    total_users: number | null;
    rt_active_period: string;
    kt_active_period: string;
}

interface Props {
    stats: Stats;
    rt: Organization;
    karangTaruna: Organization;
    recentContents: ContentItem[];
    upcomingEvents: ContentItem[];
    recentActivities: ContentItem[];
    recentMembers: OrganizationMember[];
    recentLogs: AuditLog[];
}

const props = defineProps<Props>();

const formatDate = (dateStr?: string | null) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};
</script>

<template>
    <AdminLayout title="Ringkasan & Dashboard Pengurus">
        <Head title="Dashboard Pengurus - Admin Portal RT 09" />

        <div class="space-y-8">
            <!-- Header Welcome Card -->
            <div class="bg-gradient-to-r from-[#003087] via-[#00246B] to-[#001B52] rounded-3xl p-6 sm:p-8 text-white shadow-level-2 border border-[#009CDE]/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F5BA2E] text-xs font-bold">
                        <Sparkles class="w-3.5 h-3.5" />
                        <span>Sistem Informasi Terpadu Lingkungan RT 09</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-white">
                        Selamat Datang di Portal Pengurus
                    </h2>
                    <p class="text-sm text-white/80 max-w-xl leading-relaxed">
                        Kelola data keanggotaan warga, kepengurusan RT 09, kegiatan pemuda Karang Taruna, dan publikasi informasi secara terpadu.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <Button :href="route('admin.contents.create', { type: 'announcement' })" variant="gold" size="md">
                        <PlusCircle class="w-4 h-4 mr-1.5" />
                        <span>Buat Pengumuman</span>
                    </Button>
                    <Button :href="route('admin.contents.create', { type: 'event' })" variant="secondary" size="md">
                        <Calendar class="w-4 h-4 mr-1.5 text-[#003087]" />
                        <span>Jadwal Agenda</span>
                    </Button>
                </div>
            </div>

            <!-- Top Metric KPI Cards (Focused on Community, Members, Contents, Events) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Pengurus RT 09 -->
                <Card class="p-6 bg-white border-[#CBD2D6] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#003087]">Pengurus RT 09</span>
                            <div class="w-8 h-8 rounded-lg bg-[#EBF3FF] text-[#003087] flex items-center justify-center font-bold text-xs">
                                RT
                            </div>
                        </div>
                        <div class="text-3xl font-black text-[#003087] tabular-nums">
                            {{ stats.rt_members_count }} <span class="text-sm font-semibold text-[#687173]">Pengurus</span>
                        </div>
                        <p class="text-xs text-[#687173] mt-2">
                            Periode Aktif: <strong class="text-[#003087]">{{ stats.rt_active_period }}</strong>
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#CBD2D6]/60 flex items-center justify-between text-xs">
                        <span class="text-[#687173]">Ketua, Sekr, Bend, Humas</span>
                        <Link :href="route('admin.organization.members.index', { org: 'rt' })" class="text-[#003087] font-bold hover:underline">
                            Kelola RT →
                        </Link>
                    </div>
                </Card>

                <!-- Card 2: Anggota Karang Taruna -->
                <Card class="p-6 bg-white border-[#CBD2D6] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#009CDE]">Karang Taruna</span>
                            <div class="w-8 h-8 rounded-lg bg-[#E0F2FE] text-[#009CDE] flex items-center justify-center font-bold text-xs">
                                KT
                            </div>
                        </div>
                        <div class="text-3xl font-black text-[#003087] tabular-nums">
                            {{ stats.kt_members_count }} <span class="text-sm font-semibold text-[#687173]">Anggota Pemuda</span>
                        </div>
                        <p class="text-xs text-[#687173] mt-2">
                            Periode Aktif: <strong class="text-[#009CDE]">{{ stats.kt_active_period }}</strong>
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#CBD2D6]/60 flex items-center justify-between text-xs">
                        <span class="text-[#687173]">Tunas Karya RT 09</span>
                        <Link :href="route('admin.organization.members.index', { org: 'karang_taruna' })" class="text-[#009CDE] font-bold hover:underline">
                            Kelola KT →
                        </Link>
                    </div>
                </Card>

                <!-- Card 3: Pengumuman & Publikasi -->
                <Card class="p-6 bg-white border-[#CBD2D6] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#019C34]">Pengumuman & Berita</span>
                            <Megaphone class="w-5 h-5 text-[#019C34]" />
                        </div>
                        <div class="text-3xl font-black text-[#003087] tabular-nums">
                            {{ stats.total_announcements + stats.total_articles }} <span class="text-sm font-semibold text-[#687173]">Publikasi</span>
                        </div>
                        <p class="text-xs text-[#687173] mt-2">
                            {{ stats.total_announcements }} Pengumuman • {{ stats.total_articles }} Artikel
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#CBD2D6]/60 flex items-center justify-between text-xs">
                        <span class="text-[#687173]">{{ stats.total_media }} Foto di Pustaka</span>
                        <Link :href="route('admin.contents.index')" class="text-[#019C34] font-bold hover:underline">
                            Semua Konten →
                        </Link>
                    </div>
                </Card>

                <!-- Card 4: Agenda & Kegiatan -->
                <Card class="p-6 bg-white border-[#CBD2D6] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#F5BA2E]">Agenda Lingkungan</span>
                            <Calendar class="w-5 h-5 text-[#F5BA2E]" />
                        </div>
                        <div class="text-3xl font-black text-[#003087] tabular-nums">
                            {{ stats.upcoming_events_count }} <span class="text-sm font-semibold text-[#687173]">Agenda Terjadwal</span>
                        </div>
                        <p class="text-xs text-[#687173] mt-2">
                            {{ stats.total_activities }} Dokumentasi Kegiatan Selesai
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#CBD2D6]/60 flex items-center justify-between text-xs">
                        <span class="text-[#687173]">Gotong Royong & Event</span>
                        <Link :href="route('admin.contents.index', { type: 'event' })" class="text-[#003087] font-bold hover:underline">
                            Lihat Jadwal →
                        </Link>
                    </div>
                </Card>
            </div>

            <!-- Organization Management Hub Cards (Explicit RT 09 & Karang Taruna Panels) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Panel 1: Manajemen RT 09 -->
                <Card padding="none" class="overflow-hidden border-[#CBD2D6]">
                    <div class="p-5 bg-[#F0F7FC] border-b border-[#CBD2D6] flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#003087] text-white font-black flex items-center justify-center text-sm shadow-xs">
                                RT
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-[#003087]">Pengurus Rukun Tetangga 09</h3>
                                <p class="text-xs text-[#687173]">Struktur & keanggotaan pengurus RT</p>
                            </div>
                        </div>
                        <Badge variant="primary">{{ stats.rt_members_count }} Pengurus</Badge>
                    </div>

                    <div class="p-5 space-y-3">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                            <Link
                                :href="route('admin.organization.profile', 'rt')"
                                class="p-3 rounded-xl bg-[#F8FAFC] hover:bg-[#EBF3FF] border border-[#CBD2D6]/60 transition-colors flex flex-col items-center gap-1.5"
                            >
                                <Building class="w-4 h-4 text-[#003087]" />
                                <span class="font-bold text-[#003087]">Profil RT</span>
                            </Link>
                            <Link
                                :href="route('admin.organization.positions.index', { org: 'rt' })"
                                class="p-3 rounded-xl bg-[#F8FAFC] hover:bg-[#EBF3FF] border border-[#CBD2D6]/60 transition-colors flex flex-col items-center gap-1.5"
                            >
                                <Users class="w-4 h-4 text-[#003087]" />
                                <span class="font-bold text-[#003087]">Struktur & Seksi</span>
                            </Link>
                            <Link
                                :href="route('admin.organization.periods.index', { org: 'rt' })"
                                class="p-3 rounded-xl bg-[#F8FAFC] hover:bg-[#EBF3FF] border border-[#CBD2D6]/60 transition-colors flex flex-col items-center gap-1.5"
                            >
                                <Calendar class="w-4 h-4 text-[#003087]" />
                                <span class="font-bold text-[#003087]">Periode RT</span>
                            </Link>
                            <Link
                                :href="route('admin.organization.members.index', { org: 'rt' })"
                                class="p-3 rounded-xl bg-[#003087] text-white hover:bg-[#00246B] transition-colors flex flex-col items-center gap-1.5"
                            >
                                <UserCheck class="w-4 h-4 text-[#F5BA2E]" />
                                <span class="font-bold">Anggota RT</span>
                            </Link>
                        </div>
                    </div>
                </Card>

                <!-- Panel 2: Manajemen Karang Taruna Tunas Karya -->
                <Card padding="none" class="overflow-hidden border-[#CBD2D6]">
                    <div class="p-5 bg-[#F0FDF4] border-b border-[#CBD2D6] flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#009CDE] text-white font-black flex items-center justify-center text-sm shadow-xs">
                                KT
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-[#003087]">Karang Taruna Tunas Karya</h3>
                                <p class="text-xs text-[#687173]">Struktur & keanggotaan pemuda</p>
                            </div>
                        </div>
                        <Badge variant="info">{{ stats.kt_members_count }} Anggota</Badge>
                    </div>

                    <div class="p-5 space-y-3">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                            <Link
                                :href="route('admin.organization.profile', 'karang_taruna')"
                                class="p-3 rounded-xl bg-[#F8FAFC] hover:bg-[#E0F2FE] border border-[#CBD2D6]/60 transition-colors flex flex-col items-center gap-1.5"
                            >
                                <Building class="w-4 h-4 text-[#009CDE]" />
                                <span class="font-bold text-[#003087]">Profil KT</span>
                            </Link>
                            <Link
                                :href="route('admin.organization.positions.index', { org: 'karang_taruna' })"
                                class="p-3 rounded-xl bg-[#F8FAFC] hover:bg-[#E0F2FE] border border-[#CBD2D6]/60 transition-colors flex flex-col items-center gap-1.5"
                            >
                                <Users class="w-4 h-4 text-[#009CDE]" />
                                <span class="font-bold text-[#003087]">Struktur KT</span>
                            </Link>
                            <Link
                                :href="route('admin.organization.periods.index', { org: 'karang_taruna' })"
                                class="p-3 rounded-xl bg-[#F8FAFC] hover:bg-[#E0F2FE] border border-[#CBD2D6]/60 transition-colors flex flex-col items-center gap-1.5"
                            >
                                <Calendar class="w-4 h-4 text-[#009CDE]" />
                                <span class="font-bold text-[#003087]">Periode KT</span>
                            </Link>
                            <Link
                                :href="route('admin.organization.members.index', { org: 'karang_taruna' })"
                                class="p-3 rounded-xl bg-[#009CDE] text-white hover:bg-[#0086BF] transition-colors flex flex-col items-center gap-1.5"
                            >
                                <UserCheck class="w-4 h-4 text-white" />
                                <span class="font-bold">Anggota KT</span>
                            </Link>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Two Columns: Upcoming Events & Latest Content Publications -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Col 1: Agenda Acara Mendatang -->
                <Card padding="none" class="overflow-hidden">
                    <div class="p-6 border-b border-[#CBD2D6] bg-[#F8FAFC] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Calendar class="w-5 h-5 text-[#003087]" />
                            <h3 class="text-lg font-bold text-[#003087]">Agenda & Jadwal Mendatang</h3>
                        </div>
                        <Link :href="route('admin.contents.create', { type: 'event' })" class="text-xs font-bold text-[#003087] hover:underline">
                            + Tambah Agenda
                        </Link>
                    </div>

                    <div class="divide-y divide-[#CBD2D6]/50">
                        <div
                            v-for="ev in upcomingEvents"
                            :key="ev.id"
                            class="p-4 flex items-center gap-4 hover:bg-[#F8FAFC] transition-colors"
                        >
                            <div class="w-12 h-12 rounded-xl bg-[#EBF3FF] border border-[#009CDE]/40 flex flex-col items-center justify-center text-[#003087] shrink-0 font-bold">
                                <span class="text-[9px] uppercase">{{ new Date(ev.event?.start_at || '').toLocaleDateString('id-ID', { month: 'short' }) }}</span>
                                <span class="text-lg font-black leading-none">{{ new Date(ev.event?.start_at || '').getDate() }}</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <Link :href="route('admin.contents.edit', ev.id)" class="text-sm font-bold text-[#003087] hover:underline truncate block">
                                    {{ ev.title }}
                                </Link>
                                <div class="flex items-center gap-3 text-xs text-[#687173] mt-0.5">
                                    <span class="flex items-center gap-1">
                                        <Clock class="w-3 h-3" />
                                        {{ new Date(ev.event?.start_at || '').toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }} WIB
                                    </span>
                                    <span class="flex items-center gap-1 truncate">
                                        <MapPin class="w-3 h-3" />
                                        {{ ev.event?.location }}
                                    </span>
                                </div>
                            </div>

                            <Link
                                :href="route('admin.contents.edit', ev.id)"
                                class="px-2.5 py-1 bg-[#F5F7FA] hover:bg-[#003087] hover:text-white text-[#003087] rounded-lg text-xs font-bold transition-colors shrink-0"
                            >
                                Edit
                            </Link>
                        </div>

                        <div v-if="upcomingEvents.length === 0" class="p-8 text-center text-xs text-[#687173]">
                            Belum ada agenda kegiatan mendatang yang dijadwalkan.
                        </div>
                    </div>
                </Card>

                <!-- Col 2: Pengumuman & Berita Terbaru -->
                <Card padding="none" class="overflow-hidden">
                    <div class="p-6 border-b border-[#CBD2D6] bg-[#F8FAFC] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <FileText class="w-5 h-5 text-[#003087]" />
                            <h3 class="text-lg font-bold text-[#003087]">Konten & Publikasi Terbaru</h3>
                        </div>
                        <Link :href="route('admin.contents.index')" class="text-xs font-bold text-[#003087] hover:underline">
                            Lihat Semua →
                        </Link>
                    </div>

                    <div class="divide-y divide-[#CBD2D6]/50">
                        <div
                            v-for="cnt in recentContents"
                            :key="cnt.id"
                            class="p-4 flex items-center justify-between hover:bg-[#F8FAFC] transition-colors"
                        >
                            <div class="min-w-0 pr-3">
                                <Link :href="route('admin.contents.edit', cnt.id)" class="text-sm font-bold text-[#003087] hover:underline truncate block mb-1">
                                    {{ cnt.title }}
                                </Link>
                                <div class="flex items-center gap-2 text-xs text-[#687173]">
                                    <Badge size="sm" :variant="cnt.status === 'published' ? 'success' : 'neutral'">
                                        {{ cnt.status }}
                                    </Badge>
                                    <span class="capitalize">{{ cnt.type }}</span>
                                    <span>•</span>
                                    <span>{{ formatDate(cnt.published_at || cnt.updated_at) }}</span>
                                </div>
                            </div>
                            <Link
                                :href="route('admin.contents.edit', cnt.id)"
                                class="px-2.5 py-1 bg-[#EBF3FF] hover:bg-[#003087] hover:text-white text-[#003087] rounded-lg text-xs font-bold transition-colors shrink-0"
                            >
                                Edit
                            </Link>
                        </div>

                        <div v-if="recentContents.length === 0" class="p-8 text-center text-xs text-[#687173]">
                            Belum ada konten publikasi yang dibuat.
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Recent Members Registered Table -->
            <Card padding="none" class="overflow-hidden">
                <div class="p-6 border-b border-[#CBD2D6] bg-[#F8FAFC] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Users class="w-5 h-5 text-[#003087]" />
                        <h3 class="text-lg font-bold text-[#003087]">Anggota & Pengurus Terdaftar Terbaru</h3>
                    </div>
                    <Link :href="route('admin.organization.members.index')" class="text-xs font-bold text-[#003087] hover:underline">
                        Buka Semua Anggota →
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#F0F4F8] text-[#003087] uppercase border-b border-[#CBD2D6]">
                            <tr>
                                <th class="py-3 px-6">Nama Anggota</th>
                                <th class="py-3 px-6">Organisasi</th>
                                <th class="py-3 px-6">Jabatan Aktif</th>
                                <th class="py-3 px-6">Kontak HP</th>
                                <th class="py-3 px-6">Status</th>
                                <th class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD2D6]/50">
                            <tr v-for="m in recentMembers" :key="m.id" class="hover:bg-[#F8FAFC]">
                                <td class="py-3 px-6 font-bold text-[#1A1A2E] flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-[#003087] text-white flex items-center justify-center font-bold text-xs">
                                        {{ m.name.charAt(0) }}
                                    </div>
                                    <span>{{ m.name }}</span>
                                </td>
                                <td class="py-3 px-6">
                                    <Badge size="sm" :variant="m.organization?.type === 'rt' ? 'primary' : 'info'">
                                        {{ m.organization?.type === 'rt' ? 'RT 09' : 'Karang Taruna' }}
                                    </Badge>
                                </td>
                                <td class="py-3 px-6 text-[#334155]">
                                    <span v-if="m.positions && m.positions.length > 0" class="font-semibold text-[#003087]">
                                        {{ m.positions[0]?.position?.name }}
                                    </span>
                                    <span v-else class="text-[#94A3B8] italic">Anggota Warga</span>
                                </td>
                                <td class="py-3 px-6 text-[#687173]">{{ m.phone || '-' }}</td>
                                <td class="py-3 px-6">
                                    <Badge size="sm" variant="success">{{ m.status }}</Badge>
                                </td>
                                <td class="py-3 px-6 text-right">
                                    <Link
                                        :href="route('admin.organization.members.index', { org_id: m.organization_id, search: m.name })"
                                        class="text-xs font-bold text-[#003087] hover:underline"
                                    >
                                        Detail →
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="recentMembers.length === 0">
                                <td colspan="6" class="p-8 text-center text-xs text-[#687173]">
                                    Belum ada data anggota yang didaftarkan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>

            <!-- Recent Audit Logs (Super Admin Only) -->
            <Card v-if="recentLogs && recentLogs.length > 0" padding="none" class="overflow-hidden">
                <div class="p-6 border-b border-[#CBD2D6] bg-[#F8FAFC] flex items-center justify-between">
                    <h3 class="text-base font-bold text-[#003087] flex items-center gap-2">
                        <History class="w-4 h-4 text-[#009CDE]" />
                        <span>Log Aktivitas Sistem Terbaru</span>
                    </h3>
                    <Link :href="route('admin.audit_logs.index')" class="text-xs font-bold text-[#003087] hover:underline">
                        Buka Riwayat Lengkap →
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#F0F4F8] text-[#003087] uppercase border-b border-[#CBD2D6]">
                            <tr>
                                <th class="py-3 px-6">Waktu</th>
                                <th class="py-3 px-6">Pengguna</th>
                                <th class="py-3 px-6">Aksi</th>
                                <th class="py-3 px-6">Model</th>
                                <th class="py-3 px-6">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD2D6]/50">
                            <tr v-for="log in recentLogs" :key="log.id" class="hover:bg-[#F8FAFC]">
                                <td class="py-3 px-6 whitespace-nowrap text-[#687173]">{{ new Date(log.created_at || '').toLocaleString('id-ID') }}</td>
                                <td class="py-3 px-6 font-bold text-[#1A1A2E]">{{ log.user?.name || 'Sistem' }}</td>
                                <td class="py-3 px-6"><Badge size="sm" variant="primary">{{ log.action }}</Badge></td>
                                <td class="py-3 px-6 text-[#687173]">{{ log.model_type }} #{{ log.model_id }}</td>
                                <td class="py-3 px-6 text-[#687173] font-mono">{{ log.ip_address }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>
        </div>
    </AdminLayout>
</template>
