<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Pagination from '@/components/ui/Pagination.vue';
import type { Organization, ContentItem, PaginatedData } from '@/types';
import { Plus, Edit2, Trash2, Search, FileText, Megaphone, Calendar, Activity, Eye, Globe } from 'lucide-vue-next';

interface Filters {
    type?: string | null;
    status?: string | null;
    search?: string | null;
}

interface Props {
    organizations: Organization[];
    selectedOrgId: number;
    contents: PaginatedData<ContentItem>;
    filters: Filters;
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search || '');
const typeFilter = ref(props.filters.type || '');
const statusFilter = ref(props.filters.status || '');

const handleFilter = () => {
    router.get(route('admin.contents.index'), {
        org_id: props.selectedOrgId,
        type: typeFilter.value || undefined,
        status: statusFilter.value || undefined,
        search: searchQuery.value || undefined,
    }, { preserveState: true });
};

const deleteContent = (content: ContentItem) => {
    if (confirm(`Hapus konten '${content.title}'? Seluruh gambar dan dokumen lampirannya di server akan otomatis dibersihkan.`)) {
        router.delete(route('admin.contents.destroy', content.id));
    }
};

const changeOrg = (orgId: number) => {
    router.get(route('admin.contents.index'), { org_id: orgId });
};

const formatDate = (dateStr?: string | null) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};
</script>

<template>
    <AdminLayout title="Manajemen Konten & Publikasi">
        <Head title="Manajemen Konten - Admin" />

        <div class="space-y-6">
            <!-- Top Controls -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <div v-if="organizations.length > 1" class="flex items-center gap-2">
                        <span class="text-xs font-bold text-[#687173] uppercase">Organisasi:</span>
                        <select
                            :value="selectedOrgId"
                            @change="changeOrg(Number(($event.target as HTMLSelectElement).value))"
                            class="h-10 px-3 rounded-xl border border-[#CBD2D6] bg-white text-sm font-bold text-[#003087]"
                        >
                            <option v-for="org in organizations" :key="org.id" :value="org.id">
                                {{ org.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <select
                        v-model="typeFilter"
                        @change="handleFilter"
                        class="h-10 px-3 rounded-xl border border-[#CBD2D6] bg-white text-xs font-semibold text-[#1A1A2E]"
                    >
                        <option value="">Semua Jenis Konten</option>
                        <option value="announcement">Pengumuman</option>
                        <option value="article">Artikel / Berita</option>
                        <option value="event">Agenda Acara</option>
                        <option value="activity">Dokumentasi Kegiatan</option>
                        <option value="page">Halaman Kustom</option>
                    </select>

                    <!-- Search Input -->
                    <form @submit.prevent="handleFilter" class="relative">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari judul..."
                            class="h-10 pl-9 pr-4 rounded-xl border border-[#CBD2D6] bg-white text-xs focus:outline-none focus:border-[#003087]"
                        />
                        <Search class="w-4 h-4 text-[#687173] absolute left-3 top-3" />
                    </form>
                </div>

                <!-- Create dropdown / buttons -->
                <Button :href="route('admin.contents.create', { org_id: selectedOrgId, type: typeFilter || 'announcement' })" variant="primary" size="md">
                    <Plus class="w-4 h-4 mr-1.5" />
                    <span>Buat Konten Baru</span>
                </Button>
            </div>

            <!-- Content Table -->
            <Card padding="none" class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-[#F0F4F8] text-[#003087] font-bold text-xs uppercase border-b border-[#CBD2D6]">
                            <tr>
                                <th class="py-4 px-6">Judul Konten</th>
                                <th class="py-4 px-6">Jenis</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6">Penulis</th>
                                <th class="py-4 px-6">Tanggal Publikasi</th>
                                <th class="py-4 px-6">Dilihat</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD2D6]/60">
                            <tr v-for="cnt in contents.data" :key="cnt.id" class="hover:bg-[#F8FAFC]">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-[#003087] text-base mb-0.5">
                                        {{ cnt.title }}
                                    </div>
                                    <span class="text-xs text-[#687173] font-mono">/{{ cnt.type }}/{{ cnt.slug }}</span>
                                </td>
                                <td class="py-4 px-6 uppercase text-xs font-bold text-[#475569]">
                                    {{ cnt.type }}
                                </td>
                                <td class="py-4 px-6">
                                    <Badge :variant="cnt.status === 'published' ? 'success' : (cnt.status === 'draft' ? 'warning' : 'neutral')">
                                        {{ cnt.status }}
                                    </Badge>
                                </td>
                                <td class="py-4 px-6 text-xs text-[#687173]">
                                    {{ cnt.author?.name || 'Admin' }}
                                </td>
                                <td class="py-4 px-6 text-xs text-[#687173] whitespace-nowrap">
                                    {{ formatDate(cnt.published_at || cnt.updated_at) }}
                                </td>
                                <td class="py-4 px-6 text-xs text-[#687173]">
                                    {{ cnt.view_count }}x
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                    <Link
                                        :href="route('admin.contents.edit', cnt.id)"
                                        class="p-1.5 inline-block text-[#003087] hover:bg-[#EBF3FF] rounded-lg transition-colors"
                                        title="Edit"
                                    >
                                        <Edit2 class="w-4 h-4" />
                                    </Link>
                                    <button
                                        type="button"
                                        @click="deleteContent(cnt)"
                                        class="p-1.5 text-[#D20000] hover:bg-[#FDE7E7] rounded-lg transition-colors cursor-pointer"
                                        title="Hapus"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="contents.data.length === 0">
                                <td colspan="7" class="py-12 text-center text-[#687173]">
                                    Tidak ada konten yang sesuai dengan filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-[#CBD2D6]">
                    <Pagination :links="contents.links" />
                </div>
            </Card>
        </div>
    </AdminLayout>
</template>
