<template>
    <div class="px-8 py-4">
        <h1 class="text-2xl mb-4">{{ type }} Watcher</h1>
        <message-box
            v-if="template && !testResults"
            :title="`Found domain query for ${template.domain}`"
        >
            Checking for template and auto-filling form...
        </message-box>
        <div v-if="testResults">
            <message-box
                v-if="testResults.error"
                title="Danger"
                colour="red"
            >
                {{ testResults.error }}
            </message-box>
            <div v-else>
                <message-box title="Results">
                    <button
                        v-if="testResults.title !== name"
                        class="bg-white py-2 px-4 rounded text-black border hover:border-gray-300 float-right"
                        @click="autoFillName"
                    >
                        Update Name
                    </button>
                    {{ testResults.title }}<br><br>
                    Price: <span class="font-bold">{{ testResults.value }}</span><br>
                    Stock: {{ testResults.has_stock }}
                </message-box>
                <message-box
                    class="mt-4"
                    v-if="showDebug"
                    title="Debug Information"
                    colour="black"
                >
                    Price InnerText: <strong>{{ testResults.debug.value_inner_text }}</strong><br><br>
                    Stock InnerText: <strong>{{ testResults.debug.stock_inner_text }}</strong><br><br>
                    Stock OuterHtml: <strong>{{ testResults.debug.stock_outer_html }}</strong>
                </message-box>
            </div>
        </div>

        <div class="flex flex-col mt-8">
            <label class="flex flex-col">
                Url
                <input
                    class="rounded dark:bg-gray-900"
                    :class="{'border-red-500': formErrors.url}"
                    id="url"
                    type="url"
                    v-model="url"
                    placeholder="https://www.example.com/product.html"
                    @input="autoFill"
                />
            </label>
            <div v-if="formErrors.url" class="text-sm text-red-500">{{ formErrors.url[0] }}</div>
        </div>

        <FormInput
            class="mt-8"
            label="Name"
            placeholder="Product Name"
            :errors="formErrors.name"
            v-model="name"
        />

        <QueryInput
            label="Price Query"
            :errors="formErrors.price_query || formErrors.price_query_type"
            radio-buttons-name="price_query_type"
            v-model:query="priceQuery"
            v-model:query-type="priceQueryType"
        />

        <QueryInput
            label="Stock Query"
            :errors="formErrors.stock_query || formErrors.stock_query_type"
            radio-buttons-name="stock_query_type"
            v-model:query="stockQuery"
            v-model:query-type="stockQueryType"
        />

        <StockConditionsInput v-model:condition="stockCondition" v-model:text="stockText"/>

        <label class="flex items-center gap-2 mt-1">
            <input
                class="rounded"
                type="checkbox"
                v-model="stockRequiresPrice"
            />
            Stock updates require price (Helps reduce false positives)
        </label>

        <div class="mt-8">
            <h2 class="text-xl font-bold">Notifications</h2>

            <AlertConditionsInput
                v-model:condition="alertCondition"
                v-model:amount="alertValue"
                :errors="formErrors"
            />

            <label class="flex items-center gap-2 mt-3">
                <input class="rounded" type="checkbox" v-model="stockAlert"/>
                Notify When In Stock
            </label>
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-bold">Watcher Settings</h2>
            <label class="flex flex-col mt-4">
                Interval
                <select class="rounded dark:bg-gray-900" v-model="interval">
                    <option
                        v-for="option in intervals"
                        :value="option.id"
                        :key="option.id">
                        {{ option.name }}
                    </option>
                </select>
            </label>
            <div v-if="formErrors['interval_id']" class="text-sm text-red-500">
                {{ formErrors['interval_id'][0] }}
            </div>

            <div class="mt-8">
                <label>Html Client</label>
                <div class="flex gap-3 pb-1">
                    <RadioButton label="Browsershot" name="client" value="browsershot" v-model="client"/>
                    <RadioButton label="Curl" name="client" value="curl" v-model="client"/>
                    <RadioButton label="Guzzle" name="client" value="guzzle" v-model="client"/>
                    <RadioButton label="Puppeteer" name="client" value="puppeteer" v-model="client"/>
                </div>
                <div v-if="formErrors.client" class="text-sm text-red-500">
                    {{ formErrors.client[0] }}
                </div>
            </div>

            <label class="flex flex-col mt-4">
                Region
                <select class="rounded dark:bg-gray-900" v-model="region">
                    <option value="null">None</option>
                    <option
                        v-for="option in regions"
                        :value="option.id"
                        :key="option.id">
                        {{ option.label }}
                    </option>
                </select>

            </label>
            <div v-if="formErrors['region_id']" class="text-sm text-red-500">
                {{ formErrors['region_id'][0] }}
            </div>
        </div>

        <div class="mt-8 flex justify-between">
            <div class="flex items-center gap-2">
                <button
                    class="border rounded px-4 py-2 hover:border-gray-100 w-[100px] h-11 flex items-center justify-center"
                    :disabled="loadingCheck"
                    @click="check"
                >
                    <spinner v-if="loadingCheck"/>
                    {{ loadingCheck ? '' : 'Check' }}
                </button>
                <label>
                    <input class="rounded" type="checkbox" v-model="showDebug"/>
                    Show Debug
                </label>
            </div>
            <div class="flex items-center gap-2">
                <label>
                    <input class="rounded" type="checkbox" v-model="updateQueries"/>
                    Update Domain Query
                </label>
                <button
                    class="rounded bg-blue-500 hover:bg-blue-600 py-2 px-4 text-white"
                    @click="submit"
                >
                    {{ type }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import debounce from 'lodash/debounce';
import FormInput from "@Components/Form/FormInput.vue";
import Spinner from "@Components/Form/Spinner.vue";
import MessageBox from "@Components/Form/MessageBox.vue";
import QueryInput from "@Components/Form/QueryInput.vue";
import RadioButton from "@Components/Form/RadioButton.vue";
import StockConditionsInput from "@Components/Form/StockConditionsInput.vue";
import AlertConditionsInput from "@Components/Form/AlertConditionsInput.vue";

const props = defineProps({
    intervals: {
        type: Array,
        required: true
    },
    regions: {
        type: Array,
        required: true
    },
    watcher: {
        type: Object,
        default: null,
    },
    type: {
        type: String,
        default: 'Create',
    }
});

onMounted(() => {
    if (props.watcher) {
        id.value = props.watcher.id;
        name.value = props.watcher.name;
        interval.value = props.watcher.interval_id;
        region.value = props.watcher.region_id;
        priceQuery.value = props.watcher.price_query;
        priceQueryType.value = props.watcher.price_query_type;
        stockQuery.value = props.watcher.stock_query;
        stockQueryType.value = props.watcher.stock_query_type;
        url.value = props.watcher.url;
        alertValue.value = props.watcher.alert_value;
        client.value = props.watcher.client;
        stockText.value = props.watcher.stock_text;
        stockAlert.value = props.watcher.stock_alert === true;
        stockRequiresPrice.value = props.watcher.stock_requires_price === true;
        stockCondition.value = props.watcher.stock_condition;
        alertCondition.value = props.watcher.alert_condition;
        updateQueries.value = false;
    }
});

const testResults = ref(null);
const loading = ref(false);
const loadingTemplate = ref(false);
const id = ref(null);
const name = ref('');
const interval = ref(8);
const region = ref(null);
const alertValue = ref('');
const alertCondition = ref('less_than');
const priceQuery = ref('');
const priceQueryType = ref('xpath');
const stockQuery = ref('');
const stockQueryType = ref('xpath');
const url = ref('');
const formErrors = ref({});
const template = ref(null);
const client = ref('browsershot');
const stockText = ref('');
const stockAlert = ref(false);
const stockRequiresPrice = ref(true);
const stockCondition = ref('contains_text');
const updateQueries = ref(true);
const showDebug = ref(false);

const loadingCheck = computed(() => {
    return loading.value || loadingTemplate.value;
});

const autoFill = debounce(function () {
    if (!id.value) {
        testResults.value = null;
        templateSearch();
    }
}, 500);

const autoFillName = () => {
    name.value = testResults.value.title;
};

const submit = () => {
    loading.value = true;
    formErrors.value = {};

    if (id.value) {
        update();
    } else {
        create();
    }

    loading.value = false;
};

const create = () => {
    axios.post('/watcher', {
        name: name.value,
        interval_id: interval.value,
        region_id: region.value,
        url: url.value,
        price_query: priceQuery.value,
        price_query_type: priceQueryType.value,
        stock_query: stockQuery.value,
        stock_query_type: stockQueryType.value,
        alert_value: alertValue.value,
        alert_condition: alertCondition.value,
        client: client.value,
        stock_text: stockText.value,
        stock_alert: stockAlert.value,
        stock_condition: stockCondition.value,
        stock_requires_price: stockRequiresPrice.value,
        update_queries: updateQueries.value,
    }).then(() => {
        window.location = '/home';
    }).catch((err) => {
        if (err.response.status === 422) {
            formErrors.value = err.response.data.errors;
        } else {
            console.error(err);
        }
    });
};

const check = () => {
    formErrors.value = {};
    testResults.value = null;
    loading.value = true;
    axios.post('/watcher/check', {
        url: url.value,
        price_query: priceQuery.value,
        price_query_type: priceQueryType.value,
        stock_query: stockQuery.value,
        stock_query_type: stockQueryType.value,
        client: client.value,
        stock_text: stockText.value,
        stock_condition: stockCondition.value,
        stock_requires_price: stockRequiresPrice.value,
    }).then(({data}) => {
        testResults.value = data;
        if (!name.value) {
            name.value = testResults.value.title;
        }
        if (!alertValue.value) {
            alertValue.value = testResults.value.value;
        }
    }).catch((err) => {
        if (err.response.status === 422) {
            formErrors.value = err.response.data.errors;
        } else if (err.response.status === 400) {
            testResults.value = err.response.data;
        } else {
            testResults.value = {
                error: err,
            }
        }
    }).finally(() => {
        loading.value = false;
    });
};

const templateSearch = () => {
    loadingTemplate.value = true;
    template.value = null;
    axios.post('/template/search-by-url', {
        url: url.value,
    }).then(({data}) => {
        priceQuery.value = data.price_query;
        priceQueryType.value = data.price_query_type;
        stockQuery.value = data.stock_query;
        stockQueryType.value = data.stock_query_type;
        client.value = data.client;
        stockText.value = data.stock_text;
        stockCondition.value = data.stock_condition;
        stockRequiresPrice.value = data.stock_requires_price;
        template.value = data;
        updateQueries.value = false;
        check();
    }).catch((err) => {
        console.log(err);
    }).finally(() => {
        loadingTemplate.value = false;
    });
};

const update = () => {
    axios.put(`/watcher/${id.value}`, {
        id: id.value,
        name: name.value,
        interval_id: interval.value,
        region_id: region.value,
        url: url.value,
        price_query: priceQuery.value,
        price_query_type: priceQueryType.value,
        stock_query: stockQuery.value,
        stock_query_type: stockQueryType.value,
        alert_value: alertValue.value,
        alert_condition: alertCondition.value,
        client: client.value,
        stock_text: stockText.value,
        stock_alert: stockAlert.value,
        stock_condition: stockCondition.value,
        stock_requires_price: stockRequiresPrice.value,
        update_queries: updateQueries.value,
    }).then(() => {
        window.location = '/home';
    }).catch((err) => {
        if (err.response.status === 422) {
            formErrors.value = err.response.data.errors;
        } else {
            console.error(err);
        }
    });
};
</script>
