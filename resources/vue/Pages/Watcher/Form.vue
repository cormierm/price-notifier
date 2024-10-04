<template>
    <div class="bg-white px-8 py-4">
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

        <form>
            <div class="flex flex-col mt-8">
                <label class="flex flex-col">
                    Url
                    <input
                        class="rounded"
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

            <form-input
                class="mt-8"
                label="Name"
                placeholder="Product Name"
                :errors="formErrors.name"
                v-model="name"
            />

            <form-input
                class="mt-8"
                label="Price Query"
                :placeholder="pricePlaceholder"
                :errors="formErrors.price_query"
                v-model="priceQuery"
            >
                <div class="flex gap-3 pb-1">
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            v-model="priceQueryType"
                            name="price_query_type"
                            value="xpath"
                        />
                        XPath
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            v-model="priceQueryType"
                            name="price_query_type"
                            value="selector"
                        />
                        Query Selector
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            v-model="priceQueryType"
                            name="price_query_type"
                            value="regex"
                        />
                        Regex
                    </label>
                </div>
            </form-input>

            <form-input
                class="mt-8"
                label="Stock Query"
                :placeholder="stockPlaceholder"
                :errors="formErrors.stock_query"
                v-model="stockQuery"
            >
                <div class="flex gap-3 pb-1">
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            v-model="stockQueryType"
                            name="stock_query_type"
                            value="xpath"
                        />
                        XPath
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            v-model="stockQueryType"
                            name="stock_query_type"
                            value="selector"
                        />
                        Query Selector
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            v-model="stockQueryType"
                            name="stock_query_type"
                            value="regex"
                        />
                        Regex
                    </label>
                </div>
            </form-input>

            <div class="mt-4 flex items-center">
                <select class="rounded" v-model="stockCondition">
                    <option
                        v-for="option in stockConditions"
                        :value="option.value"
                        :key="option.value">
                        {{ option.label }}
                    </option>
                </select>
                <form-input
                    class="w-full"
                    placeholder="In Stock."
                    v-model="stockText"
                />
            </div>

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
                <label class="flex items-center gap-2 mt-3">
                    <input class="rounded" type="checkbox" v-model="stockAlert"/>
                    Notify When In Stock
                </label>

                <form-input
                    class="mt-4"
                    label="Notify When Price Below"
                    type="number"
                    placeholder="5.00"
                    :errors="formErrors['alert_value']"
                    v-model="alertValue"
                />
            </div>

            <div class="mt-8">
                <h2 class="text-xl font-bold">Watcher Settings</h2>
                <label class="flex flex-col mt-4">
                    Interval
                    <select class="rounded" placeholder="Select an interval" v-model="interval">
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
                        <label class="flex items-center gap-2">
                            <input type="radio" v-model="client" name="client" value="browsershot"/>
                            Browsershot
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" v-model="client" name="client" value="curl"/>
                            Curl
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" v-model="client" name="client" value="guzzle"/>
                            Guzzle
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" v-model="client" name="client" value="puppeteer"/>
                            Puppeteer
                        </label>
                    </div>

                    <div v-if="formErrors['client']" class="text-sm text-red-500">
                        {{ formErrors['client'][0] }}
                    </div>
                </div>

                <label class="flex flex-col mt-4">
                    Region
                    <select class="rounded" v-model="region">
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
        </form>

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
const stockConditions = ref([
    {label: 'InnerText Contains', value: 'contains_text'},
    {label: 'InnerText Missing', value: 'missing_text'},
    {label: 'OuterHtml Contains', value: 'contains_html'},
    {label: 'OuterHtml Missing', value: 'missing_html'},
]);
const showDebug = ref(false);

const loadingCheck = computed(() => {
    return loading.value || loadingTemplate.value;
});

const pricePlaceholder = computed(() => {
    return queryPlaceholder(priceQueryType.value, 'price');
});

const stockPlaceholder = computed(() => {
    return queryPlaceholder(stockQueryType.value, 'stock');
});

const autoFill = debounce(function () {
    if (!id.value) {
        testResults.value = null;
        templateSearch();
    }
}, 300);

const autoFillName = () => {
    name.value = testResults.value.title;
};

const queryPlaceholder = (queryType, field) => {
    switch (queryType) {
        case 'regex':
            return '/textBefore(.*?)textAfter/';
        case 'selector':
            return `.${field} span`;
        default:
            return `//span[@class="${field}"]`;
    }
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
            alertValue.value = testResults.value;
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
