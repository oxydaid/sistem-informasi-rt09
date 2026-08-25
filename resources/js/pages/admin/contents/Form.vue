<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Select from '@/components/ui/Select.vue';
import TiptapEditor from '@/components/editor/TiptapEditor.vue';
import type { Organization, ContentItem, ContentAttachmentItem, ActivityMediaItem } from '@/types';
import { Save, ArrowLeft, Image as ImageIcon, FileText, Trash2, Upload, Plus } from 'lucide-vue-next';

interface Props {
    organizations: Organization[];
    selectedOrgId: number;
    type: string;
    contentItem?: ContentItem | null;
}

const props = defineProps<Props>();

const isEditing = !!props.contentItem;

const form = useForm({
    organization_id: props.contentItem?.organization_id || props.selectedOrgId,
    type: props.contentItem?.type || props.type || 'announcement',
    title: props.contentItem?.title || '',
    slug: props.contentItem?.slug || '',
    excerpt: props.contentItem?.excerpt || '',
    content: props.contentItem?.content || null,
    status: props.contentItem?.status || 'published',
    published_at: props.contentItem?.published_at ? props.contentItem.published_at.substring(0, 16) : new Date().toISOString().substring(0, 16),
    is_featured: props.contentItem?.is_featured || false,
    thumbnail: null as File | null,
    // Event specific
    event_start_at: props.contentItem?.event?.start_at ? props.contentItem.event.start_at.substring(0, 16) : '',
    event_end_at: props.contentItem?.event?.end_at ? props.contentItem.event.end_at.substring(0, 16) : '',
    event_location: props.contentItem?.event?.location || 'Balai Pertemuan Warga RT 09',
    event_maps_url: props.contentItem?.event?.maps_url || '',
    event_is_online: props.contentItem?.event?.is_online || false,
    // Activity specific
    activity_date: props.contentItem?.activity?.activity_date || new Date().toISOString().split('T')[0],
    activity_location: props.contentItem?.activity?.location || 'Lingkungan RT 09',
});

// Attachments upload form
const docForm = useForm({
    document: null as File | null,
    display_name: '',
});

// Gallery upload form
const galleryForm = useForm({
    photos: [] as File[],
});

const submit = () => {
    if (isEditing && props.contentItem) {
        form.post(route('admin.contents.update', props.contentItem.id), {
            _method: 'put',
            forceFormData: true,
        });
    } else {
        form.post(route('admin.contents.store'), {
            forceFormData: true,
        });
    }
};

const uploadAttachment = () => {
    if (!props.contentItem || !docForm.document) return;
    docForm.post(route('admin.contents.upload_attachment', props.contentItem.id), {
        forceFormData: true,
        onSuccess: () => { docForm.reset(); },
    });
};

const deleteAttachment = (att: ContentAttachmentItem) => {
    if (confirm(`Hapus lampiran '${att.display_name}'? File di server akan dihapus.`)) {
        router.delete(route('admin.contents.delete_attachment', att.id));
    }
};

const uploadGallery = () => {
    if (!props.contentItem || galleryForm.photos.length === 0) return;
    galleryForm.post(route('admin.contents.upload_gallery', props.contentItem.id), {
        forceFormData: true,
        onSuccess: () => { galleryForm.reset(); },
    });
};

const deleteGalleryPhoto = (actMedia: ActivityMediaItem) => {
    if (confirm('Hapus foto galeri ini? File di server akan dihapus.')) {
        router.delete(route('admin.contents.delete_gallery', actMedia.id));
    }
};
</script>

<template>
    <AdminLayout :title="isEditing ? `Edit ${form.type}: ${form.title}` : `Buat ${form.type} Baru`">
        <Head :title="isEditing ? 'Edit Konten' : 'Buat Konten Baru'" />

        <div class="space-y-6">
            <!-- Back button -->
            <Link
                :href="route('admin.contents.index', { org_id: form.organization_id, type: form.type })"
                class="inline-flex items-center gap-2 text-sm font-bold text-[#003087] hover:underline"
            >
                <ArrowLeft class="w-4 h-4" />
                <span>Kembali ke Daftar Konten</span>
            </Link>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Main Details Card -->
                <Card class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Organisasi</label>
                            <select v-model="form.organization_id" required class="w-full h-11 px-3 rounded-lg border border-[#CBD2D6] bg-white text-sm">
                                <option v-for="org in organizations" :key="org.id" :value="org.id">{{ org.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Jenis Konten</label>
                            <select v-model="form.type" required class="w-full h-11 px-3 rounded-lg border border-[#CBD2D6] bg-white text-sm uppercase">
                                <option value="announcement">Pengumuman</option>
                                <option value="article">Artikel / Berita</option>
                                <option value="event">Agenda Acara</option>
                                <option value="activity">Dokumentasi Kegiatan</option>
                                <option value="page">Halaman Kustom</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Status Publikasi</label>
                            <select v-model="form.status" required class="w-full h-11 px-3 rounded-lg border border-[#CBD2D6] bg-white text-sm font-bold">
                                <option value="published">Langsung Publikasikan</option>
                                <option value="draft">Simpan Sebagai Draft</option>
                                <option value="archived">Arsipkan</option>
                            </select>
                        </div>
                    </div>

                    <Input
                        v-model="form.title"
                        label="Judul Konten"
                        placeholder="contoh: Kerja Bakti Akbar Lingkungan RT 09"
                        required
                        :error="form.errors.title"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <Input
                            v-model="form.slug"
                            label="Slug URL (Opsional - Otomatis dibuatkan jika kosong)"
                            placeholder="kerja-bakti-akbar-rt09"
                            :error="form.errors.slug"
                        />

                        <Input
                            v-model="form.published_at"
                            type="datetime-local"
                            label="Tanggal & Waktu Terbit"
                            :error="form.errors.published_at"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Ringkasan Singkat (Excerpt)</label>
                        <textarea
                            v-model="form.excerpt"
                            rows="2"
                            class="w-full p-3 rounded-lg border border-[#CBD2D6] text-sm text-[#1A1A2E] focus:outline-none focus:border-[#003087]"
                            placeholder="Ringkasan 1-2 kalimat untuk tampilan pratinjau kartu..."
                        />
                    </div>

                    <!-- Event specific inputs -->
                    <div v-if="form.type === 'event'" class="p-6 rounded-2xl bg-[#F0F7FC] border border-[#009CDE]/40 space-y-4">
                        <h4 class="font-bold text-[#003087] text-base">Rincian Agenda Acara</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Input
                                v-model="form.event_start_at"
                                type="datetime-local"
                                label="Waktu Mulai Acara"
                                required
                                :error="form.errors.event_start_at"
                            />
                            <Input
                                v-model="form.event_end_at"
                                type="datetime-local"
                                label="Waktu Selesai Acara (Opsional)"
                                :error="form.errors.event_end_at"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Input
                                v-model="form.event_location"
                                label="Lokasi Acara"
                                required
                                :error="form.errors.event_location"
                            />
                            <Input
                                v-model="form.event_maps_url"
                                label="Tautan Google Maps Lokasi (Opsional)"
                                placeholder="https://maps.google.com/..."
                            />
                        </div>
                    </div>

                    <!-- Activity specific inputs -->
                    <div v-if="form.type === 'activity'" class="p-6 rounded-2xl bg-[#F0FDF4] border border-[#019C34]/40 space-y-4">
                        <h4 class="font-bold text-[#019C34] text-base">Rincian Pelaksanaan Kegiatan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Input
                                v-model="form.activity_date"
                                type="date"
                                label="Tanggal Pelaksanaan Kegiatan"
                                required
                                :error="form.errors.activity_date"
                            />
                            <Input
                                v-model="form.activity_location"
                                label="Lokasi Kegiatan"
                                placeholder="contoh: Balai Pertemuan & Lapangan RT 09"
                            />
                        </div>
                    </div>

                    <!-- Thumbnail upload -->
                    <div>
                        <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Foto Sampul Utama / Thumbnail</label>
                        <input
                            type="file"
                            accept="image/*"
                            @input="form.thumbnail = ($event.target as HTMLInputElement).files?.[0] || null"
                            class="w-full text-sm text-[#475569] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#EBF3FF] file:text-[#003087]"
                        />
                        <div v-if="contentItem?.thumbnail" class="mt-2">
                            <img :src="contentItem.thumbnail" alt="Thumbnail" class="w-32 h-20 rounded-xl object-cover border border-[#CBD2D6]" />
                        </div>
                    </div>

                    <!-- Tiptap Rich Editor -->
                    <div>
                        <label class="block text-sm font-semibold text-[#1A1A2E] mb-2">Isi Konten Lengkap (Tiptap Rich Page Builder)</label>
                        <TiptapEditor v-model="form.content" />
                    </div>

                    <div class="pt-6 border-t border-[#CBD2D6]/70 flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="form.is_featured" type="checkbox" class="w-5 h-5 rounded border-[#CBD2D6] text-[#003087]" />
                            <span class="text-sm font-bold text-[#003087]">Tampilkan sebagai Konten Unggulan (Featured)</span>
                        </label>

                        <Button type="submit" variant="primary" size="lg" :loading="form.processing">
                            <Save class="w-5 h-5 mr-2" />
                            <span>{{ isEditing ? 'Simpan Perubahan Konten' : 'Terbitkan Konten' }}</span>
                        </Button>
                    </div>
                </Card>
            </form>

            <!-- Attachments Management (If editing) -->
            <Card v-if="isEditing && contentItem" class="p-8 space-y-6">
                <h3 class="text-xl font-bold text-[#003087] flex items-center gap-2">
                    <FileText class="w-5 h-5 text-[#009CDE]" />
                    <span>Dokumen Lampiran (PDF, Excel, Word)</span>
                </h3>

                <!-- Upload Attachment Form -->
                <form @submit.prevent="uploadAttachment" class="flex flex-wrap items-end gap-3 p-4 bg-[#F8FAFC] rounded-2xl border border-[#CBD2D6]">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-[#687173] uppercase mb-1">Nama Tampilan Dokumen</label>
                        <input
                            v-model="docForm.display_name"
                            type="text"
                            placeholder="contoh: Proposal Kegiatan.pdf"
                            class="h-10 px-3 w-full rounded-lg border border-[#CBD2D6] bg-white text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#687173] uppercase mb-1">Pilih Berkas</label>
                        <input
                            type="file"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
                            required
                            @input="docForm.document = ($event.target as HTMLInputElement).files?.[0] || null"
                            class="text-xs text-[#475569]"
                        />
                    </div>
                    <Button type="submit" variant="primary" size="sm" :loading="docForm.processing">
                        <Upload class="w-4 h-4 mr-1" /> Unggah Berkas
                    </Button>
                </form>

                <!-- Attachment List -->
                <div v-if="contentItem.attachments && contentItem.attachments.length > 0" class="space-y-2">
                    <div
                        v-for="att in contentItem.attachments"
                        :key="att.id"
                        class="p-3 rounded-xl bg-white border border-[#CBD2D6] flex items-center justify-between"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📎</span>
                            <div>
                                <h5 class="text-sm font-bold text-[#1A1A2E]">{{ att.display_name }}</h5>
                                <span class="text-xs text-[#687173]">{{ number_format(att.media?.file_size / 1024, 1) }} KB</span>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="deleteAttachment(att)"
                            class="p-1.5 text-[#D20000] hover:bg-[#FDE7E7] rounded-lg cursor-pointer"
                        >
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </Card>

            <!-- Activity Photo Gallery (If editing and type == activity) -->
            <Card v-if="isEditing && contentItem && form.type === 'activity'" class="p-8 space-y-6">
                <h3 class="text-xl font-bold text-[#003087] flex items-center gap-2">
                    <ImageIcon class="w-5 h-5 text-[#009CDE]" />
                    <span>Galeri Foto Kegiatan</span>
                </h3>

                <form @submit.prevent="uploadGallery" class="p-4 bg-[#F8FAFC] rounded-2xl border border-[#CBD2D6] flex flex-wrap items-center gap-3">
                    <input
                        type="file"
                        multiple
                        accept="image/*"
                        required
                        @input="galleryForm.photos = Array.from(($event.target as HTMLInputElement).files || [])"
                        class="text-sm text-[#475569]"
                    />
                    <Button type="submit" variant="primary" size="sm" :loading="galleryForm.processing">
                        <Upload class="w-4 h-4 mr-1" /> Unggah Foto ke Galeri
                    </Button>
                </form>

                <div v-if="contentItem.activity?.gallery && contentItem.activity.gallery.length > 0" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                    <div
                        v-for="gm in contentItem.activity.gallery"
                        :key="gm.id"
                        class="relative h-28 rounded-xl overflow-hidden border border-[#CBD2D6] group bg-[#001B52]"
                    >
                        <img v-if="gm.media" :src="gm.media.url" alt="Foto" class="w-full h-full object-cover" />
                        <button
                            type="button"
                            @click="deleteGalleryPhoto(gm)"
                            class="absolute top-1 right-1 p-1 bg-[#D20000] text-white rounded-md opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer shadow-xs"
                            title="Hapus foto"
                        >
                            <Trash2 class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </Card>
        </div>
    </AdminLayout>
</template>
