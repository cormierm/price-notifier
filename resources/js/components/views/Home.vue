<template>
    <div>
        <div class="container">
            <div class="row justify-content-end">
                    <a href="/watcher/create">Add Price Watcher</a>
            </div>
            <div class="row justify-content-center">
                <b-table :data="tableData" class="watcher-table">
                    <template slot-scope="props">
                        <b-table-column field="name" label="Name">
                            <a :href="props.row.url">{{ props.row.name }}</a>
                        </b-table-column>

                        <b-table-column field="last_updated" label="Last Synced">
                            {{ props.row.last_sync }}
                        </b-table-column>

                        <b-table-column field="initial_value" label="Initial Value" centered>
                            {{ props.row.initial_value ? props.row.initial_value : '-' }}
                        </b-table-column>

                        <b-table-column field="value" label="Value" centered>
                            {{ props.row.value ? props.row.value : '-' }}
                        </b-table-column>

                        <b-table-column field="tools" label="Tools" centered>
                            <b-button type="is-default" icon-right="refresh" :loading="loading.watchers[props.row.id]" @click="refresh(props.row.id)"/>
                            <a :href="`/watcher/${props.row.id}/edit`"><b-button type="is-default" icon-right="pencil"/></a>
                            <b-button type="is-danger" icon-right="delete" @click="deleteWatcher(props.row.id)" />
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
            columns: [
                {
                    field: 'name',
                    label: 'Name',
                },
                {
                    field: 'last_sync',
                    label: 'Last Synced',
                },
                {
                    field: 'value',
                    label: 'Value',
                }
            ]
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

<style scoped>
    .watcher-table {
        width: 80%;
    }
</style>
