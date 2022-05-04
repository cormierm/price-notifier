<template>
    <div class="stock-change-table">
        <h3>{{ title }}</h3>
        <b-table
            :columns="columns"
            :data="tableData"
            default-sort="created_at"
            default-sort-direction="desc"
        ></b-table>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: "StockChangeTable",
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
    data() {
        return {
            columns: [
                {
                    field: 'stock',
                    label: 'Status',
                },
                {
                    field: 'created_at',
                    visible: false,
                },
                {
                    label: 'Date',
                    field: 'created_at_formatted',
                },
            ],
        }
    }
}
</script>

<style scoped>
    h2 {
        font-weight: bold;
    }
</style>
