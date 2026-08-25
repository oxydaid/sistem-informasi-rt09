<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Pagination from '@/components/ui/Pagination.vue';
import Modal from '@/components/ui/Modal.vue';
import type { MediaItem, PaginatedData } from '@/types';
import { Image as ImageIcon, Upload, Trash2, Search, FileText, Maximize2 } from 'lucide-vue-next';

interface Filters {
    search?: string | null;
    type?: string | null;
}

interface Props {
    media: PaginatedData<MediaItem>;
    filters: Filters;
}

const props = defineProps<Props>();

const uploadForm = useForm({
    files: [] as File[],
});

const previewMedia = ref<MediaItem | null>(null);
const searchQuery = ref(props.filters.search || '');

const handleUpload = () => {
    if (uploadForm.files.length === 0) return;
    uploadForm.post(route('admin.media.store'), {
        forceFormData: true,
        onSuccess: () => {
            uploadForm.reset();
        },
    });
};

const deleteMediaItem = (item: MediaItem) => {
    if (confirm(`Hapus berkas '${item.original_name}' dari server?`)) {
        router.delete(route('admin.media.destroy', item.id));
    }
};

const handleSearch = () => {
    router.get(route('admin.media.index'), {
        search: searchQuery.value || undefined,
        type: props.filters.type || undefined,
    }, { preserveState: true });
};
</script>

<template>
    <AdminLayout title="Pustaka Media & Berkas">
        <Head title="Pustaka Media - Admin" />

        <div class="space-y-6">
            <!-- Upload Box -->
            <Card class="p-6 bg-gradient-to-r from-[#EBF3FF] to-white border-[#009CDE]/40">
                <form @submit.prevent="handleUpload" class="flex flex-wrap items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-[#003087]">Unggah Berkas ke Pustaka Media</h3>
                        <p class="text-xs text-[#687173]">Mendukung gambar (PNG, JPG, WebP) dan dokumen (PDF, XLSX, DOCX). Maksimal 10MB per berkas.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            type="file"
                            multiple
                            required
                            @input="uploadForm.files = Array.from(($event.target as HTMLInputElement).files || [])"
                            class="text-xs text-[#475569]"
                        />
                        <Button type="submit" variant="primary" size="md" :loading="uploadForm.processing">
                            <Upload class="w-4 h-4 mr-1.5" />
                            <span>Unggah</span>
                        </Button>
                    </div>
                </form>
            </Card>

            <!-- Search bar -->
            <div class="flex items-center justify-between">
                <form @submit.prevent="handleSearch" class="relative max-w-sm w-full">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama berkas..."
                        class="h-10 pl-9 pr-4 rounded-xl border border-[#CBD2D6] bg-white text-xs w-full focus:outline-none focus:border-[#003087]"
                    />
                    <Search class="w-4 h-4 text-[#687173] absolute left-3 top-3" />
                </form>
            </div>

            <!-- Media Grid -->
            <div v-if="media.data && media.data.length > 0">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <Card
                        v-for="item in media.data"
                        :key="item.id"
                        padding="none"
                        class="overflow-hidden group relative flex flex-col justify-between"
                    >
                        <div
                            @click="previewMedia = item"
                            class="h-32 bg-[#F8FAFC] flex items-center justify-center cursor-pointer relative overflow-hidden"
                        >
                            <img
                                v-if="item.mime_type.startsWith('image/')"
                                :src="item.url"
                                :alt="item.original_name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                loading="lazy"
                            />
                            <div v-else class="text-center p-3 text-[#003087]">
                                <FileText class="w-10 h-10 mx-auto opacity-70 mb-1" />
                                <span class="text-[10px] font-bold uppercase truncate block max-w-[90px]">{{ item.mime_type.split('/')[1] }}</span>
                            </div>

                            <div class="absolute inset-0 bg-[#003087]/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                <Maximize2 class="w-5 h-5" />
                            </div>
                        </div>

                        <div class="p-2.5 bg-white border-t border-[#CBD2D6]/60 flex items-center justify-between">
                            <div class="min-w-0 pr-1">
                                <div class="text-xs font-bold text-[#1A1A2E] truncate" :title="item.original_name">
                                    {{ item.original_name }}
                                </div>
                                <span class="text-[10px] text-[#687173]">{{ (item.file_size / 1024).toFixed(1) }} KB</span>
                            </div>
                            <button
                                type="button"
                                @click="deleteMediaItem(item)"
                                class="p-1 text-[#D20000] hover:bg-[#FDE7E7] rounded cursor-pointer shrink-0"
                                title="Hapus dari server"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </Card>
                </div>

                <div class="mt-6">
                    <Pagination :links="media.links" />
                </div>
            </div>

            <div v-else class="p-12 text-center bg-white rounded-3xl border border-dashed border-[#CBD2D6]">
                <ImageIcon class="w-12 h-12 text-[#687173] mx-auto mb-3 opacity-60" />
                <h4 class="text-lg font-bold text-[#1A1A2E] mb-1">Pustaka Media Kosong</h4>
                <p class="text-xs text-[#687173]">Unggah berkas untuk digunakan pada artikel, pengumuman, atau dokumen lampiran.</p>
            </div>

            <!-- Preview Modal -->
            <Modal :show="!!previewMedia" max-width="lg" @close="previewMedia = null">
                <div v-if="previewMedia" class="space-y-4">
                    <div v-if="previewMedia.mime_type.startsWith('image/')" class="rounded-xl overflow-hidden bg-[#001B52]">
                        <img :src="previewMedia.url" :alt="previewMedia.original_name" class="w-full h-auto max-h-[60vh] object-contain mx-auto" />
                    </div>
                    <div class="space-y-2 text-xs text-[#475569]">
                        <div><strong class="text-[#003087]">Nama File:</strong> {{ previewMedia.original_name }}</div>
                        <div><strong class="text-[#003087]">URL Publik:</strong> <span class="font-mono">{{ previewMedia.url }}</span></div>
                        <div><strong class="text-[#003087]">Ukuran:</strong> {{ (previewMedia.file_size / 1024).toFixed(1) }} KB</div>
                    </div>
                </div>
            </Modal>
        </div>
    </AdminLayout>
</template>
