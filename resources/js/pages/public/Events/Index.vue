<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Pagination from '@/components/ui/Pagination.vue';
import type { PaginatedData, ContentItem } from '@/types';
import { Calendar, Clock, MapPin, ArrowRight, ExternalLink } from 'lucide-vue-next';

interface Props {
    events: PaginatedData<ContentItem>;
    filter: 'upcoming' | 'past' | 'all';
}

const props = defineProps<Props>();

const setFilter = (val: string) => {
    router.get(route('public.events.index'), { filter: val }, { preserveState: true });
};

const formatDate = (dateStr?: string | null) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>
    <PublicLayout>
        <Head title="Agenda & Kegiatan Warga RT 09" />

        <div class="bg-gradient-to-r from-[#001B52] to-[#003087] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <Badge variant="warning" class="mb-3">Jadwal Acara</Badge>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white mb-3">
                        Agenda Lingkungan
                    </h1>
                    <p class="text-base sm:text-lg text-white/90">
                        Jadwal kegiatan kerja bakti, pertemuan triwulan warga, turnamen Karang Taruna, dan rapat kepengurusan.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Filter Tabs (Clean, Single Line on Mobile) -->
            <div class="flex flex-wrap items-center gap-2 mb-8">
                <button
                    type="button"
                    @click="setFilter('upcoming')"
                    class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl font-bold text-xs sm:text-sm border transition-all cursor-pointer"
                    :class="filter === 'upcoming' ? 'bg-[#003087] text-white border-[#003087] shadow-level-1' : 'bg-white text-[#475569] border-[#CBD2D6] hover:bg-[#EBF3FF]'"
                >
                    Mendatang
                </button>
                <button
                    type="button"
                    @click="setFilter('past')"
                    class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl font-bold text-xs sm:text-sm border transition-all cursor-pointer"
                    :class="filter === 'past' ? 'bg-[#003087] text-white border-[#003087] shadow-level-1' : 'bg-white text-[#475569] border-[#CBD2D6] hover:bg-[#EBF3FF]'"
                >
                    Selesai
                </button>
                <button
                    type="button"
                    @click="setFilter('all')"
                    class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl font-bold text-xs sm:text-sm border transition-all cursor-pointer"
                    :class="filter === 'all' ? 'bg-[#003087] text-white border-[#003087] shadow-level-1' : 'bg-white text-[#475569] border-[#CBD2D6] hover:bg-[#EBF3FF]'"
                >
                    Semua
                </button>
            </div>

            <!-- List of Events -->
            <div v-if="events.data && events.data.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card
                        v-for="ev in events.data"
                        :key="ev.id"
                        hover
                        class="flex flex-col justify-between"
                    >
                        <div>
                            <!-- Date Ribbon Header -->
                            <div class="flex items-center gap-3 p-3 bg-[#EBF3FF] rounded-xl border border-[#009CDE]/30 mb-4">
                                <div class="w-12 h-12 rounded-lg bg-[#003087] text-white flex flex-col items-center justify-center font-bold text-xs shrink-0">
                                    <span>{{ new Date(ev.event?.start_at || '').toLocaleDateString('id-ID', { month: 'short' }) }}</span>
                                    <span class="text-lg font-black leading-none">{{ new Date(ev.event?.start_at || '').getDate() }}</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-[#003087] uppercase">
                                        {{ ev.organization?.name || 'RT 09' }}
                                    </div>
                                    <div class="text-xs text-[#475569] font-medium flex items-center gap-1">
                                        <Clock class="w-3.5 h-3.5" />
                                        {{ new Date(ev.event?.start_at || '').toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }} WIB
                                    </div>
                                </div>
                            </div>

                            <Link :href="route('public.events.show', ev.slug)">
                                <h3 class="text-xl font-bold text-[#003087] hover:text-[#009CDE] leading-snug mb-2 transition-colors">
                                    {{ ev.title }}
                                </h3>
                            </Link>

                            <p class="text-base text-[#475569] line-clamp-3 leading-relaxed mb-4">
                                {{ ev.excerpt }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-[#CBD2D6]/50 flex flex-col gap-2">
                            <div class="text-xs text-[#687173] flex items-center gap-1.5 truncate">
                                <MapPin class="w-4 h-4 text-[#003087] shrink-0" />
                                <span>{{ ev.event?.location }}</span>
                            </div>
                            <Link
                                :href="route('public.events.show', ev.slug)"
                                class="text-sm font-bold text-[#003087] hover:underline self-end inline-flex items-center gap-1"
                            >
                                Rincian Acara →
                            </Link>
                        </div>
                    </Card>
                </div>

                <Pagination :links="events.links" />
            </div>

            <div v-else class="p-12 text-center bg-white rounded-3xl border border-dashed border-[#CBD2D6]">
                <Calendar class="w-12 h-12 text-[#687173] mx-auto mb-3 opacity-60" />
                <h4 class="text-xl font-bold text-[#1A1A2E] mb-2">Tidak ada agenda</h4>
                <p class="text-base text-[#687173]">Tidak ada jadwal agenda kegiatan yang cocok dengan filter yang dipilih.</p>
            </div>
        </div>
    </PublicLayout>
</template>
