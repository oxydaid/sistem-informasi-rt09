<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Pagination from '@/components/ui/Pagination.vue';
import type { PaginatedData, ContentItem } from '@/types';
import { Megaphone, Search, Clock, ArrowRight, User } from 'lucide-vue-next';

interface Props {
    announcements: PaginatedData<ContentItem>;
    search?: string;
}

const props = defineProps<Props>();
const searchQuery = ref(props.search || '');

const handleSearch = () => {
    router.get(route('public.announcements.index'), {
        search: searchQuery.value,
    }, { preserveState: true });
};

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
        <Head title="Papan Pengumuman Warga RT 09" />

        <div class="bg-gradient-to-r from-[#001B52] to-[#003087] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <Badge variant="warning" class="mb-3">Papan Warga</Badge>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white mb-3">
                        Pengumuman Lingkungan
                    </h1>
                    <p class="text-base sm:text-lg text-white/90">
                        Informasi resmi seputar gotong royong, pemberitahuan pengurus, jadwal kegiatan, dan tata tertib warga.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Search Filter -->
            <div class="mb-8 max-w-md">
                <form @submit.prevent="handleSearch" class="relative flex items-center">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari pengumuman..."
                        class="w-full h-12 pl-11 pr-4 bg-white border border-[#CBD2D6] rounded-xl text-[#1A1A2E] text-base focus:outline-none focus:border-[#003087] shadow-xs"
                    />
                    <Search class="w-5 h-5 text-[#687173] absolute left-3.5" />
                </form>
            </div>

            <!-- List Grid -->
            <div v-if="announcements.data && announcements.data.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card
                        v-for="ann in announcements.data"
                        :key="ann.id"
                        hover
                        class="flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <Badge variant="primary">
                                    {{ ann.organization?.name || 'RT 09' }}
                                </Badge>
                                <span class="text-xs sm:text-sm text-[#687173] flex items-center gap-1 font-medium">
                                    <Clock class="w-3.5 h-3.5" />
                                    {{ formatDate(ann.published_at) }}
                                </span>
                            </div>

                            <Link :href="route('public.announcements.show', ann.slug)">
                                <h3 class="text-xl font-bold text-[#003087] hover:text-[#009CDE] leading-snug mb-3 transition-colors">
                                    {{ ann.title }}
                                </h3>
                            </Link>

                            <p class="text-base text-[#475569] line-clamp-3 leading-relaxed mb-4">
                                {{ ann.excerpt }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-[#CBD2D6]/50 flex items-center justify-between">
                            <span class="text-xs text-[#687173]">
                                {{ ann.author?.name || 'Pengurus RT' }}
                            </span>
                            <Link
                                :href="route('public.announcements.show', ann.slug)"
                                class="text-sm font-bold text-[#003087] hover:underline inline-flex items-center gap-1"
                            >
                                Baca →
                            </Link>
                        </div>
                    </Card>
                </div>

                <Pagination :links="announcements.links" />
            </div>

            <div v-else class="p-12 text-center bg-white rounded-3xl border border-dashed border-[#CBD2D6]">
                <Megaphone class="w-12 h-12 text-[#687173] mx-auto mb-3 opacity-60" />
                <h4 class="text-xl font-bold text-[#1A1A2E] mb-2">Tidak ada pengumuman</h4>
                <p class="text-base text-[#687173]">Pengumuman yang Anda cari tidak ditemukan.</p>
            </div>
        </div>
    </PublicLayout>
</template>
