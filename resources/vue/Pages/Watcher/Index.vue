<template>
    <div class="w-full p-4 bg-white dark:bg-black dark:text-gray-200">
        <h1 class="text-2xl">Watchers</h1>
        <div class="flex justify-between items-center mt-4">
            <div class="flex gap-4">
                <div v-for="(column, index) in columnsVisible" :key="index" class="flex items-center gap-1">
                    <input class="rounded" type="checkbox" v-model="column.display" @input="saveColumnSettings">
                    <label>{{ column.title }}</label>
                </div>
            </div>
            <AddWatcherButton/>
        </div>

        <div class="relative overflow-x-auto pb-4 pt-2">
            <table class="text-gray-600 w-full">
                <thead class="text-xs uppercase bg-gray-700 text-gray-300">
                <tr>
                    <th class="py-2 px-4 text-left">Name</th>
                    <th v-if="columnsVisible.interval.display" class="py-2 px-4 w-[100px]">Interval</th>
                    <th v-if="columnsVisible.original.display" class="py-2 px-4">Original</th>
                    <th class="py-2 px-4">Current</th>
                    <th v-if="columnsVisible.change.display" class="py-2 px-4">Change</th>
                    <th v-if="columnsVisible.lowestPrice.display" class="py-2 px-4">Lowest</th>
                    <th v-if="columnsVisible.hasStock.display" class="py-2 px-4">Stock</th>
                    <th v-if="columnsVisible.alert.display" class="py-2 px-4">Alert</th>
                    <th v-if="columnsVisible.client.display" class="py-2 px-4">Client</th>
                    <th v-if="columnsVisible.region.display" class="py-2 px-4">Region</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in tableData"
                    :key="row.id"
                    class="bg-white border-b dark:bg-gray-800 dark:text-gray-100 dark:border-b-gray-700"
                >
                    <DetailsColumn :watcher="row"/>
                    <td v-if="columnsVisible.interval.display" class="py-2 px-4">
                        <IntervalSelect
                            :intervals="intervals"
                            :watcher-id="row.id"
                            :value="row.interval_id"
                            @update="updateWatcherList"
                        />
                    </td>
                    <PriceColumn v-if="columnsVisible.original.display" :amount="row.initial_value" :date="row.created_at"/>
                    <PriceColumn :amount="row.value" :date="row.last_sync"/>
                    <td v-if="columnsVisible.change.display" class="py-2 px-4 text-center">
                        <ChangeColumn
                            :initial-value="row.initial_value"
                            :current-value="row.value"
                        />
                    </td>
                    <PriceColumn v-if="columnsVisible.lowestPrice.display" :amount="row.lowest_price" :date="row.lowest_at"/>
                    <td v-if="columnsVisible.hasStock.display" class="py-2 px-4 text-center">
                        {{ row.has_stock === true ? 'Yes' : row.has_stock === false ? 'No' : '-' }}
                    </td>
                    <td v-if="columnsVisible.alert.display" class="py-2 px-4 text-center">
                        {{ row.alert_value ? row.alert_value : '-' }}
                    </td>
                    <td v-if="columnsVisible.client.display" class="py-2 px-4 text-center">
                        {{ row.client }}
                    </td>
                    <td v-if="columnsVisible.region.display" class="py-2 px-4 text-center">
                        {{ row.region ? row.region.label : '-' }}
                    </td>
                    <td class="py-2 px-4">
                        <div class="flex flex-end">
                            <RefreshButton :watcher-id="row.id" @update="updateWatcherList"/>
                            <InfoButton :watcher-id="row.id"/>
                            <EditButton :watcher-id="row.id"/>
                            <DeleteButton
                                model-name="Watcher"
                                path-name="watcher"
                                :model="row"
                                :dialog-info="row.name"
                                @delete="removeWatcherFromList"
                            />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import {ref, reactive, computed, onMounted} from 'vue';
import Pusher from 'pusher-js';
import ChangeColumn from "@Components/Watcher/ChangeColumn.vue";
import DeleteButton from "@Components/Shared/DeleteButton.vue";
import IntervalSelect from "@Components/Watcher/IntervalSelect.vue";
import RefreshButton from "@Components/Watcher/RefreshButton.vue";
import EditButton from "@Components/Shared/EditButton.vue";
import InfoButton from "@Components/Shared/InfoButton.vue";
import AddWatcherButton from "@Components/Watcher/AddWatcherButton.vue";
import DetailsColumn from "@Components/Watcher/DetailsColumn.vue";
import PriceColumn from "@Components/Watcher/PriceColumn.vue";

const props = defineProps({
    userId: {
        type: Number,
        required: true
    },
    watchers: {
        type: Array,
        default: () => ([])
    },
    intervals: {
        type: Array,
        required: true
    }
});

const watchersList = ref([]);

const columnsVisible = reactive({
    interval: {title: 'Interval', display: true},
    original: {title: 'Original', display: true},
    change: {title: 'Change', display: true},
    lowestPrice: {title: 'Lowest', display: true},
    hasStock: {title: 'Stock', display: true},
    alert: {title: 'Alert', display: false},
    client: {title: 'Client', display: false},
    region: {title: 'Region', display: false},
    showInactive: {title: 'Show Inactive', display: false}
});

const tableData = computed(() => {
    return watchersList.value
        .filter((watcher) => columnsVisible.showInactive.display || watcher.interval_id !== 1)
        .sort((a, b) => a.name.localeCompare(b.name));
});

onMounted(() => {
    watchersList.value = props.watchers;

    const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER
    });
    const channel = pusher.subscribe(`user.${props.userId}.watchers`);
    channel.bind('update', (data) => {
        updateWatcherList(data.watcher);
    });

    restoreColumnSettings();
});

const updateWatcherList = (updatedWatcher) => {
    watchersList.value = [
        ...watchersList.value.filter((watcher) => (watcher.id !== updatedWatcher.id)),
        updatedWatcher
    ];
};

const removeWatcherFromList = (watcher) => {
    watchersList.value = watchersList.value.filter((w) => (w.id !== watcher.id));
};

const saveColumnSettings = () => {
    localStorage.setItem('column-settings', JSON.stringify(columnsVisible));
};

const restoreColumnSettings = () => {
    const columns = JSON.parse(localStorage.getItem('column-settings'));
    if (columns) {
        Object.assign(columnsVisible, columns);
    }
};
</script>
