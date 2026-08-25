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
import Pagination from '@/components/ui/Pagination.vue';
import type { User, Organization, PaginatedData } from '@/types';
import { Plus, Edit2, Trash2, Search, ShieldCheck } from 'lucide-vue-next';

interface Filters {
    search?: string | null;
    role?: string | null;
}

interface Props {
    users: PaginatedData<User>;
    organizations: Organization[];
    filters: Filters;
}

const props = defineProps<Props>();

const showModal = ref(false);
const editingUser = ref<User | null>(null);
const searchQuery = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || '');

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'admin_rt' as 'super_admin' | 'admin_rt' | 'admin_karang_taruna',
    organization_id: '' as string | number,
    phone: '',
    is_active: true,
});

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.is_active = true;
    form.role = 'admin_rt';
    showModal.value = true;
};

const openEditModal = (user: User) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.role = user.role;
    form.organization_id = user.organization_id || '';
    form.phone = user.phone || '';
    form.is_active = user.is_active ?? true;
    showModal.value = true;
};

const submitForm = () => {
    if (editingUser.value) {
        form.put(route('admin.users.update', editingUser.value.id), {
            onSuccess: () => { showModal.value = false; },
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => { showModal.value = false; },
        });
    }
};

const deleteUser = (user: User) => {
    if (confirm(`Hapus akun admin '${user.name}' (${user.email})?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

const handleFilter = () => {
    router.get(route('admin.users.index'), {
        search: searchQuery.value || undefined,
        role: roleFilter.value || undefined,
    }, { preserveState: true });
};
</script>

<template>
    <AdminLayout title="Manajemen Pengguna Admin">
        <Head title="Pengguna Admin - Admin" />

        <div class="space-y-6">
            <!-- Header bar & Controls -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <select
                        v-model="roleFilter"
                        @change="handleFilter"
                        class="h-10 px-3 rounded-xl border border-[#CBD2D6] bg-white text-xs font-semibold text-[#1A1A2E]"
                    >
                        <option value="">Semua Peran (Role)</option>
                        <option value="super_admin">Super Administrator</option>
                        <option value="admin_rt">Admin RT 09</option>
                        <option value="admin_karang_taruna">Admin Karang Taruna</option>
                    </select>

                    <form @submit.prevent="handleFilter" class="relative">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari nama / email..."
                            class="h-10 pl-9 pr-4 rounded-xl border border-[#CBD2D6] bg-white text-xs focus:outline-none focus:border-[#003087]"
                        />
                        <Search class="w-4 h-4 text-[#687173] absolute left-3 top-3" />
                    </form>
                </div>

                <Button @click="openCreateModal" variant="primary" size="md">
                    <Plus class="w-4 h-4 mr-1.5" />
                    <span>Tambah Pengguna Admin</span>
                </Button>
            </div>

            <!-- Users Table -->
            <Card padding="none" class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-[#F0F4F8] text-[#003087] font-bold text-xs uppercase border-b border-[#CBD2D6]">
                            <tr>
                                <th class="py-4 px-6">Nama Pengguna</th>
                                <th class="py-4 px-6">Email Login</th>
                                <th class="py-4 px-6">Peran (Role)</th>
                                <th class="py-4 px-6">Organisasi Terikat</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD2D6]/60">
                            <tr v-for="u in users.data" :key="u.id" class="hover:bg-[#F8FAFC]">
                                <td class="py-4 px-6 font-bold text-[#1A1A2E]">
                                    {{ u.name }}
                                </td>
                                <td class="py-4 px-6 font-mono text-xs text-[#003087]">
                                    {{ u.email }}
                                </td>
                                <td class="py-4 px-6">
                                    <Badge :variant="u.role === 'super_admin' ? 'warning' : (u.role === 'admin_rt' ? 'primary' : 'secondary')">
                                        {{ u.role === 'super_admin' ? 'Super Admin' : (u.role === 'admin_rt' ? 'Admin RT 09' : 'Admin Karang Taruna') }}
                                    </Badge>
                                </td>
                                <td class="py-4 px-6 text-xs text-[#687173]">
                                    {{ u.organization?.name || 'Semua (Akses Global)' }}
                                </td>
                                <td class="py-4 px-6">
                                    <Badge :variant="u.is_active ? 'success' : 'danger'">
                                        {{ u.is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </Badge>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                    <button
                                        type="button"
                                        @click="openEditModal(u)"
                                        class="p-1.5 text-[#003087] hover:bg-[#EBF3FF] rounded-lg transition-colors cursor-pointer"
                                        title="Edit"
                                    >
                                        <Edit2 class="w-4 h-4" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteUser(u)"
                                        class="p-1.5 text-[#D20000] hover:bg-[#FDE7E7] rounded-lg transition-colors cursor-pointer"
                                        title="Hapus"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-[#CBD2D6]">
                    <Pagination :links="users.links" />
                </div>
            </Card>
        </div>

        <!-- Create / Edit Modal -->
        <Modal
            :show="showModal"
            :title="editingUser ? 'Edit Akun Pengguna Admin' : 'Tambah Akun Admin Baru'"
            @close="showModal = false"
        >
            <form @submit.prevent="submitForm" class="space-y-4">
                <Input
                    v-model="form.name"
                    label="Nama Lengkap"
                    required
                    :error="form.errors.name"
                />

                <Input
                    v-model="form.email"
                    type="email"
                    label="Alamat Email (Digunakan untuk Login)"
                    required
                    :error="form.errors.email"
                />

                <Input
                    v-model="form.password"
                    type="password"
                    :label="editingUser ? 'Ganti Kata Sandi (Kosongkan jika tidak diubah)' : 'Kata Sandi (Password)'"
                    :required="!editingUser"
                    :error="form.errors.password"
                />

                <div>
                    <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Peran Hak Akses (Role)</label>
                    <select v-model="form.role" required class="w-full h-11 px-3 rounded-lg border border-[#CBD2D6] bg-white text-sm font-bold">
                        <option value="admin_rt">Admin RT 09 (Kelola Data RT)</option>
                        <option value="admin_karang_taruna">Admin Karang Taruna (Kelola Data Karang Taruna)</option>
                        <option value="super_admin">Super Administrator (Akses Penuh Seluruh Sistem)</option>
                    </select>
                </div>

                <div v-if="form.role !== 'super_admin'">
                    <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Batasi Organisasi</label>
                    <select v-model="form.organization_id" class="w-full h-11 px-3 rounded-lg border border-[#CBD2D6] bg-white text-sm">
                        <option value="">-- Bebas / Sesuai Role --</option>
                        <option v-for="org in organizations" :key="org.id" :value="org.id">{{ org.name }}</option>
                    </select>
                </div>

                <Input
                    v-model="form.phone"
                    label="Nomor Telepon / WhatsApp"
                    placeholder="0812-xxxx-xxxx"
                    :error="form.errors.phone"
                />

                <div class="p-3 rounded-xl bg-[#F0FDF4] border border-[#019C34]/30 flex items-center gap-3">
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        id="user_active"
                        class="w-5 h-5 rounded border-[#CBD2D6] text-[#003087]"
                    />
                    <label for="user_active" class="text-sm font-bold text-[#019C34] cursor-pointer">
                        Akun Aktif (Bisa Login ke Sistem)
                    </label>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-[#CBD2D6]/70">
                    <Button type="button" variant="outline" @click="showModal = false">Batal</Button>
                    <Button type="submit" variant="primary" :loading="form.processing">Simpan Pengguna</Button>
                </div>
            </form>
        </Modal>
    </AdminLayout>
</template>
