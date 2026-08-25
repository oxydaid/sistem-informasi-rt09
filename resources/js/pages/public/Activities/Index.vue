<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Pagination from '@/components/ui/Pagination.vue';
import type { PaginatedData, ContentItem } from '@/types';
import { Activity, Calendar, MapPin, Image as ImageIcon } from 'lucide-vue-next';

interface Props {
    activities: PaginatedData<ContentItem>;
}

defineProps<Props>();

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
        <Head title="Dokumentasi Kegiatan Warga RT 09" />

        <div class="bg-gradient-to-r from-[#001B52] to-[#003087] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <Badge variant="warning" class="mb-3">Dokumentasi & Galeri</Badge>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white mb-3">
                        Dokumentasi Kegiatan
                    </h1>
                    <p class="text-base sm:text-lg text-white/90">
                        Album kenangan dan dokumentasi nyata berbagai kegiatan gotong royong, perlombaan, dan bakti sosial warga RT 09.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div v-if="activities.data && activities.data.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card
                        v-for="act in activities.data"
                        :key="act.id"
                        hover
                        padding="none"
                        class="overflow-hidden flex flex-col justify-between"
                    >
                        <div>
                            <div class="h-48 bg-[#00246B] relative flex items-center justify-center">
                                <img
                                    v-if="act.thumbnail"
                                    :src="act.thumbnail"
                                    :alt="act.title"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="text-white/60 flex flex-col items-center">
                                    <Activity class="w-10 h-10 mb-1" />
                                    <span class="text-xs font-semibold">Dokumentasi RT 09</span>
                                </div>
                                <span class="absolute bottom-2 right-2 bg-[#1A1A2E]/80 backdrop-blur-xs text-white text-xs px-2.5 py-1 rounded-full font-medium flex items-center gap-1">
                                    <ImageIcon class="w-3.5 h-3.5" />
                                    {{ act.activity?.gallery?.length || 0 }} Foto
                                </span>
                            </div>

                            <div class="p-6">
                                <Badge variant="secondary" class="mb-2">
                                    {{ act.organization?.name || 'RT 09' }}
                                </Badge>
                                <Link :href="route('public.activities.show', act.slug)">
                                    <h3 class="text-xl font-bold text-[#003087] hover:text-[#009CDE] leading-snug mb-2 transition-colors">
                                        {{ act.title }}
                                    </h3>
                                </Link>
                                <p class="text-base text-[#475569] line-clamp-3 leading-relaxed">
                                    {{ act.excerpt }}
                                </p>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-[#CBD2D6]/50 bg-[#F8FAFC] flex items-center justify-between text-xs text-[#687173]">
                            <span class="flex items-center gap-1">
                                <Calendar class="w-3.5 h-3.5" />
                                {{ formatDate(act.activity?.activity_date) }}
                            </span>
                            <Link
                                :href="route('public.activities.show', act.slug)"
                                class="text-sm font-bold text-[#003087] hover:underline"
                            >
                                Lihat Galeri →
                            </Link>
                        </div>
                    </Card>
                </div>

                <Pagination :links="activities.links" />
            </div>

            <div v-else class="p-12 text-center bg-white rounded-3xl border border-dashed border-[#CBD2D6]">
                <Activity class="w-12 h-12 text-[#687173] mx-auto mb-3 opacity-60" />
                <h4 class="text-xl font-bold text-[#1A1A2E] mb-2">Belum ada dokumentasi</h4>
                <p class="text-base text-[#687173]">Dokumentasi kegiatan warga belum dipublikasikan.</p>
            </div>
        </div>
    </PublicLayout>
</template>
