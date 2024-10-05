<template>
    <div class="w-full p-4 bg-white">
        <div class="flex justify-between">
            <h1 class="text-2xl">Watchers</h1>
            <a href="/watcher/create">
                <button class="border rounded px-4 py-2">+ Add New Watcher</button>
            </a>
        </div>
        <div class="flex gap-4 mt-4">
            <div v-for="(column, index) in columnsVisible" :key="index" class="flex items-center gap-1">
                <input class="rounded" type="checkbox" v-model="column.display" @input="saveColumnSettings">
                <label>{{ column.title }}</label>
            </div>
            <div class="flex items-center gap-1">
                <input class="rounded" type="checkbox" v-model="showInactive"
                       @input="saveColumnSetting('show-inactive', $event)">
                <label>Show Inactive</label>
            </div>
        </div>

        <div class="relative overflow-x-auto pb-4 pt-2">
            <table class="text-gray-600 w-full">
                <thead class="text-xs uppercase bg-gray-700 text-gray-300">
                <tr>
                    <th class="py-2 px-4 text-left">Name</th>
                    <th v-if="columnsVisible.interval.display" class="py-2 px-4 w-[100px]">Interval</th>
                    <th v-if="columnsVisible.initial_value.display" class="py-2 px-4">Original</th>
                    <th class="py-2 px-4">Current</th>
                    <th v-if="columnsVisible.change.display" class="py-2 px-4">Change</th>
                    <th v-if="columnsVisible.lowest_price.display" class="py-2 px-4">Lowest</th>
                    <th v-if="columnsVisible.has_stock.display" class="py-2 px-4">Stock</th>
                    <th v-if="columnsVisible.alert_value.display" class="py-2 px-4">Alert</th>
                    <th v-if="columnsVisible.client.display" class="py-2 px-4">Client</th>
                    <th v-if="columnsVisible.region.display" class="py-2 px-4">Region</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in tableData" :key="row.id" class="bg-white border-b">
                    <td class="py-2 px-4">
                        <div class="min-w-96">
                            <div class="text-blue-600 space-x-1">
                                <a :href="`/watcher/${row.id}`">{{ row.name }}</a>
                                <a :href="row.url">
                                    <FontAwesomeIcon :icon="faLink"/>
                                </a>
                            </div>
                            <span class="text-sm text-gray-500">{{ row.url_domain }}</span>
                        </div>
                    </td>
                    <td v-if="columnsVisible.interval.display" class="py-2 px-4">
                        <interval-select
                            :intervals="intervals"
                            :watcher-id="row.id"
                            :value="row.interval_id"
                            @update="updateWatcherList"
                        />
                    </td>
                    <td v-if="columnsVisible.initial_value.display" class="py-2 px-4">
                        <div class="flex flex-col items-center text-lg">
                            {{ row.initial_value ? row.initial_value : '-' }}
                            <span class="text-xs">{{ row.created_at }}</span>
                        </div>
                    </td>
                    <td class="py-2 px-4">
                        <div class="flex flex-col items-center text-lg">
                            {{ row.value ? row.value : '-' }}
                            <span class="text-xs">{{ row.last_sync }}</span>
                        </div>
                    </td>
                    <td v-if="columnsVisible.change.display" class="py-2 px-4 text-center">
                        <change-column
                            :initial-value="row.initial_value"
                            :current-value="row.value"
                        />
                    </td>
                    <td v-if="columnsVisible.lowest_price.display" class="py-2 px-4">
                        <div class="flex flex-col items-center text-lg">
                            {{ row.lowest_price ? row.lowest_price : '-' }}
                            <span class="text-xs">{{ row.lowest_at }}</span>
                        </div>
                    </td>
                    <td v-if="columnsVisible.has_stock.display" class="py-2 px-4 text-center">
                        {{ row.has_stock === true ? 'Yes' : row.has_stock === false ? 'No' : '-' }}
                    </td>
                    <td v-if="columnsVisible.alert_value.display" class="py-2 px-4 text-center">
                        {{ row.alert_value ? row.alert_value : '-' }}
                    </td>
                    <td v-if="columnsVisible.client.display" class="py-2 px-4 text-center">
                        {{ row.client }}
                    </td>
                    <td v-if="columnsVisible.region.display" class="py-2 px-4 text-center">
                        {{ row.region ? row.region.label : '-' }}
                    </td>
                    <td class="py-2 px-4 text-center text-lg font-black">
                        <div class="flex">
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
import moment from 'moment';
import Pusher from 'pusher-js';
import ChangeColumn from "@Components/Watcher/ChangeColumn.vue";
import DeleteButton from "@Components/Shared/DeleteButton.vue";
import IntervalSelect from "@Components/Watcher/IntervalSelect.vue";
import RefreshButton from "@Components/Watcher/RefreshButton.vue";
import {faLink} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import EditButton from "@Components/Shared/EditButton.vue";
import InfoButton from "@Components/Shared/InfoButton.vue";

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

const showInactive = ref(true);
const watchersList = ref([]);
const loading = reactive({
    watchers: {}
});
const columnsVisible = reactive({
    interval: {title: 'Interval', display: true},
    initial_value: {title: 'Original', display: true},
    change: {title: 'Change', display: true},
    lowest_price: {title: 'Lowest', display: true},
    has_stock: {title: 'Stock', display: true},
    alert_value: {title: 'Alert', display: false},
    client: {title: 'Client', display: false},
    region: {title: 'Region', display: false}
});

const tableData = computed(() => {
    return watchersList.value
        .filter((watcher) => showInactive.value || watcher.interval_id !== 1)
        .map((watcher) => ({
            ...watcher,
            created_at: moment.utc(watcher.created_at).fromNow(),
            last_sync: watcher.last_sync ? moment.utc(watcher.last_sync).fromNow() : 'Never',
            lowest_at: watcher.lowest_at ? moment.utc(watcher.lowest_at).fromNow() : '',
        }))
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
    showInactive.value = restoreColumnSetting('show-inactive');
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

const saveColumnSetting = (column, setting) => {
    localStorage.setItem(`column-setting-${column}`, setting);
};

const saveColumnSettings = () => {
    localStorage.setItem('column-settings', JSON.stringify(columnsVisible));
};

const restoreColumnSetting = (column) => {
    return localStorage.getItem(`column-setting-${column}`) === 'true';
};

const restoreColumnSettings = () => {
    const columns = JSON.parse(localStorage.getItem('column-settings'));
    if (columns) {
        Object.assign(columnsVisible, columns);
    }
};
</script>
