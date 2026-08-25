<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import type { Organization } from '@/types';
import { Building, Save, Image as ImageIcon } from 'lucide-vue-next';

interface Props {
    organization: Organization;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.organization.name,
    code: props.organization.code || '',
    description: props.organization.description || '',
    address: props.organization.address || '',
    phone: props.organization.phone || '',
    email: props.organization.email || '',
    logo: null as File | null,
    cover_image: null as File | null,
});

const submit = () => {
    form.post(route('admin.organization.update', props.organization.id), {
        _method: 'put',
        forceFormData: true,
    });
};
</script>

<template>
    <AdminLayout :title="`Profil ${organization.name}`">
        <Head :title="`Profil ${organization.name} - Admin`" />

        <div class="space-y-6">
            <!-- Organization Switcher if allowed -->
            <div class="flex items-center gap-3">
                <Link
                    :href="route('admin.organization.profile', 'rt')"
                    class="px-5 py-2.5 rounded-xl font-bold text-sm border transition-all"
                    :class="organization.type === 'rt' ? 'bg-[#003087] text-white border-[#003087] shadow-level-1' : 'bg-white text-[#475569] border-[#CBD2D6] hover:bg-[#EBF3FF]'"
                >
                    Profil RT 09
                </Link>
                <Link
                    :href="route('admin.organization.profile', 'karang_taruna')"
                    class="px-5 py-2.5 rounded-xl font-bold text-sm border transition-all"
                    :class="organization.type === 'karang_taruna' ? 'bg-[#003087] text-white border-[#003087] shadow-level-1' : 'bg-white text-[#475569] border-[#CBD2D6] hover:bg-[#EBF3FF]'"
                >
                    Profil Karang Taruna
                </Link>
            </div>

            <Card class="p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <Input
                            v-model="form.name"
                            label="Nama Organisasi"
                            required
                            :error="form.errors.name"
                        />

                        <Input
                            v-model="form.code"
                            label="Kode / Singkatan Organisasi"
                            placeholder="contoh: RT-09 / KT-09"
                            :error="form.errors.code"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#1A1A2E] mb-1.5">Deskripsi / Visi Misi Organisasi</label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="w-full p-4 rounded-xl bg-white border border-[#CBD2D6] text-base text-[#1A1A2E] focus:outline-none focus:border-[#003087] focus:ring-2 focus:ring-[#003087]/20"
                            placeholder="Tuliskan gambaran umum visi, misi, dan tujuan organisasi..."
                        />
                        <p v-if="form.errors.description" class="text-xs text-[#D20000] mt-1">{{ form.errors.description }}</p>
                    </div>

                    <Input
                        v-model="form.address"
                        label="Alamat Sekretariat"
                        placeholder="contoh: Jl. Melati Indah Blok B RT 09 / RW 04"
                        :error="form.errors.address"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <Input
                            v-model="form.phone"
                            label="Nomor Telepon / WhatsApp Sekretariat"
                            placeholder="0812-3456-7890"
                            :error="form.errors.phone"
                        />

                        <Input
                            v-model="form.email"
                            type="email"
                            label="Alamat Email Resmi"
                            placeholder="pengurus@rt09.id"
                            :error="form.errors.email"
                        />
                    </div>

                    <!-- Logo Upload -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-[#CBD2D6]/70">
                        <div>
                            <label class="block text-sm font-semibold text-[#1A1A2E] mb-1.5">Logo Organisasi</label>
                            <input
                                type="file"
                                accept="image/*"
                                @input="form.logo = ($event.target as HTMLInputElement).files?.[0] || null"
                                class="w-full text-sm text-[#475569] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#EBF3FF] file:text-[#003087] hover:file:bg-[#D6E6FF]"
                            />
                            <p class="text-xs text-[#687173] mt-1">Maksimal 2MB (PNG, JPG, WebP)</p>
                            <div v-if="organization.logo" class="mt-2">
                                <img :src="organization.logo" alt="Logo" class="w-16 h-16 rounded-xl object-cover border border-[#CBD2D6]" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#1A1A2E] mb-1.5">Foto Sampul / Banner Wilayah</label>
                            <input
                                type="file"
                                accept="image/*"
                                @input="form.cover_image = ($event.target as HTMLInputElement).files?.[0] || null"
                                class="w-full text-sm text-[#475569] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#EBF3FF] file:text-[#003087] hover:file:bg-[#D6E6FF]"
                            />
                            <p class="text-xs text-[#687173] mt-1">Maksimal 4MB</p>
                            <div v-if="organization.cover_image" class="mt-2">
                                <img :src="organization.cover_image" alt="Cover" class="w-32 h-16 rounded-xl object-cover border border-[#CBD2D6]" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-[#CBD2D6]/70 flex justify-end">
                        <Button type="submit" variant="primary" size="lg" :loading="form.processing">
                            <Save class="w-5 h-5 mr-2" />
                            <span>Simpan Perubahan Profil</span>
                        </Button>
                    </div>
                </form>
            </Card>
        </div>
    </AdminLayout>
</template>
