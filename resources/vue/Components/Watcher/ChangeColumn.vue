<template>
    <div class="text-sm" :class="changeClass">
        <div v-if="showChange">
            <span v-if="changePercentage > 0">▲</span>
            <span v-else-if="changePercentage < 0">▼</span>
            <span>{{ changePercentage }}%</span>
        </div>
        <div v-else>-</div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    initialValue: {
        type: String,
        default: null
    },
    currentValue: {
        type: String,
        default: null
    }
});

const changePercentage = computed(() => {
    if (props.initialValue && props.currentValue) {
        return (((props.currentValue - props.initialValue) / props.initialValue) * 100).toFixed(1);
    }
    return 0;
});

const changeClass = computed(() => {
    if (changePercentage.value > 0) {
        return 'text-red-500';
    }
    if (changePercentage.value < 0) {
        return 'text-green-500';
    }
    return '';
});

const showChange = computed(() => {
    return props.currentValue && props.initialValue && props.currentValue !== props.initialValue;
});
</script>
