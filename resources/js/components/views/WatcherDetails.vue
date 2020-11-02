<template>
    <div class="container">
        <div class="title-header">
            <h1 class="title">Watcher Details</h1>
            <div class="tool-buttons">
                <refresh-button :watcher-id="watcher.id" @update="updateWatcher"></refresh-button>
                <a :href="`/watcher/${watcher.id}/edit`">
                    <b-button type="is-default" icon-right="pencil"/>
                </a>
                <delete-button :watcher="watcher" @delete="redirectToWatchers"></delete-button>
            </div>
        </div>

        <article class="panel is-primary">
            <p class="panel-heading">
                <strong>{{ watcher.name }}</strong><span v-if="watcher.value" class="is-pulled-right"><strong>${{watcher.value}}</strong></span><br>
                <a :href="watcher.url">{{ watcher.url }}</a>
            </p>
            <p class="panel">
                Original Price: {{ watcher.initial_value }} ({{ formatDate(watcher.created_at) }})<br>
                Current Price: {{ watcher.value }} ({{ formatDate(watcher.last_sync) }})<br>
                Lowest Price: {{ watcher.lowest_price }} ({{ formatDate(watcher.lowest_at) }})<br>
                Alert Price: {{ watcher.alert_value }}<br><br>
                XPath Query Price: {{ watcher.query }}<br><br>
                XPath Query Stock: {{ watcher.xpath_stock }}<br>
                Stock contains: {{ watcher.stock_contains }}<br>
                Stock text match: {{ watcher.stock_text }}<br><br>
                Region: {{ watcher.region ? watcher.region.label : '' }}<br><br>
                Interval:
                <interval-select
                    :intervals="intervals"
                    :watcher-id="watcher.id"
                    :value="watcher.interval_id"
                    @update="updateWatcher"
                />
            </p>
        </article>

        <price-change-table :price-changes="priceChanges" />

        <stock-change-table :stock-changes="stockChanges" />

        <watcher-logs :watcher-id="watcher.id"></watcher-logs>
    </div>
</template>

<script>
import moment from "moment";
import IntervalSelect from "../watcher/IntervalSelect";
import DeleteButton from "../watcher/DeleteButton";
import RefreshButton from "../watcher/RefreshButton";
import PriceChangeTable from "../tables/PriceChangeTable";
import StockChangeTable from "../tables/StockChangeTable";

export default {
    name: "WatcherDetails",
    components: { DeleteButton, IntervalSelect, RefreshButton, PriceChangeTable, StockChangeTable },
    props: {
        watcher: {
            type: Object,
            required: true
        },
        priceChanges: {
            type: Array,
            default: () => ([])
        },
        stockChanges: {
            type: Array,
            default: () => ([])
        },
        intervals: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            currentWatcher: this.watcher,
        }
    },
    methods: {
        updateWatcher(watcher) {
            this.currentWatcher = watcher;
        },
        redirectToWatchers() {
            window.location = '/home';
        },
        formatDate(datetime) {
            return moment.utc(datetime).format('lll');
        }
    }
}
</script>

<style scoped>
    .title-header {
        display: flex;
        justify-content: space-between;
    }

    .tool-buttons {
        display: flex;
    }

</style>
