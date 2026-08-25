<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Modal from '@/components/ui/Modal.vue';
import Pagination from '@/components/ui/Pagination.vue';
import type { AuditLog, PaginatedData } from '@/types';
import { History, Search, Eye } from 'lucide-vue-next';

interface Filters {
    search?: string | null;
    action?: string | null;
}

interface Props {
    logs: PaginatedData<AuditLog>;
    filters: Filters;
}

const props = defineProps<Props>();

const viewingLog = ref<AuditLog | null>(null);
const searchQuery = ref(props.filters.search || '');
const actionFilter = ref(props.filters.action || '');

const handleFilter = () => {
    router.get(route('admin.audit_logs.index'), {
        search: searchQuery.value || undefined,
        action: actionFilter.value || undefined,
    }, { preserveState: true });
};
</script>

<template>
    <AdminLayout title="Log Aktivitas & Audit Trail">
        <Head title="Log Aktivitas - Admin" />

        <div class="space-y-6">
            <!-- Header search & filter -->
            <div class="flex flex-wrap items-center gap-3">
                <select
                    v-model="actionFilter"
                    @change="handleFilter"
                    class="h-10 px-3 rounded-xl border border-[#CBD2D6] bg-white text-xs font-semibold text-[#1A1A2E]"
                >
                    <option value="">Semua Aksi</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="created">Created</option>
                    <option value="updated">Updated</option>
                    <option value="deleted">Deleted</option>
                    <option value="voided">Voided</option>
                </select>

                <form @submit.prevent="handleFilter" class="relative max-w-sm w-full">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari user / model / IP..."
                        class="h-10 pl-9 pr-4 rounded-xl border border-[#CBD2D6] bg-white text-xs w-full focus:outline-none focus:border-[#003087]"
                    />
                    <Search class="w-4 h-4 text-[#687173] absolute left-3 top-3" />
                </form>
            </div>

            <!-- Audit Logs Table -->
            <Card padding="none" class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse">
                        <thead class="bg-[#F0F4F8] text-[#003087] font-bold text-xs uppercase border-b border-[#CBD2D6]">
                            <tr>
                                <th class="py-4 px-6">Waktu</th>
                                <th class="py-4 px-6">Pengguna</th>
                                <th class="py-4 px-6">Aksi</th>
                                <th class="py-4 px-6">Entitas Model</th>
                                <th class="py-4 px-6">IP Address</th>
                                <th class="py-4 px-6 text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD2D6]/60">
                            <tr v-for="log in logs.data" :key="log.id" class="hover:bg-[#F8FAFC]">
                                <td class="py-4 px-6 whitespace-nowrap text-[#687173]">
                                    {{ new Date(log.created_at || '').toLocaleString('id-ID') }}
                                </td>
                                <td class="py-4 px-6 font-bold text-[#1A1A2E]">
                                    {{ log.user?.name || 'Sistem' }}
                                    <span v-if="log.user" class="block text-[11px] font-normal text-[#687173]">{{ log.user.role }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <Badge :variant="log.action === 'created' ? 'success' : (log.action === 'deleted' || log.action === 'voided' ? 'danger' : 'primary')">
                                        {{ log.action }}
                                    </Badge>
                                </td>
                                <td class="py-4 px-6 text-xs text-[#475569]">
                                    <span v-if="log.model_type">{{ log.model_type }} #{{ log.model_id }}</span>
                                    <span v-else class="text-[#CBD2D6]">-</span>
                                </td>
                                <td class="py-4 px-6 font-mono text-xs text-[#687173]">
                                    {{ log.ip_address || '-' }}
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button
                                        v-if="log.old_values || log.new_values"
                                        type="button"
                                        @click="viewingLog = log"
                                        class="p-1.5 text-[#003087] hover:bg-[#EBF3FF] rounded-lg transition-colors cursor-pointer"
                                        title="Lihat Perubahan"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="logs.data.length === 0">
                                <td colspan="6" class="py-12 text-center text-[#687173]">
                                    Belum ada log aktivitas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-[#CBD2D6]">
                    <Pagination :links="logs.links" />
                </div>
            </Card>
        </div>

        <!-- Detail Diff Modal -->
        <Modal :show="!!viewingLog" title="Detail Perubahan Data" max-width="lg" @close="viewingLog = null">
            <div v-if="viewingLog" class="space-y-4 text-xs font-mono">
                <div v-if="viewingLog.old_values">
                    <span class="font-bold text-[#D20000] block mb-1">Nilai Sebelumnya:</span>
                    <pre class="bg-[#FFF5F5] p-3 rounded-lg overflow-x-auto border border-red-200 text-red-900">{{ JSON.stringify(viewingLog.old_values, null, 2) }}</pre>
                </div>
                <div v-if="viewingLog.new_values">
                    <span class="font-bold text-[#019C34] block mb-1">Nilai Baru / Setelah Perubahan:</span>
                    <pre class="bg-[#F0FDF4] p-3 rounded-lg overflow-x-auto border border-green-200 text-green-900">{{ JSON.stringify(viewingLog.new_values, null, 2) }}</pre>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
