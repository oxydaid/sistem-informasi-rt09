<script setup lang="ts">
import { ref, onMounted, onUpdated, nextTick } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import type { ContentItem } from '@/types';
import { FileText, Download, Clock, ChevronRight, ArrowLeft } from 'lucide-vue-next';

interface Props {
    page: ContentItem;
}

const props = defineProps<Props>();
const contentContainer = ref<HTMLElement | null>(null);

const makeTablesAndMediaResponsive = () => {
    if (!contentContainer.value) return;

    // Wrap tables in responsive scroll container without adding card borders
    const tables = contentContainer.value.querySelectorAll('table');
    tables.forEach((table) => {
        if (!table.parentElement?.classList.contains('table-responsive-wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive-wrapper overflow-x-auto w-full my-6';
            table.parentNode?.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });

    // Ensure all images are responsive and fit container
    const images = contentContainer.value.querySelectorAll('img');
    images.forEach((img) => {
        img.classList.add('max-w-full', 'h-auto', 'rounded-xl');
    });
};

onMounted(() => {
    nextTick(() => {
        makeTablesAndMediaResponsive();
    });
});

onUpdated(() => {
    nextTick(() => {
        makeTablesAndMediaResponsive();
    });
});

const formatDate = (dateStr?: string | null) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>
    <PublicLayout>
        <Head :title="`${page.title} - RT 09`" />

        <!-- Clean Pure White Container (Direct Content, Full Width Aligned with Navbar) -->
        <div class="bg-white py-8 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs sm:text-sm text-[#687173] mb-4 flex-wrap">
                    <Link :href="route('home')" class="hover:text-[#003087] transition-colors font-medium">Beranda</Link>
                    <ChevronRight class="w-3.5 h-3.5 text-[#94A3B8]" />
                    <span class="text-[#003087] font-semibold">Halaman Informasi</span>
                    <ChevronRight class="w-3.5 h-3.5 text-[#94A3B8]" />
                    <span class="text-[#1A1A2E] truncate max-w-xs sm:max-w-md font-medium">{{ page.title }}</span>
                </nav>

                <!-- Title Header (Directly in Container, Straight & Aligned) -->
                <header class="pb-6 mb-8 border-b border-[#CBD2D6]/80 space-y-3">
                    <Badge variant="primary">Halaman Informasi</Badge>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-[#001B52] leading-tight">
                        {{ page.title }}
                    </h1>
                    <div v-if="page.published_at || page.updated_at" class="flex items-center gap-4 text-xs sm:text-sm text-[#687173] pt-1">
                        <span class="flex items-center gap-1.5 font-medium">
                            <Clock class="w-4 h-4 text-[#009CDE]" />
                            Terakhir diperbarui: {{ formatDate(page.updated_at || page.published_at) }}
                        </span>
                        <span v-if="page.organization?.name" class="font-bold text-[#003087]">
                            • {{ page.organization.name }}
                        </span>
                    </div>
                </header>

                <!-- Hero Thumbnail if present -->
                <div v-if="page.thumbnail" class="mb-8 rounded-2xl overflow-hidden max-h-[480px]">
                    <img
                        :src="page.thumbnail"
                        :alt="page.title"
                        class="w-full h-full object-cover"
                    />
                </div>

                <!-- Main Content (Direct Prose Canvas, No Card Box) -->
                <main
                    ref="contentContainer"
                    class="prose-tiptap text-base sm:text-lg text-[#1A1A2E] leading-relaxed max-w-none w-full"
                    v-html="page.content_html"
                />

                <!-- Downloadable Attachments Section (Clean Direct Minimal Style) -->
                <section
                    v-if="page.attachments && page.attachments.length > 0"
                    class="mt-10 pt-8 border-t border-[#CBD2D6] space-y-4"
                >
                    <h3 class="text-xl font-bold text-[#003087] flex items-center gap-2">
                        <FileText class="w-5 h-5 text-[#009CDE]" />
                        <span>Lampiran & Dokumen Terkait</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <a
                            v-for="att in page.attachments"
                            :key="att.id"
                            :href="att.media?.url"
                            target="_blank"
                            class="p-4 rounded-xl border border-[#CBD2D6] hover:border-[#003087] hover:bg-[#F8FAFC] flex items-center justify-between transition-all group"
                        >
                            <div class="min-w-0 pr-3">
                                <span class="font-bold text-sm text-[#003087] group-hover:text-[#009CDE] block truncate">
                                    {{ att.display_name }}
                                </span>
                                <span class="text-xs text-[#687173]">Klik untuk mengunduh</span>
                            </div>
                            <Download class="w-5 h-5 text-[#009CDE] group-hover:translate-y-0.5 transition-transform shrink-0" />
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </PublicLayout>
</template>
