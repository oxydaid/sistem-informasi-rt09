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
import type { Organization, OrganizationMember, OrganizationPeriod, OrganizationPosition, User, PaginatedData } from '@/types';
import { Plus, Edit2, Trash2, Search, Link2, UserPlus, Phone, Mail, Award, Calendar, Briefcase, CheckCircle2 } from 'lucide-vue-next';

interface Filters {
    org_id: number;
    status?: string | null;
    period_id?: number | null;
    search?: string | null;
}

interface Props {
    organizations: Organization[];
    currentOrg: Organization;
    members: PaginatedData<OrganizationMember>;
    eligibleUsers: User[];
    filters: Filters;
}

const props = defineProps<Props>();

const showMemberModal = ref(false);
const showAssignModal = ref(false);
const editingMember = ref<OrganizationMember | null>(null);
const assigningMember = ref<OrganizationMember | null>(null);
const searchQuery = ref(props.filters.search || '');

// Member Biodata Form (Clean, no inline position fields)
const memberForm = useForm({
    organization_id: props.currentOrg.id,
    user_id: '' as string | number,
    name: '',
    gender: 'male' as 'male' | 'female',
    phone: '',
    email: '',
    bio: '',
    join_year: new Date().getFullYear(),
    status: 'active' as 'active' | 'inactive' | 'alumni',
    avatar: null as File | null,
});

// Position Assignment Form
const assignForm = useForm({
    period_id: '' as string | number,
    position_id: '' as string | number,
    notes: '',
});

const openCreateMemberModal = () => {
    editingMember.value = null;
    memberForm.reset();
    memberForm.organization_id = props.currentOrg.id;
    memberForm.status = 'active';
    memberForm.gender = 'male';
    memberForm.join_year = new Date().getFullYear();
    showMemberModal.value = true;
};

const openEditMemberModal = (member: OrganizationMember) => {
    editingMember.value = member;
    memberForm.organization_id = member.organization_id;
    memberForm.user_id = member.user_id || '';
    memberForm.name = member.name;
    memberForm.gender = member.gender || 'male';
    memberForm.phone = member.phone || '';
    memberForm.email = member.email || '';
    memberForm.bio = member.bio || '';
    memberForm.join_year = member.join_year || new Date().getFullYear();
    memberForm.status = member.status;
    memberForm.avatar = null;
    showMemberModal.value = true;
};

const submitMemberForm = () => {
    if (editingMember.value) {
        memberForm.post(route('admin.organization.members.update', editingMember.value.id), {
            _method: 'put',
            forceFormData: true,
            onSuccess: () => { showMemberModal.value = false; },
        });
    } else {
        memberForm.post(route('admin.organization.members.store'), {
            forceFormData: true,
            onSuccess: () => { showMemberModal.value = false; },
        });
    }
};

const deleteMember = (member: OrganizationMember) => {
    if (confirm(`Hapus data anggota '${member.name}'? Foto avatar dan seluruh riwayat jabatannya akan dihapus.`)) {
        router.delete(route('admin.organization.members.destroy', member.id));
    }
};

const openAssignModal = (member: OrganizationMember) => {
    assigningMember.value = member;
    assignForm.reset();
    // Default to active period or first available period
    const activePeriod = props.currentOrg.periods?.find(p => p.is_active);
    assignForm.period_id = activePeriod?.id || (props.currentOrg.periods?.[0]?.id || '');
    assignForm.position_id = props.currentOrg.positions?.[0]?.id || '';
    showAssignModal.value = true;
};

const submitAssignForm = () => {
    if (!assigningMember.value) return;
    assignForm.post(route('admin.organization.members.assign', assigningMember.value.id), {
        onSuccess: () => {
            // Keep modal open with updated data or reset form inputs
            assignForm.notes = '';
            // Refresh assigningMember reference from updated members prop
            const updated = props.members.data.find(m => m.id === assigningMember.value?.id);
            if (updated) {
                assigningMember.value = updated;
            }
        },
    });
};

const removeAssignment = (assignmentId: number) => {
    if (confirm('Hapus penugasan jabatan ini?')) {
        router.delete(route('admin.organization.members.remove_assignment', assignmentId), {
            onSuccess: () => {
                const updated = props.members.data.find(m => m.id === assigningMember.value?.id);
                if (updated) {
                    assigningMember.value = updated;
                }
            },
        });
    }
};

const handleSearch = () => {
    router.get(route('admin.organization.members.index'), {
        org_id: props.currentOrg.id,
        search: searchQuery.value,
        status: props.filters.status,
        period_id: props.filters.period_id,
    }, { preserveState: true });
};

const changeOrg = (orgId: number) => {
    router.get(route('admin.organization.members.index'), { org_id: orgId });
};
</script>

<template>
    <AdminLayout :title="`Data Anggota & Pengurus - ${currentOrg.name}`">
        <Head :title="`Data Anggota & Pengurus - Admin`" />

        <div class="space-y-6">
            <!-- Header bar & Controls -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <div v-if="organizations.length > 1" class="flex items-center gap-2">
                        <span class="text-xs font-bold text-[#687173] uppercase">Organisasi:</span>
                        <select
                            :value="currentOrg.id"
                            @change="changeOrg(Number(($event.target as HTMLSelectElement).value))"
                            class="h-10 px-3 rounded-xl border border-[#CBD2D6] bg-white text-sm font-bold text-[#003087]"
                        >
                            <option v-for="org in organizations" :key="org.id" :value="org.id">
                                {{ org.name }}
                            </option>
                        </select>
                    </div>

                    <form @submit.prevent="handleSearch" class="relative">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari nama / nomor HP..."
                            class="h-10 pl-9 pr-4 rounded-xl border border-[#CBD2D6] bg-white text-sm focus:outline-none focus:border-[#003087]"
                        />
                        <Search class="w-4 h-4 text-[#687173] absolute left-3 top-3" />
                    </form>
                </div>

                <Button @click="openCreateMemberModal" variant="primary" size="md">
                    <UserPlus class="w-4 h-4 mr-1.5" />
                    <span>Tambah Data Anggota</span>
                </Button>
            </div>

            <!-- Members Table -->
            <Card padding="none" class="overflow-hidden shadow-level-1">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-[#F0F4F8] text-[#003087] font-bold text-xs uppercase border-b border-[#CBD2D6]">
                            <tr>
                                <th class="py-4 px-6">Foto & Nama Anggota</th>
                                <th class="py-4 px-6">Kontak</th>
                                <th class="py-4 px-6">Jabatan & Periode</th>
                                <th class="py-4 px-6">Akun User Login</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD2D6]/60">
                            <tr v-for="m in members.data" :key="m.id" class="hover:bg-[#F8FAFC]">
                                <!-- Avatar & Name -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-full bg-[#EBF3FF] border border-[#CBD2D6] overflow-hidden flex items-center justify-center shrink-0 shadow-xs">
                                            <img v-if="m.avatar" :src="m.avatar" :alt="m.name" class="w-full h-full object-cover" />
                                            <span v-else class="font-bold text-[#003087] text-sm">{{ m.name.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-[#1A1A2E] text-base">{{ m.name }}</div>
                                            <span class="text-xs text-[#687173]">
                                                {{ m.gender === 'female' ? 'Perempuan' : 'Laki-laki' }} • Masuk {{ m.join_year || 2024 }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contact -->
                                <td class="py-4 px-6 text-xs text-[#475569] space-y-1">
                                    <div v-if="m.phone" class="flex items-center gap-1.5">
                                        <Phone class="w-3.5 h-3.5 text-[#019C34]" />
                                        <span>{{ m.phone }}</span>
                                    </div>
                                    <div v-if="m.email" class="flex items-center gap-1.5">
                                        <Mail class="w-3.5 h-3.5 text-[#003087]" />
                                        <span>{{ m.email }}</span>
                                    </div>
                                    <div v-if="!m.phone && !m.email" class="text-gray-400 italic">-</div>
                                </td>

                                <!-- Positions in periods (Clean badge display) -->
                                <td class="py-4 px-6">
                                    <div v-if="m.positions && m.positions.length > 0" class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="p in m.positions"
                                            :key="p.id"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#EBF3FF] border border-[#009CDE]/30 rounded-lg text-xs font-semibold text-[#003087]"
                                        >
                                            <Award class="w-3 h-3 text-[#009CDE]" />
                                            <span>{{ p.position?.name }}</span>
                                            <span class="text-[10px] text-[#687173]">({{ p.period?.name }})</span>
                                        </span>
                                    </div>
                                    <span v-else class="text-xs text-[#94A3B8] italic">Anggota Biasa</span>
                                </td>

                                <!-- User Link (Optional) -->
                                <td class="py-4 px-6 text-xs">
                                    <div v-if="m.user" class="flex items-center gap-1.5 text-[#019C34] font-semibold">
                                        <Link2 class="w-4 h-4" />
                                        <span>{{ m.user.email }}</span>
                                    </div>
                                    <span v-else class="text-[#94A3B8] italic">Bukan User Login</span>
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-6">
                                    <Badge :variant="m.status === 'active' ? 'success' : (m.status === 'alumni' ? 'secondary' : 'danger')">
                                        {{ m.status === 'active' ? 'Aktif' : (m.status === 'alumni' ? 'Alumni' : 'Non-Aktif') }}
                                    </Badge>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                    <!-- Dedicated Action Button to Manage Positions in Modal -->
                                    <button
                                        type="button"
                                        @click="openAssignModal(m)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#FFFBEB] hover:bg-[#FEF3C7] text-[#B45309] border border-[#FDE68A] rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-xs"
                                        title="Atur Jabatan & Periode"
                                    >
                                        <Award class="w-4 h-4 text-[#D97706]" />
                                        <span>Atur Jabatan</span>
                                    </button>

                                    <!-- Edit Member -->
                                    <button
                                        type="button"
                                        @click="openEditMemberModal(m)"
                                        class="p-2 text-[#003087] hover:bg-[#EBF3FF] rounded-xl transition-colors cursor-pointer border border-transparent hover:border-[#CBD2D6]"
                                        title="Edit Biodata Anggota"
                                    >
                                        <Edit2 class="w-4 h-4" />
                                    </button>

                                    <!-- Delete Member -->
                                    <button
                                        type="button"
                                        @click="deleteMember(m)"
                                        class="p-2 text-[#D20000] hover:bg-[#FDE7E7] rounded-xl transition-colors cursor-pointer border border-transparent hover:border-red-200"
                                        title="Hapus Anggota"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="members.data.length === 0">
                                <td colspan="6" class="py-12 text-center text-[#687173]">
                                    Belum ada data anggota / pengurus yang terdaftar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-[#CBD2D6]">
                    <Pagination :links="members.links" />
                </div>
            </Card>
        </div>

        <!-- 1. Create / Edit Member Biodata Modal -->
        <Modal
            :show="showMemberModal"
            :title="editingMember ? 'Edit Data Anggota' : 'Tambah Anggota Baru'"
            maxWidth="lg"
            @close="showMemberModal = false"
        >
            <form @submit.prevent="submitMemberForm" class="space-y-4">
                <Input
                    v-model="memberForm.name"
                    label="Nama Lengkap Beserta Gelar"
                    placeholder="contoh: H. Bambang Sudarmono, S.E."
                    required
                    :error="memberForm.errors.name"
                />

                <div class="grid grid-cols-2 gap-4">
                    <Select
                        v-model="memberForm.gender"
                        label="Jenis Kelamin"
                        :options="[{ label: 'Laki-laki', value: 'male' }, { label: 'Perempuan', value: 'female' }]"
                    />
                    <Select
                        v-model="memberForm.status"
                        label="Status Keanggotaan"
                        :options="[{ label: 'Aktif', value: 'active' }, { label: 'Non-Aktif', value: 'inactive' }, { label: 'Alumni', value: 'alumni' }]"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <Input
                        v-model="memberForm.phone"
                        label="Nomor HP / WhatsApp"
                        placeholder="0812-xxxx-xxxx"
                        :error="memberForm.errors.phone"
                    />
                    <Input
                        v-model="memberForm.email"
                        type="email"
                        label="Email (Opsional)"
                        placeholder="nama@email.com"
                        :error="memberForm.errors.email"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <Input
                        v-model="memberForm.join_year"
                        type="number"
                        label="Tahun Bergabung"
                        :error="memberForm.errors.join_year"
                    />

                    <div>
                        <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Tautkan ke Akun Login (Opsional)</label>
                        <select
                            v-model="memberForm.user_id"
                            class="w-full h-11 px-3 rounded-lg border border-[#CBD2D6] bg-white text-sm text-[#1A1A2E]"
                        >
                            <option value="">-- Tidak Ditautkan (Bukan Admin) --</option>
                            <option v-for="u in eligibleUsers" :key="u.id" :value="u.id">
                                {{ u.name }} ({{ u.email }})
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Foto Profil / Avatar</label>
                    <input
                        type="file"
                        accept="image/*"
                        @input="memberForm.avatar = ($event.target as HTMLInputElement).files?.[0] || null"
                        class="w-full text-sm text-[#475569] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#EBF3FF] file:text-[#003087]"
                    />
                    <p class="text-xs text-[#687173] mt-1">Maksimal 2MB. Format JPG, PNG, atau WEBP.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Biografi Singkat</label>
                    <textarea
                        v-model="memberForm.bio"
                        rows="2"
                        class="w-full p-3 rounded-lg border border-[#CBD2D6] text-sm text-[#1A1A2E] focus:outline-none focus:border-[#003087]"
                        placeholder="Pengalaman, profesi, atau catatan singkat..."
                    />
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-[#CBD2D6]/70">
                    <Button type="button" variant="outline" @click="showMemberModal = false">Batal</Button>
                    <Button type="submit" variant="primary" :loading="memberForm.processing">Simpan Data Anggota</Button>
                </div>
            </form>
        </Modal>

        <!-- 2. Dedicated Modal for Managing Member Positions across Periods -->
        <Modal
            :show="showAssignModal"
            :title="`Atur Jabatan Organisasi: ${assigningMember?.name}`"
            maxWidth="lg"
            @close="showAssignModal = false"
        >
            <div class="space-y-6">
                <!-- Member Summary Card -->
                <div class="flex items-center gap-3 p-3.5 bg-[#F8FAFC] rounded-2xl border border-[#CBD2D6]">
                    <div class="w-12 h-12 rounded-full bg-[#003087] text-white font-bold flex items-center justify-center text-base shrink-0 overflow-hidden">
                        <img v-if="assigningMember?.avatar" :src="assigningMember.avatar" :alt="assigningMember.name" class="w-full h-full object-cover" />
                        <span v-else>{{ assigningMember?.name.charAt(0) }}</span>
                    </div>
                    <div>
                        <div class="font-bold text-base text-[#003087]">{{ assigningMember?.name }}</div>
                        <div class="text-xs text-[#687173]">
                            {{ currentOrg.name }} • Status: {{ assigningMember?.status === 'active' ? 'Aktif' : 'Non-Aktif' }}
                        </div>
                    </div>
                </div>

                <!-- Section: Current Assigned Positions -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#687173] mb-2 flex items-center gap-1.5">
                        <Briefcase class="w-3.5 h-3.5 text-[#003087]" />
                        <span>Daftar Jabatan Terdaftar (Periode Aktif & Arsip)</span>
                    </h4>

                    <div v-if="assigningMember?.positions && assigningMember.positions.length > 0" class="space-y-2">
                        <div
                            v-for="p in assigningMember.positions"
                            :key="p.id"
                            class="p-3 bg-white rounded-xl border border-[#CBD2D6] flex items-center justify-between shadow-xs"
                        >
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#EBF3FF] text-[#003087] flex items-center justify-center shrink-0 font-bold">
                                    <Award class="w-4 h-4" />
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-[#003087]">{{ p.position?.name }}</div>
                                    <div class="text-xs text-[#687173] flex items-center gap-2 mt-0.5">
                                        <span class="inline-flex items-center gap-1">
                                            <Calendar class="w-3 h-3 text-[#009CDE]" />
                                            {{ p.period?.name }}
                                        </span>
                                        <span v-if="p.notes" class="text-slate-500 italic">({{ p.notes }})</span>
                                    </div>
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="removeAssignment(p.id)"
                                class="px-2.5 py-1 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg border border-transparent hover:border-red-200 transition-colors cursor-pointer"
                                title="Hapus Penugasan Jabatan Ini"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div v-else class="p-4 rounded-xl bg-gray-50 border border-dashed border-[#CBD2D6] text-center text-xs text-[#94A3B8] italic">
                        Belum ada penugasan jabatan untuk anggota ini. Silakan tambahkan pada formulir di bawah.
                    </div>
                </div>

                <!-- Section: Add / Assign New Position Form -->
                <div class="p-4 rounded-2xl bg-[#F0F7FC] border border-[#009CDE]/30 space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#003087] flex items-center gap-1.5">
                        <Plus class="w-4 h-4 text-[#009CDE]" />
                        <span>Tambah / Tautkan Penugasan Jabatan Baru</span>
                    </h4>

                    <form @submit.prevent="submitAssignForm" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-[#1A1A2E] mb-1">Pilih Periode Kepengurusan</label>
                                <select
                                    v-model="assignForm.period_id"
                                    required
                                    class="w-full h-10 px-3 rounded-lg border border-[#CBD2D6] bg-white text-xs text-[#1A1A2E] focus:outline-none focus:border-[#003087]"
                                >
                                    <option value="" disabled>-- Pilih Periode --</option>
                                    <option v-for="p in currentOrg.periods" :key="p.id" :value="p.id">
                                        {{ p.name }} {{ p.is_active ? '(Periode Aktif)' : '(Arsip)' }}
                                    </option>
                                </select>
                                <p v-if="assignForm.errors.period_id" class="text-xs text-[#D20000] mt-1">{{ assignForm.errors.period_id }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#1A1A2E] mb-1">Pilih Jabatan / Posisi</label>
                                <select
                                    v-model="assignForm.position_id"
                                    required
                                    class="w-full h-10 px-3 rounded-lg border border-[#CBD2D6] bg-white text-xs text-[#1A1A2E] focus:outline-none focus:border-[#003087]"
                                >
                                    <option value="" disabled>-- Pilih Jabatan --</option>
                                    <option v-for="pos in currentOrg.positions" :key="pos.id" :value="pos.id">
                                        {{ pos.name }} (Maks. {{ pos.max_members }} Orang)
                                    </option>
                                </select>
                                <p v-if="assignForm.errors.position_id" class="text-xs text-[#D20000] mt-1">{{ assignForm.errors.position_id }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#1A1A2E] mb-1">Catatan Penugasan / SK (Opsional)</label>
                            <input
                                v-model="assignForm.notes"
                                type="text"
                                placeholder="contoh: SK Pelantikan 2024 / Humas Koordinator RW"
                                class="w-full h-10 px-3 rounded-lg border border-[#CBD2D6] bg-white text-xs text-[#1A1A2E] focus:outline-none focus:border-[#003087]"
                            />
                        </div>

                        <div class="pt-2 flex justify-end">
                            <Button type="submit" variant="primary" size="md" :loading="assignForm.processing">
                                <CheckCircle2 class="w-4 h-4 mr-1.5" />
                                <span>Simpan & Tautkan Jabatan</span>
                            </Button>
                        </div>
                    </form>
                </div>

                <div class="flex justify-end pt-2 border-t border-[#CBD2D6]">
                    <Button type="button" variant="outline" size="md" @click="showAssignModal = false">
                        Tutup
                    </Button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
