<template>
    <label class="flex items-center gap-2 mt-3">Price Alert Condition</label>
    <div class="mt-4 flex items-center">
        <select class="rounded dark:bg-gray-900" v-model="condition">
            <option
                v-for="option in alertConditions"
                :value="option.value"
                :key="option.value">
                {{ option.label }}
            </option>
        </select>
        <FormInput
            class="w-full"
            type="number"
            placeholder="5.00"
            v-model="amount"
        />
    </div>
    <div v-if="error" class="text-sm text-red-500">{{ error }}</div>
</template>

<script setup>
import FormInput from "@Components/Form/FormInput.vue";
import {computed} from "vue";

const condition = defineModel('condition')
const amount = defineModel('amount')

const props = defineProps({
    label: {
        type: String,
        default: 'Watcher'
    },
    errors: {
        type: Object,
        default: null
    },
})

const error = computed(() => {
    if (Array.isArray(props.errors.alert_value) && props.errors.alert_value.length > 0) {
        return props.errors.alert_value[0];
    }
    if (Array.isArray(props.errors.alert_condition) && props.errors.alert_condition.length > 0) {
        return props.errors.alert_condition[0];
    }
    return null;
})

const alertConditions = [
    {label: 'Less Than', value: 'less_than'},
    {label: 'Greater Than', value: 'greater_than'},
];
</script>
