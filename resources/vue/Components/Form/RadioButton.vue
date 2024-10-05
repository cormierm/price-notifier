<template>
    <label class="flex items-center gap-2">
        <input
            type="radio"
            :checked="modelValue === value"
            @change="$emit('update:modelValue', value)"
            :name="name"
            :value="value"
        />
        {{ label }}
    </label>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    name: {
        type: String,
        required: true,
    },
});
</script>
