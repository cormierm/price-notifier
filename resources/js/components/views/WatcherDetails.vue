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

        <article class="panel">
            <div class="panel-heading">
                <strong>{{ watcher.name }}</strong>
                <div v-if="watcher.value" class="is-pulled-right"><strong>${{watcher.value}}</strong></div>
                <br>
                <a class="header-link" :href="watcher.url">{{ watcher.url }}</a>
            </div>
            <div class="panel-block">
                <div class="container">
                    Original Price: {{ watcher.initial_value }} ({{ formatDate(watcher.created_at) }})<br>
                    Current Price: {{ watcher.value }} ({{ formatDate(watcher.last_sync) }})<br>
                    Lowest Price: {{ watcher.lowest_price }} ({{ formatDate(watcher.lowest_at) }})<br>
                    Has Stock: {{ watcher.has_stock === true ? 'Yes' : watcher.has_stock === false ? 'No' : 'Unknown' }}<br><
                    Alert Price: {{ watcher.alert_value }}<br>

                    Price Query: {{ watcher.price_query }}<br>
                    Price Query Type: {{ watcher.price_query_type }}<br><br>

                    Stock Query: {{ watcher.stock_query }}<br>
                    Stock Query Type: {{ watcher.stock_query_type }}<br><br>
                    Stock condition: {{ watcher.stock_condition }}<br>
                    Stock text match: {{ watcher.stock_text }}<br><br>

                    Region: {{ watcher.region ? watcher.region.label : '' }}<br><br>
                    Interval:
                    <interval-select
                        :intervals="intervals"
                        :watcher-id="watcher.id"
                        :value="watcher.interval_id"
                        @update="updateWatcher"
                    />
                </div>
            </div>
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
            return moment.utc(datetime).local().format('lll');
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

    .header-link {
        font-size: .7em;
    }

    .panel-heading {
        font-size: 1.2em;
    }

</style>
