<template>
    <div>
        <div class="container">
            <div class="row justify-content-end">
                    <a href="/watcher/create">Add Price Watcher</a>
            </div>
            <div class="row justify-content-center">
                <b-table :data="tableData" class="watcher-table" default-sort="id">
                    <template slot-scope="props">
                        <b-table-column field="id" label="ID" width="40" sortable numeric>
                            {{ props.row.id }}
                        </b-table-column>

                        <b-table-column field="name" label="Name" sortable>
                            <div class="name-field">
                                <a :href="props.row.url">{{ props.row.name }}</a>
                                <span>{{ props.row.url_domain }}</span>
                            </div>
                        </b-table-column>

                        <b-table-column field="interval" label="Interval" centered>
                            <b-select
                                placeholder="None"
                                :value="props.row.interval_id"
                                @input="updateInterval(props.row.id, $event)"
                                :loading="loadingInterval[props.row.id]"
                            >
                                <option
                                    v-for="option in intervals"
                                    :value="option.id"
                                    :key="option.id">
                                    {{ option.name }}
                                </option>
                            </b-select>
                        </b-table-column>

                        <b-table-column field="value" label="Current"  width="120" centered>
                            <div class="value-field">
                                {{ props.row.value ? props.row.value : '-' }}
                                <span>{{ props.row.last_sync }}</span>
                            </div>
                        </b-table-column>

                        <b-table-column field="alert_value" label="Alert" centered>
                            {{ props.row.alert_value ? props.row.alert_value : '-' }}
                        </b-table-column>

                        <b-table-column field="tools" centered>
                            <div class="tool-buttons">
                                <b-button type="is-default" icon-right="information-outline" :loading="loading.watchers[props.row.id]" @click="refresh(props.row.id)"/>
                                <b-button type="is-default" icon-right="refresh" :loading="loading.watchers[props.row.id]" @click="refresh(props.row.id)"/>
                                <a :href="`/watcher/${props.row.id}/edit`"><b-button type="is-default" icon-right="pencil"/></a>
                                <b-button type="is-danger" icon-right="delete" @click="deleteWatcher(props.row.id)" />
                            </div>
                        </b-table-column>
                    </template>
                </b-table>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    name: "Home",
    props: {
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
    },
    data() {
        return {
            watchersList: [],
            loading: {
                watchers: {

                },
            },
            loadingInterval: {}
        };
    },
    computed: {
        tableData() {
            return this.watchersList.map((watcher) => {
                return {
                    ...watcher,
                    last_sync: watcher.last_sync ? moment.utc(watcher.last_sync).fromNow() : 'Never',
                }
            })
        }
    },
    methods: {
        updateInterval(watcherId, intervalId) {
            this.loadingInterval = {
                ...this.loadingInterval,
                [watcherId]: true
            }
            axios.put(`/watcher/${watcherId}`, {
                interval_id: intervalId,
            }).catch((err) => {
                console.error(err);
            }).finally(() => {
                this.loadingInterval = {
                    ...this.loadingInterval,
                    [watcherId]: false
                }
            });
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
        loadingWatcher(id, state) {
            this.loading = {
                ...this.loading,
                watchers: {
                    ...this.loading.watchers,
                    [id]: state,
                },
            };
        },
        refresh(id) {
            this.loadingWatcher(id, true);
            axios.get(`/watcher/${id}/sync`)
                .then(({data}) => {
                    this.watchersList = [
                        ...this.watchersList.filter((watcher) => (watcher.id !== id)),
                        data.watcher
                    ];
                })
                .catch((err) => {
                    console.log(err);
                })
                .finally(() => {
                    this.loadingWatcher(id, false);
                });
        }
    }
}
</script>

<style lang="scss" scoped>
    .watcher-table {
        width: 80%;
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
