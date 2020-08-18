<template>
    <b-table
        :columns="columns"
        :data="logs"
        :row-class="(row) => row.error && 'is-danger'"
        default-sort="created_at"
        default-sort-direction="desc"
    ></b-table>
</template>

<script>
export default {
    name: "watcher-logs",
    props: {
        logs: {
            type: Array,
            default: () => ([])
        }
    },
    data() {
        return {
            columns: [
                {
                    field: 'created_at',
                    label: 'Created',
                    sortable: true
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
                    field: 'duration',
                    label: 'Duration (ms)',
                },
                {
                    field: 'error',
                    label: 'Error',
                },
            ]
        }
    }
}
</script>

<style scoped>

</style>

<style>
    tr.is-danger {
        background: #ffc4c4;
        color: #ff0000;
    }
</style>
