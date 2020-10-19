<template>
    <div class="container">
        <recent-price-table class="recent-table" :price-changes="priceChanges"/>
        <recent-stock-table class="recent-table" :stock-changes="stockChanges"/>
        <dashboard-errors-table :errors="errors"/>
    </div>
</template>

<script>
import RecentPriceTable from "../tables/RecentPriceTable";
import RecentStockTable from "../tables/RecentStockTable";
import DashboardErrorsTable from "../tables/DashboardErrorsTable";

export default {
    name: "DashboardIndex",
    components: { DashboardErrorsTable, RecentPriceTable, RecentStockTable },
    props: {
        errors: {
            type: Array,
            default: () => ([])
        },
        priceChanges: {
            type: Array,
            default: () => ([])
        },
        stockChanges: {
            type: Array,
            default: () => ([])
        }
    }
}
</script>

<style scoped>
    .recent-table {
        padding-bottom: 10px;
    }
</style>
