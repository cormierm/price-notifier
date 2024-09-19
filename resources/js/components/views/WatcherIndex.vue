<template>
    <div>
        <div class="container">
            <div class="title-header">
                <h1 class="title">Watchers</h1>
                <a href="/watcher/create">
                    <b-button icon-left="plus">Add New Watcher</b-button>
                </a>
            </div>
            <b-field grouped group-multiline>
                <div class="control">
                    <b-checkbox v-model="hasMobileCards" @input="saveColumnSetting('mobile-cards', $event)">
                        Mobile Cards
                    </b-checkbox>
                </div>
                <div v-for="(column, index) in columnsVisible"
                     :key="index"
                     class="control">
                    <b-checkbox v-model="column.display" @input="saveColumnSettings">
                        {{ column.title }}
                    </b-checkbox>
                </div>
                <div class="control">
                    <b-checkbox v-model="showInactive" @input="saveColumnSetting('show-inactive', $event)">
                        Show Inactive
                    </b-checkbox>
                </div>
            </b-field>
            <b-table
                :data="tableData"
                class="watcher-table"
                default-sort="name"
                :row-class="(row) => `is-${row.status}`"
                :mobile-cards="hasMobileCards"
            >
                <b-table-column field="name" label="Name" sortable v-slot="props">

                    <div class="name-field">
                        <div>
                            <a :href="`/watcher/${props.row.id}`">{{ props.row.name }}</a>
                            <a :href="props.row.url" :target="`price-watcher-${props.row.id}`">
                                <b-icon icon="link"/>
                            </a>
                        </div>
                        <span>{{ props.row.url_domain }}</span>
                    </div>
                </b-table-column>

                <b-table-column
                    field="interval"
                    :visible="columnsVisible['interval'].display"
                    :label="columnsVisible['interval'].title"
                    v-slot="props"
                >
                    <interval-select
                        :intervals="intervals"
                        :watcher-id="props.row.id"
                        :value="props.row.interval_id"
                        @update="updateWatcherList"
                    />
                </b-table-column>

                <b-table-column
                    field="initial_value"
                    :visible="columnsVisible['initial_value'].display"
                    :label="columnsVisible['initial_value'].title"
                    width="120"
                    centered
                    v-slot="props"
                >
                    <div class="value-field">
                        {{ props.row.initial_value ? props.row.initial_value : '-' }}
                        <span>{{ props.row.created_at }}</span>
                    </div>
                </b-table-column>

                <b-table-column field="value" label="Current" width="120" centered v-slot="props">
                    <div class="value-field">
                        {{ props.row.value ? props.row.value : '-' }}
                        <span>{{ props.row.last_sync }}</span>
                    </div>
                </b-table-column>

                <b-table-column
                    field="change"
                    :visible="columnsVisible['change'].display"
                    :label="columnsVisible['change'].title"
                    width="120"
                    centered
                    v-slot="props"
                >
                    <change-column
                        :initial-value="props.row.initial_value"
                        :current-value="props.row.value"
                    />
                </b-table-column>

                <b-table-column
                    field="lowest_price"
                    :visible="columnsVisible['lowest_price'].display"
                    :label="columnsVisible['lowest_price'].title"
                    width="120"
                    centered
                    v-slot="props"
                >
                    <div class="value-field">
                        {{ props.row.lowest_price ? props.row.lowest_price : '-' }}
                        <span>{{ props.row.lowest_at }}</span>
                    </div>
                </b-table-column>

                <b-table-column
                    field="has_stock"
                    :visible="columnsVisible['has_stock'].display"
                    :label="columnsVisible['has_stock'].title"
                    centered
                    v-slot="props"
                >
                    {{ props.row.has_stock === true ? 'Yes' : props.row.has_stock === false ? 'No' : '-' }}
                </b-table-column>

                <b-table-column
                    field="alert_value"
                    :visible="columnsVisible['alert_value'].display"
                    :label="columnsVisible['alert_value'].title"
                    centered
                    v-slot="props"
                >
                    {{ props.row.alert_value ? props.row.alert_value : '-' }}
                </b-table-column>

                <b-table-column
                    field="client"
                    :visible="columnsVisible['client'].display"
                    :label="columnsVisible['client'].title"
                    centered
                    v-slot="props"
                >
                    {{ props.row.client }}
                </b-table-column>

                <b-table-column
                    field="region"
                    :visible="columnsVisible['region'].display"
                    :label="columnsVisible['region'].title"
                    centered
                    v-slot="props"
                >
                    {{ props.row.region ? props.row.region.label : '-' }}
                </b-table-column>

                <b-table-column field="tools" centered v-slot="props">
                    <div class="tool-buttons">
                        <refresh-button :watcher-id="props.row.id" @update="updateWatcherList"></refresh-button>
                        <a :href="`/watcher/${props.row.id}`">
                            <b-button type="is-default" icon-right="information-outline"/>
                        </a>
                        <a :href="`/watcher/${props.row.id}/edit`">
                            <b-button type="is-default" icon-right="pencil"/>
                        </a>
                        <delete-button :watcher="props.row" @delete="removeWatcherFromList"></delete-button>
                    </div>
                </b-table-column>
            </b-table>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import IntervalSelect from "../watcher/IntervalSelect.vue";
import DeleteButton from "../watcher/DeleteButton.vue";
import RefreshButton from "../watcher/RefreshButton.vue";
import Pusher from 'pusher-js';
import ChangeColumn from "./ChangeColumn.vue";

export default {
    name: "Home",
    components: {ChangeColumn, DeleteButton, IntervalSelect, RefreshButton},
    props: {
        userId: {
            type: Number,
            required: true
        },
        watchers: {
            type: Array,
            default: () => ([])
        },
        intervals: {
            type: Array,
            required: true
        }
    },
    mounted() {
        this.watchersList = this.watchers;

        const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER
        });
        const channel = pusher.subscribe(`user.${this.userId}.watchers`);
        channel.bind('update', (data) => {
            this.updateWatcherList(data.watcher);
        });

        this.restoreColumnSettings();
        this.hasMobileCards = this.restoreColumnSetting('mobile-cards');
        this.showInactive = this.restoreColumnSetting('show-inactive');
    },
    data() {
        return {
            hasMobileCards: true,
            showInactive: true,
            watchersList: [],
            loading: {
                watchers: {},
            },
            columnsVisible: {
                interval: {title: 'Interval', display: true},
                initial_value: {title: 'Original', display: true},
                change: {title: 'Change', display: true},
                lowest_price: {title: 'Lowest', display: true},
                has_stock: {title: 'Stock', display: true},
                alert_value: {title: 'Alert', display: false},
                client: {title: 'Client', display: false},
                region: {title: 'Region', display: false},
            },
        };
    },
    computed: {
        tableData() {
            return this.watchersList
                .filter((watcher) => this.showInactive || watcher.interval_id !== 1)
                .map((watcher) => ({
                    ...watcher,
                    created_at: moment.utc(watcher.created_at).fromNow(),
                    last_sync: watcher.last_sync ? moment.utc(watcher.last_sync).fromNow() : 'Never',
                    lowest_at: watcher.lowest_at ? moment.utc(watcher.lowest_at).fromNow() : '',
                }))
        },
    },
    methods: {
        updateWatcherList(updatedWatcher) {
            this.watchersList = [
                ...this.watchersList.filter((watcher) => (watcher.id !== updatedWatcher.id)),
                updatedWatcher
            ];
        },
        removeWatcherFromList(watcher) {
            this.watchersList = this.watchersList.filter((w) => (w.id !== watcher.id));
        },
        saveColumnSetting(column, setting) {
            localStorage.setItem(`column-setting-${column}`, setting);
        },
        saveColumnSettings() {
            localStorage.setItem('column-settings', JSON.stringify(this.columnsVisible))
        },
        restoreColumnSetting(column) {
            return localStorage.getItem(`column-setting-${column}`) === 'true';
        },
        restoreColumnSettings() {
            const columns = JSON.parse(localStorage.getItem('column-settings'));
            if (columns) {
                this.columnsVisible = {
                    ...this.columnsVisible,
                    ...columns,
                }
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.title-header {
    display: flex;
    justify-content: space-between;
}

.name-field, .value-field {
    display: flex;
    flex-direction: column;

    span {
        color: #666;
        font-size: 0.7em;
    }
}

.tool-buttons {
    display: flex;
}
</style>

<style>
tr.is-error {
    background: #ffd4d4;
}

tr.is-disabled {
    background: #f6f6f6;
    color: #4c4c4c;
}
</style>
