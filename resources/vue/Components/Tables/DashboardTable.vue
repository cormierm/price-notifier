<template>
    <div class="flex flex-col container bg-white border rounded-lg w-full mt-4 px-4 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
        <h2 class="text-2xl py-4">{{ title }}</h2>
        <div class="relative overflow-x-auto pb-4">
            <table class="text-gray-600 w-full">
                <thead class="text-xs text-left uppercase bg-gray-700 text-gray-300">
                <tr>
                    <th class="py-2 px-4 w-[170px]">Created</th>
                    <th class="py-2 px-4">Watcher</th>
                    <th class="py-2 px-4" :class="{'text-right': column !== 'Error'}">{{ column }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in tableData" class="bg-white border-b dark:bg-gray-800 dark:text-gray-100 dark:border-b-gray-700">
                    <td class="py-2 px-4 whitespace-nowrap">{{ row.created_at_formatted }}</td>
                    <DetailsColumn :watcher="row.watcher"/>
                    <td class="py-2 px-4"
                        :class="{'text-red-500': column === 'Error', 'text-right': column !== 'Error'}">
                        {{ row[column.toLowerCase()] }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import {computed} from "vue";
import {formatDate} from "@js/utils/date-utils.js";
import DetailsColumn from "@Components/Watcher/DetailsColumn.vue";

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    data: {
        type: Array,
        default: () => ([])
    },
    column: {
        type: String,
        required: true
    }
});

const tableData = computed(() => {
    return props.data?.map((change) => {
        return {
            ...change,
            created_at_formatted: change.created_at
                ? formatDate(change.created_at)
                : '',
        };
    });
});
</script>
