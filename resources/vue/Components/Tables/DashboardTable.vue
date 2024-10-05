<template>
    <div class="flex flex-col container bg-white border rounded-lg w-full mt-4 px-4">
        <h2 class="text-2xl py-4">{{ title }}</h2>
        <div class="relative overflow-x-auto pb-4">
            <table class="text-sm text-gray-600 w-full">
                <thead class="text-xs text-left uppercase bg-gray-700 text-gray-300">
                <tr>
                    <th class="py-2 px-4 w-[170px]">Created</th>
                    <th class="py-2 px-4">Watcher</th>
                    <th class="py-2 px-4" :class="{'text-right': column !== 'Error'}">{{ column }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in tableData" class="bg-white border-b">
                    <td class="py-2 px-4 whitespace-nowrap">{{ row.created_at_formatted }}</td>
                    <td class="py-2 px-4">
                        <div class="text-blue-600 space-x-1">
                            <a :href="`/watcher/${row.watcher.id}`">{{ row.watcher.name }}</a>
                            <a :href="row.watcher.url">
                                <FontAwesomeIcon :icon="faLink"/>
                            </a>
                        </div>
                        <span class="text-xs text-gray-500">{{ row.watcher.url_domain }}</span>
                    </td>
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
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import {faLink} from '@fortawesome/free-solid-svg-icons';

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
