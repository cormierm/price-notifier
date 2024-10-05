<template>
    <h3 class="text-lg">Price History</h3>
    <table class="text-sm text-gray-600 w-full">
        <thead class="text-xs text-left uppercase bg-gray-700 text-gray-300">
        <tr>
            <th class="py-2 px-4">Type</th>
            <th class="py-2 px-4">Price</th>
            <th class="py-2 px-4">Date</th>
        </tr>
        </thead>
        <tbody>
        <tr class="bg-white border-b dark:bg-gray-800 dark:text-gray-100 dark:border-b-gray-700">
            <td class="py-2 px-4">Current</td>
            <td class="py-2 px-4">${{ watcher.value }}</td>
            <td class="py-2 px-4">{{ formatDate(watcher.last_sync) }}</td>
        </tr>
        <tr class="bg-white border-b dark:bg-gray-800 dark:text-gray-100 dark:border-b-gray-700">
            <td class="py-2 px-4">Lowest</td>
            <td class="py-2 px-4">${{ watcher.lowest_price }}</td>
            <td class="py-2 px-4">{{ formatDate(watcher.lowest_at) }}</td>
        </tr>
        <tr class="bg-white border-b dark:bg-gray-800 dark:text-gray-100 dark:border-b-gray-700">
            <td class="py-2 px-4">Original</td>
            <td class="py-2 px-4">${{ watcher.initial_value }}</td>
            <td class="py-2 px-4 whitespace-nowrap">{{ formatDate(watcher.created_at) }}</td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import {formatDate} from "@js/utils/date-utils.js";

const props = defineProps({
    watcher: {
        type: Object,
        require: true
    }
})
</script>
