<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface LinkItem {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    links: LinkItem[];
}

defineProps<Props>();
</script>

<template>
    <div v-if="links && links.length > 0" class="flex flex-wrap items-center justify-center gap-1.5 pt-8">
        <template v-for="(link, key) in links" :key="key">
            <!-- Disabled button (e.g. Previous/Next on page 1) -->
            <div
                v-if="link.url === null"
                class="px-3.5 py-2 text-xs sm:text-sm text-[#687173] bg-[#F1F5F9] rounded-xl border border-[#CBD2D6]/60 select-none opacity-60 font-medium"
                v-html="link.label"
            />

            <!-- Active / Clickable link -->
            <Link
                v-else
                :href="link.url"
                prefetch
                class="px-3.5 py-2 text-xs sm:text-sm font-bold rounded-xl border transition-all duration-150 cursor-pointer shadow-2xs"
                :class="[
                    link.active
                        ? 'bg-[#003087] text-white border-[#003087] shadow-level-1'
                        : 'bg-white text-[#1A1A2E] border-[#CBD2D6] hover:bg-[#EBF3FF] hover:text-[#003087]',
                ]"
                v-html="link.label"
            />
        </template>
    </div>
</template>
