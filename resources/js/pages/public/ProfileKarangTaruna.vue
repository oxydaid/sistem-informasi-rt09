<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import type { Organization } from '@/types';
import { Users, Award, Sparkles, Phone, Mail, MapPin, HeartHandshake } from 'lucide-vue-next';

interface Props {
    karangTaruna: Organization;
}

const props = defineProps<Props>();
</script>

<template>
    <PublicLayout>
        <Head title="Karang Taruna Tunas Karya RT 09" />

        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#003087] via-[#00246B] to-[#009CDE] text-white py-12 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <Badge variant="warning" class="mb-3">Wadah Pemuda RT 09</Badge>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white mb-4">
                        {{ karangTaruna.name }}
                    </h1>
                    <p class="text-lg sm:text-xl text-white/90 leading-relaxed">
                        Membangun generasi muda yang kreatif, berdaya saing, peduli sosial lingkungan, dan berjiwa wirausaha.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Main Content (2 cols) -->
                <div class="lg:col-span-2 space-y-10">
                    <!-- About Karang Taruna -->
                    <Card class="space-y-4">
                        <h2 class="text-2xl font-black text-[#003087]">
                            Tentang Karang Taruna Tunas Karya
                        </h2>
                        <p class="text-base sm:text-lg text-[#334155] leading-relaxed">
                            {{ karangTaruna.description }}
                        </p>
                        <p class="text-base sm:text-lg text-[#334155] leading-relaxed">
                            Sebagai motor penggerak kegiatan kepemudaan di RT 09, Karang Taruna Tunas Karya aktif menyelenggarakan berbagai program sosial, perayaan hari besar nasional, turnamen olahraga, serta program pengelolaan sampah bernilai ekonomis (Bank Sampah).
                        </p>
                    </Card>

                    <!-- Program Kerja Unggulan -->
                    <Card class="space-y-6">
                        <h3 class="text-2xl font-black text-[#003087]">
                            Program Kerja Unggulan
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-5 rounded-2xl bg-[#F8FAFC] border border-[#CBD2D6]/70 space-y-2">
                                <span class="text-2xl">🏆</span>
                                <h4 class="font-bold text-[#003087] text-lg">Pesta Rakyat & 17 Agustus</h4>
                                <p class="text-sm text-[#475569]">Penyelenggaraan perlombaan anak & dewasa, pentas seni warga, dan malam syukuran kemerdekaan.</p>
                            </div>
                            <div class="p-5 rounded-2xl bg-[#F8FAFC] border border-[#CBD2D6]/70 space-y-2">
                                <span class="text-2xl">♻️</span>
                                <h4 class="font-bold text-[#003087] text-lg">Bank Sampah Pemuda</h4>
                                <p class="text-sm text-[#475569]">Pengumpulan botol plastik, kardus, dan minyak jelantah dari warga untuk didaur ulang menjadi kas mandiri.</p>
                            </div>
                            <div class="p-5 rounded-2xl bg-[#F8FAFC] border border-[#CBD2D6]/70 space-y-2">
                                <span class="text-2xl">⚽</span>
                                <h4 class="font-bold text-[#003087] text-lg">Klub Olahraga Rutin</h4>
                                <p class="text-sm text-[#475569]">Jadwal rutin bulutangkis, futsal pemuda, dan senam kebugaran jasmani bersama warga.</p>
                            </div>
                            <div class="p-5 rounded-2xl bg-[#F8FAFC] border border-[#CBD2D6]/70 space-y-2">
                                <span class="text-2xl">🤝</span>
                                <h4 class="font-bold text-[#003087] text-lg">Bakti Sosial Lingkungan</h4>
                                <p class="text-sm text-[#475569]">Bantuan tanggap darurat bagi warga yang sakit, takziyah, dan santunan anak yatim.</p>
                            </div>
                        </div>
                    </Card>

                    <!-- Daftar Anggota Pemuda -->
                    <Card v-if="karangTaruna.members && karangTaruna.members.length > 0" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-black text-[#003087]">
                                Anggota Karang Taruna
                            </h3>
                            <Badge variant="primary">{{ karangTaruna.members.length }} Anggota Terdaftar</Badge>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <div
                                v-for="member in karangTaruna.members"
                                :key="member.id"
                                class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#CBD2D6]/50 flex items-center gap-3"
                            >
                                <div class="w-10 h-10 rounded-full bg-[#009CDE] text-white font-bold flex items-center justify-center text-sm shadow-xs shrink-0">
                                    {{ member.name.charAt(0) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h5 class="text-base font-bold text-[#1A1A2E] truncate">{{ member.name }}</h5>
                                    <p class="text-xs text-[#687173]">Bergabung sejak {{ member.join_year || 2024 }}</p>
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Sidebar (1 col) -->
                <div class="space-y-6">
                    <Card class="space-y-4 bg-[#F8FAFC]">
                        <h3 class="text-lg font-bold text-[#003087] border-b border-[#CBD2D6] pb-3">
                            Kontak & Sekretariat
                        </h3>
                        <div class="space-y-3 text-sm sm:text-base text-[#334155]">
                            <div class="flex items-start gap-3">
                                <MapPin class="w-5 h-5 text-[#009CDE] shrink-0 mt-0.5" />
                                <span>{{ karangTaruna.address || 'Balai Warga RT 09' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <Phone class="w-5 h-5 text-[#009CDE] shrink-0" />
                                <span>{{ karangTaruna.phone || '0857-1234-5678' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <Mail class="w-5 h-5 text-[#009CDE] shrink-0" />
                                <span>{{ karangTaruna.email || 'karangtaruna@rt09.id' }}</span>
                            </div>
                        </div>
                    </Card>

                    <Card class="bg-gradient-to-br from-[#009CDE] to-[#003087] text-white space-y-4">
                        <h3 class="text-lg font-bold text-[#F5BA2E]">
                            Kepengurusan Karang Taruna
                        </h3>
                        <p class="text-sm text-white/90">
                            {{ karangTaruna.active_period?.name || 'Periode Aktif' }}
                        </p>
                        <Link
                            :href="route('public.structure', { org: 'karang_taruna' })"
                            class="inline-block w-full py-3 bg-white text-[#003087] text-center rounded-xl font-bold text-sm hover:bg-[#F5BA2E] hover:text-[#1A1A2E] transition-all"
                        >
                            Lihat Struktur Pengurus KT →
                        </Link>
                    </Card>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
