<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Modal from '@/components/ui/Modal.vue';
import Input from '@/components/ui/Input.vue';
import type { Organization, OrganizationPosition } from '@/types';
import { Plus, Edit2, Trash2, Briefcase } from 'lucide-vue-next';

interface Props {
    organizations: Organization[];
    selectedOrgId: number;
    positions: OrganizationPosition[];
}

const props = defineProps<Props>();

const showModal = ref(false);
const editingPos = ref<OrganizationPosition | null>(null);

const form = useForm({
    organization_id: props.selectedOrgId,
    name: '',
    max_members: 1,
    sort_order: 1,
});

const openCreateModal = () => {
    editingPos.value = null;
    form.reset();
    form.organization_id = props.selectedOrgId;
    form.max_members = 1;
    form.sort_order = props.positions.length + 1;
    showModal.value = true;
};

const openEditModal = (pos: OrganizationPosition) => {
    editingPos.value = pos;
    form.organization_id = pos.organization_id;
    form.name = pos.name;
    form.max_members = pos.max_members;
    form.sort_order = pos.sort_order;
    showModal.value = true;
};

const submitForm = () => {
    if (editingPos.value) {
        form.put(route('admin.organization.positions.update', editingPos.value.id), {
            onSuccess: () => { showModal.value = false; },
        });
    } else {
        form.post(route('admin.organization.positions.store'), {
            onSuccess: () => { showModal.value = false; },
        });
    }
};

const deletePosition = (pos: OrganizationPosition) => {
    if (confirm(`Hapus jabatan '${pos.name}'?`)) {
        router.delete(route('admin.organization.positions.destroy', pos.id));
    }
};

const changeOrg = (orgId: number) => {
    router.get(route('admin.organization.positions.index'), { org_id: orgId }, { preserveState: true });
};
</script>

<template>
    <AdminLayout title="Struktur & Jabatan Organisasi">
        <Head title="Jabatan Organisasi - Admin" />

        <div class="space-y-6">
            <!-- Header bar -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
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
                <div v-else />

                <Button @click="openCreateModal" variant="primary" size="md">
                    <Plus class="w-4 h-4 mr-1.5" />
                    <span>Tambah Jabatan / Seksi Baru</span>
                </Button>
            </div>

            <!-- Positions Table -->
            <Card padding="none" class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-[#F0F4F8] text-[#003087] font-bold text-xs uppercase border-b border-[#CBD2D6]">
                            <tr>
                                <th class="py-4 px-6">Urutan</th>
                                <th class="py-4 px-6">Nama Jabatan / Seksi</th>
                                <th class="py-4 px-6">Batas Anggota</th>
                                <th class="py-4 px-6">Jenis</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD2D6]/60">
                            <tr v-for="pos in positions" :key="pos.id" class="hover:bg-[#F8FAFC]">
                                <td class="py-4 px-6 font-bold text-[#687173]">
                                    #{{ pos.sort_order }}
                                </td>
                                <td class="py-4 px-6 font-bold text-[#003087]">
                                    {{ pos.name }}
                                </td>
                                <td class="py-4 px-6 font-semibold text-[#1A1A2E]">
                                    Maks. {{ pos.max_members }} Orang
                                </td>
                                <td class="py-4 px-6">
                                    <Badge :variant="pos.is_default ? 'primary' : 'secondary'">
                                        {{ pos.is_default ? 'Jabatan Inti' : 'Seksi Tambahan' }}
                                    </Badge>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                    <button
                                        type="button"
                                        @click="openEditModal(pos)"
                                        class="p-1.5 text-[#003087] hover:bg-[#EBF3FF] rounded-lg transition-colors cursor-pointer"
                                        title="Edit"
                                    >
                                        <Edit2 class="w-4 h-4" />
                                    </button>
                                    <button
                                        v-if="!pos.is_default"
                                        type="button"
                                        @click="deletePosition(pos)"
                                        class="p-1.5 text-[#D20000] hover:bg-[#FDE7E7] rounded-lg transition-colors cursor-pointer"
                                        title="Hapus"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="positions.length === 0">
                                <td colspan="5" class="py-12 text-center text-[#687173]">
                                    Belum ada jabatan yang ditambahkan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>
        </div>

        <!-- Create / Edit Modal -->
        <Modal
            :show="showModal"
            :title="editingPos ? 'Edit Jabatan / Seksi' : 'Tambah Jabatan / Seksi'"
            @close="showModal = false"
        >
            <form @submit.prevent="submitForm" class="space-y-4">
                <Input
                    v-model="form.name"
                    label="Nama Jabatan / Seksi"
                    placeholder="contoh: Seksi Kebersihan & Lingkungan"
                    required
                    :error="form.errors.name"
                />

                <div class="grid grid-cols-2 gap-4">
                    <Input
                        v-model="form.max_members"
                        type="number"
                        label="Maksimal Anggota"
                        helper="Bisa diisi 2 orang untuk Sekretaris/Bendahara KT"
                        required
                        :error="form.errors.max_members"
                    />
                    <Input
                        v-model="form.sort_order"
                        type="number"
                        label="Urutan Tampilan"
                        required
                        :error="form.errors.sort_order"
                    />
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-[#CBD2D6]/70">
                    <Button type="button" variant="outline" @click="showModal = false">Batal</Button>
                    <Button type="submit" variant="primary" :loading="form.processing">Simpan Jabatan</Button>
                </div>
            </form>
        </Modal>
    </AdminLayout>
</template>
