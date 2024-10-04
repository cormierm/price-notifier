<template>
    <div class="flex flex-col">
        <label class="flex flex-col">
            {{ label }}
            <slot></slot>
            <input
                class="rounded"
                :class="{'border-red-500': errors, 'bg-gray-100 border-gray-100 text-gray-700': disabled}"
                :type="type"
                :placeholder="placeholder"
                :value="modelValue"
                :disabled="disabled"
                @input="$emit('update:modelValue', $event.target.value)"
            />
        </label>
        <div v-if="errors" class="text-sm text-red-500">{{ errors[0] }}</div>
    </div>
</template>

<script setup>
const props = defineProps({
    label: {
        type: String,
        required: false
    },
    type: {
        type: String,
        default: 'text'
    },
    placeholder: {
        type: String,
        default: ''
    },
    errors: {
        type: Array,
        default: null
    },
    modelValue: {
        type: [String, Number],
        required: true
    },
    disabled: {
        type: Boolean,
        default: false
    }
});
</script>
