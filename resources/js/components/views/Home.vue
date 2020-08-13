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
                            {{ props.row.name }}
                        </b-table-column>

                        <b-table-column field="last_updated" label="Last Updated">
                            {{ props.row.last_updated }}
                        </b-table-column>

                        <b-table-column field="value" label="Value">
                            {{ props.row.value }}
                        </b-table-column>

                        <b-table-column field="tools" label="Tools" centered>
                            <b-button type="is-default" icon-right="refresh" @click="refresh(props.row.id)"/>
                            <b-button type="is-default" icon-right="pencil" />
                            <b-button type="is-danger" icon-right="delete" />
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
            columns: [
                {
                    field: 'name',
                    label: 'Name',
                },
                {
                    field: 'updated_at',
                    label: 'Updated at',
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
                    last_updated: moment(watcher.updated_at).fromNow(),
                }
            })
        }
    },
    methods: {
        refresh(id) {
            axios.get(`/watcher/${id}/sync`)
                .then((response) => {
                    console.log(response.data);
                })
                .catch((err) => {
                    console.log(err);
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
