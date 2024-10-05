<template>
    <div>
        <div class="flex justify-between mt-4 items-center">
            <h3 class="text-lg">Logs</h3>
            <div class="flex items-center gap-1">
                <label>
                    Display:
                    <select
                        class="rounded dark:bg-gray-900"
                        v-model="limit"
                    >
                        <option v-for="option in limits" :value="option" :key="option">
                            {{ option }}
                        </option>
                    </select>
                </label>
            </div>
        </div>

        <watcher-logs-table class="mt-4" :data="logs"/>
    </div>
</template>

<script setup>
import WatcherLogsTable from "../Tables/WatcherLogsTable.vue";
import {onMounted, ref, watch} from "vue";

const props = defineProps({
    watcherId: {
        type: Number,
        required: true
    },
});

onMounted(() => {
    getLogs();
});

const limit = ref(5);
const limits = ref([5, 10, 25, 100, 250, 1000]);
const loading = ref(false);
const logs = ref([]);

watch(limit, () => {
    getLogs();
})

const getLogs = () => {
    loading.value = true;

    axios.get(`/watcher/${props.watcherId}/logs?limit=${limit.value}`)
        .then(({data}) => {
            logs.value = data;
        })
        .catch((err) => {
            console.log(err);
        })
        .finally(() => {
            loading.value = false;
        });
};
</script>
