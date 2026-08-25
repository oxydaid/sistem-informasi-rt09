<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import type { Organization } from '@/types';
import { Building, MapPin, Phone, Mail, Users, CheckCircle, ShieldCheck } from 'lucide-vue-next';

interface Props {
    rt: Organization;
}

const props = defineProps<Props>();
</script>

<template>
    <PublicLayout>
        <Head title="Profil Rukun Tetangga 09 / RW 04" />

        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#001B52] to-[#003087] text-white py-12 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <Badge variant="warning" class="mb-3">Profil Wilayah & Pengurus</Badge>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white mb-4">
                        {{ rt.name }}
                    </h1>
                    <p class="text-lg sm:text-xl text-white/90 leading-relaxed">
                        Kelurahan Sukamaju, Kecamatan Cilodong, Kota Depok, Jawa Barat.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Main Content (2 cols) -->
                <div class="lg:col-span-2 space-y-10">
                    <!-- About RT -->
                    <Card class="space-y-4">
                        <h2 class="text-2xl font-black text-[#003087]">
                            Tentang Lingkungan RT 09
                        </h2>
                        <p class="text-base sm:text-lg text-[#334155] leading-relaxed">
                            {{ rt.description }}
                        </p>
                        <p class="text-base sm:text-lg text-[#334155] leading-relaxed">
                            Wilayah RT 09 berdiri dengan semangat persatuan, kekeluargaan, dan gotong royong. Seluruh program dan kegiatan didasarkan pada musyawarah mufakat warga demi mewujudkan tempat tinggal yang aman, nyaman, asri, dan ramah untuk seluruh keluarga.
                        </p>
                    </Card>

                    <!-- Visi & Misi -->
                    <Card class="space-y-6">
                        <div>
                            <h3 class="text-xl font-bold text-[#003087] mb-2 flex items-center gap-2">
                                <ShieldCheck class="w-6 h-6 text-[#009CDE]" />
                                <span>Visi RT 09</span>
                            </h3>
                            <p class="text-base sm:text-lg text-[#334155] bg-[#F8FAFC] p-4 rounded-xl border border-[#CBD2D6]/50">
                                "Mewujudkan lingkungan RT 09 yang asri, berwawasan lingkungan, aman dari kriminalitas, tertib dalam pelayanan warga, serta menjunjung tinggi nilai guyub rukun kekeluargaan."
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-[#003087] mb-3 flex items-center gap-2">
                                <CheckCircle class="w-6 h-6 text-[#019C34]" />
                                <span>Misi Utama</span>
                            </h3>
                            <ul class="space-y-3 text-base sm:text-lg text-[#334155]">
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-[#EBF3FF] text-[#003087] font-bold text-sm flex items-center justify-center shrink-0 mt-0.5">1</span>
                                    <span>Menjaga keamanan lingkungan melalui siskamling ronda malam dan optimalisasi portal keamanan warga.</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-[#EBF3FF] text-[#003087] font-bold text-sm flex items-center justify-center shrink-0 mt-0.5">2</span>
                                    <span>Menyelenggarakan kerja bakti berkala dan pemilahan sampah organik & anorganik mandiri.</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-[#EBF3FF] text-[#003087] font-bold text-sm flex items-center justify-center shrink-0 mt-0.5">3</span>
                                    <span>Meningkatkan fasilitas umum, penerangan jalan, serta kenyamanan fasilitas ruang publik warga.</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-[#EBF3FF] text-[#003087] font-bold text-sm flex items-center justify-center shrink-0 mt-0.5">4</span>
                                    <span>Memberdayakan generasi muda melalui wadah kepemudaan Karang Taruna Tunas Karya.</span>
                                </li>
                            </ul>
                        </div>
                    </Card>

                    <!-- Fasilitas & Sarana Lingkungan -->
                    <Card class="space-y-4">
                        <h2 class="text-2xl font-black text-[#003087]">
                            Sarana & Fasilitas Bersama
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="p-4 rounded-xl bg-[#F0F7FC] border border-[#009CDE]/30">
                                <h4 class="font-bold text-[#003087] text-lg mb-1">🏠 Balai Pertemuan Warga</h4>
                                <p class="text-sm text-[#475569]">Fasilitas rapat, posyandu lansia & balita, serta kegiatan silaturahmi warga.</p>
                            </div>
                            <div class="p-4 rounded-xl bg-[#F0F7FC] border border-[#009CDE]/30">
                                <h4 class="font-bold text-[#003087] text-lg mb-1">🏸 Lapangan Serbaguna</h4>
                                <p class="text-sm text-[#475569]">Lapangan bulutangkis, senam pagi ibu-ibu, dan area olahraga pemuda.</p>
                            </div>
                            <div class="p-4 rounded-xl bg-[#F0F7FC] border border-[#009CDE]/30">
                                <h4 class="font-bold text-[#003087] text-lg mb-1">🚨 Pos Ronda & CCTV 24 Jam</h4>
                                <p class="text-sm text-[#475569]">Pos keamanan gerbang utama dengan penjagaan dan pantauan kamera lingkungan.</p>
                            </div>
                            <div class="p-4 rounded-xl bg-[#F0F7FC] border border-[#009CDE]/30">
                                <h4 class="font-bold text-[#003087] text-lg mb-1">🌱 Taman Obat Keluarga (TOGA)</h4>
                                <p class="text-sm text-[#475569]">Pekarangan hijau tanaman herbal dan sayuran organik yang dikelola warga.</p>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Sidebar Info (1 col) -->
                <div class="space-y-6">
                    <!-- Sekretariat Box -->
                    <Card class="space-y-4 bg-[#F8FAFC]">
                        <h3 class="text-lg font-bold text-[#003087] border-b border-[#CBD2D6] pb-3">
                            Informasi Sekretariat
                        </h3>
                        <div class="space-y-3 text-sm sm:text-base text-[#334155]">
                            <div class="flex items-start gap-3">
                                <MapPin class="w-5 h-5 text-[#003087] shrink-0 mt-0.5" />
                                <span>{{ rt.address }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <Phone class="w-5 h-5 text-[#003087] shrink-0" />
                                <span>{{ rt.phone || '0812-3456-7890' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <Mail class="w-5 h-5 text-[#003087] shrink-0" />
                                <span>{{ rt.email || 'rt09@sukamaju.id' }}</span>
                            </div>
                        </div>
                    </Card>

                    <!-- Active Period Pengurus Banner -->
                    <Card class="bg-gradient-to-br from-[#003087] to-[#001B52] text-white space-y-4">
                        <h3 class="text-lg font-bold text-[#F5BA2E]">
                            Kepengurusan Aktif
                        </h3>
                        <p class="text-sm text-white/80">
                            {{ rt.active_period?.name || 'Periode 2024 - 2027' }}
                        </p>
                        <Link
                            :href="route('public.structure', { org: 'rt' })"
                            class="inline-block w-full py-3 bg-white text-[#003087] text-center rounded-xl font-bold text-sm hover:bg-[#F5BA2E] hover:text-[#1A1A2E] transition-all"
                        >
                            Lihat Bagan Struktur Pengurus →
                        </Link>
                    </Card>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
