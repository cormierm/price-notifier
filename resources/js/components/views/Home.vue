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

                        <b-table-column field="value" label="Value">
                            {{ props.row.value }}
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
    data() {
        return {
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
            return this.watchers.map((watcher) => {
                return {
                    ...watcher,
                    last_sync: moment.utc(watcher.last_sync).fromNow(),
                }
            })
        }
    },
    methods: {
        deleteWatcher(id) {
            axios.delete(`/watcher/${id}`)
                .then(({data}) => {
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
        updateLoadingWatcher(id, state) {
            this.loading = {
                ...this.loading,
                watchers: {
                    ...this.loading.watchers,
                    [id]: state,
                },
            };
        },
        refresh(id) {
            this.updateLoadingWatcher(id, true);
            axios.get(`/watcher/${id}/sync`)
                .then(({data}) => {


                })
                .catch((err) => {
                    console.log(err);
                })
                .finally(() => {
                    this.updateLoadingWatcher(id, false);
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
