<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import type { Organization, ContentItem } from '@/types';
import {
    Megaphone, Calendar, Activity, FileText, ArrowRight,
    MapPin, Users, Sparkles, Clock, Image, ArrowUpRight
} from 'lucide-vue-next';

interface Props {
    rt: Organization;
    karangTaruna: Organization;
    latestAnnouncements: ContentItem[];
    upcomingEvents: ContentItem[];
    latestActivities: ContentItem[];
    latestArticles: ContentItem[];
}

const props = defineProps<Props>();

const formatDate = (dateStr?: string | null) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>
    <PublicLayout>
        <Head title="Beranda Informasi Warga RT 09 & Karang Taruna" />

        <!-- Hero Section -->
        <section class="relative bg-gradient-to-b from-[#00246B] via-[#003087] to-[#001B52] text-white py-14 sm:py-20 overflow-hidden border-b-4 border-[#F5BA2E]">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#009CDE_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none" />

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto space-y-4">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-[#F5BA2E] font-bold text-xs sm:text-sm shadow-xs">
                        <Sparkles class="w-4 h-4" />
                        <span>Portal Informasi Resmi RT 09 / RW 04 Kelurahan Sukamaju</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight text-white">
                        Guyub Rukun, Aman, Nyaman & Asri
                    </h1>

                    <p class="text-base sm:text-lg text-white/90 font-normal leading-relaxed max-w-2xl mx-auto">
                        Wadah komunikasi dan informasi resmi warga lingkungan RT 09. Menghubungkan kegiatan gotong royong, pemberitahuan pengurus, agenda warga, serta wadah kepemudaan Karang Taruna Tunas Karya.
                    </p>

                    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                        <Button :href="route('public.announcements.index')" variant="gold" size="lg">
                            <Megaphone class="w-5 h-5 mr-2" />
                            <span>Lihat Pengumuman</span>
                        </Button>
                        <Button :href="route('public.structure')" variant="secondary" size="lg">
                            <Users class="w-5 h-5 mr-2 text-[#003087]" />
                            <span>Struktur Pengurus</span>
                        </Button>
                    </div>
                </div>

                <!-- Community Photo / Banner Showcase (Configurable in Admin Settings) -->
                <div class="mt-12 max-w-5xl mx-auto">
                    <!-- If custom hero banner is uploaded -->
                    <div
                        v-if="$page.props.settings?.hero_banner_image"
                        class="rounded-3xl overflow-hidden border-2 border-white/20 shadow-level-3 max-h-[380px] relative group"
                    >
                        <img
                            :src="$page.props.settings.hero_banner_image"
                            alt="Suasana Lingkungan RT 09"
                            class="w-full h-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#001B52]/80 via-transparent to-transparent flex items-end p-6">
                            <span class="text-xs sm:text-sm font-bold text-white bg-white/20 backdrop-blur-md px-3 py-1 rounded-full border border-white/30">
                                Suasana & Kebersamaan Warga RT 09
                            </span>
                        </div>
                    </div>

                    <!-- 3-Card Activity & Environment Photo Showcase (Configurable in Admin Dashboard Settings) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Slot 1 -->
                        <div class="rounded-2xl overflow-hidden border border-white/20 bg-white/10 backdrop-blur-md relative h-48 group shadow-level-1">
                            <img
                                v-if="$page.props.settings?.hero_image_1 || latestActivities[0]?.thumbnail"
                                :src="$page.props.settings?.hero_image_1 || latestActivities[0]?.thumbnail"
                                :alt="$page.props.settings?.hero_caption_1 || latestActivities[0]?.title || 'Foto 1'"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div v-else class="w-full h-full bg-gradient-to-br from-[#003087] to-[#009CDE] flex flex-col items-center justify-center p-4 text-center">
                                <Activity class="w-8 h-8 text-white/70 mb-1" />
                                <span class="text-xs font-bold text-white">Kerja Bakti Warga</span>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent flex items-end p-3">
                                <span class="text-xs font-bold text-white truncate">
                                    {{ $page.props.settings?.hero_caption_1 || latestActivities[0]?.title || 'Gotong Royong & Kerja Bakti' }}
                                </span>
                            </div>
                        </div>

                        <!-- Slot 2 -->
                        <div class="rounded-2xl overflow-hidden border border-white/20 bg-white/10 backdrop-blur-md relative h-48 group shadow-level-1">
                            <img
                                v-if="$page.props.settings?.hero_image_2 || latestActivities[1]?.thumbnail"
                                :src="$page.props.settings?.hero_image_2 || latestActivities[1]?.thumbnail"
                                :alt="$page.props.settings?.hero_caption_2 || latestActivities[1]?.title || 'Foto 2'"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div v-else class="w-full h-full bg-gradient-to-br from-[#00246B] to-[#019C34] flex flex-col items-center justify-center p-4 text-center">
                                <Sparkles class="w-8 h-8 text-[#F5BA2E]/80 mb-1" />
                                <span class="text-xs font-bold text-white">Peringatan Hari Kemerdekaan</span>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent flex items-end p-3">
                                <span class="text-xs font-bold text-white truncate">
                                    {{ $page.props.settings?.hero_caption_2 || latestActivities[1]?.title || 'Semarak Pesta Rakyat & Lomba' }}
                                </span>
                            </div>
                        </div>

                        <!-- Slot 3 -->
                        <div class="rounded-2xl overflow-hidden border border-white/20 bg-white/10 backdrop-blur-md relative h-48 group shadow-level-1">
                            <img
                                v-if="$page.props.settings?.hero_image_3 || latestActivities[2]?.thumbnail"
                                :src="$page.props.settings?.hero_image_3 || latestActivities[2]?.thumbnail"
                                :alt="$page.props.settings?.hero_caption_3 || latestActivities[2]?.title || 'Foto 3'"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div v-else class="w-full h-full bg-gradient-to-br from-[#001540] to-[#009CDE] flex flex-col items-center justify-center p-4 text-center">
                                <Users class="w-8 h-8 text-white/70 mb-1" />
                                <span class="text-xs font-bold text-white">Karang Taruna Tunas Karya</span>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent flex items-end p-3">
                                <span class="text-xs font-bold text-white truncate">
                                    {{ $page.props.settings?.hero_caption_3 || latestActivities[2]?.title || 'Kegiatan Pemuda & Karang Taruna' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 1: Papan Pengumuman Warga -->
        <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
                <div>
                    <div class="inline-flex items-center gap-2 text-[#003087] font-bold text-xs uppercase tracking-wider mb-1">
                        <Megaphone class="w-4 h-4 text-[#003087]" />
                        <span>Informasi Warga</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-[#003087]">
                        Papan Pengumuman Terkini
                    </h2>
                </div>
                <Link
                    :href="route('public.announcements.index')"
                    class="text-sm font-bold text-[#003087] hover:text-[#009CDE] flex items-center gap-1 group"
                >
                    <span>Semua Pengumuman</span>
                    <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </Link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <Card
                    v-for="ann in latestAnnouncements"
                    :key="ann.id"
                    hover
                    class="flex flex-col justify-between"
                >
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <Badge variant="primary">
                                {{ ann.organization?.name || 'RT 09' }}
                            </Badge>
                            <span class="text-xs sm:text-sm text-[#687173] font-medium flex items-center gap-1">
                                <Clock class="w-4 h-4" />
                                {{ formatDate(ann.published_at) }}
                            </span>
                        </div>
                        <Link :href="route('public.announcements.show', ann.slug)">
                            <h3 class="text-xl sm:text-2xl font-bold text-[#003087] hover:text-[#009CDE] leading-snug mb-2 transition-colors">
                                {{ ann.title }}
                            </h3>
                        </Link>
                        <p class="text-sm sm:text-base text-[#475569] line-clamp-3 leading-relaxed mb-4">
                            {{ ann.excerpt }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#CBD2D6]/50 flex items-center justify-between">
                        <span class="text-xs sm:text-sm text-[#687173]">Oleh: {{ ann.author?.name || 'Pengurus RT' }}</span>
                        <Link
                            :href="route('public.announcements.show', ann.slug)"
                            class="text-sm sm:text-base font-bold text-[#003087] hover:underline inline-flex items-center gap-1"
                        >
                            Baca Selengkapnya →
                        </Link>
                    </div>
                </Card>
            </div>
        </section>

        <!-- Section 2: Dokumentasi Kegiatan Terbaru (Replacing the old facility boxes) -->
        <section class="py-12 bg-white border-y border-[#CBD2D6]/70">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-8">
                    <div>
                        <div class="inline-flex items-center gap-2 text-[#003087] font-bold text-xs uppercase tracking-wider mb-1">
                            <Activity class="w-4 h-4 text-[#009CDE]" />
                            <span>Dokumentasi Warga</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-[#003087]">
                            Dokumentasi Kegiatan Lingkungan
                        </h2>
                        <p class="text-xs sm:text-sm text-[#475569] mt-1">
                            Rekam jejak kebersamaan warga dalam kegiatan gotong royong, posyandu, dan perayaan di RT 09.
                        </p>
                    </div>
                    <Link
                        :href="route('public.activities.index')"
                        class="text-sm font-bold text-[#003087] hover:text-[#009CDE] flex items-center gap-1 group shrink-0"
                    >
                        <span>Semua Kegiatan & Foto</span>
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </Link>
                </div>

                <div v-if="latestActivities.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <Card
                        v-for="act in latestActivities"
                        :key="act.id"
                        hover
                        padding="none"
                        class="overflow-hidden flex flex-col justify-between"
                    >
                        <div>
                            <div class="h-44 bg-[#00246B] flex items-center justify-center text-white relative overflow-hidden group">
                                <img
                                    v-if="act.thumbnail"
                                    :src="act.thumbnail"
                                    :alt="act.title"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                />
                                <div v-else class="flex flex-col items-center justify-center p-4 text-center">
                                    <Activity class="w-10 h-10 opacity-60 mb-1" />
                                    <span class="text-xs font-semibold text-white/80">Dokumentasi RT 09</span>
                                </div>

                                <div class="absolute bottom-2 left-2">
                                    <Badge size="sm" variant="neutral" class="bg-black/60 text-white border-0 backdrop-blur-xs">
                                        {{ formatDate(act.activity?.activity_date) }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="p-4">
                                <Link :href="route('public.activities.show', act.slug)">
                                    <h4 class="text-base font-bold text-[#003087] hover:text-[#009CDE] line-clamp-2 mb-2 leading-snug">
                                        {{ act.title }}
                                    </h4>
                                </Link>
                                <p class="text-xs sm:text-sm text-[#475569] line-clamp-2 leading-relaxed">
                                    {{ act.excerpt }}
                                </p>
                            </div>
                        </div>

                        <div class="p-4 pt-0">
                            <Link
                                :href="route('public.activities.show', act.slug)"
                                class="text-xs font-bold text-[#003087] hover:underline inline-flex items-center gap-1"
                            >
                                <span>Lihat Dokumentasi</span>
                                <ArrowUpRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </Card>
                </div>

                <div v-else class="p-10 text-center bg-[#F8FAFC] rounded-2xl border border-[#CBD2D6] text-[#687173] text-sm">
                    Belum ada dokumentasi kegiatan warga yang dipublikasikan.
                </div>
            </div>
        </section>

        <!-- Section 3: Agenda Mendatang & Jadwal Lingkungan (Clean, Balanced Full-Width Layout) -->
        <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 text-[#003087] font-bold text-xs uppercase tracking-wider mb-1">
                        <Calendar class="w-4 h-4 text-[#003087]" />
                        <span>Jadwal Lingkungan</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-[#003087]">
                        Agenda & Jadwal Kegiatan Mendatang
                    </h2>
                </div>
                <Link
                    :href="route('public.events.index')"
                    class="text-sm font-bold text-[#003087] hover:text-[#009CDE] flex items-center gap-1 group shrink-0"
                >
                    <span>Semua Agenda</span>
                    <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </Link>
            </div>

            <div v-if="upcomingEvents.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <Card
                    v-for="ev in upcomingEvents"
                    :key="ev.id"
                    hover
                    class="flex flex-col justify-between"
                >
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-16 h-16 rounded-2xl bg-[#EBF3FF] border-2 border-[#009CDE]/30 flex flex-col items-center justify-center text-[#003087] shrink-0 font-black shadow-xs">
                            <span class="text-[11px] uppercase tracking-wider font-bold">{{ new Date(ev.event?.start_at || '').toLocaleDateString('id-ID', { month: 'short' }) }}</span>
                            <span class="text-2xl font-black leading-none">{{ new Date(ev.event?.start_at || '').getDate() }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <Badge size="sm" variant="primary" class="mb-1.5">
                                {{ ev.organization?.name || 'RT 09' }}
                            </Badge>
                            <Link :href="route('public.events.show', ev.slug)">
                                <h4 class="text-base sm:text-lg font-bold text-[#003087] hover:text-[#009CDE] line-clamp-2 leading-snug">
                                    {{ ev.title }}
                                </h4>
                            </Link>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[#CBD2D6]/60 space-y-1.5 text-xs text-[#687173]">
                        <div class="flex items-center gap-2">
                            <Clock class="w-3.5 h-3.5 text-[#003087] shrink-0" />
                            <span>{{ new Date(ev.event?.start_at || '').toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }} WIB</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <MapPin class="w-3.5 h-3.5 text-[#003087] shrink-0" />
                            <span class="truncate">{{ ev.event?.location }}</span>
                        </div>
                    </div>
                </Card>
            </div>

            <div v-else class="p-10 text-center bg-white rounded-2xl border border-[#CBD2D6] text-[#687173] text-sm">
                Belum ada agenda kegiatan mendatang yang dijadwalkan.
            </div>
        </section>

        <!-- Section 4: Artikel & Wawasan Warga -->
        <section v-if="latestArticles.length > 0" class="py-12 bg-white border-t border-[#CBD2D6]/70">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-[#003087] font-bold text-xs uppercase tracking-wider mb-1">
                            <FileText class="w-4 h-4 text-[#003087]" />
                            <span>Kabar & Artikel</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-[#003087]">
                            Artikel & Tips Lingkungan
                        </h2>
                    </div>
                    <Link
                        :href="route('public.articles.index')"
                        class="text-sm font-bold text-[#003087] hover:text-[#009CDE] flex items-center gap-1 group shrink-0"
                    >
                        <span>Semua Artikel</span>
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Card
                        v-for="art in latestArticles"
                        :key="art.id"
                        hover
                        class="flex flex-col justify-between"
                    >
                        <div>
                            <span class="text-xs text-[#687173] font-medium block mb-2">
                                {{ formatDate(art.published_at) }}
                            </span>
                            <Link :href="route('public.articles.show', art.slug)">
                                <h4 class="text-lg font-bold text-[#003087] hover:text-[#009CDE] line-clamp-2 leading-snug mb-2">
                                    {{ art.title }}
                                </h4>
                            </Link>
                            <p class="text-xs sm:text-sm text-[#475569] line-clamp-3 leading-relaxed mb-4">
                                {{ art.excerpt }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-[#CBD2D6]/60 flex items-center justify-between text-xs">
                            <span class="text-[#687173]">Oleh: {{ art.author?.name || 'Warga' }}</span>
                            <Link :href="route('public.articles.show', art.slug)" class="font-bold text-[#003087] hover:underline">
                                Baca Artikel →
                            </Link>
                        </div>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Section 5: Profil Organisasi (RT 09 & Karang Taruna) -->
        <section class="py-12 bg-[#EBF3FF] border-t border-[#CBD2D6]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-8">
                    <h2 class="text-2xl sm:text-3xl font-black text-[#003087] mb-2">
                        Dua Organisasi, Satu Lingkungan Harmonis
                    </h2>
                    <p class="text-sm sm:text-base text-[#475569]">
                        Sinergi kepengurusan Rukun Tetangga dan wadah pemuda Karang Taruna Tunas Karya demi kenyamanan seluruh warga.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Card RT 09 -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-level-1 border border-[#CBD2D6] flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-[#003087] text-white font-black text-lg flex items-center justify-center mb-4 shadow-level-1">
                                RT
                            </div>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-[#003087] mb-2">
                                {{ rt.name }}
                            </h3>
                            <p class="text-sm sm:text-base text-[#475569] leading-relaxed mb-6">
                                {{ rt.description }}
                            </p>
                        </div>
                        <div class="pt-4 border-t border-[#CBD2D6]/60 flex items-center justify-between">
                            <span class="text-xs sm:text-sm font-semibold text-[#003087]">Sekretariat: Blok B</span>
                            <Button :href="route('public.rt.profile')" variant="primary" size="md">
                                Profil RT →
                            </Button>
                        </div>
                    </div>

                    <!-- Card Karang Taruna -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-level-1 border border-[#CBD2D6] flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-[#009CDE] text-white font-black text-lg flex items-center justify-center mb-4 shadow-level-1">
                                KT
                            </div>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-[#003087] mb-2">
                                {{ karangTaruna.name }}
                            </h3>
                            <p class="text-sm sm:text-base text-[#475569] leading-relaxed mb-6">
                                {{ karangTaruna.description }}
                            </p>
                        </div>
                        <div class="pt-4 border-t border-[#CBD2D6]/60 flex items-center justify-between">
                            <span class="text-xs sm:text-sm font-semibold text-[#009CDE]">Wadah Pemuda Warga</span>
                            <Button :href="route('public.kt.profile')" variant="secondary" size="md">
                                Profil Karang Taruna →
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
