<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import type { SharedProps } from '@/types';
import {
    Menu, X, ChevronDown, ChevronRight, Shield, MapPin, Phone, Mail, Globe,
    Building, Users, Megaphone, Calendar, Activity, FileText, Image
} from 'lucide-vue-next';

interface Props {
    title?: string;
}

defineProps<Props>();
const page = usePage<SharedProps>();

const mobileMenuOpen = ref(false);
const profileDropdownOpen = ref(false);

const appName = computed(() => page.props.appName || 'Sistem Informasi RT 09');
const settings = computed(() => page.props.settings || {});

const isAboutActive = computed(() => {
    return route.current('public.rt.profile') ||
           route.current('public.kt.profile') ||
           route.current('public.structure');
});
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#F5F7FA] text-[#1A1A2E] antialiased">
        <!-- Main Navigation Bar (Clean, Polished, Senior-Friendly, No text wrap glitches) -->
        <nav class="bg-white border-b border-[#CBD2D6] sticky top-0 z-50 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20 gap-4">
                    <!-- Brand / Logo -->
                    <Link :href="route('home')" class="flex items-center gap-3 shrink-0 group">
                        <div v-if="settings.site_logo" class="w-11 h-11 rounded-xl overflow-hidden flex items-center justify-center bg-white border border-[#CBD2D6] p-1 shadow-level-1 shrink-0">
                            <img :src="settings.site_logo" alt="Logo" class="max-w-full max-h-full object-contain" />
                        </div>
                        <div v-else class="w-11 h-11 rounded-xl bg-[#003087] flex items-center justify-center text-white shadow-level-1 group-hover:bg-[#00246B] transition-colors font-black text-lg tracking-tight shrink-0">
                            RT09
                        </div>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-base sm:text-lg text-[#003087] leading-tight whitespace-nowrap">
                                {{ appName }}
                            </span>
                            <span class="text-xs text-[#687173] font-medium whitespace-nowrap">
                                {{ settings.site_tagline || 'RW 04 Kel. Sukamaju' }}
                            </span>
                        </div>
                    </Link>

                    <!-- Desktop Nav Links (Clean, Centered, Never Wraps) -->
                    <div class="hidden lg:flex items-center gap-1 xl:gap-1.5 shrink-0">
                        <!-- Beranda -->
                        <Link
                            :href="route('home')"
                            prefetch
                            class="px-3.5 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150"
                            :class="route.current('home')
                                ? 'bg-[#003087] text-white shadow-level-1'
                                : 'text-[#334155] hover:text-[#003087] hover:bg-[#F0F4F8]'"
                        >
                            Beranda
                        </Link>

                        <!-- Dropdown: Profil & Organisasi -->
                        <div
                            class="relative"
                            @mouseenter="profileDropdownOpen = true"
                            @mouseleave="profileDropdownOpen = false"
                        >
                            <button
                                type="button"
                                @click="profileDropdownOpen = !profileDropdownOpen"
                                class="px-3.5 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150 inline-flex items-center gap-1.5 cursor-pointer"
                                :class="isAboutActive
                                    ? 'bg-[#EBF3FF] text-[#003087]'
                                    : 'text-[#334155] hover:text-[#003087] hover:bg-[#F0F4F8]'"
                            >
                                <span>Organisasi</span>
                                <ChevronDown class="w-4 h-4 transition-transform duration-150" :class="profileDropdownOpen ? 'rotate-180 text-[#003087]' : 'text-[#687173]'" />
                            </button>

                            <!-- Dropdown Menu Box -->
                            <div
                                v-if="profileDropdownOpen"
                                class="absolute left-0 top-full pt-1.5 w-60 z-50 animate-in fade-in zoom-in-95 duration-150"
                            >
                                <div class="bg-white rounded-2xl shadow-level-2 border border-[#CBD2D6] p-2 space-y-1">
                                    <Link
                                        :href="route('public.rt.profile')"
                                        prefetch
                                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-colors"
                                        :class="route.current('public.rt.profile') ? 'bg-[#EBF3FF] text-[#003087]' : 'text-[#334155] hover:bg-[#F8FAFC] hover:text-[#003087]'"
                                    >
                                        <Building class="w-4 h-4 text-[#003087] shrink-0" />
                                        <span>Profil RT 09</span>
                                    </Link>
                                    <Link
                                        :href="route('public.kt.profile')"
                                        prefetch
                                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-colors"
                                        :class="route.current('public.kt.profile') ? 'bg-[#EBF3FF] text-[#003087]' : 'text-[#334155] hover:bg-[#F8FAFC] hover:text-[#003087]'"
                                    >
                                        <Users class="w-4 h-4 text-[#009CDE] shrink-0" />
                                        <span>Karang Taruna</span>
                                    </Link>
                                    <Link
                                        :href="route('public.structure')"
                                        prefetch
                                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-colors"
                                        :class="route.current('public.structure') ? 'bg-[#EBF3FF] text-[#003087]' : 'text-[#334155] hover:bg-[#F8FAFC] hover:text-[#003087]'"
                                    >
                                        <Users class="w-4 h-4 text-[#019C34] shrink-0" />
                                        <span>Struktur Pengurus</span>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Pengumuman -->
                        <Link
                            :href="route('public.announcements.index')"
                            prefetch
                            class="px-3.5 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150"
                            :class="route.current('public.announcements.*')
                                ? 'bg-[#003087] text-white shadow-level-1'
                                : 'text-[#334155] hover:text-[#003087] hover:bg-[#F0F4F8]'"
                        >
                            Pengumuman
                        </Link>

                        <!-- Agenda -->
                        <Link
                            :href="route('public.events.index')"
                            prefetch
                            class="px-3.5 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150"
                            :class="route.current('public.events.*')
                                ? 'bg-[#003087] text-white shadow-level-1'
                                : 'text-[#334155] hover:text-[#003087] hover:bg-[#F0F4F8]'"
                        >
                            Agenda
                        </Link>

                        <!-- Kegiatan -->
                        <Link
                            :href="route('public.activities.index')"
                            prefetch
                            class="px-3.5 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150"
                            :class="route.current('public.activities.*')
                                ? 'bg-[#003087] text-white shadow-level-1'
                                : 'text-[#334155] hover:text-[#003087] hover:bg-[#F0F4F8]'"
                        >
                            Kegiatan
                        </Link>

                        <!-- Artikel -->
                        <Link
                            :href="route('public.articles.index')"
                            prefetch
                            class="px-3.5 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150"
                            :class="route.current('public.articles.*')
                                ? 'bg-[#003087] text-white shadow-level-1'
                                : 'text-[#334155] hover:text-[#003087] hover:bg-[#F0F4F8]'"
                        >
                            Artikel
                        </Link>

                        <!-- Galeri -->
                        <Link
                            :href="route('public.gallery')"
                            prefetch
                            class="px-3.5 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150"
                            :class="route.current('public.gallery')
                                ? 'bg-[#003087] text-white shadow-level-1'
                                : 'text-[#334155] hover:text-[#003087] hover:bg-[#F0F4F8]'"
                        >
                            Galeri
                        </Link>
                    </div>

                    <!-- Right CTA: Login Pengurus / Dashboard -->
                    <div class="hidden sm:flex items-center gap-3 shrink-0">
                        <Link
                            v-if="page.props.auth?.user"
                            :href="route('admin.dashboard')"
                            class="h-10 px-4 bg-[#003087] hover:bg-[#00246B] text-white rounded-xl font-bold text-xs flex items-center gap-2 shadow-level-1 transition-colors whitespace-nowrap"
                        >
                            <Shield class="w-4 h-4 text-[#F5BA2E]" />
                            <span>Dashboard Pengurus</span>
                        </Link>
                        <Link
                            v-else
                            :href="route('login')"
                            class="h-10 px-4 text-[#003087] hover:bg-[#EBF3FF] border border-[#CBD2D6] rounded-xl font-bold text-xs flex items-center gap-1.5 transition-colors whitespace-nowrap"
                        >
                            <Shield class="w-4 h-4 text-[#003087]" />
                            <span>Masuk Pengurus</span>
                        </Link>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="lg:hidden flex items-center gap-2">
                        <button
                            type="button"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="p-2.5 rounded-xl text-[#003087] hover:bg-[#F5F7FA] border border-[#CBD2D6] cursor-pointer"
                            aria-label="Menu Navigasi"
                        >
                            <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
                            <X v-else class="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Drawer as Absolute Overlay (Does NOT push content down) -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div
                    v-if="mobileMenuOpen"
                    class="lg:hidden absolute top-full left-0 right-0 z-50 bg-white border-b border-[#CBD2D6] px-4 pt-3 pb-6 space-y-3 shadow-2xl max-h-[calc(100vh-5rem)] overflow-y-auto"
                >
                <div class="grid grid-cols-2 gap-2">
                    <Link
                        :href="route('home')"
                        @click="mobileMenuOpen = false"
                        class="p-3 rounded-xl text-sm font-bold flex items-center justify-between border"
                        :class="route.current('home') ? 'bg-[#003087] text-white border-[#003087]' : 'bg-[#F8FAFC] text-[#1A1A2E] border-[#CBD2D6]/70'"
                    >
                        <span>Beranda</span>
                        <ChevronRight class="w-4 h-4 opacity-70" />
                    </Link>
                    <Link
                        :href="route('public.rt.profile')"
                        @click="mobileMenuOpen = false"
                        class="p-3 rounded-xl text-sm font-bold flex items-center justify-between border"
                        :class="route.current('public.rt.profile') ? 'bg-[#003087] text-white border-[#003087]' : 'bg-[#F8FAFC] text-[#1A1A2E] border-[#CBD2D6]/70'"
                    >
                        <span>Profil RT 09</span>
                        <ChevronRight class="w-4 h-4 opacity-70" />
                    </Link>
                    <Link
                        :href="route('public.kt.profile')"
                        @click="mobileMenuOpen = false"
                        class="p-3 rounded-xl text-sm font-bold flex items-center justify-between border"
                        :class="route.current('public.kt.profile') ? 'bg-[#003087] text-white border-[#003087]' : 'bg-[#F8FAFC] text-[#1A1A2E] border-[#CBD2D6]/70'"
                    >
                        <span>Karang Taruna</span>
                        <ChevronRight class="w-4 h-4 opacity-70" />
                    </Link>
                    <Link
                        :href="route('public.structure')"
                        @click="mobileMenuOpen = false"
                        class="p-3 rounded-xl text-sm font-bold flex items-center justify-between border"
                        :class="route.current('public.structure') ? 'bg-[#003087] text-white border-[#003087]' : 'bg-[#F8FAFC] text-[#1A1A2E] border-[#CBD2D6]/70'"
                    >
                        <span>Struktur Pengurus</span>
                        <ChevronRight class="w-4 h-4 opacity-70" />
                    </Link>
                    <Link
                        :href="route('public.announcements.index')"
                        @click="mobileMenuOpen = false"
                        class="p-3 rounded-xl text-sm font-bold flex items-center justify-between border"
                        :class="route.current('public.announcements.*') ? 'bg-[#003087] text-white border-[#003087]' : 'bg-[#F8FAFC] text-[#1A1A2E] border-[#CBD2D6]/70'"
                    >
                        <span>Pengumuman</span>
                        <ChevronRight class="w-4 h-4 opacity-70" />
                    </Link>
                    <Link
                        :href="route('public.events.index')"
                        @click="mobileMenuOpen = false"
                        class="p-3 rounded-xl text-sm font-bold flex items-center justify-between border"
                        :class="route.current('public.events.*') ? 'bg-[#003087] text-white border-[#003087]' : 'bg-[#F8FAFC] text-[#1A1A2E] border-[#CBD2D6]/70'"
                    >
                        <span>Agenda</span>
                        <ChevronRight class="w-4 h-4 opacity-70" />
                    </Link>
                    <Link
                        :href="route('public.activities.index')"
                        @click="mobileMenuOpen = false"
                        class="p-3 rounded-xl text-sm font-bold flex items-center justify-between border"
                        :class="route.current('public.activities.*') ? 'bg-[#003087] text-white border-[#003087]' : 'bg-[#F8FAFC] text-[#1A1A2E] border-[#CBD2D6]/70'"
                    >
                        <span>Kegiatan</span>
                        <ChevronRight class="w-4 h-4 opacity-70" />
                    </Link>
                    <Link
                        :href="route('public.articles.index')"
                        @click="mobileMenuOpen = false"
                        class="p-3 rounded-xl text-sm font-bold flex items-center justify-between border"
                        :class="route.current('public.articles.*') ? 'bg-[#003087] text-white border-[#003087]' : 'bg-[#F8FAFC] text-[#1A1A2E] border-[#CBD2D6]/70'"
                    >
                        <span>Artikel</span>
                        <ChevronRight class="w-4 h-4 opacity-70" />
                    </Link>
                    <Link
                        :href="route('public.gallery')"
                        @click="mobileMenuOpen = false"
                        class="col-span-2 p-3 rounded-xl text-sm font-bold flex items-center justify-between border"
                        :class="route.current('public.gallery') ? 'bg-[#003087] text-white border-[#003087]' : 'bg-[#F8FAFC] text-[#1A1A2E] border-[#CBD2D6]/70'"
                    >
                        <span>Galeri Foto Warga</span>
                        <ChevronRight class="w-4 h-4 opacity-70" />
                    </Link>
                </div>

                <div class="pt-2 border-t border-[#CBD2D6]">
                    <Link
                        v-if="page.props.auth?.user"
                        :href="route('admin.dashboard')"
                        class="w-full py-3 bg-[#003087] text-white text-center rounded-xl font-bold text-sm block"
                    >
                        Buka Dashboard Admin
                    </Link>
                    <Link
                        v-else
                        :href="route('login')"
                        class="w-full py-3 bg-[#003087] text-white text-center rounded-xl font-bold text-sm block"
                    >
                        Masuk Portal Pengurus
                    </Link>
                </div>
            </div>
        </transition>
    </nav>

        <!-- Main Body Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Clean Footer -->
        <footer class="bg-[#001B52] text-white mt-16 border-t-4 border-[#009CDE]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Col 1: About RT -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white overflow-hidden flex items-center justify-center p-1 shadow-sm shrink-0">
                                <img :src="settings.site_logo || '/logo.png'" alt="Logo" class="max-w-full max-h-full object-contain" />
                            </div>
                            <span class="font-extrabold text-xl">{{ appName }}</span>
                        </div>
                        <p class="text-sm text-white/80 leading-relaxed">
                            {{ settings.site_description || 'Portal resmi informasi warga RT 09 / RW 04 Kelurahan Sukamaju. Menjalin kebersamaan, kerukunan warga, dan kemajuan lingkungan.' }}
                        </p>
                    </div>

                    <!-- Col 2: Navigation Links -->
                    <div>
                        <h4 class="text-sm font-bold text-[#F5BA2E] uppercase tracking-wider mb-4">
                            Menu Warga
                        </h4>
                        <ul class="space-y-2 text-sm text-white/90 font-medium">
                            <li><Link :href="route('public.rt.profile')" class="hover:text-[#009CDE] transition-colors">Profil RT 09</Link></li>
                            <li><Link :href="route('public.kt.profile')" class="hover:text-[#009CDE] transition-colors">Karang Taruna</Link></li>
                            <li><Link :href="route('public.structure')" class="hover:text-[#009CDE] transition-colors">Struktur Pengurus</Link></li>
                            <li><Link :href="route('public.announcements.index')" class="hover:text-[#009CDE] transition-colors">Pengumuman</Link></li>
                            <li><Link :href="route('public.events.index')" class="hover:text-[#009CDE] transition-colors">Agenda Kegiatan</Link></li>
                            <li><Link :href="route('public.activities.index')" class="hover:text-[#009CDE] transition-colors">Dokumentasi</Link></li>
                        </ul>
                    </div>

                    <!-- Col 3: Contact & Address -->
                    <div>
                        <h4 class="text-sm font-bold text-[#F5BA2E] uppercase tracking-wider mb-4">
                            Sekretariat
                        </h4>
                        <ul class="space-y-3 text-sm text-white/80">
                            <li class="flex items-start gap-2.5">
                                <MapPin class="w-5 h-5 text-[#009CDE] shrink-0 mt-0.5" />
                                <span>{{ settings.contact_address || 'Jl. Melati Indah Blok B, RT 09 / RW 04 Sukamaju' }}</span>
                            </li>
                            <li v-if="settings.contact_phone" class="flex items-center gap-2.5">
                                <Phone class="w-4 h-4 text-[#009CDE] shrink-0" />
                                <span>{{ settings.contact_phone }}</span>
                            </li>
                            <li v-if="settings.contact_email" class="flex items-center gap-2.5">
                                <Mail class="w-4 h-4 text-[#009CDE] shrink-0" />
                                <span>{{ settings.contact_email }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Col 4: Pengurus Login -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold text-[#F5BA2E] uppercase tracking-wider mb-4">
                            Akses Pengurus
                        </h4>
                        <p class="text-xs text-white/70 leading-relaxed">
                            Khusus jajaran pengurus RT dan Karang Taruna untuk mengelola konten, agenda, dan administrasi internal.
                        </p>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-[#003087] rounded-xl font-bold text-xs hover:bg-[#F5BA2E] hover:text-[#1A1A2E] transition-all shadow-level-1"
                        >
                            <Shield class="w-4 h-4" />
                            <span>Portal Pengurus</span>
                        </Link>
                    </div>
                </div>

                <div class="mt-12 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-white/60">
                    <p>© {{ new Date().getFullYear() }} {{ appName }}. RW 04 Kelurahan Sukamaju.</p>
                    <p>Sistem Informasi Rukun Tetangga & Karang Taruna</p>
                </div>
            </div>
        </footer>
    </div>
</template>
