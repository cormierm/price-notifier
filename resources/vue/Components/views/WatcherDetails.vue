<template>
    <div class="bg-white p-4">
        <div class="flex justify-between">
            <h1 class="text-2xl">Watcher Details</h1>
            <div>
                <refresh-button :watcher-id="watcher.id" @update="updateWatcher"></refresh-button>
                <a :href="`/watcher/${watcher.id}/edit`">
                    <button class="w-10 h-10 border rounded text-center">&#9998;</button>
                </a>
                <delete-button :watcher="watcher" @delete="redirectToWatchers"></delete-button>
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
            <div class="flex p-4 gap-8">
                <div class="w-2/3 m-h-96">
                    <canvas id="myChart"></canvas>
                </div>
                <div class="w-1/3 flex flex-col gap-4">
                    <div>
                        <h3 class="text-lg">Price History</h3>
                        <table class="text-sm text-gray-600 w-full">
                            <thead class="text-xs text-left uppercase bg-gray-700 text-gray-300">
                            <tr>
                                <th class="py-2 px-4">Type</th>
                                <th class="py-2 px-4">Price</th>
                                <th class="py-2 px-4">Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="bg-white border-b">
                                <td class="py-2 px-4">Current</td>
                                <td class="py-2 px-4">${{ watcher.value }}</td>
                                <td class="py-2 px-4">{{ formatDate(watcher.last_sync) }}</td>
                            </tr>
                            <tr class="bg-white border-b">
                                <td class="py-2 px-4">Lowest</td>
                                <td class="py-2 px-4">${{ watcher.lowest_price }}</td>
                                <td class="py-2 px-4">{{ formatDate(watcher.lowest_at) }}</td>
                            </tr>
                            <tr class="bg-white border-b">
                                <td class="py-2 px-4">Original</td>
                                <td class="py-2 px-4">${{ watcher.initial_value }}</td>
                                <td class="py-2 px-4">{{ formatDate(watcher.created_at) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <stock-change-table :stock-changes="stockChanges"/>
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
                            <interval-select
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

        <watcher-logs :watcher-id="watcher.id"></watcher-logs>
    </div>
</template>

<script>
import moment from "moment";
import DeleteButton from "../Watcher/DeleteButton.vue";
import IntervalSelect from "../Watcher/IntervalSelect.vue";
import RefreshButton from "../Watcher/RefreshButton.vue";
import StockChangeTable from "../Tables/StockChangeTable.vue";
import Chart from 'chart.js/auto';
import 'chartjs-adapter-date-fns';

export default {
    name: "WatcherDetails",
    components: {DeleteButton, IntervalSelect, RefreshButton, StockChangeTable},
    props: {
        watcher: {
            type: Object,
            required: true
        },
        priceChanges: {
            type: Array,
            default: () => ([])
        },
        stockChanges: {
            type: Array,
            default: () => ([])
        },
        intervals: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            currentWatcher: this.watcher,
        }
    },
    methods: {
        updateWatcher(watcher) {
            this.currentWatcher = watcher;
        },
        redirectToWatchers() {
            window.location = '/home';
        },
        formatDate(datetime) {
            return moment.utc(datetime).local().format('lll');
        }
    },
    mounted() {
        const points = [
            {x: this.watcher.last_sync, y: this.watcher.value},
            ...this.priceChanges.map((p) => ({x: p.created_at, y: p.price})),
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
    }
}
</script>
