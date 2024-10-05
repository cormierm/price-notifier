<template>
    <div class="mt-4 flex items-center">
        <select class="rounded" v-model="condition">
            <option
                v-for="option in stockConditions"
                :value="option.value"
                :key="option.value">
                {{ option.label }}
            </option>
        </select>
        <FormInput
            class="w-full"
            placeholder="In Stock."
            v-model="text"
        />
    </div>
</template>

<script setup>
import FormInput from "@Components/Form/FormInput.vue";

const condition = defineModel('condition')
const text = defineModel('text')

const stockConditions = [
    {label: 'Contains Text', value: 'contains_text'},
    {label: 'Missing Text', value: 'missing_text'},
    {label: 'Contains Html', value: 'contains_html'},
    {label: 'Missing Html', value: 'missing_html'},
];
</script>
