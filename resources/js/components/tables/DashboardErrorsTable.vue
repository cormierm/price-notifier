<template>
    <div class="recent-price-table">
        <h2>{{ title }}</h2>

        <b-table
            :columns="columns"
            :data="tableData"
            default-sort="created_at"
            default-sort-direction="desc"
        ></b-table>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: "DashboardErrorsTable",
    props: {
        title: {
            type: String,
            default: 'Most Recent Watcher Errors',
        },
        errors: {
            type: Array,
            default: () => ([])
        },
    },
    computed: {
        tableData() {
            return this.errors.map((error) => {
                return {
                    ...error,
                    created_at_formatted: error.created_at
                        ? moment(error.created_at).format('YYYY-MM-DD HH:mm:ss')
                        : '',
                }
            });
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
                    field: 'watcher.name',
                    label: 'Watcher',
                },
                {
                    field: 'error',
                    label: 'Error',
                }
            ],
        }
    }
}
</script>

<style scoped>
h2 {
    font-weight: bold;
}
</style>
