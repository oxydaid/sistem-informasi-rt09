<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import type { SharedProps } from '@/types';
import {
    LayoutDashboard, Building, Calendar, Users, Briefcase,
    DollarSign, Tag, FileText, Image, Settings, ShieldCheck,
    History, LogOut, Globe, Menu, X, ChevronDown, CheckCircle2, AlertCircle, UserCheck
} from 'lucide-vue-next';

interface Props {
    title?: string;
}

defineProps<Props>();
const page = usePage<SharedProps>();

const sidebarOpen = ref(false);
const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash);
const appName = computed(() => page.props.appName || 'Sistem Informasi RT 09');

const isSuperAdmin = computed(() => user.value?.role === 'super_admin');
const isAdminRt = computed(() => user.value?.role === 'admin_rt' || isSuperAdmin.value);
const isAdminKt = computed(() => user.value?.role === 'admin_karang_taruna' || isSuperAdmin.value);

// Accordion state - all expanded by default
const openMenus = ref({
    rt: true,
    kt: true,
    contents: true,
    finance: true,
    system: true,
});

const toggleMenu = (key: keyof typeof openMenus.value) => {
    openMenus.value[key] = !openMenus.value[key];
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <!-- Fixed Viewport Application Shell (No whole-page body scroll) -->
    <div class="h-screen max-h-screen w-full overflow-hidden bg-[#F5F7FA] text-[#1A1A2E] flex">
        <!-- Sidebar Backdrop on Mobile -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-[#1A1A2E]/60 backdrop-blur-xs lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Modern Fixed Sidebar (Always full height, scrollable nav, pinned footer) -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 h-screen max-h-screen bg-[#0A1629] text-white flex flex-col transition-transform duration-200 lg:static lg:translate-x-0 shadow-level-3 shrink-0 border-r border-white/5"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Sidebar Header (Fixed at top of sidebar) -->
            <div class="h-18 shrink-0 px-5 flex items-center justify-between border-b border-white/10 bg-[#07101E]">
                <Link :href="route('admin.dashboard')" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl overflow-hidden flex items-center justify-center bg-white p-1 shadow-md shrink-0">
                        <img :src="page.props.settings?.site_logo || '/logo.png'" alt="Logo" class="max-w-full max-h-full object-contain" />
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-sm tracking-tight text-white leading-tight">Admin Portal</span>
                        <span class="text-[11px] text-[#94A3B8]">RT 09 & Karang Taruna</span>
                    </div>
                </Link>
                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="lg:hidden text-white/70 hover:text-white p-1 cursor-pointer"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Navigation Links (Independently scrollable) -->
            <div class="flex-1 overflow-y-auto px-3 py-3 space-y-3 text-xs">
                <!-- Dashboard Utama -->
                <Link
                    :href="route('admin.dashboard')"
                    prefetch
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all"
                    :class="route.current('admin.dashboard')
                        ? 'bg-[#003087] text-white shadow-xs border-l-3 border-[#009CDE]'
                        : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                >
                    <LayoutDashboard class="w-4 h-4 text-[#009CDE]" />
                    <span class="text-sm">Dashboard Utama</span>
                </Link>

                <!-- Accordion 1: Pengurus RT 09 -->
                <div v-if="isAdminRt" class="space-y-1">
                    <button
                        type="button"
                        @click="toggleMenu('rt')"
                        class="w-full flex items-center justify-between px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-[#F5BA2E]/90 hover:text-[#F5BA2E] cursor-pointer"
                    >
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#F5BA2E]" />
                            <span>Pengurus RT 09</span>
                        </div>
                        <ChevronDown class="w-3.5 h-3.5 transition-transform" :class="openMenus.rt ? 'rotate-180' : ''" />
                    </button>

                    <div v-if="openMenus.rt" class="space-y-0.5 pl-2 border-l border-white/10 ml-2">
                        <Link
                            :href="route('admin.organization.profile', 'rt')"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.organization.profile') && page.url.includes('rt') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <Building class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Profil RT 09</span>
                        </Link>
                        <Link
                            :href="route('admin.organization.members.index', { org: 'rt' })"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.organization.members.*') && page.url.includes('org=rt') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <Users class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Pengurus & Anggota</span>
                        </Link>
                        <Link
                            :href="route('admin.organization.positions.index', { org: 'rt' })"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.organization.positions.*') && page.url.includes('org=rt') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <Briefcase class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Struktur & Jabatan</span>
                        </Link>
                        <Link
                            :href="route('admin.organization.periods.index', { org: 'rt' })"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.organization.periods.*') && page.url.includes('org=rt') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <Calendar class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Periode Kepengurusan</span>
                        </Link>
                    </div>
                </div>

                <!-- Accordion 2: Karang Taruna Tunas Karya -->
                <div v-if="isAdminKt" class="space-y-1">
                    <button
                        type="button"
                        @click="toggleMenu('kt')"
                        class="w-full flex items-center justify-between px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-[#009CDE] hover:text-cyan-300 cursor-pointer"
                    >
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#009CDE]" />
                            <span>Karang Taruna</span>
                        </div>
                        <ChevronDown class="w-3.5 h-3.5 transition-transform" :class="openMenus.kt ? 'rotate-180' : ''" />
                    </button>

                    <div v-if="openMenus.kt" class="space-y-0.5 pl-2 border-l border-white/10 ml-2">
                        <Link
                            :href="route('admin.organization.profile', 'karang_taruna')"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.organization.profile') && page.url.includes('karang_taruna') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <Building class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Profil Karang Taruna</span>
                        </Link>
                        <Link
                            :href="route('admin.organization.members.index', { org: 'karang_taruna' })"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.organization.members.*') && page.url.includes('karang_taruna') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <UserCheck class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Anggota Pemuda KT</span>
                        </Link>
                        <Link
                            :href="route('admin.organization.positions.index', { org: 'karang_taruna' })"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.organization.positions.*') && page.url.includes('karang_taruna') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <Briefcase class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Struktur Jabatan KT</span>
                        </Link>
                        <Link
                            :href="route('admin.organization.periods.index', { org: 'karang_taruna' })"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.organization.periods.*') && page.url.includes('karang_taruna') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <Calendar class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Periode Karang Taruna</span>
                        </Link>
                    </div>
                </div>

                <!-- Accordion 3: Konten & Publikasi -->
                <div class="space-y-1">
                    <button
                        type="button"
                        @click="toggleMenu('contents')"
                        class="w-full flex items-center justify-between px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-400 hover:text-slate-200 cursor-pointer"
                    >
                        <div class="flex items-center gap-2">
                            <FileText class="w-3.5 h-3.5 text-slate-400" />
                            <span>Konten & Media</span>
                        </div>
                        <ChevronDown class="w-3.5 h-3.5 transition-transform" :class="openMenus.contents ? 'rotate-180' : ''" />
                    </button>

                    <div v-if="openMenus.contents" class="space-y-0.5 pl-2 border-l border-white/10 ml-2">
                        <Link
                            :href="route('admin.contents.index')"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.contents.*') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <FileText class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Semua Konten</span>
                        </Link>
                        <Link
                            :href="route('admin.media.index')"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.media.*') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <Image class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Pustaka Foto & Media</span>
                        </Link>
                    </div>
                </div>

                <!-- Accordion 4: Keuangan Internal -->
                <div class="space-y-1">
                    <button
                        type="button"
                        @click="toggleMenu('finance')"
                        class="w-full flex items-center justify-between px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-400 hover:text-slate-200 cursor-pointer"
                    >
                        <div class="flex items-center gap-2">
                            <DollarSign class="w-3.5 h-3.5 text-[#019C34]" />
                            <span>Keuangan Internal</span>
                        </div>
                        <ChevronDown class="w-3.5 h-3.5 transition-transform" :class="openMenus.finance ? 'rotate-180' : ''" />
                    </button>

                    <div v-if="openMenus.finance" class="space-y-0.5 pl-2 border-l border-white/10 ml-2">
                        <Link
                            :href="route('admin.finance.index')"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.finance.index') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <DollarSign class="w-3.5 h-3.5 text-[#019C34]" />
                            <span>Buku Kas & Transaksi</span>
                        </Link>
                        <Link
                            :href="route('admin.finance.categories.index')"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.finance.categories.*') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <Tag class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Kategori Transaksi</span>
                        </Link>
                    </div>
                </div>

                <!-- Accordion 5: Sistem & Pengaturan (Super Admin) -->
                <div v-if="isSuperAdmin" class="space-y-1">
                    <button
                        type="button"
                        @click="toggleMenu('system')"
                        class="w-full flex items-center justify-between px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-400 hover:text-slate-200 cursor-pointer"
                    >
                        <div class="flex items-center gap-2">
                            <Settings class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Sistem & Pengaturan</span>
                        </div>
                        <ChevronDown class="w-3.5 h-3.5 transition-transform" :class="openMenus.system ? 'rotate-180' : ''" />
                    </button>

                    <div v-if="openMenus.system" class="space-y-0.5 pl-2 border-l border-white/10 ml-2">
                        <Link
                            :href="route('admin.settings.index')"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.settings.*') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <Settings class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Pengaturan Website</span>
                        </Link>
                        <Link
                            :href="route('admin.users.index')"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.users.*') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <ShieldCheck class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Pengguna Admin</span>
                        </Link>
                        <Link
                            :href="route('admin.audit_logs.index')"
                            prefetch
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors"
                            :class="route.current('admin.audit_logs.*') ? 'bg-[#003087] text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
                        >
                            <History class="w-3.5 h-3.5 text-[#009CDE]" />
                            <span>Log Aktivitas</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Sidebar Footer (ALWAYS pinned and visible at bottom of screen) -->
            <div class="h-14 shrink-0 p-3 border-t border-white/10 bg-[#07101E] flex items-center justify-between">
                <Link
                    :href="route('home')"
                    target="_blank"
                    class="flex items-center gap-2 text-xs text-slate-400 hover:text-white transition-colors"
                >
                    <Globe class="w-4 h-4 text-[#009CDE]" />
                    <span>Web Publik</span>
                </Link>
                <button
                    type="button"
                    @click="logout"
                    class="text-xs text-red-400 hover:text-red-300 font-semibold flex items-center gap-1.5 px-2 py-1 rounded-lg hover:bg-red-500/10 cursor-pointer transition-colors"
                    title="Keluar dari Portal Admin"
                >
                    <LogOut class="w-4 h-4" />
                    <span>Keluar</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area (Fixed layout with independent scroll) -->
        <div class="flex-1 flex flex-col h-screen max-h-screen min-w-0 overflow-hidden">
            <!-- Topbar Header (Fixed at top) -->
            <header class="h-18 shrink-0 bg-white border-b border-[#CBD2D6] px-4 sm:px-8 flex items-center justify-between shadow-xs z-10">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="sidebarOpen = true"
                        class="lg:hidden p-2 rounded-lg text-[#003087] border border-[#CBD2D6] cursor-pointer"
                    >
                        <Menu class="w-5 h-5" />
                    </button>
                    <h1 class="text-xl sm:text-2xl font-black text-[#003087]">
                        <slot name="title">{{ title || 'Dashboard' }}</slot>
                    </h1>
                </div>

                <!-- User profile, Web link, & Topbar Logout Action -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <Link
                        :href="route('home')"
                        target="_blank"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#003087] bg-[#EBF3FF] hover:bg-[#D6E6FF] transition-colors"
                    >
                        <Globe class="w-3.5 h-3.5" />
                        <span>Lihat Website</span>
                    </Link>

                    <div class="flex items-center gap-3 pl-3 border-l border-[#CBD2D6]">
                        <div class="w-9 h-9 rounded-full bg-[#003087] text-white font-bold flex items-center justify-center text-xs shadow-level-1 shrink-0">
                            {{ user?.name?.charAt(0) || 'A' }}
                        </div>
                        <div class="hidden md:flex flex-col">
                            <span class="text-sm font-bold text-[#1A1A2E] leading-tight">
                                {{ user?.name }}
                            </span>
                            <span class="text-[11px] font-medium text-[#687173]">
                                {{ user?.role === 'super_admin' ? 'Super Administrator' : (user?.role === 'admin_rt' ? 'Admin RT 09' : 'Admin Karang Taruna') }}
                            </span>
                        </div>
                    </div>

                    <!-- Direct Logout button in topbar -->
                    <button
                        type="button"
                        @click="logout"
                        class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-xl border border-transparent hover:border-red-200 transition-colors cursor-pointer"
                        title="Keluar (Logout)"
                    >
                        <LogOut class="w-4 h-4" />
                    </button>
                </div>
            </header>

            <!-- Flash Alert Toasts (Fixed strip if active) -->
            <div v-if="flash?.success || flash?.error" class="shrink-0 px-4 sm:px-8 pt-4">
                <div
                    v-if="flash.success"
                    class="p-3.5 rounded-xl bg-[#E6F4EA] border border-[#019C34] text-[#019C34] flex items-center justify-between shadow-level-1 font-semibold text-xs sm:text-sm"
                >
                    <div class="flex items-center gap-2.5">
                        <CheckCircle2 class="w-4 h-4 shrink-0" />
                        <span>{{ flash.success }}</span>
                    </div>
                </div>

                <div
                    v-if="flash.error"
                    class="p-3.5 rounded-xl bg-[#FDE7E7] border border-[#D20000] text-[#D20000] flex items-center justify-between shadow-level-1 font-semibold text-xs sm:text-sm"
                >
                    <div class="flex items-center gap-2.5">
                        <AlertCircle class="w-4 h-4 shrink-0" />
                        <span>{{ flash.error }}</span>
                    </div>
                </div>
            </div>

            <!-- Page Content Body (Smooth independent vertical scrolling) -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
