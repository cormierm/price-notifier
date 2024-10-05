<template>
    <div class="mt-4 flex items-center">
        <select class="rounded dark:bg-gray-900" v-model="condition">
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
    {label: 'InnerText Contains', value: 'contains_text'},
    {label: 'InnerText Missing', value: 'missing_text'},
    {label: 'OuterHtml Contains', value: 'contains_html'},
    {label: 'OuterHtml Missing', value: 'missing_html'},
];
</script>
