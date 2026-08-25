<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Pagination from '@/components/ui/Pagination.vue';
import type { PaginatedData, CashTransaction, CashAccount } from '@/types';
import { DollarSign, ArrowDownLeft, ArrowUpRight, Filter, ShieldCheck, FileText, Lock } from 'lucide-vue-next';

interface Summary {
    total_balance: number;
    period_income: number;
    period_expense: number;
}

interface Props {
    publicFinance: boolean;
    orgType?: 'all' | 'rt' | 'karang_taruna';
    selectedMonth?: number;
    selectedYear?: number;
    accounts?: CashAccount[];
    transactions?: PaginatedData<CashTransaction> | null;
    summary?: Summary | null;
}

const props = defineProps<Props>();

const filterOrg = ref(props.orgType || 'all');
const filterMonth = ref(props.selectedMonth || new Date().getMonth() + 1);
const filterYear = ref(props.selectedYear || new Date().getFullYear());

const months = [
    { value: 1, label: 'Januari' },
    { value: 2, label: 'Februari' },
    { value: 3, label: 'Maret' },
    { value: 4, label: 'April' },
    { value: 5, label: 'Mei' },
    { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' },
    { value: 8, label: 'Agustus' },
    { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' },
    { value: 11, label: 'November' },
    { value: 12, label: 'Desember' },
];

const applyFilter = () => {
    router.get(route('public.finance'), {
        org: filterOrg.value,
        month: filterMonth.value,
        year: filterYear.value,
    }, { preserveState: true });
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
    <PublicLayout>
        <Head title="Laporan Transparansi Keuangan Kas RT 09 & Karang Taruna" />

        <!-- Header -->
        <div class="bg-gradient-to-r from-[#001B52] via-[#003087] to-[#00246B] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <Badge variant="warning" class="mb-3">Keterbukaan Publik</Badge>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white mb-3">
                        Transparansi Kas Lingkungan
                    </h1>
                    <p class="text-base sm:text-lg text-white/90">
                        Laporan arus kas pemasukan dan pengeluaran RT 09 & Karang Taruna yang dapat dipantau oleh seluruh warga demi akuntabilitas bersama.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div v-if="publicFinance && summary">
                <!-- Summary Metrics (Trust Blue Pay Cards) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <!-- Total Kas -->
                    <div class="bg-gradient-to-br from-[#003087] to-[#001B52] text-white rounded-3xl p-6 shadow-level-2 border border-[#009CDE]/40 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#009CDE]">Total Saldo Kas Saat Ini</span>
                            <div class="text-3xl sm:text-4xl font-extrabold text-[#F5BA2E] tabular-nums mt-2">
                                {{ formatRupiah(summary.total_balance) }}
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-white/20 text-xs text-white/80">
                            Saldo aman tersimpan di rekening kas resmi lingkungan.
                        </div>
                    </div>

                    <!-- Total Pemasukan Periode Ini -->
                    <div class="bg-[#F0FDF4] border-2 border-[#019C34]/40 rounded-3xl p-6 shadow-level-1 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#019C34]">Total Pemasukan Periode Ini</span>
                            <div class="text-2xl sm:text-3xl font-extrabold text-[#019C34] tabular-nums mt-2">
                                + {{ formatRupiah(summary.period_income) }}
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-[#019C34]/20 text-xs text-[#475569]">
                            Iuran warga bulanan, donasi donatur & sumbangan sukarela.
                        </div>
                    </div>

                    <!-- Total Pengeluaran Periode Ini -->
                    <div class="bg-[#FEF2F2] border-2 border-[#D20000]/30 rounded-3xl p-6 shadow-level-1 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#D20000]">Total Pengeluaran Periode Ini</span>
                            <div class="text-2xl sm:text-3xl font-extrabold text-[#D20000] tabular-nums mt-2">
                                - {{ formatRupiah(summary.period_expense) }}
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-[#D20000]/20 text-xs text-[#475569]">
                            Gaji petugas kebersihan, pos ronda, dan perbaikan fasilitas.
                        </div>
                    </div>
                </div>

                <!-- Account breakdown cards -->
                <div v-if="accounts && accounts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
                    <Card
                        v-for="acc in accounts"
                        :key="acc.id"
                        class="flex items-center justify-between p-5 bg-white"
                    >
                        <div>
                            <span class="text-xs font-bold uppercase text-[#687173]">{{ acc.organization?.name }}</span>
                            <h4 class="text-lg font-bold text-[#003087]">{{ acc.name }}</h4>
                            <p class="text-xs text-[#687173]">{{ acc.account_number || 'Kas Tunai / Bank' }}</p>
                        </div>
                        <div class="text-xl font-extrabold text-[#003087] tabular-nums">
                            {{ formatRupiah(acc.current_balance) }}
                        </div>
                    </Card>
                </div>

                <!-- Filter Controls -->
                <Card class="mb-8 p-6">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="w-full sm:w-auto">
                            <label class="block text-xs font-bold text-[#687173] uppercase mb-1">Organisasi</label>
                            <select
                                v-model="filterOrg"
                                class="h-11 px-4 rounded-xl border border-[#CBD2D6] bg-white text-sm font-semibold text-[#1A1A2E]"
                            >
                                <option value="all">Semua Kas Lingkungan</option>
                                <option value="rt">Khusus Kas RT 09</option>
                                <option value="karang_taruna">Khusus Kas Karang Taruna</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#687173] uppercase mb-1">Bulan</label>
                            <select
                                v-model="filterMonth"
                                class="h-11 px-4 rounded-xl border border-[#CBD2D6] bg-white text-sm font-semibold text-[#1A1A2E]"
                            >
                                <option v-for="m in months" :key="m.value" :value="m.value">
                                    {{ m.label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#687173] uppercase mb-1">Tahun</label>
                            <select
                                v-model="filterYear"
                                class="h-11 px-4 rounded-xl border border-[#CBD2D6] bg-white text-sm font-semibold text-[#1A1A2E]"
                            >
                                <option :value="2024">2024</option>
                                <option :value="2025">2025</option>
                                <option :value="2026">2026</option>
                                <option :value="2027">2027</option>
                            </select>
                        </div>

                        <button
                            type="button"
                            @click="applyFilter"
                            class="h-11 px-6 bg-[#003087] hover:bg-[#00246B] text-white font-bold text-sm rounded-xl cursor-pointer shadow-level-1 transition-colors flex items-center gap-2"
                        >
                            <Filter class="w-4 h-4" />
                            <span>Terapkan Filter</span>
                        </button>
                    </div>
                </Card>

                <!-- Transactions Table -->
                <Card padding="none" class="overflow-hidden">
                    <div class="p-6 border-b border-[#CBD2D6] bg-[#F8FAFC]">
                        <h3 class="text-xl font-bold text-[#003087]">
                            Riwayat Mutasi Transaksi Kas
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm sm:text-base border-collapse">
                            <thead class="bg-[#F0F4F8] text-[#003087] font-bold text-xs uppercase border-b border-[#CBD2D6]">
                                <tr>
                                    <th class="py-4 px-6">Tanggal</th>
                                    <th class="py-4 px-6">Akun Kas</th>
                                    <th class="py-4 px-6">Kategori</th>
                                    <th class="py-4 px-6">Keterangan Transaksi</th>
                                    <th class="py-4 px-6 text-right">Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#CBD2D6]/60">
                                <tr
                                    v-for="trx in transactions?.data"
                                    :key="trx.id"
                                    class="hover:bg-[#F8FAFC] transition-colors"
                                >
                                    <td class="py-4 px-6 whitespace-nowrap text-[#687173]">
                                        {{ formatDate(trx.transaction_date) }}
                                    </td>
                                    <td class="py-4 px-6 font-semibold text-[#003087] whitespace-nowrap">
                                        {{ trx.account?.name }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <Badge :variant="trx.type === 'income' ? 'success' : 'danger'">
                                            {{ trx.category?.name }}
                                        </Badge>
                                    </td>
                                    <td class="py-4 px-6 text-[#1A1A2E] max-w-md">
                                        {{ trx.description }}
                                    </td>
                                    <td
                                        class="py-4 px-6 text-right font-extrabold tabular-nums whitespace-nowrap"
                                        :class="trx.type === 'income' ? 'text-[#019C34]' : 'text-[#D20000]'"
                                    >
                                        {{ trx.type === 'income' ? '+' : '-' }} {{ formatRupiah(trx.amount) }}
                                    </td>
                                </tr>

                                <tr v-if="!transactions?.data || transactions.data.length === 0">
                                    <td colspan="5" class="py-12 text-center text-[#687173]">
                                        Tidak ada data transaksi kas pada filter bulan dan tahun ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="transactions" class="p-6 border-t border-[#CBD2D6]">
                        <Pagination :links="transactions.links" />
                    </div>
                </Card>
            </div>

            <!-- If Public Finance is turned off in Settings -->
            <div v-else class="p-16 text-center bg-white rounded-3xl border border-[#CBD2D6] max-w-xl mx-auto shadow-level-1 space-y-4">
                <div class="w-16 h-16 rounded-full bg-[#EBF3FF] text-[#003087] flex items-center justify-center mx-auto">
                    <Lock class="w-8 h-8" />
                </div>
                <h3 class="text-2xl font-black text-[#003087]">Informasi Kas Internal</h3>
                <p class="text-base text-[#687173]">
                    Laporan kas umum saat ini sedang dalam proses rekonsiliasi pengurus atau ditutup untuk publik sesuai kebijakan musyawarah warga.
                </p>
            </div>
        </div>
    </PublicLayout>
</template>
