<template>
    <div class="recent-price-table">
        <h2>{{ title }}</h2>

        <b-table
            :data="tableData"
        >
            <template slot-scope="props">
                <b-table-column field="created_at" label="Date" width="115">
                    {{ props.row.created_at_formatted }}
                </b-table-column>
                <b-table-column field="watcher.name" label="Watcher">
                    <a :href="`/watcher/${props.row.watcher.id}`">{{ props.row.watcher.name }}</a>
                </b-table-column>
                <b-table-column field="error" label="Error">
                    {{ props.row.error }}
                </b-table-column>
            </template>
        </b-table>
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
}
</script>

<style lang="scss" scoped>
h2 {
    font-size: 1.2em;
    font-weight: bold;
}

.name-field {
    display: flex;
    flex-direction: column;

    span {
        color: #666;
        font-size: 0.7em;
    }
}
</style>
