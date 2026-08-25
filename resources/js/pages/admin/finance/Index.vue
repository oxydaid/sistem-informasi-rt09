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
import type { Organization, CashAccount, CashCategory, CashTransaction, PaginatedData } from '@/types';
import { Plus, DollarSign, ArrowDownLeft, ArrowUpRight, Ban, Eye, FileText, Filter } from 'lucide-vue-next';

interface Summary {
    current_balance: number;
    filtered_income: number;
    filtered_expense: number;
}

interface Filters {
    account_id?: number | null;
    type?: string | null;
    category_id?: number | null;
    start_date?: string | null;
    end_date?: string | null;
}

interface Props {
    organizations: Organization[];
    selectedOrgId: number;
    accounts: CashAccount[];
    activeAccount?: CashAccount | null;
    categories: CashCategory[];
    transactions: PaginatedData<CashTransaction>;
    summary: Summary;
    filters: Filters;
}

const props = defineProps<Props>();

const showTrxModal = ref(false);
const previewReceiptUrl = ref<string | null>(null);

const trxForm = useForm({
    cash_account_id: props.activeAccount?.id || (props.accounts[0]?.id || ''),
    cash_category_id: '' as string | number,
    type: 'income' as 'income' | 'expense',
    amount: '',
    transaction_date: new Date().toISOString().split('T')[0],
    description: '',
    reference_number: '',
    receipt: null as File | null,
});

const filterType = ref(props.filters.type || '');
const filterCat = ref(props.filters.category_id || '');
const filterStart = ref(props.filters.start_date || '');
const filterEnd = ref(props.filters.end_date || '');

const openCreateModal = (type: 'income' | 'expense') => {
    trxForm.reset();
    trxForm.type = type;
    trxForm.cash_account_id = props.activeAccount?.id || (props.accounts[0]?.id || '');
    trxForm.transaction_date = new Date().toISOString().split('T')[0];
    showTrxModal.value = true;
};

const submitTransaction = () => {
    trxForm.post(route('admin.finance.transactions.store'), {
        forceFormData: true,
        onSuccess: () => { showTrxModal.value = false; },
    });
};

const voidTrx = (trx: CashTransaction) => {
    if (confirm(`Batalkan (Void) transaksi '${trx.description}' senilai ${formatRupiah(trx.amount)}? Saldo kas akan dihitung ulang secara otomatis.`)) {
        router.post(route('admin.finance.transactions.void', trx.id));
    }
};

const applyFilter = () => {
    router.get(route('admin.finance.index'), {
        org_id: props.selectedOrgId,
        account_id: props.activeAccount?.id,
        type: filterType.value || undefined,
        category_id: filterCat.value || undefined,
        start_date: filterStart.value || undefined,
        end_date: filterEnd.value || undefined,
    }, { preserveState: true });
};

const changeAccount = (accId: number) => {
    router.get(route('admin.finance.index'), {
        org_id: props.selectedOrgId,
        account_id: accId,
    });
};

const changeOrg = (orgId: number) => {
    router.get(route('admin.finance.index'), { org_id: orgId });
};

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(val);
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
    <AdminLayout title="Manajemen Kas & Transaksi">
        <Head title="Keuangan & Kas - Admin" />

        <div class="space-y-6">
            <!-- Header bar -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
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
                </div>

                <div class="flex items-center gap-2">
                    <Button @click="openCreateModal('income')" variant="primary" size="md">
                        <ArrowDownLeft class="w-4 h-4 mr-1 text-[#019C34]" />
                        <span>+ Pemasukan</span>
                    </Button>
                    <Button @click="openCreateModal('expense')" variant="danger" size="md">
                        <ArrowUpRight class="w-4 h-4 mr-1 text-white" />
                        <span>- Pengeluaran</span>
                    </Button>
                </div>
            </div>

            <!-- Cash Accounts Selector & Balance Strip -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Active Account Balance -->
                <Card class="bg-gradient-to-br from-[#003087] to-[#001B52] text-white p-6 border-[#009CDE]/40 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#009CDE]">Saldo Kas {{ activeAccount?.name }}</span>
                        <div class="text-3xl sm:text-4xl font-extrabold text-[#F5BA2E] tabular-nums mt-2">
                            {{ formatRupiah(summary.current_balance) }}
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/20 text-xs text-white/80">
                        {{ activeAccount?.account_number || 'Kas Tunai / Bank' }}
                    </div>
                </Card>

                <!-- Filtered Income -->
                <Card class="p-6 bg-[#F0FDF4] border-[#019C34]/40 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#019C34]">Total Pemasukan (Filter)</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-[#019C34] tabular-nums mt-2">
                            + {{ formatRupiah(summary.filtered_income) }}
                        </div>
                    </div>
                    <span class="text-xs text-[#475569] mt-3">Sesuai rentang filter aktif</span>
                </Card>

                <!-- Filtered Expense -->
                <Card class="p-6 bg-[#FEF2F2] border-[#D20000]/30 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#D20000]">Total Pengeluaran (Filter)</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-[#D20000] tabular-nums mt-2">
                            - {{ formatRupiah(summary.filtered_expense) }}
                        </div>
                    </div>
                    <span class="text-xs text-[#475569] mt-3">Sesuai rentang filter aktif</span>
                </Card>
            </div>

            <!-- Filter Controls -->
            <Card class="p-5">
                <div class="flex flex-wrap items-end gap-3">
                    <div v-if="accounts.length > 1">
                        <label class="block text-xs font-bold text-[#687173] uppercase mb-1">Pilih Rekening Kas</label>
                        <select
                            :value="activeAccount?.id"
                            @change="changeAccount(Number(($event.target as HTMLSelectElement).value))"
                            class="h-10 px-3 rounded-xl border border-[#CBD2D6] bg-white text-xs font-bold text-[#003087]"
                        >
                            <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                {{ acc.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#687173] uppercase mb-1">Jenis Transaksi</label>
                        <select v-model="filterType" class="h-10 px-3 rounded-xl border border-[#CBD2D6] bg-white text-xs">
                            <option value="">Semua (Pemasukan & Pengeluaran)</option>
                            <option value="income">Pemasukan Saja</option>
                            <option value="expense">Pengeluaran Saja</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#687173] uppercase mb-1">Kategori</label>
                        <select v-model="filterCat" class="h-10 px-3 rounded-xl border border-[#CBD2D6] bg-white text-xs">
                            <option value="">Semua Kategori</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#687173] uppercase mb-1">Dari Tanggal</label>
                        <input v-model="filterStart" type="date" class="h-10 px-3 rounded-xl border border-[#CBD2D6] bg-white text-xs" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#687173] uppercase mb-1">Sampai Tanggal</label>
                        <input v-model="filterEnd" type="date" class="h-10 px-3 rounded-xl border border-[#CBD2D6] bg-white text-xs" />
                    </div>

                    <button
                        type="button"
                        @click="applyFilter"
                        class="h-10 px-4 bg-[#003087] text-white font-bold text-xs rounded-xl hover:bg-[#00246B] transition-colors cursor-pointer flex items-center gap-1.5"
                    >
                        <Filter class="w-3.5 h-3.5" />
                        <span>Filter</span>
                    </button>
                </div>
            </Card>

            <!-- Transactions Table -->
            <Card padding="none" class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-[#F0F4F8] text-[#003087] font-bold text-xs uppercase border-b border-[#CBD2D6]">
                            <tr>
                                <th class="py-4 px-6">Tanggal</th>
                                <th class="py-4 px-6">No. Ref</th>
                                <th class="py-4 px-6">Kategori</th>
                                <th class="py-4 px-6">Keterangan Transaksi</th>
                                <th class="py-4 px-6">Pencatat</th>
                                <th class="py-4 px-6">Bukti</th>
                                <th class="py-4 px-6 text-right">Nominal</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD2D6]/60">
                            <tr
                                v-for="trx in transactions.data"
                                :key="trx.id"
                                class="hover:bg-[#F8FAFC]"
                                :class="trx.status === 'void' ? 'opacity-50 line-through bg-[#FFF5F5]' : ''"
                            >
                                <td class="py-4 px-6 whitespace-nowrap text-[#687173]">
                                    {{ formatDate(trx.transaction_date) }}
                                </td>
                                <td class="py-4 px-6 font-mono text-xs text-[#687173]">
                                    {{ trx.reference_number || '-' }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <Badge :variant="trx.type === 'income' ? 'success' : 'danger'">
                                        {{ trx.category?.name }}
                                    </Badge>
                                </td>
                                <td class="py-4 px-6 font-medium text-[#1A1A2E] max-w-xs">
                                    {{ trx.description }}
                                    <span v-if="trx.status === 'void'" class="ml-2 px-1.5 py-0.5 bg-[#D20000] text-white text-[10px] rounded font-bold uppercase no-underline inline-block">
                                        Dibatalkan (Void)
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-xs text-[#687173]">
                                    {{ trx.creator?.name || 'Admin' }}
                                </td>
                                <td class="py-4 px-6 text-xs">
                                    <a
                                        v-if="trx.attachment_path"
                                        :href="`/storage/${trx.attachment_path}`"
                                        target="_blank"
                                        class="text-[#009CDE] font-bold hover:underline flex items-center gap-1"
                                    >
                                        <FileText class="w-3.5 h-3.5" />
                                        <span>Lihat</span>
                                    </a>
                                    <span v-else class="text-[#CBD2D6]">-</span>
                                </td>
                                <td
                                    class="py-4 px-6 text-right font-extrabold tabular-nums whitespace-nowrap"
                                    :class="trx.type === 'income' ? 'text-[#019C34]' : 'text-[#D20000]'"
                                >
                                    {{ trx.type === 'income' ? '+' : '-' }} {{ formatRupiah(trx.amount) }}
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button
                                        v-if="trx.status !== 'void'"
                                        type="button"
                                        @click="voidTrx(trx)"
                                        class="p-1.5 text-[#D20000] hover:bg-[#FDE7E7] rounded-lg transition-colors cursor-pointer"
                                        title="Batalkan Transaksi (Void)"
                                    >
                                        <Ban class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="transactions.data.length === 0">
                                <td colspan="8" class="py-12 text-center text-[#687173]">
                                    Tidak ada catatan transaksi pada rekening kas ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-[#CBD2D6]">
                    <Pagination :links="transactions.links" />
                </div>
            </Card>
        </div>

        <!-- Create Transaction Modal -->
        <Modal
            :show="showTrxModal"
            :title="trxForm.type === 'income' ? 'Catat Pemasukan Kas' : 'Catat Pengeluaran Kas'"
            @close="showTrxModal = false"
        >
            <form @submit.prevent="submitTransaction" class="space-y-4">
                <div class="flex items-center gap-2 p-3 rounded-xl" :class="trxForm.type === 'income' ? 'bg-[#F0FDF4] text-[#019C34]' : 'bg-[#FEF2F2] text-[#D20000]'">
                    <span class="font-bold text-sm">Jenis: {{ trxForm.type === 'income' ? 'Pemasukan Kas' : 'Pengeluaran Kas' }}</span>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Rekening Kas</label>
                    <select v-model="trxForm.cash_account_id" required class="w-full h-11 px-3 rounded-lg border border-[#CBD2D6] bg-white text-sm">
                        <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Kategori Transaksi</label>
                    <select v-model="trxForm.cash_category_id" required class="w-full h-11 px-3 rounded-lg border border-[#CBD2D6] bg-white text-sm">
                        <option value="" disabled>-- Pilih Kategori --</option>
                        <option
                            v-for="c in categories.filter(c => c.type === trxForm.type)"
                            :key="c.id"
                            :value="c.id"
                        >
                            {{ c.name }}
                        </option>
                    </select>
                    <p v-if="trxForm.errors.cash_category_id" class="text-xs text-[#D20000] mt-1">{{ trxForm.errors.cash_category_id }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <Input
                        v-model="trxForm.amount"
                        type="number"
                        label="Nominal (Rp)"
                        placeholder="contoh: 250000"
                        required
                        :error="trxForm.errors.amount"
                    />
                    <Input
                        v-model="trxForm.transaction_date"
                        type="date"
                        label="Tanggal Transaksi"
                        required
                        :error="trxForm.errors.transaction_date"
                    />
                </div>

                <Input
                    v-model="trxForm.description"
                    label="Keterangan / Rincian"
                    placeholder="contoh: Pembayaran iuran warga Blok B bulan Agustus"
                    required
                    :error="trxForm.errors.description"
                />

                <Input
                    v-model="trxForm.reference_number"
                    label="Nomor Referensi / Kwitansi (Opsional)"
                    placeholder="contoh: KW-009"
                    :error="trxForm.errors.reference_number"
                />

                <div>
                    <label class="block text-sm font-semibold text-[#1A1A2E] mb-1">Unggah Nota / Bukti Transfer (Opsional)</label>
                    <input
                        type="file"
                        accept="image/*,application/pdf"
                        @input="trxForm.receipt = ($event.target as HTMLInputElement).files?.[0] || null"
                        class="w-full text-sm text-[#475569] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#EBF3FF] file:text-[#003087]"
                    />
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-[#CBD2D6]/70">
                    <Button type="button" variant="outline" @click="showTrxModal = false">Batal</Button>
                    <Button type="submit" variant="primary" :loading="trxForm.processing">Simpan Transaksi</Button>
                </div>
            </form>
        </Modal>
    </AdminLayout>
</template>
