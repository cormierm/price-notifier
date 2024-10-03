<template>
    <div>
        <div class="flex justify-between mt-4 items-center">
            <h3 class="text-lg">Logs</h3>
            <div class="flex items-center gap-1">
                <label for="pagination-select">Display:</label>
                <select
                    id="pagination-select"
                    class="rounded"
                    :value="limit"
                    @input="getLogs($event.target.value)"
                    :loading="loading"
                >
                    <option v-for="option in limits" :value="option" :key="option">
                        {{ option }}
                    </option>
                </select>
            </div>
        </div>

        <watcher-logs-table class="mt-4" :data="logs"/>
    </div>
</template>

<script>
import WatcherLogsTable from "../Tables/WatcherLogsTable.vue";

export default {
    components: {WatcherLogsTable},
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
            limit: 5,
            limits: [5, 10, 25, 100, 250, 1000],
            loading: false,
            logs: [],
        }
    },
    methods: {
        getLogs(limit = 5) {
            this.limit = limit;
            this.loading = true;
            axios.get(`/watcher/${this.watcherId}/logs?limit=${limit}`)
                .then(({data}) => {
                    this.logs = data;
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
