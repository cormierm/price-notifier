<template>
    <table class="text-sm text-gray-600 w-full">
        <thead class="text-xs text-left uppercase bg-gray-700 text-gray-300">
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
        <tr v-for="row in data" class="bg-white border-b">
            <td class="py-2 px-4">{{ row.domain }}</td>
            <td class="py-2 px-4">{{ row.price_query_type }}</td>
            <td class="py-2 px-4">{{ row.price_query }}</td>
            <td class="py-2 px-4">{{ row.stock_query_type }}</td>
            <td class="py-2 px-4">{{ row.stock_query }}</td>
            <td class="py-2 px-4">{{ row.stock_condition }}</td>
            <td class="py-2 px-4">{{ row.stock_text }}</td>
            <td class="py-2 px-4">{{ row.client }}</td>
            <td>
                <div class="flex">
                    <a :href="`/template/${row.id}/edit`">
                        <button>&#9998;</button>
                    </a>
                    <delete-button :template="row" @delete="reloadPage"></delete-button>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
import DeleteButton from "@components/template/DeleteButton.vue";

export default {
    components: {DeleteButton},
    props: {
        data: {
            type: Array,
            default: () => ([])
        },
    },
    methods: {
        reloadPage() {
            location.reload();
        }
    }
}
</script>
