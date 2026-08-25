<script setup lang="ts">
interface Props {
    modelValue?: string | number | null;
    label?: string;
    type?: string;
    placeholder?: string;
    error?: string;
    helper?: string;
    required?: boolean;
    disabled?: boolean;
    id?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    type: 'text',
    required: false,
    disabled: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void;
}>();

const inputId = props.id || 'input-' + Math.random().toString(36).substring(2, 9);
</script>

<template>
    <div class="w-full flex flex-col gap-1.5">
        <label
            v-if="label"
            :for="inputId"
            class="text-sm font-semibold text-[#1A1A2E] flex items-center gap-1"
        >
            {{ label }}
            <span v-if="required" class="text-[#D20000]">*</span>
        </label>
        
        <input
            :id="inputId"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            class="h-11 w-full px-4 rounded-lg bg-white border text-[#1A1A2E] placeholder-[#687173] text-base transition-colors duration-150 focus:outline-none disabled:bg-[#F5F7FA] disabled:cursor-not-allowed"
            :class="[
                error
                    ? 'border-[#D20000] focus:ring-2 focus:ring-[#D20000]/20'
                    : 'border-[#CBD2D6] focus:border-[#003087] focus:ring-2 focus:ring-[#003087]/20',
            ]"
        />

        <p v-if="error" class="text-xs text-[#D20000] font-medium mt-0.5">
            {{ error }}
        </p>
        <p v-else-if="helper" class="text-xs text-[#687173] mt-0.5">
            {{ helper }}
        </p>
    </div>
</template>
