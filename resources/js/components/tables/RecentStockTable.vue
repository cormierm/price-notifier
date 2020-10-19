<template>
    <div class="recent-stock-table">
        <h2>{{ title }}</h2>
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
    name: "RecentStockTable",
    props: {
        title: {
            type: String,
            default: 'Most Recent Stock Changes',
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
                    created_at_formatted: change.created_at ? moment(change.created_at).format('YYYY-MM-DD HH:mm:ss') : '',
                }
            });
        }
    },
    data() {
        return {
            columns: [
                {
                    field: 'created_at',
                    visible: false,
                },
                {
                    label: 'Created_at',
                    field: 'created_at_formatted',
                },
                {
                    field: 'watcher.name',
                    label: 'Watcher',
                },
                {
                    field: 'stock',
                    label: 'Stock',
                }
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
