<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Modal from '@/components/ui/Modal.vue';
import Input from '@/components/ui/Input.vue';
import Select from '@/components/ui/Select.vue';
import type { Organization, CashCategory } from '@/types';
import { Plus, Edit2, Trash2, Tag } from 'lucide-vue-next';

interface Props {
    organizations: Organization[];
    selectedOrgId: number;
    categories: CashCategory[];
}

const props = defineProps<Props>();

const showModal = ref(false);
const editingCat = ref<CashCategory | null>(null);

const form = useForm({
    organization_id: props.selectedOrgId,
    name: '',
    type: 'income' as 'income' | 'expense',
    description: '',
    is_active: true,
});

const openCreateModal = () => {
    editingCat.value = null;
    form.reset();
    form.organization_id = props.selectedOrgId;
    form.type = 'income';
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (cat: CashCategory) => {
    editingCat.value = cat;
    form.organization_id = cat.organization_id;
    form.name = cat.name;
    form.type = cat.type;
    form.description = cat.description || '';
    form.is_active = cat.is_active;
    showModal.value = true;
};

const submitForm = () => {
    if (editingCat.value) {
        form.put(route('admin.finance.categories.update', editingCat.value.id), {
            onSuccess: () => { showModal.value = false; },
        });
    } else {
        form.post(route('admin.finance.categories.store'), {
            onSuccess: () => { showModal.value = false; },
        });
    }
};

const deleteCategory = (cat: CashCategory) => {
    if (confirm(`Hapus kategori '${cat.name}'?`)) {
        router.delete(route('admin.finance.categories.destroy', cat.id));
    }
};

const changeOrg = (orgId: number) => {
    router.get(route('admin.finance.categories.index'), { org_id: orgId }, { preserveState: true });
};
</script>

<template>
    <AdminLayout title="Kategori Transaksi Kas">
        <Head title="Kategori Kas - Admin" />

        <div class="space-y-6">
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
                    <span>Tambah Kategori Baru</span>
                </Button>
            </div>

            <!-- Categories Table -->
            <Card padding="none" class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-[#F0F4F8] text-[#003087] font-bold text-xs uppercase border-b border-[#CBD2D6]">
                            <tr>
                                <th class="py-4 px-6">Nama Kategori</th>
                                <th class="py-4 px-6">Jenis Arus Kas</th>
                                <th class="py-4 px-6">Total Transaksi</th>
                                <th class="py-4 px-6">Keterangan</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD2D6]/60">
                            <tr v-for="c in categories" :key="c.id" class="hover:bg-[#F8FAFC]">
                                <td class="py-4 px-6 font-bold text-[#003087]">
                                    {{ c.name }}
                                </td>
                                <td class="py-4 px-6">
                                    <Badge :variant="c.type === 'income' ? 'success' : 'danger'">
                                        {{ c.type === 'income' ? 'Pemasukan Kas' : 'Pengeluaran Kas' }}
                                    </Badge>
                                </td>
                                <td class="py-4 px-6 font-semibold text-[#1A1A2E]">
                                    {{ c.transactions_count || 0 }} Transaksi
                                </td>
                                <td class="py-4 px-6 text-xs text-[#687173]">
                                    {{ c.description || '-' }}
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                    <button
                                        type="button"
                                        @click="openEditModal(c)"
                                        class="p-1.5 text-[#003087] hover:bg-[#EBF3FF] rounded-lg transition-colors cursor-pointer"
                                        title="Edit"
                                    >
                                        <Edit2 class="w-4 h-4" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteCategory(c)"
                                        class="p-1.5 text-[#D20000] hover:bg-[#FDE7E7] rounded-lg transition-colors cursor-pointer"
                                        title="Hapus"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="categories.length === 0">
                                <td colspan="5" class="py-12 text-center text-[#687173]">
                                    Belum ada kategori transaksi.
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
            :title="editingCat ? 'Edit Kategori Transaksi' : 'Tambah Kategori Baru'"
            @close="showModal = false"
        >
            <form @submit.prevent="submitForm" class="space-y-4">
                <Input
                    v-model="form.name"
                    label="Nama Kategori"
                    placeholder="contoh: Iuran Kebersihan / Dana Sosial"
                    required
                    :error="form.errors.name"
                />

                <Select
                    v-model="form.type"
                    label="Jenis Transaksi"
                    :options="[{ label: 'Pemasukan Kas', value: 'income' }, { label: 'Pengeluaran Kas', value: 'expense' }]"
                />

                <div>
                    <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Deskripsi Kategori</label>
                    <textarea
                        v-model="form.description"
                        rows="2"
                        class="w-full p-3 rounded-lg border border-[#CBD2D6] text-sm text-[#1A1A2E] focus:outline-none focus:border-[#003087]"
                        placeholder="Keterangan kategori..."
                    />
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-[#CBD2D6]/70">
                    <Button type="button" variant="outline" @click="showModal = false">Batal</Button>
                    <Button type="submit" variant="primary" :loading="form.processing">Simpan Kategori</Button>
                </div>
            </form>
        </Modal>
    </AdminLayout>
</template>
