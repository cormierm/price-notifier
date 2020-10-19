<template>
    <div class="container">
        <h1>Dashboard</h1>
        <recent-price-table :price-changes="priceChanges"/>
    </div>
</template>

<script>
import RecentPriceTable from "../tables/RecentPriceTable";

export default {
    name: "DashboardIndex",
    components: { RecentPriceTable },
    props: {
        priceChanges: {
            type: Array,
            default: () => ([])
        }
    }
}
</script>

<style scoped>

</style>
