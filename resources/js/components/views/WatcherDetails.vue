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

        <watcher-logs :watcher-id="watcher.id"></watcher-logs>
    </div>
</template>

<script>
import IntervalSelect from "../watcher/IntervalSelect";
import DeleteButton from "../watcher/DeleteButton";
import RefreshButton from "../watcher/RefreshButton";

export default {
    name: "WatcherDetails",
    components: { DeleteButton, IntervalSelect, RefreshButton },
    props: {
        watcher: {
            type: Object,
            required: true
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