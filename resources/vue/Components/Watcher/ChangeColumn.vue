<template>
    <div class="text-sm" :class="changeClass">
        <div v-if="showChange">
            <span v-if="changePercentage > 0">▲</span>
            <span v-else-if="changePercentage < 0">▼</span>
            <span>{{ Math.abs(changePercentage) }}%</span>
        </div>
        <div v-else>-</div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    watcher: {
        type: Object,
        required: true
    }
});

const changePercentage = computed(() => {
    if (props.watcher.initial_value && props.watcher.value) {
        return (((props.watcher.value - props.watcher.initial_value) / props.watcher.initial_value) * 100).toFixed(1);
    }
    return 0;
});

const changeClass = computed(() => {
    if (changePercentage.value > 0) {
        return props.watcher.alert_condition === 'less_than' ? 'text-red-500' : 'text-green-500';
    }
    if (changePercentage.value < 0) {
        return props.watcher.alert_condition === 'less_than' ? 'text-green-500' : 'text-red-500';
    }
    return '';
});

const showChange = computed(() => {
    return props.watcher.value && props.watcher.initial_value && props.watcher.value !== props.watcher.initial_value;
});
</script>
