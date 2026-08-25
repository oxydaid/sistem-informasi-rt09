<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import type { ContentItem } from '@/types';
import { Clock, User, Eye, Share2, FileText, ArrowLeft, Download, Megaphone } from 'lucide-vue-next';

interface Props {
    announcement: ContentItem;
    related: ContentItem[];
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

const shareToWhatsApp = () => {
    const text = `*${props.announcement.title}*\n\n${props.announcement.excerpt || ''}\n\nBaca pengumuman lengkapnya di website RT 09:\n${window.location.href}`;
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
};
</script>

<template>
    <PublicLayout>
        <Head :title="`${announcement.title} - Pengumuman RT 09`" />

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Back button -->
            <Link
                :href="route('public.announcements.index')"
                class="inline-flex items-center gap-2 text-base font-bold text-[#003087] hover:text-[#009CDE] mb-6"
            >
                <ArrowLeft class="w-5 h-5" />
                <span>Kembali ke Daftar Pengumuman</span>
            </Link>

            <article class="bg-white rounded-3xl p-6 sm:p-10 border border-[#CBD2D6] shadow-level-1 space-y-8">
                <!-- Header -->
                <header class="space-y-4 border-b border-[#CBD2D6]/70 pb-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <Badge variant="primary">{{ announcement.organization?.name || 'RT 09' }}</Badge>
                        <Badge variant="warning">Pengumuman Resmi</Badge>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-[#003087] leading-tight">
                        {{ announcement.title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-sm sm:text-base text-[#687173] font-medium">
                        <span class="flex items-center gap-1.5">
                            <Clock class="w-4 h-4 text-[#003087]" />
                            {{ formatDate(announcement.published_at) }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <User class="w-4 h-4 text-[#003087]" />
                            {{ announcement.author?.name || 'Pengurus' }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <Eye class="w-4 h-4 text-[#003087]" />
                            {{ announcement.view_count }}x dibaca
                        </span>
                    </div>
                </header>

                <!-- Featured Thumbnail if exists -->
                <div v-if="announcement.thumbnail" class="rounded-2xl overflow-hidden shadow-level-1">
                    <img :src="announcement.thumbnail" :alt="announcement.title" class="w-full h-auto object-cover" />
                </div>

                <!-- Tiptap Content Body (Clean, Senior-Friendly Readable Typography) -->
                <div
                    class="prose-tiptap text-lg sm:text-xl text-[#1A1A2E] leading-relaxed"
                    v-html="announcement.content_html"
                />

                <!-- Attachments List -->
                <div v-if="announcement.attachments && announcement.attachments.length > 0" class="pt-6 border-t border-[#CBD2D6]/70 space-y-3">
                    <h3 class="text-lg font-bold text-[#003087] flex items-center gap-2">
                        <FileText class="w-5 h-5 text-[#009CDE]" />
                        <span>Dokumen Lampiran Unduhan:</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a
                            v-for="att in announcement.attachments"
                            :key="att.id"
                            :href="att.media?.url"
                            target="_blank"
                            class="p-4 rounded-xl bg-[#F0F7FC] border border-[#009CDE]/40 hover:bg-[#003087] hover:text-white flex items-center justify-between transition-colors group"
                        >
                            <span class="font-bold text-base truncate">{{ att.display_name }}</span>
                            <Download class="w-5 h-5 text-[#009CDE] group-hover:text-white shrink-0 ml-2" />
                        </a>
                    </div>
                </div>

                <!-- Social Share / WhatsApp Button -->
                <div class="pt-6 border-t border-[#CBD2D6]/70 flex flex-col sm:flex-row items-center justify-between gap-4 bg-[#F8FAFC] -mx-6 sm:-mx-10 -mb-6 sm:-mb-10 p-6 rounded-b-3xl">
                    <span class="text-sm font-bold text-[#475569]">Bagikan pengumuman ini ke warga lain:</span>
                    <button
                        type="button"
                        @click="shareToWhatsApp"
                        class="px-5 py-2.5 bg-[#019C34] hover:bg-[#01862C] text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all cursor-pointer shadow-level-1"
                    >
                        <Share2 class="w-4 h-4" />
                        <span>Bagikan ke Grup WhatsApp</span>
                    </button>
                </div>
            </article>

            <!-- Related Announcements -->
            <div v-if="related && related.length > 0" class="mt-12 space-y-4">
                <h3 class="text-2xl font-black text-[#003087]">Pengumuman Terkait Lainnya</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <Card
                        v-for="rel in related"
                        :key="rel.id"
                        hover
                        class="p-4 flex flex-col justify-between"
                    >
                        <Link :href="route('public.announcements.show', rel.slug)">
                            <h4 class="font-bold text-base text-[#003087] hover:text-[#009CDE] line-clamp-2 mb-2">
                                {{ rel.title }}
                            </h4>
                        </Link>
                        <span class="text-xs text-[#687173]">{{ formatDate(rel.published_at) }}</span>
                    </Card>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
