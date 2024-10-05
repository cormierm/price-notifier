<template>
    <div class="bg-white p-4">
        <div class="flex justify-between">
            <h1 class="text-2xl">Watcher Details</h1>
            <div class="flex">
                <RefreshButton :watcher-id="watcher.id" @update="updateWatcher"/>
                <EditButton :watcher-id="watcher.id"/>
                <DeleteButton
                    model-name="Watcher"
                    path-name="watcher"
                    :model="watcher"
                    :dialog-info="watcher.name"
                    @delete="redirectToWatchers"
                />
            </div>
        </div>

        <article class="border rounded mt-4">
            <div class="p-4 bg-gray-200">
                <div class="flex justify-between font-bold text-xl">
                    {{ watcher.name }}
                    <div v-if="watcher.value">${{ watcher.value }}</div>
                </div>

                <a class="text-blue-500 text-sm" :href="watcher.url">{{ watcher.url }}</a>
            </div>
            <div class="flex flex-col md:flex-row p-4 gap-8">
                <div class="w-full md:w-2/3 m-h-[800px]">
                    <canvas id="myChart"></canvas>
                </div>
                <div class="w-full md:w-1/3 flex flex-col gap-4">
                    <PriceHistoryTable :watcher="watcher"/>

                    <StockChangeTable :stock-changes="stockChanges"/>
                </div>
            </div>
        </article>

        <div class="flex mt-4 gap-4">
            <div class="border rounded w-1/2">
                <article>
                    <div class="text-lg font-bold p-4 bg-gray-200">
                        Query
                    </div>
                    <div class="m-4">
                        Price Query: {{ watcher.price_query }}<br>
                        Price Query Type: {{ watcher.price_query_type }}<br>
                        Stock Query: {{ watcher.stock_query }}<br>
                        Stock Query Type: {{ watcher.stock_query_type }}<br>
                        Stock condition: {{ watcher.stock_condition }}<br>
                        Stock text match: {{ watcher.stock_text }}<br>
                        Stock Requires Price: {{ watcher.stock_requires_price }}<br><br>
                    </div>
                </article>
            </div>
            <div class="border rounded w-1/2">
                <article class="">
                    <div class="text-lg font-bold p-4 bg-gray-200">
                        Settings
                    </div>
                    <div class="m-4">
                        <div>
                            Interval:
                            <IntervalSelect
                                style="padding-left: 10px"
                                :intervals="intervals"
                                :watcher-id="watcher.id"
                                :value="watcher.interval_id"
                                @update="updateWatcher"
                            />
                        </div>
                        Region: {{ watcher.region ? watcher.region.label : 'Not Set' }}<br>
                        Alert Price: {{ watcher.alert_value }}<br>
                    </div>
                </article>
            </div>
        </div>

        <Logs :watcher-id="watcher.id"></Logs>
    </div>
</template>

<script setup>
import moment from "moment";
import DeleteButton from "@Components/Shared/DeleteButton.vue";
import IntervalSelect from "@Components/Watcher/IntervalSelect.vue";
import Logs from "@Components/Watcher/Logs.vue";
import RefreshButton from "@Components/Watcher/RefreshButton.vue";
import StockChangeTable from "@Components/Tables/StockChangeTable.vue";
import Chart from 'chart.js/auto';
import 'chartjs-adapter-date-fns';
import {onMounted, ref} from "vue";
import EditButton from "@Components/Shared/EditButton.vue";
import PriceHistoryTable from "@Components/Tables/PriceHistoryTable.vue";

const props = defineProps({
    watcher: {
        type: Object,
        required: true
    },
    priceChanges: {
        type: Array,
        default: () => []
    },
    stockChanges: {
        type: Array,
        default: () => []
    },
    intervals: {
        type: Array,
        required: true
    }
});

const currentWatcher = ref(props.watcher.value);

const updateWatcher = (watcher) => {
    currentWatcher.value = watcher;
};

const redirectToWatchers = () => {
    window.location = '/home';
};

const formatDate = (datetime) => {
    return moment.utc(datetime).local().format('lll');
};

onMounted(() => {
    const points = [
        {x: props.watcher.last_sync, y: props.watcher.value},
        ...props.priceChanges.map((p) => ({x: p.created_at, y: p.price})),
    ];
    const ctx = document.getElementById('myChart');
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [{
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                fill: false,
                stepped: 'after',
                data: points,
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Price Change History'
                },
                legend: false
            },
            scales: {
                x: {
                    type: 'time',
                },
            }
        }
    });
});
</script>
