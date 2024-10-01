<template>
    <div class="flex flex-col items-center">
        <dashboard-table title="Most Recent Price Changes" column="Price" :data="priceChanges"/>
        <dashboard-table title="Most Stock Price Changes" column="Stock" :data="stockChanges"/>
        <dashboard-table title="Most Recent Watcher Errors" column="Error" :data="errors"/>
    </div>
</template>

<script>
import DashboardTable from "../tables/DashboardTable.vue";

export default {
    name: "DashboardIndex",
    components: { DashboardTable },
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
