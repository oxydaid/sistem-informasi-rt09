<script setup lang="ts">
interface Props {
    hover?: boolean;
    padding?: 'none' | 'sm' | 'md' | 'lg';
    statusBorder?: 'none' | 'success' | 'warning' | 'danger' | 'primary';
}

const props = withDefaults(defineProps<Props>(), {
    hover: false,
    padding: 'md',
    statusBorder: 'none',
});

const paddingClasses = {
    none: 'p-0',
    sm: 'p-4',
    md: 'p-6',
    lg: 'p-8',
}[props.padding];

const statusBorderClasses = {
    none: '',
    success: 'border-l-4 border-l-[#019C34]',
    warning: 'border-l-4 border-l-[#F5BA2E]',
    danger: 'border-l-4 border-l-[#D20000]',
    primary: 'border-l-4 border-l-[#003087]',
}[props.statusBorder];
</script>

<template>
    <div
        class="bg-white rounded-2xl border border-[#CBD2D6]/80 shadow-level-1 transition-all duration-200"
        :class="[
            paddingClasses,
            statusBorderClasses,
            hover ? 'hover:shadow-level-2 hover:border-[#009CDE]/50 cursor-pointer' : '',
        ]"
    >
        <slot />
    </div>
</template>
