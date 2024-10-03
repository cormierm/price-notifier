<template>
    <div>
        <h3 class="text-lg">{{ title }}</h3>

        <table class="text-sm text-gray-600 w-full">
            <thead class="text-xs uppercase bg-gray-700 text-gray-300">
            <tr>
                <th class="py-2 px-4 text-left">Date</th>
                <th class="py-2 px-4 text-center">Status</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="row in tableData" class="bg-white border-b">
                <td class="py-2 px-4 text-left">{{ row.created_at_formatted }}</td>
                <td class="py-2 px-4 text-center">{{ row.stock }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import moment from "moment";

export default {
    props: {
        title: {
            type: String,
            default: 'Stock Changes',
        },
        stockChanges: {
            type: Array,
            default: () => ([])
        },
    },
    computed: {
        tableData() {
            return this.stockChanges.map((change) => {
                return {
                    ...change,
                    stock: change.stock ? 'Yes' : 'No',
                    created_at_formatted: change.created_at ? moment.utc(change.created_at).local().format('lll') : '',
                }
            });
        }
    },
}
</script>
