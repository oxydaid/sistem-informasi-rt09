<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Modal from '@/components/ui/Modal.vue';
import Pagination from '@/components/ui/Pagination.vue';
import type { PaginatedData, MediaItem } from '@/types';
import { Image as ImageIcon, Maximize2 } from 'lucide-vue-next';

interface Props {
    photos: PaginatedData<MediaItem>;
}

defineProps<Props>();
const activePhoto = ref<MediaItem | null>(null);
</script>

<template>
    <PublicLayout>
        <Head title="Galeri Foto Lingkungan RT 09" />

        <div class="bg-gradient-to-r from-[#001B52] to-[#003087] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <Badge variant="warning" class="mb-3">Album Lingkungan</Badge>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white mb-3">
                        Pustaka Galeri Foto
                    </h1>
                    <p class="text-base sm:text-lg text-white/90">
                        Kumpulan album foto kegiatan gotong royong, kebersihan lingkungan, peringatan HUT RI, dan olahraga pemuda RT 09.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div v-if="photos.data && photos.data.length > 0">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <div
                        v-for="photo in photos.data"
                        :key="photo.id"
                        @click="activePhoto = photo"
                        class="h-48 sm:h-60 rounded-2xl overflow-hidden relative group cursor-pointer border border-[#CBD2D6] shadow-level-1 bg-[#001B52]"
                    >
                        <img
                            :src="photo.url"
                            :alt="photo.alt_text || 'Foto Dokumentasi'"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        />
                        <div class="absolute inset-0 bg-[#003087]/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                            <Maximize2 class="w-8 h-8" />
                        </div>
                    </div>
                </div>

                <Pagination :links="photos.links" />
            </div>

            <div v-else class="p-12 text-center bg-white rounded-3xl border border-dashed border-[#CBD2D6]">
                <ImageIcon class="w-12 h-12 text-[#687173] mx-auto mb-3 opacity-60" />
                <h4 class="text-xl font-bold text-[#1A1A2E] mb-2">Belum ada foto galeri</h4>
                <p class="text-base text-[#687173]">Foto kegiatan warga akan segera ditampilkan di sini.</p>
            </div>

            <!-- Lightbox Modal -->
            <Modal :show="!!activePhoto" max-width="2xl" @close="activePhoto = null">
                <div v-if="activePhoto" class="space-y-3">
                    <img :src="activePhoto.url" :alt="activePhoto.alt_text || 'Foto Kegiatan'" class="w-full h-auto rounded-xl max-h-[75vh] object-contain mx-auto" />
                    <p v-if="activePhoto.caption" class="text-sm text-center text-[#475569]">
                        {{ activePhoto.caption }}
                    </p>
                </div>
            </Modal>
        </div>
    </PublicLayout>
</template>
