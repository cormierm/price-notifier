<template>
    <form-input
        class="mt-8"
        label="Price Query"
        :placeholder="pricePlaceholder"
        :errors="errors"
        v-model="query"
    >
        <div class="flex gap-3 pb-1">
            <label class="flex items-center gap-2">
                <input
                    type="radio"
                    v-model="queryType"
                    name="price_query_type"
                    value="xpath"
                />
                XPath
            </label>
            <label class="flex items-center gap-2">
                <input
                    type="radio"
                    v-model="queryType"
                    name="price_query_type"
                    value="selector"
                />
                Query Selector
            </label>
            <label class="flex items-center gap-2">
                <input
                    type="radio"
                    v-model="queryType"
                    name="price_query_type"
                    value="regex"
                />
                Regex
            </label>
        </div>
    </form-input>
</template>

<script setup>

import FormInput from "@Components/Form/FormInput.vue";

const props = defineProps({
    errors: {
        type: Object,
        required: true
    },
    queryPlaceholder: {
        type: String,
        required: true
    }
})

const query = defineModel('query');
const queryType = defineModel('queryType')
</script>
