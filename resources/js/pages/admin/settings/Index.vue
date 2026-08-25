<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import { Save, Globe, MessageCircle, Image as ImageIcon, Sparkles, LayoutGrid } from 'lucide-vue-next';

interface Props {
    settings: Record<string, string>;
}

const props = defineProps<Props>();

const logoPreview = ref<string | null>(props.settings.site_logo || '/logo.png');
const faviconPreview = ref<string | null>(props.settings.site_favicon || '/favicon.svg');
const hero1Preview = ref<string | null>(props.settings.hero_image_1 || null);
const hero2Preview = ref<string | null>(props.settings.hero_image_2 || null);
const hero3Preview = ref<string | null>(props.settings.hero_image_3 || null);

const form = useForm({
    site_name: props.settings.site_name || 'Sistem Informasi RT 09',
    site_tagline: props.settings.site_tagline || '',
    site_description: props.settings.site_description || '',
    contact_address: props.settings.contact_address || '',
    contact_phone: props.settings.contact_phone || '',
    contact_wa: props.settings.contact_wa || '',
    contact_email: props.settings.contact_email || '',
    public_finance: props.settings.public_finance === '1' || props.settings.public_finance === 'true',
    meta_keywords: props.settings.meta_keywords || '',
    meta_description: props.settings.meta_description || '',
    logo: null as File | null,
    favicon: null as File | null,
    hero_image_1: null as File | null,
    hero_caption_1: props.settings.hero_caption_1 || 'Gotong Royong & Kerja Bakti',
    hero_image_2: null as File | null,
    hero_caption_2: props.settings.hero_caption_2 || 'Semarak Pesta Rakyat & Lomba',
    hero_image_3: null as File | null,
    hero_caption_3: props.settings.hero_caption_3 || 'Kegiatan Pemuda & Karang Taruna',
});

const onLogoChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] || null;
    form.logo = file;
    if (file) {
        logoPreview.value = URL.createObjectURL(file);
    }
};

const onFaviconChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] || null;
    form.favicon = file;
    if (file) {
        faviconPreview.value = URL.createObjectURL(file);
    }
};

const onHero1Change = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] || null;
    form.hero_image_1 = file;
    if (file) {
        hero1Preview.value = URL.createObjectURL(file);
    }
};

const onHero2Change = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] || null;
    form.hero_image_2 = file;
    if (file) {
        hero2Preview.value = URL.createObjectURL(file);
    }
};

const onHero3Change = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] || null;
    form.hero_image_3 = file;
    if (file) {
        hero3Preview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('admin.settings.update'), {
        forceFormData: true,
    });
};
</script>

<template>
    <AdminLayout title="Pengaturan Sistem & Website">
        <Head title="Pengaturan Sistem - Admin" />

        <div class="space-y-6">
            <Card class="p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-8">
                    <!-- Section 1: Branding & Identitas -->
                    <div class="space-y-5">
                        <div class="border-b border-[#CBD2D6] pb-3 flex items-center gap-2">
                            <Globe class="w-5 h-5 text-[#003087]" />
                            <h3 class="text-lg font-bold text-[#003087]">Identitas & Branding Visual</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <Input
                                v-model="form.site_name"
                                label="Nama Website / Aplikasi"
                                required
                                :error="form.errors.site_name"
                            />
                            <Input
                                v-model="form.site_tagline"
                                label="Slogan / Tagline Lingkungan"
                                placeholder="Guyub Rukun, Aman, Nyaman & Asri"
                                :error="form.errors.site_tagline"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Deskripsi Singkat Website</label>
                            <textarea
                                v-model="form.site_description"
                                rows="2"
                                class="w-full p-3 rounded-lg border border-[#CBD2D6] text-sm text-[#1A1A2E] focus:outline-none focus:border-[#003087]"
                                placeholder="Deskripsi untuk warga dan mesin pencari (SEO)..."
                            />
                        </div>

                        <!-- Logo & Favicon Upload Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                            <!-- Logo Upload Box -->
                            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#CBD2D6] space-y-3">
                                <div>
                                    <label class="block text-sm font-bold text-[#003087] flex items-center gap-1.5">
                                        <Sparkles class="w-4 h-4 text-[#009CDE]" />
                                        <span>Logo Website (Header & Footer)</span>
                                    </label>
                                    <p class="text-xs text-[#687173] mt-0.5">
                                        Format PNG, SVG, atau WEBP transparan (Maks. 2MB).
                                    </p>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl bg-white border border-[#CBD2D6] flex items-center justify-center p-2 shrink-0 shadow-xs overflow-hidden">
                                        <img v-if="logoPreview" :src="logoPreview" alt="Logo preview" class="max-w-full max-h-full object-contain" />
                                        <div v-else class="w-10 h-10 rounded-xl bg-[#003087] flex items-center justify-center text-white font-black text-xs">
                                            RT09
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        <input
                                            type="file"
                                            accept="image/png,image/svg+xml,image/webp,image/jpeg"
                                            @change="onLogoChange"
                                            class="w-full text-xs text-[#687173] file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#003087] file:text-white hover:file:bg-[#00246B] cursor-pointer"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Favicon Upload Box -->
                            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#CBD2D6] space-y-3">
                                <div>
                                    <label class="block text-sm font-bold text-[#003087] flex items-center gap-1.5">
                                        <Globe class="w-4 h-4 text-[#009CDE]" />
                                        <span>Favicon Browser Tab Icon</span>
                                    </label>
                                    <p class="text-xs text-[#687173] mt-0.5">
                                        Ikon kecil tab browser (ICO, PNG, SVG maks. 1MB).
                                    </p>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl bg-white border border-[#CBD2D6] flex items-center justify-center p-2 shrink-0 shadow-xs overflow-hidden">
                                        <img v-if="faviconPreview" :src="faviconPreview" alt="Favicon preview" class="w-8 h-8 object-contain" />
                                        <div v-else class="w-8 h-8 rounded-lg bg-[#009CDE] flex items-center justify-center text-white font-black text-[10px]">
                                            ICO
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        <input
                                            type="file"
                                            accept=".ico,image/png,image/svg+xml,image/x-icon,image/webp"
                                            @change="onFaviconChange"
                                            class="w-full text-xs text-[#687173] file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#003087] file:text-white hover:file:bg-[#00246B] cursor-pointer"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Atur 3 Foto Showcase Hero Beranda -->
                    <div class="space-y-4 pt-2">
                        <div class="border-b border-[#CBD2D6] pb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <LayoutGrid class="w-5 h-5 text-[#003087]" />
                                <h3 class="text-lg font-bold text-[#003087]">Pengaturan 3 Foto Showcase Beranda</h3>
                            </div>
                            <span class="text-xs text-[#687173]">Hero Section Homepage</span>
                        </div>
                        <p class="text-xs sm:text-sm text-[#475569]">
                            Atur foto dokumentasi dan judul label yang tampil pada 3 kartu foto di bagian banner utama halaman depan.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Slot 1 -->
                            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#CBD2D6] flex flex-col justify-between space-y-3">
                                <div>
                                    <span class="text-xs font-bold text-[#003087] uppercase tracking-wider block mb-1">Foto Showcase 1</span>
                                    <div class="w-full h-32 rounded-xl bg-[#003087] overflow-hidden border border-[#CBD2D6] flex items-center justify-center relative mb-3">
                                        <img v-if="hero1Preview" :src="hero1Preview" alt="Slot 1" class="w-full h-full object-cover" />
                                        <div v-else class="text-center p-2 text-white/80">
                                            <ImageIcon class="w-6 h-6 mx-auto mb-1 opacity-70" />
                                            <span class="text-[11px] font-medium block">Default: Foto Kegiatan 1</span>
                                        </div>
                                    </div>

                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="onHero1Change"
                                        class="w-full text-xs text-[#687173] file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#003087] file:text-white cursor-pointer mb-2"
                                    />

                                    <Input
                                        v-model="form.hero_caption_1"
                                        label="Label / Judul Foto 1"
                                        placeholder="Gotong Royong & Kerja Bakti"
                                    />
                                </div>
                            </div>

                            <!-- Slot 2 -->
                            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#CBD2D6] flex flex-col justify-between space-y-3">
                                <div>
                                    <span class="text-xs font-bold text-[#003087] uppercase tracking-wider block mb-1">Foto Showcase 2</span>
                                    <div class="w-full h-32 rounded-xl bg-[#00246B] overflow-hidden border border-[#CBD2D6] flex items-center justify-center relative mb-3">
                                        <img v-if="hero2Preview" :src="hero2Preview" alt="Slot 2" class="w-full h-full object-cover" />
                                        <div v-else class="text-center p-2 text-white/80">
                                            <ImageIcon class="w-6 h-6 mx-auto mb-1 opacity-70" />
                                            <span class="text-[11px] font-medium block">Default: Foto Kegiatan 2</span>
                                        </div>
                                    </div>

                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="onHero2Change"
                                        class="w-full text-xs text-[#687173] file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#003087] file:text-white cursor-pointer mb-2"
                                    />

                                    <Input
                                        v-model="form.hero_caption_2"
                                        label="Label / Judul Foto 2"
                                        placeholder="Peringatan Hari Kemerdekaan"
                                    />
                                </div>
                            </div>

                            <!-- Slot 3 -->
                            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#CBD2D6] flex flex-col justify-between space-y-3">
                                <div>
                                    <span class="text-xs font-bold text-[#003087] uppercase tracking-wider block mb-1">Foto Showcase 3</span>
                                    <div class="w-full h-32 rounded-xl bg-[#001540] overflow-hidden border border-[#CBD2D6] flex items-center justify-center relative mb-3">
                                        <img v-if="hero3Preview" :src="hero3Preview" alt="Slot 3" class="w-full h-full object-cover" />
                                        <div v-else class="text-center p-2 text-white/80">
                                            <ImageIcon class="w-6 h-6 mx-auto mb-1 opacity-70" />
                                            <span class="text-[11px] font-medium block">Default: Foto Pemuda KT</span>
                                        </div>
                                    </div>

                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="onHero3Change"
                                        class="w-full text-xs text-[#687173] file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#003087] file:text-white cursor-pointer mb-2"
                                    />

                                    <Input
                                        v-model="form.hero_caption_3"
                                        label="Label / Judul Foto 3"
                                        placeholder="Karang Taruna Tunas Karya"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Kontak & Hotline WA -->
                    <div class="space-y-4 pt-2">
                        <div class="border-b border-[#CBD2D6] pb-3 flex items-center gap-2">
                            <MessageCircle class="w-5 h-5 text-[#019C34]" />
                            <h3 class="text-lg font-bold text-[#003087]">Kontak & Alamat Sekretariat</h3>
                        </div>

                        <Input
                            v-model="form.contact_address"
                            label="Alamat Sekretariat Bersama"
                            placeholder="Jl. Melati Indah Blok B RT 09 / RW 04 Sukamaju"
                            :error="form.errors.contact_address"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <Input
                                v-model="form.contact_phone"
                                label="Nomor Telepon Tampilan"
                                placeholder="0812-3456-7890"
                                :error="form.errors.contact_phone"
                            />
                            <Input
                                v-model="form.contact_wa"
                                label="Nomor WhatsApp Hotline"
                                helper="Format: 6281234567890"
                                placeholder="6281234567890"
                                :error="form.errors.contact_wa"
                            />
                            <Input
                                v-model="form.contact_email"
                                type="email"
                                label="Email Sekretariat"
                                placeholder="pengurus@rt09.id"
                                :error="form.errors.contact_email"
                            />
                        </div>
                    </div>

                    <!-- Section 4: SEO Metadata -->
                    <div class="space-y-4 pt-2">
                        <div class="border-b border-[#CBD2D6] pb-3">
                            <h3 class="text-lg font-bold text-[#003087]">Metadata SEO Default</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <Input
                                v-model="form.meta_keywords"
                                label="Kata Kunci (Keywords)"
                                placeholder="RT 09, Sukamaju, Karang Taruna, Info Warga"
                                :error="form.errors.meta_keywords"
                            />
                            <Input
                                v-model="form.meta_description"
                                label="Meta Description"
                                placeholder="Portal resmi informasi warga RT 09..."
                                :error="form.errors.meta_description"
                            />
                        </div>
                    </div>

                    <div class="pt-6 border-t border-[#CBD2D6]/70 flex justify-end">
                        <Button type="submit" variant="primary" size="lg" :loading="form.processing">
                            <Save class="w-5 h-5 mr-2" />
                            <span>Simpan Pengaturan</span>
                        </Button>
                    </div>
                </form>
            </Card>
        </div>
    </AdminLayout>
</template>
