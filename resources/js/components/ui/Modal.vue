<script setup lang="ts">
import { watch } from 'vue';
import { X } from 'lucide-vue-next';

interface Props {
    show: boolean;
    title?: string;
    maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
}

const props = withDefaults(defineProps<Props>(), {
    show: false,
    maxWidth: 'md',
});

const emit = defineEmits<{
    (e: 'close'): void;
}>();

watch(
    () => props.show,
    (val) => {
        if (val) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
);

const maxWidthClass = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
}[props.maxWidth];
</script>

<template>
    <teleport to="body">
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 overflow-y-auto bg-[#1A1A2E]/60 backdrop-blur-xs flex items-center justify-center p-4"
                @click.self="emit('close')"
            >
                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-4 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:scale-95"
                >
                    <div
                        class="w-full bg-white rounded-2xl shadow-level-3 border border-[#CBD2D6] overflow-hidden transition-all transform flex flex-col max-h-[90vh]"
                        :class="[maxWidthClass]"
                    >
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-[#CBD2D6] flex items-center justify-between bg-[#F8FAFC]">
                            <h3 class="text-lg font-bold text-[#003087]">
                                <slot name="title">{{ title }}</slot>
                            </h3>
                            <button
                                type="button"
                                @click="emit('close')"
                                class="text-[#687173] hover:text-[#1A1A2E] p-1.5 rounded-lg hover:bg-[#CBD2D6]/30 transition-colors"
                            >
                                <X class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 overflow-y-auto">
                            <slot />
                        </div>

                        <!-- Footer -->
                        <div v-if="$slots.footer" class="px-6 py-3.5 border-t border-[#CBD2D6] bg-[#F8FAFC] flex justify-end gap-3">
                            <slot name="footer" />
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>
