<template>
    <div class="flex flex-col items-center">
        <dashboard-table title="Most Recent Price Changes" column="Price" :data="priceChanges"/>
        <dashboard-table title="Most Stock Price Changes" column="Stock" :data="stockChanges"/>
        <dashboard-table title="Most Recent Watcher Errors" column="Error" :data="errors"/>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';
import DashboardTable from "@components/tables/DashboardTable.vue";

const props = defineProps({
    errors: {
        type: Array,
        default: () => []
    },
    priceChanges: {
        type: Array,
        default: () => []
    },
    stockChanges: {
        type: Array,
        default: () => []
    }
});
</script>
