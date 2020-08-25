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
                <div v-for="(column, index) in columnsVisible"
                     :key="index"
                     class="control">
                    <b-checkbox v-model="column.display">
                        {{ column.title }}
                    </b-checkbox>
                </div>
            </b-field>
            <b-table
                detailed
                :data="tableData"
                class="watcher-table"
                default-sort="id"
                :row-class="(row) => `is-${row.status}`"
            >
                <template slot-scope="props">
                    <b-table-column
                        field="id"
                        :visible="columnsVisible['id'].display"
                        :label="columnsVisible['id'].title"
                        width="40"
                        sortable
                        numeric
                    >
                        {{ props.row.id }}
                    </b-table-column>

                    <b-table-column field="name" label="Name" sortable>
                        <div class="name-field">
                            <a :href="props.row.url">{{ props.row.name }}</a>
                            <span>{{ props.row.url_domain }}</span>
                        </div>
                    </b-table-column>

                    <b-table-column
                        field="interval"
                        :visible="columnsVisible['interval'].display"
                        :label="columnsVisible['interval'].title"
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
                        centered
                    >
                        {{ props.row.initial_value ? props.row.initial_value : '-' }}
                    </b-table-column>

                    <b-table-column field="value" label="Current" width="120" centered>
                        <div class="value-field">
                            {{ props.row.value ? props.row.value : '-' }}
                            <span>{{ props.row.last_sync }}</span>
                        </div>
                    </b-table-column>

                    <b-table-column
                        field="lowest_price"
                        :visible="columnsVisible['lowest_price'].display"
                        :label="columnsVisible['lowest_price'].title"
                        width="120"
                        centered
                    >
                        <div class="value-field">
                            {{ props.row.lowest_price ? props.row.lowest_price : '-' }}
                            <span>{{ props.row.lowest_at }}</span>
                        </div>
                    </b-table-column>

                    <b-table-column
                        field="alert_value"
                        :visible="columnsVisible['alert_value'].display"
                        :label="columnsVisible['alert_value'].title"
                        centered
                    >
                        {{ props.row.alert_value ? props.row.alert_value : '-' }}
                    </b-table-column>

                    <b-table-column field="tools" centered>
                        <div class="tool-buttons">
                            <refresh-button :watcher-id="props.row.id" @update="updateWatcherList"></refresh-button>
                            <a :href="`/watcher/${props.row.id}`">
                                <b-button type="is-default" icon-right="information-outline"/>
                            </a>
                            <a :href="`/watcher/${props.row.id}/edit`">
                                <b-button type="is-default" icon-right="pencil"/>
                            </a>
                            <b-button type="is-danger" icon-right="delete" @click="confirmDeleteWatcher(props.row)"/>
                        </div>
                    </b-table-column>
                </template>

                <template slot="detail" slot-scope="props">
                    <watcher-logs :watcher-id="props.row.id"></watcher-logs>
                </template>
            </b-table>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import IntervalSelect from "../watcher/IntervalSelect";
import RefreshButton from "../watcher/RefreshButton";
import Pusher from 'pusher-js';

export default {
    name: "Home",
    components: {IntervalSelect, RefreshButton},
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

        const pusher = new Pusher('a4e15e941c6e4777254a', {
            cluster: 'us2'
        });
        const channel = pusher.subscribe(`user.${this.userId}.watchers`);
        channel.bind('update', (data) => {
            this.updateWatcherList(data.watcher);
        });
        channel.bind('delete', (data) => {
            this.removeWatcherFromList(data.id);
        });
    },
    data() {
        return {
            watchersList: [],
            loading: {
                watchers: {},
            },
            columnsVisible: {
                id: { title: 'ID', display: false },
                interval: { title: 'Interval', display: true },
                initial_value: { title: 'Original', display: true },
                lowest_price: { title: 'Lowest', display: true },
                alert_value: { title: 'Alert', display: false },
            },
        };
    },
    computed: {
        tableData() {
            return this.watchersList.map((watcher) => {
                return {
                    ...watcher,
                    last_sync: watcher.last_sync ? moment.utc(watcher.last_sync).fromNow() : 'Never',
                    lowest_at: watcher.lowest_at ? moment.utc(watcher.lowest_at).fromNow() : '',
                }
            })
        }
    },
    methods: {
        confirmDeleteWatcher(watcher) {
            this.$buefy.dialog.confirm({
                title: 'Deleting watcher',
                message: `Are you sure you want to <b>delete</b> this watcher?<br><strong>${watcher.name}</strong><br>This action cannot be undone.`,
                confirmText: 'Delete Watcher',
                type: 'is-danger',
                hasIcon: true,
                onConfirm: () => this.deleteWatcher(watcher.id)
            })
        },
        deleteWatcher(id) {
            axios.delete(`/watcher/${id}`)
                .then(({data}) => {
                    this.watchersList = this.watchersList.filter((watcher) => (watcher.id !== id));
                    this.$buefy.toast.open({
                        message: data.message,
                        type: 'is-success'
                    })
                })
                .catch((err) => {
                    this.$buefy.toast.open({
                        duration: 5000,
                        message: 'Error deleting watcher.',
                        type: 'is-danger'
                    });
                    console.log(err);
                });
        },
        updateWatcherList(updatedWatcher) {
            this.watchersList = [
                ...this.watchersList.filter((watcher) => (watcher.id !== updatedWatcher.id)),
                updatedWatcher
            ];
        },
        removeWatcherFromList(id) {
            this.watchersList = this.watchersList.filter((watcher) => (watcher.id !== id));
        },
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
