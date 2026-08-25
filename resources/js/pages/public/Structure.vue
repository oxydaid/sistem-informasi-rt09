<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import type { Organization, OrganizationPeriod } from '@/types';
import { Users, Building, ShieldCheck, Phone, Mail, UserCheck } from 'lucide-vue-next';

interface Props {
    currentOrg: Organization;
    orgType: 'rt' | 'karang_taruna';
    periods: OrganizationPeriod[];
    selectedPeriod: OrganizationPeriod | null;
}

const props = defineProps<Props>();

const selectPeriod = (periodId: number) => {
    router.get(route('public.structure'), {
        org: props.orgType,
        period: periodId,
    }, { preserveState: true });
};

const switchOrg = (type: 'rt' | 'karang_taruna') => {
    router.get(route('public.structure'), {
        org: type,
    });
};
</script>

<template>
    <PublicLayout>
        <Head :title="`Bagan Struktur Pengurus - ${currentOrg.name}`" />

        <!-- Header -->
        <div class="bg-gradient-to-r from-[#001B52] to-[#003087] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl">
                <Badge variant="warning" class="mb-3">Bagan Kepengurusan</Badge>
                <h1 class="text-3xl sm:text-5xl font-black text-white mb-3">
                    Struktur Organisasi
                </h1>
                <p class="text-base sm:text-lg text-white/90">
                    Susunan jajaran pengurus amanah warga yang siap melayani dan mendampingi kebutuhan lingkungan.
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Organization Switcher Tabs (RT 09 vs Karang Taruna) -->
            <div class="flex justify-center mb-8">
                <div class="inline-flex w-full sm:w-auto p-1.5 rounded-2xl bg-white border border-[#CBD2D6] shadow-level-1 gap-1.5 sm:gap-2">
                    <button
                        type="button"
                        @click="switchOrg('rt')"
                        class="flex-1 sm:flex-initial px-3.5 sm:px-6 py-2.5 sm:py-3 rounded-xl font-bold text-xs sm:text-base transition-all cursor-pointer flex items-center justify-center gap-1.5 sm:gap-2 whitespace-nowrap"
                        :class="orgType === 'rt' ? 'bg-[#003087] text-white shadow-level-1' : 'text-[#475569] hover:bg-[#F5F7FA]'"
                    >
                        <Building class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" />
                        <span>Pengurus RT 09</span>
                    </button>
                    <button
                        type="button"
                        @click="switchOrg('karang_taruna')"
                        class="flex-1 sm:flex-initial px-3.5 sm:px-6 py-2.5 sm:py-3 rounded-xl font-bold text-xs sm:text-base transition-all cursor-pointer flex items-center justify-center gap-1.5 sm:gap-2 whitespace-nowrap"
                        :class="orgType === 'karang_taruna' ? 'bg-[#003087] text-white shadow-level-1' : 'text-[#475569] hover:bg-[#F5F7FA]'"
                    >
                        <Users class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" />
                        <span>Karang Taruna</span>
                    </button>
                </div>
            </div>

            <!-- Leadership Period Selector Pill -->
            <div v-if="periods && periods.length > 0" class="flex flex-wrap items-center justify-center gap-2 mb-10">
                <span class="text-sm font-bold text-[#687173] mr-2">Pilih Periode:</span>
                <button
                    v-for="p in periods"
                    :key="p.id"
                    type="button"
                    @click="selectPeriod(p.id)"
                    class="px-4 py-2 rounded-xl text-sm font-bold border transition-all cursor-pointer flex items-center gap-2"
                    :class="[
                        selectedPeriod?.id === p.id
                            ? 'bg-[#009CDE] text-white border-[#009CDE] shadow-level-1'
                            : 'bg-white text-[#1A1A2E] border-[#CBD2D6] hover:bg-[#EBF3FF]',
                    ]"
                >
                    <span>{{ p.name }}</span>
                    <span v-if="p.is_active" class="px-2 py-0.5 text-[10px] uppercase font-black bg-[#F5BA2E] text-[#1A1A2E] rounded-full">
                        Aktif
                    </span>
                </button>
            </div>

            <!-- Structure Cards Grid (Senior Friendly Big Typography & Photos) -->
            <div v-if="selectedPeriod && selectedPeriod.member_positions && selectedPeriod.member_positions.length > 0">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <Card
                        v-for="pos in selectedPeriod.member_positions"
                        :key="pos.id"
                        hover
                        class="flex flex-col items-center text-center p-8 relative overflow-hidden"
                    >
                        <!-- Top Position Ribbon -->
                        <div class="w-full bg-[#EBF3FF] border border-[#009CDE]/30 py-2 px-4 rounded-xl text-sm font-extrabold text-[#003087] mb-6">
                            {{ pos.position?.name }}
                            <span v-if="pos.notes" class="block text-xs font-normal text-[#687173] mt-0.5">
                                ({{ pos.notes }})
                            </span>
                        </div>

                        <!-- Member Avatar -->
                        <div class="w-28 h-28 rounded-full border-4 border-[#003087] overflow-hidden mb-4 bg-[#F0F4F8] flex items-center justify-center shadow-level-2">
                            <img
                                v-if="pos.member?.avatar"
                                :src="pos.member.avatar"
                                :alt="pos.member.name"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="text-3xl font-black text-[#003087]">
                                {{ pos.member?.name.charAt(0) }}
                            </div>
                        </div>

                        <!-- Member Name & Bio -->
                        <h3 class="text-xl sm:text-2xl font-black text-[#1A1A2E] mb-2 leading-tight">
                            {{ pos.member?.name }}
                        </h3>

                        <p v-if="pos.member?.bio" class="text-base text-[#475569] line-clamp-3 mb-4 leading-relaxed">
                            {{ pos.member.bio }}
                        </p>

                        <!-- Contact info if available -->
                        <div class="w-full pt-4 border-t border-[#CBD2D6]/60 mt-auto flex flex-col gap-1.5 text-sm text-[#687173]">
                            <span v-if="pos.member?.phone" class="flex items-center justify-center gap-1.5 font-medium">
                                <Phone class="w-4 h-4 text-[#019C34]" />
                                {{ pos.member.phone }}
                            </span>
                            <span class="text-xs text-[#94A3B8]">Warga sejak {{ pos.member?.join_year || 2020 }}</span>
                        </div>
                    </Card>
                </div>
            </div>

            <!-- Empty State if no members in period -->
            <div v-else class="p-12 text-center bg-white rounded-3xl border border-dashed border-[#CBD2D6] max-w-xl mx-auto">
                <Users class="w-12 h-12 text-[#687173] mx-auto mb-3 opacity-60" />
                <h4 class="text-xl font-bold text-[#1A1A2E] mb-2">Belum ada pengurus di periode ini</h4>
                <p class="text-base text-[#687173]">Data susunan pengurus untuk periode ini belum dimasukkan ke sistem.</p>
            </div>
        </div>
    </PublicLayout>
</template>
