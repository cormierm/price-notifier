<template>
    <form-input
        class="mt-8"
        :label="label"
        :placeholder="placeholder"
        :errors="errors"
        v-model="query"
    >
        <div class="flex gap-3 pb-1">
            <label class="flex items-center gap-2">
                <input
                    type="radio"
                    v-model="queryType"
                    :name="radioButtonsName"
                    value="xpath"
                />
                XPath
            </label>
            <label class="flex items-center gap-2">
                <input
                    type="radio"
                    v-model="queryType"
                    :name="radioButtonsName"
                    value="selector"
                />
                Query Selector
            </label>
            <label class="flex items-center gap-2">
                <input
                    type="radio"
                    v-model="queryType"
                    :name="radioButtonsName"
                    value="regex"
                />
                Regex
            </label>
        </div>
    </form-input>
</template>

<script setup>

import FormInput from "@Components/Form/FormInput.vue";
import {computed} from "vue";

const props = defineProps({
    label: {
        type: String,
        required: true
    },
    errors: {
        type: Array,
        required: false
    },
    radioButtonsName: {
        type: String,
        default: 'price_query_type'
    }
})

const query = defineModel('query');
const queryType = defineModel('queryType', {type: String})

const placeholder = computed(() => {
    switch (queryType.value) {
        case 'regex':
            return '/textBefore(.*?)textAfter/';
        case 'selector':
            return `.foobar span`;
        default:
            return `//span[@class="foobar"]`;
    }
})

</script>
