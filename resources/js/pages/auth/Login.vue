<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import { ShieldCheck, Lock, Mail, ArrowLeft, Key } from 'lucide-vue-next';

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.post(route('login.store'), {
        onFinish: () => form.reset('password'),
    });
};

const fillDemo = (email: string) => {
    form.email = email;
    form.password = 'password';
};
</script>

<template>
    <div class="min-h-screen bg-[#F5F7FA] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <Head title="Masuk Portal Pengurus RT 09" />

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <Link :href="route('home')" class="inline-flex items-center gap-2 text-sm font-bold text-[#003087] hover:underline mb-4 px-4 sm:px-0">
                <ArrowLeft class="w-4 h-4" />
                <span>Kembali ke Halaman Publik</span>
            </Link>

            <div class="flex items-center justify-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-[#003087] flex items-center justify-center text-white font-black text-xl shadow-level-1">
                    RT
                </div>
                <div class="flex flex-col">
                    <h2 class="text-2xl font-black text-[#003087] tracking-tight">
                        Portal Pengurus
                    </h2>
                    <p class="text-xs text-[#687173]">
                        Sistem Informasi RT 09 & Karang Taruna
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
            <Card class="p-8 shadow-level-2 border-[#CBD2D6]">
                <form @submit.prevent="submit" class="space-y-5">
                    <Input
                        v-model="form.email"
                        type="email"
                        label="Alamat Email Pengurus"
                        placeholder="contoh: admin.rt@rt09.id"
                        required
                        :error="form.errors.email"
                    />

                    <Input
                        v-model="form.password"
                        type="password"
                        label="Kata Sandi (Password)"
                        placeholder="••••••••"
                        required
                        :error="form.errors.password"
                    />

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="w-4 h-4 rounded border-[#CBD2D6] text-[#003087] focus:ring-[#003087]"
                            />
                            <span class="text-sm text-[#475569]">Ingat sesi saya</span>
                        </label>
                    </div>

                    <Button
                        type="submit"
                        variant="primary"
                        class="w-full"
                        size="lg"
                        :loading="form.processing"
                    >
                        <ShieldCheck class="w-5 h-5 mr-2" />
                        <span>Masuk ke Dashboard</span>
                    </Button>
                </form>

                <!-- Demo Credentials Quick Fill -->
                <div class="mt-8 pt-6 border-t border-[#CBD2D6]/70">
                    <span class="block text-xs font-bold text-[#687173] uppercase mb-3 text-center">
                        Pilih Akun Cepat (Demo):
                    </span>
                    <div class="grid grid-cols-1 gap-2">
                        <button
                            type="button"
                            @click="fillDemo('superadmin@rt09.id')"
                            class="px-3 py-2 bg-[#EBF3FF] hover:bg-[#D6E6FF] text-[#003087] rounded-xl text-xs font-bold flex items-center justify-between cursor-pointer transition-colors"
                        >
                            <span>Super Administrator</span>
                            <span class="text-[11px] font-normal text-[#687173]">superadmin@rt09.id</span>
                        </button>
                        <button
                            type="button"
                            @click="fillDemo('admin.rt@rt09.id')"
                            class="px-3 py-2 bg-[#F0FDF4] hover:bg-[#DCFCE7] text-[#019C34] rounded-xl text-xs font-bold flex items-center justify-between cursor-pointer transition-colors"
                        >
                            <span>Admin Pengurus RT 09</span>
                            <span class="text-[11px] font-normal text-[#687173]">admin.rt@rt09.id</span>
                        </button>
                        <button
                            type="button"
                            @click="fillDemo('admin.karangtaruna@rt09.id')"
                            class="px-3 py-2 bg-[#FFF8E1] hover:bg-[#FEF08A] text-[#9A6B00] rounded-xl text-xs font-bold flex items-center justify-between cursor-pointer transition-colors"
                        >
                            <span>Admin Karang Taruna</span>
                            <span class="text-[11px] font-normal text-[#687173]">admin.karangtaruna@rt09.id</span>
                        </button>
                    </div>
                </div>
            </Card>
        </div>
    </div>
</template>
