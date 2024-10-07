<template>
    <table class="text-sm text-gray-600 w-full">
        <thead class="text-xs text-left uppercase bg-gray-700 text-gray-300 dark:text-gray-200">
        <tr>
            <th class="py-2 px-4">Domain</th>
            <th class="py-2 px-4">Price Query Type</th>
            <th class="py-2 px-4">Price Query</th>
            <th class="py-2 px-4">Stock Query Type</th>
            <th class="py-2 px-4">Stock Query</th>
            <th class="py-2 px-4">Condition</th>
            <th class="py-2 px-4">Stock Text</th>
            <th class="py-2 px-4">Client</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in templates" class="bg-white border-b dark:bg-gray-800 dark:text-gray-100 dark:border-b-gray-700" :key="row.id">
            <td class="py-2 px-4">{{ row.domain }}</td>
            <td class="py-2 px-4">{{ row.price_query_type }}</td>
            <td class="py-2 px-4">{{ row.price_query }}</td>
            <td class="py-2 px-4">{{ row.stock_query_type }}</td>
            <td class="py-2 px-4">{{ row.stock_query }}</td>
            <td class="py-2 px-4">{{ row.stock_condition }}</td>
            <td class="py-2 px-4">{{ row.stock_text }}</td>
            <td class="py-2 px-4">{{ row.client }}</td>
            <td class="py-2 px-4">
                <div class="flex gap-1">
                    <a :href="`/template/${row.id}/edit`">
                        <button class="w-10 h-10 border rounded text-center hover:bg-gray-100 dark:bg-gray-900 dark:hover:bg-gray-800">
                            <FontAwesomeIcon :icon="faPenToSquare"/>
                        </button>
                    </a>
                    <DeleteButton
                        model-name="Domain Query"
                        path-name="template"
                        :model="row"
                        :dialog-info="row.domain"
                        @delete="reloadPage">
                    </DeleteButton>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import DeleteButton from "@Components/Shared/DeleteButton.vue";
import {faPenToSquare} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    templates: {
        type: Array,
        default: () => ([])
    },
});

const reloadPage = () => {
    location.reload();
};
</script>
