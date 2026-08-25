<script setup lang="ts">
import { Inbox } from 'lucide-vue-next';

interface Props {
    title?: string;
    description?: string;
}

withDefaults(defineProps<Props>(), {
    title: 'Belum ada data',
    description: 'Data yang Anda cari tidak ditemukan atau belum ditambahkan.',
});
</script>

<template>
    <div class="flex flex-col items-center justify-center p-12 text-center bg-white rounded-2xl border border-dashed border-[#CBD2D6]">
        <div class="w-16 h-16 rounded-full bg-[#EBF3FF] flex items-center justify-center text-[#003087] mb-4">
            <slot name="icon">
                <Inbox class="w-8 h-8 opacity-80" />
            </slot>
        </div>
        <h4 class="text-lg font-bold text-[#1A1A2E] mb-1">
            {{ title }}
        </h4>
        <p class="text-sm text-[#687173] max-w-md mb-5">
            {{ description }}
        </p>
        <slot name="action" />
    </div>
</template>
