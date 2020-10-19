<template>
    <div class="price-change-table">
        <h2>Price Changes</h2>
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
    name: "PriceChangeTable",
    props: {
        priceChanges: {
            type: Array,
            default: () => ([])
        },
    },
    computed: {
        tableData() {
            return this.priceChanges.map((change) => {
                return {
                    ...change,
                    created_at_formatted: change.created_at
                        ? moment(change.created_at).format('YYYY-MM-DD HH:mm:ss')
                        : '',
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
                    field: 'price',
                    label: 'Price',
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
