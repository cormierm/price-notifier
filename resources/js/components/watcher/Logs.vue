<template>
    <div>
        <div class="title-header">
            <p>Logs</p>
            <div class="limit-select">
                Display:
                <b-select
                    :value="5"
                    @input="getLogs($event)"
                    :loading="loading"
                >
                    <option v-for="option in limits" :value="option" :key="option">
                        {{ option }}
                    </option>
                </b-select>
            </div>

        </div>
        <b-table
            :columns="columns"
            :data="logs"
            :loading="loading"
            :row-class="(row) => row.error && 'is-danger'"
            default-sort="created_at"
            default-sort-direction="desc"
        ></b-table>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: "watcher-logs",
    props: {
        watcherId: {
            type: Number,
            required: true
        },
    },
    mounted() {
        this.getLogs();
    },
    data() {
        return {
            columns: [
                {
                    field: 'created_at',
                    sortable: true,
                    visible: false
                },
                {
                    field: 'created_at_formatted',
                    label: 'Created',
                },
                {
                    field: 'formatted_value',
                    label: 'Formatted Value',
                },
                {
                    field: 'raw_value',
                    label: 'Raw Value',
                },
                {
                    field: 'has_stock',
                    label: 'Stock',
                },
                {
                    field: 'duration',
                    label: 'Duration (ms)',
                },
                {
                    field: 'region',
                    label: 'Region',
                },
                {
                    field: 'error',
                    label: 'Error',
                },
            ],
            limits: [5,10,25,100],
            loading: false,
            logs: [],
        }
    },
    methods: {
        getLogs(limit = 5) {
            this.loading = true;
            axios.get(`/watcher/${this.watcherId}/logs?limit=${limit}`)
                .then(({data}) => {
                    this.logs = data.map((log) => {
                        return {
                            ...log,
                            created_at_formatted: log.created_at ? moment(log.created_at).format('YYYY-MM-DD HH:mm:ss') : '',
                            has_stock: log.has_stock === 1 ? 'Yes' : log.has_stock === 0 ? 'No' : '-',
                        }
                    })
                })
                .catch((err) => {
                    console.log(err);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    }
}
</script>

<style lang="scss" scoped>
    .title-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        p {
            font-weight: bold;
        }
    }

    .limit-select {
        display: flex;
        align-items: center;
    }
</style>

<style>
    tr.is-danger {
        background: #ffc4c4;
        color: #ff0000;
    }
</style>
