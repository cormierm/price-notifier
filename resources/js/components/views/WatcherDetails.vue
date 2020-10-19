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
                <strong>{{ watcher.name }}<br></strong>
                {{ watcher.url }}
            </p>
            <p class="panel-block">
                Created: {{ watcher.created_at }}<br>
                Initial Price: {{ watcher.initial_value }}<br>
                Current Price: {{ watcher.value }} ({{ watcher.last_sync }})<br>
                Lowest Price: {{ watcher.lowest_price }} ({{ watcher.lowest_at }})<br>
                Alert Price: {{ watcher.alert_value }}<br>
                XPath Query Price: {{ watcher.query }}
            </p>

            <p class="panel-block">
                Interval:
                <interval-select
                    :intervals="intervals"
                    :watcher-id="watcher.id"
                    :value="watcher.interval_id"
                    @update="updateWatcher"
                /><br>
            </p>
        </article>

        <h2>Price Changes</h2>
        <b-table
            :columns="columns"
            :data="transformedPriceChanges"
            default-sort="created_at"
            default-sort-direction="desc"
        ></b-table>

        <watcher-logs :watcher-id="watcher.id"></watcher-logs>
    </div>
</template>

<script>
import IntervalSelect from "../watcher/IntervalSelect";
import DeleteButton from "../watcher/DeleteButton";
import RefreshButton from "../watcher/RefreshButton";
import moment from "moment";

export default {
    name: "WatcherDetails",
    components: { DeleteButton, IntervalSelect, RefreshButton },
    props: {
        watcher: {
            type: Object,
            required: true
        },
        priceChanges: {
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
            currentWatcher: this.watcher,
        }
    },
    computed: {
        transformedPriceChanges() {
            return this.priceChanges.map((change) => {
                return {
                    ...change,
                    created_at_formatted: change.created_at ? moment(change.created_at).format('YYYY-MM-DD HH:mm:ss') : '',
                }
            });
        }
    },
    methods: {
        updateWatcher(watcher) {
            this.currentWatcher = watcher;
        },
        redirectToWatchers() {
            window.location = '/home';
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
