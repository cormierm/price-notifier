<template>
    <div class="container">
        <recent-price-table class="price-table" :price-changes="priceChanges"/>
        <recent-stock-table :stock-changes="stockChanges"/>
    </div>
</template>

<script>
import RecentPriceTable from "../tables/RecentPriceTable";
import RecentStockTable from "../tables/RecentStockTable";

export default {
    name: "DashboardIndex",
    components: { RecentPriceTable, RecentStockTable },
    props: {
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
    .price-table {
        padding-bottom: 10px;
    }
</style>
