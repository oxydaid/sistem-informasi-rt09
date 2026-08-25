<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Modal from '@/components/ui/Modal.vue';
import type { ContentItem, MediaItem } from '@/types';
import { Calendar, MapPin, ArrowLeft, Image as ImageIcon, Maximize2 } from 'lucide-vue-next';

interface Props {
    activity: ContentItem;
}

const props = defineProps<Props>();
const activePreviewPhoto = ref<MediaItem | null>(null);

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
        <Head :title="`${activity.title} - Dokumentasi RT 09`" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <Link
                :href="route('public.activities.index')"
                class="inline-flex items-center gap-2 text-base font-bold text-[#003087] hover:text-[#009CDE] mb-6"
            >
                <ArrowLeft class="w-5 h-5" />
                <span>Kembali ke Dokumentasi Kegiatan</span>
            </Link>

            <article class="bg-white rounded-3xl p-6 sm:p-10 border border-[#CBD2D6] shadow-level-1 space-y-8">
                <!-- Header -->
                <header class="space-y-4 border-b border-[#CBD2D6]/70 pb-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <Badge variant="primary">{{ activity.organization?.name || 'RT 09' }}</Badge>
                        <Badge variant="success">Dokumentasi Terlaksana</Badge>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-[#003087] leading-tight">
                        {{ activity.title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-4 text-sm sm:text-base text-[#687173] font-medium">
                        <span class="flex items-center gap-1.5">
                            <Calendar class="w-4 h-4 text-[#003087]" />
                            {{ formatDate(activity.activity?.activity_date) }}
                        </span>
                        <span v-if="activity.activity?.location" class="flex items-center gap-1.5">
                            <MapPin class="w-4 h-4 text-[#003087]" />
                            {{ activity.activity.location }}
                        </span>
                    </div>
                </header>

                <div v-if="activity.thumbnail" class="rounded-2xl overflow-hidden shadow-level-1">
                    <img :src="activity.thumbnail" :alt="activity.title" class="w-full h-auto object-cover max-h-[450px]" />
                </div>

                <!-- Tiptap Content Body -->
                <div
                    class="prose-tiptap text-lg sm:text-xl text-[#1A1A2E] leading-relaxed"
                    v-html="activity.content_html"
                />

                <!-- Photo Gallery Grid -->
                <div v-if="activity.activity?.gallery && activity.activity.gallery.length > 0" class="pt-8 border-t border-[#CBD2D6]/70 space-y-4">
                    <h3 class="text-2xl font-black text-[#003087] flex items-center gap-2">
                        <ImageIcon class="w-6 h-6 text-[#009CDE]" />
                        <span>Galeri Foto Dokumentasi ({{ activity.activity.gallery.length }} Foto)</span>
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        <div
                            v-for="item in activity.activity.gallery"
                            :key="item.id"
                            @click="activePreviewPhoto = item.media || null"
                            class="relative h-40 rounded-2xl overflow-hidden group cursor-pointer border border-[#CBD2D6] shadow-level-1 bg-[#001B52]"
                        >
                            <img
                                v-if="item.media"
                                :src="item.media.url"
                                :alt="item.media.alt_text || activity.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 bg-[#003087]/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                <Maximize2 class="w-6 h-6" />
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Lightbox Photo Preview Modal -->
            <Modal
                :show="!!activePreviewPhoto"
                max-width="2xl"
                @close="activePreviewPhoto = null"
            >
                <div v-if="activePreviewPhoto" class="space-y-3">
                    <img :src="activePreviewPhoto.url" :alt="activePreviewPhoto.alt_text || 'Foto Kegiatan'" class="w-full h-auto rounded-xl max-h-[75vh] object-contain mx-auto" />
                    <p v-if="activePreviewPhoto.caption" class="text-sm text-center text-[#475569]">
                        {{ activePreviewPhoto.caption }}
                    </p>
                </div>
            </Modal>
        </div>
    </PublicLayout>
</template>
