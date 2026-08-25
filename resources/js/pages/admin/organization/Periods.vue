<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Modal from '@/components/ui/Modal.vue';
import Input from '@/components/ui/Input.vue';
import type { Organization, OrganizationPeriod } from '@/types';
import { Plus, Edit2, Trash2, Calendar, CheckCircle2 } from 'lucide-vue-next';

interface Props {
    organizations: Organization[];
    selectedOrgId: number;
    periods: OrganizationPeriod[];
}

const props = defineProps<Props>();

const showModal = ref(false);
const editingPeriod = ref<OrganizationPeriod | null>(null);

const form = useForm({
    organization_id: props.selectedOrgId,
    name: '',
    start_year: new Date().getFullYear(),
    end_year: new Date().getFullYear() + 3,
    is_active: true,
    notes: '',
});

const openCreateModal = () => {
    editingPeriod.value = null;
    form.reset();
    form.organization_id = props.selectedOrgId;
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (period: OrganizationPeriod) => {
    editingPeriod.value = period;
    form.organization_id = period.organization_id;
    form.name = period.name;
    form.start_year = period.start_year;
    form.end_year = period.end_year;
    form.is_active = period.is_active;
    form.notes = period.notes || '';
    showModal.value = true;
};

const submitForm = () => {
    if (editingPeriod.value) {
        form.put(route('admin.organization.periods.update', editingPeriod.value.id), {
            onSuccess: () => { showModal.value = false; },
        });
    } else {
        form.post(route('admin.organization.periods.store'), {
            onSuccess: () => { showModal.value = false; },
        });
    }
};

const deletePeriod = (period: OrganizationPeriod) => {
    if (confirm(`Hapus periode '${period.name}'? Seluruh riwayat penugasan di periode ini akan terhapus.`)) {
        router.delete(route('admin.organization.periods.destroy', period.id));
    }
};

const changeOrg = (orgId: number) => {
    router.get(route('admin.organization.periods.index'), { org_id: orgId }, { preserveState: true });
};
</script>

<template>
    <AdminLayout title="Periode Kepengurusan">
        <Head title="Periode Kepengurusan - Admin" />

        <div class="space-y-6">
            <!-- Header bar -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <!-- Org Select -->
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
                    <span>Tambah Periode Baru</span>
                </Button>
            </div>

            <!-- Periods Table -->
            <Card padding="none" class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-[#F0F4F8] text-[#003087] font-bold text-xs uppercase border-b border-[#CBD2D6]">
                            <tr>
                                <th class="py-4 px-6">Nama Periode</th>
                                <th class="py-4 px-6">Rentang Tahun</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6">Total Pengurus</th>
                                <th class="py-4 px-6">Catatan</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD2D6]/60">
                            <tr v-for="p in periods" :key="p.id" class="hover:bg-[#F8FAFC]">
                                <td class="py-4 px-6 font-bold text-[#003087]">
                                    {{ p.name }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-[#475569]">
                                    {{ p.start_year }} - {{ p.end_year }}
                                </td>
                                <td class="py-4 px-6">
                                    <Badge :variant="p.is_active ? 'success' : 'neutral'">
                                        {{ p.is_active ? 'Aktif Saat Ini' : 'Arsip Lalu' }}
                                    </Badge>
                                </td>
                                <td class="py-4 px-6 font-semibold text-[#1A1A2E]">
                                    {{ p.member_positions_count || 0 }} Orang
                                </td>
                                <td class="py-4 px-6 text-xs text-[#687173] max-w-xs truncate">
                                    {{ p.notes || '-' }}
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                    <button
                                        type="button"
                                        @click="openEditModal(p)"
                                        class="p-1.5 text-[#003087] hover:bg-[#EBF3FF] rounded-lg transition-colors cursor-pointer"
                                        title="Edit"
                                    >
                                        <Edit2 class="w-4 h-4" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="deletePeriod(p)"
                                        class="p-1.5 text-[#D20000] hover:bg-[#FDE7E7] rounded-lg transition-colors cursor-pointer"
                                        title="Hapus"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="periods.length === 0">
                                <td colspan="6" class="py-12 text-center text-[#687173]">
                                    Belum ada periode kepengurusan yang ditambahkan.
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
            :title="editingPeriod ? 'Edit Periode Kepengurusan' : 'Tambah Periode Baru'"
            @close="showModal = false"
        >
            <form @submit.prevent="submitForm" class="space-y-4">
                <Input
                    v-model="form.name"
                    label="Nama Periode"
                    placeholder="contoh: Periode 2024 - 2027"
                    required
                    :error="form.errors.name"
                />

                <div class="grid grid-cols-2 gap-4">
                    <Input
                        v-model="form.start_year"
                        type="number"
                        label="Tahun Mulai"
                        required
                        :error="form.errors.start_year"
                    />
                    <Input
                        v-model="form.end_year"
                        type="number"
                        label="Tahun Selesai"
                        required
                        :error="form.errors.end_year"
                    />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Catatan</label>
                    <textarea
                        v-model="form.notes"
                        rows="2"
                        class="w-full p-3 rounded-lg border border-[#CBD2D6] text-sm text-[#1A1A2E] focus:outline-none focus:border-[#003087]"
                        placeholder="Keterangan tambahan..."
                    />
                </div>

                <div class="p-3 rounded-xl bg-[#F0FDF4] border border-[#019C34]/30 flex items-center gap-3">
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        id="period_active"
                        class="w-5 h-5 rounded border-[#CBD2D6] text-[#003087] focus:ring-[#003087]"
                    />
                    <label for="period_active" class="text-sm font-bold text-[#019C34] cursor-pointer">
                        Set sebagai Periode Aktif Saat Ini
                    </label>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-[#CBD2D6]/70">
                    <Button type="button" variant="outline" @click="showModal = false">Batal</Button>
                    <Button type="submit" variant="primary" :loading="form.processing">Simpan Periode</Button>
                </div>
            </form>
        </Modal>
    </AdminLayout>
</template>
