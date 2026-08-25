<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'neutral';
    size?: 'sm' | 'md';
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'md',
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'primary':
            return 'bg-[#EBF3FF] text-[#003087] border border-[#009CDE]/30';
        case 'secondary':
            return 'bg-[#E0F2FE] text-[#0369A1]';
        case 'success':
            return 'bg-[#E6F4EA] text-[#019C34] font-semibold';
        case 'warning':
            return 'bg-[#FFF8E1] text-[#9A6B00] font-semibold';
        case 'danger':
            return 'bg-[#FDE7E7] text-[#D20000] font-semibold';
        case 'neutral':
        default:
            return 'bg-[#F1F5F9] text-[#475569] border border-[#CBD2D6]';
    }
});

const sizeClasses = computed(() => {
    return props.size === 'sm' ? 'px-2.5 py-0.5 text-xs' : 'px-3 py-1 text-sm';
});
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 font-medium rounded-full whitespace-nowrap"
        :class="[variantClasses, sizeClasses]"
    >
        <slot />
    </span>
</template>
