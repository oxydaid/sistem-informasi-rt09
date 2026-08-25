<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

interface Props {
    variant?: 'primary' | 'secondary' | 'gold' | 'danger' | 'outline' | 'ghost';
    size?: 'sm' | 'md' | 'lg';
    href?: string;
    type?: 'button' | 'submit' | 'reset';
    disabled?: boolean;
    loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'md',
    type: 'button',
    disabled: false,
    loading: false,
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'primary':
            return 'bg-[#003087] hover:bg-[#00246B] text-white border-transparent shadow-level-1';
        case 'secondary':
            return 'bg-white hover:bg-[#F5F7FA] text-[#003087] border-2 border-[#003087]';
        case 'gold':
            return 'bg-[#F5BA2E] hover:bg-[#E5AC24] text-[#1A1A2E] border-transparent font-bold shadow-level-1';
        case 'danger':
            return 'bg-[#D20000] hover:bg-[#B30000] text-white border-transparent';
        case 'outline':
            return 'bg-transparent hover:bg-[#F5F7FA] text-[#1A1A2E] border border-[#CBD2D6]';
        case 'ghost':
            return 'bg-transparent hover:bg-[#F5F7FA] text-[#003087] border-transparent';
        default:
            return 'bg-[#003087] text-white';
    }
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'h-9 px-3 text-sm rounded-md gap-1.5';
        case 'lg':
            return 'h-13 px-6 text-lg rounded-xl gap-2.5';
        case 'md':
        default:
            return 'h-11 px-5 text-base rounded-lg gap-2';
    }
});
</script>

<template>
    <component
        :is="href ? Link : 'button'"
        :href="href"
        :type="!href ? type : undefined"
        :disabled="disabled || loading"
        class="inline-flex items-center justify-center font-semibold transition-all duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none active:scale-[0.98]"
        :class="[variantClasses, sizeClasses]"
    >
        <svg
            v-if="loading"
            class="animate-spin -ml-1 mr-2 h-4 w-4 text-current"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <slot />
    </component>
</template>
