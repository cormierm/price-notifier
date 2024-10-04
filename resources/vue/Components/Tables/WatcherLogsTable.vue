<template>
    <table class="text-sm text-gray-600 w-full">
        <thead class="text-xs text-left uppercase bg-gray-700 text-gray-300">
        <tr>
            <th class="py-2 px-4">Created</th>
            <th class="py-2 px-4">Formatted Value</th>
            <th class="py-2 px-4">Raw Value</th>
            <th class="py-2 px-4">Stock</th>
            <th class="py-2 px-4">Raw Stock</th>
            <th class="py-2 px-4">Duration (ms)</th>
            <th class="py-2 px-4">Region</th>
            <th class="py-2 px-4">Error</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in tableData" class="bg-white border-b">
            <td class="py-2 px-4 whitespace-nowrap">{{ row.created_at_formatted }}</td>
            <td class="py-2 px-4">{{ row.formatted_value }}</td>
            <td class="py-2 px-4">{{ row.raw_value }}</td>
            <td class="py-2 px-4">{{ row.has_stock }}</td>
            <td class="py-2 px-4">{{ row.raw_stock }}</td>
            <td class="py-2 px-4">{{ row.duration }}</td>
            <td class="py-2 px-4">{{ row.region }}</td>
            <td class="py-2 px-4">{{ row.error }}</td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import moment from "moment";
import {computed} from "vue";

const props = defineProps({
    data: {
        type: Array,
        default: () => []
    },
});

const tableData = computed(() => {
    return props.data.map((log) => {
        return {
            ...log,
            created_at_formatted: log.created_at ? moment(log.created_at).format('YYYY-MM-DD HH:mm:ss') : '',
            has_stock: log.has_stock === 1 ? 'Yes' : log.has_stock === 0 ? 'No' : '-',
        }
    })
});
</script>
