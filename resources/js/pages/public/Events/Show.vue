<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import type { ContentItem } from '@/types';
import { Calendar, Clock, MapPin, ExternalLink, ArrowLeft, Share2 } from 'lucide-vue-next';

interface Props {
    event: ContentItem;
}

const props = defineProps<Props>();

const formatDate = (dateStr?: string | null) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const formatTime = (dateStr?: string | null) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

const shareToWhatsApp = () => {
    const text = `*Agenda Acara RT 09: ${props.event.title}*\n\n📅 Waktu: ${formatDate(props.event.event?.start_at)} pukul ${formatTime(props.event.event?.start_at)} WIB\n📍 Lokasi: ${props.event.event?.location || 'Balai Warga'}\n\nLihat rincian lengkapnya:\n${window.location.href}`;
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
};
</script>

<template>
    <PublicLayout>
        <Head :title="`${event.title} - Agenda RT 09`" />

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <Link
                :href="route('public.events.index')"
                class="inline-flex items-center gap-2 text-base font-bold text-[#003087] hover:text-[#009CDE] mb-6"
            >
                <ArrowLeft class="w-5 h-5" />
                <span>Kembali ke Daftar Agenda</span>
            </Link>

            <article class="bg-white rounded-3xl p-6 sm:p-10 border border-[#CBD2D6] shadow-level-1 space-y-8">
                <!-- Header -->
                <header class="space-y-4 border-b border-[#CBD2D6]/70 pb-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <Badge variant="primary">{{ event.organization?.name || 'RT 09' }}</Badge>
                        <Badge variant="warning">Agenda Acara Lingkungan</Badge>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-[#003087] leading-tight">
                        {{ event.title }}
                    </h1>
                </header>

                <!-- Event Details Highlight Box -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6 bg-[#F0F7FC] rounded-2xl border border-[#009CDE]/40">
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <Calendar class="w-6 h-6 text-[#003087] shrink-0 mt-0.5" />
                            <div>
                                <span class="text-xs font-bold text-[#687173] uppercase">Hari & Tanggal</span>
                                <div class="text-base sm:text-lg font-black text-[#003087]">
                                    {{ formatDate(event.event?.start_at) }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <Clock class="w-6 h-6 text-[#003087] shrink-0 mt-0.5" />
                            <div>
                                <span class="text-xs font-bold text-[#687173] uppercase">Waktu Pelaksanaan</span>
                                <div class="text-base sm:text-lg font-black text-[#003087]">
                                    Pukul {{ formatTime(event.event?.start_at) }} WIB
                                    <span v-if="event.event?.end_at">s/d {{ formatTime(event.event?.end_at) }} WIB</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <MapPin class="w-6 h-6 text-[#003087] shrink-0 mt-0.5" />
                            <div>
                                <span class="text-xs font-bold text-[#687173] uppercase">Lokasi / Tempat</span>
                                <div class="text-base sm:text-lg font-black text-[#003087]">
                                    {{ event.event?.location }}
                                </div>
                                <a
                                    v-if="event.event?.maps_url"
                                    :href="event.event.maps_url"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 text-xs text-[#009CDE] font-bold hover:underline mt-1"
                                >
                                    <span>Buka di Google Maps</span>
                                    <ExternalLink class="w-3 h-3" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="event.thumbnail" class="rounded-2xl overflow-hidden shadow-level-1">
                    <img :src="event.thumbnail" :alt="event.title" class="w-full h-auto object-cover" />
                </div>

                <!-- Tiptap Content Body -->
                <div
                    class="prose-tiptap text-lg sm:text-xl text-[#1A1A2E] leading-relaxed"
                    v-html="event.content_html"
                />

                <!-- Social Share -->
                <div class="pt-6 border-t border-[#CBD2D6]/70 flex flex-col sm:flex-row items-center justify-between gap-4 bg-[#F8FAFC] -mx-6 sm:-mx-10 -mb-6 sm:-mb-10 p-6 rounded-b-3xl">
                    <span class="text-sm font-bold text-[#475569]">Ingatkan warga dan tetangga:</span>
                    <button
                        type="button"
                        @click="shareToWhatsApp"
                        class="px-5 py-2.5 bg-[#019C34] hover:bg-[#01862C] text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all cursor-pointer shadow-level-1"
                    >
                        <Share2 class="w-4 h-4" />
                        <span>Bagikan Jadwal via WhatsApp</span>
                    </button>
                </div>
            </article>
        </div>
    </PublicLayout>
</template>
