<template>
    <div>
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

        <form action="">
            <div class="flex flex-col mt-4">
                <label class="flex flex-col">
                    Url
                    <input
                        class="rounded"
                        :class="{'border-red-500': formErrors['url']}"
                        id="url"
                        type="url"
                        v-model="url"
                        placeholder="https://www.example.com/product.html"
                        @input="autoFill"
                    ></input>
                </label>
                <div v-if="formErrors['url']" class="text-sm text-red-500">{{ formErrors['url'][0] }}</div>
            </div>

            <form-input
                class="mt-4"
                label="Name"
                placeholder="Product Name"
                :errors="formErrors['name']"
                v-model="name"
            />

            <form-input
                class="mt-4"
                label="Price Query"
                :placeholder="pricePlaceholder"
                :errors="formErrors['price_query']"
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
                class="mt-4"
                label="Stock Query"
                :placeholder="stockPlaceholder"
                :errors="formErrors['stock_query']"
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
                <select class="rounded" placeholder="Select stock condition" v-model="stockCondition">
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

                <div class="mt-4">
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
                    <select class="rounded" placeholder="Select a region" v-model="region">
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
                    <spinner v-if="loadingCheck" />
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

<script>
import debounce from 'lodash/debounce';
import FormInput from "@components/form/FormInput.vue";
import Spinner from "@components/form/Spinner.vue";
import MessageBox from "@components/form/MessageBox.vue";

export default {
    name: "watcher-form",
    components: {MessageBox, Spinner, FormInput},
    props: {
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
    },
    mounted() {
        if (this.watcher) {
            this.id = this.watcher.id;
            this.name = this.watcher.name;
            this.interval = this.watcher.interval_id;
            this.region = this.watcher.region_id;
            this.priceQuery = this.watcher.price_query;
            this.priceQueryType = this.watcher.price_query_type;
            this.stockQuery = this.watcher.stock_query;
            this.stockQueryType = this.watcher.stock_query_type;
            this.url = this.watcher.url;
            this.alertValue = this.watcher.alert_value;
            this.client = this.watcher.client;
            this.stockText = this.watcher.stock_text;
            this.stockAlert = this.watcher.stock_alert === true;
            this.stockRequiresPrice = this.watcher.stock_requires_price === true;
            this.stockCondition = this.watcher.stock_condition;
            this.updateQueries = false;
        }
    },
    data() {
        return {
            testResults: null,
            loading: false,
            loadingTemplate: false,
            id: null,
            name: '',
            interval: 8,
            region: null,
            alertValue: '',
            priceQuery: '',
            priceQueryType: 'xpath',
            stockQuery: '',
            stockQueryType: 'xpath',
            url: '',
            formErrors: {},
            template: null,
            client: 'browsershot',
            stockText: '',
            stockAlert: false,
            stockRequiresPrice: true,
            stockCondition: 'contains_text',
            updateQueries: true,
            stockConditions: [
                {label: 'InnerText Contains', value: 'contains_text'},
                {label: 'InnerText Missing', value: 'missing_text'},
                {label: 'OuterHtml Contains', value: 'contains_html'},
                {label: 'OuterHtml Missing', value: 'missing_html'},
            ],
            showDebug: false,
        };
    },
    computed: {
        loadingCheck() {
            return this.loading || this.loadingTemplate;
        },
        pricePlaceholder() {
            return this.queryPlaceholder(this.priceQueryType, 'price');
        },
        stockPlaceholder() {
            return this.queryPlaceholder(this.stockQueryType, 'stock');
        }
    },
    methods: {
        autoFill: debounce(function () {
            if (!this.id) {
                this.testResults = null;
                this.templateSearch();
            }
        }, 300),
        autoFillName() {
            this.name = this.testResults.title;
        },
        queryPlaceholder(queryType, field) {
            switch (queryType) {
                case 'regex':
                    return '/textBefore(.*?)textAfter/';
                case 'selector':
                    return `.${field} span`;
                default:
                    return `//span[@class="${field}"]`;
            }
        },
        submit() {
            this.loading = true;
            this.formErrors = {};

            if (this.id) {
                this.update();
            } else {
                this.create();
            }

            this.loading = false;
        },
        create() {
            axios.post('/watcher', {
                name: this.name,
                interval_id: this.interval,
                region_id: this.region,
                url: this.url,
                price_query: this.priceQuery,
                price_query_type: this.priceQueryType,
                stock_query: this.stockQuery,
                stock_query_type: this.stockQueryType,
                alert_value: this.alertValue,
                client: this.client,
                stock_text: this.stockText,
                stock_alert: this.stockAlert,
                stock_condition: this.stockCondition,
                stock_requires_price: this.stockRequiresPrice,
                update_queries: this.updateQueries,
            }).then(() => {
                window.location = '/home';
            }).catch((err) => {
                if (err.response.status === 422) {
                    this.formErrors = err.response.data.errors;
                } else {
                    this.$buefy.toast.open({
                        duration: 5000,
                        message: err,
                        type: 'is-danger'
                    });
                    console.error(err);
                }
            });
        },
        check() {
            this.formErrors = {};
            this.testResults = null;
            this.loading = true;
            axios.post('/watcher/check', {
                url: this.url,
                price_query: this.priceQuery,
                price_query_type: this.priceQueryType,
                stock_query: this.stockQuery,
                stock_query_type: this.stockQueryType,
                client: this.client,
                stock_text: this.stockText,
                stock_condition: this.stockCondition,
                stock_requires_price: this.stockRequiresPrice,
            }).then(({data}) => {
                this.testResults = data;
                if (!this.name) {
                    this.name = this.testResults.title;
                }
                if (!this.alertValue) {
                    this.alertValue = this.testResults.value;
                }
            }).catch((err) => {
                if (err.response.status === 422) {
                    this.formErrors = err.response.data.errors;
                } else if (err.response.status === 400) {
                    this.testResults = err.response.data;
                } else {
                    this.testResults = {
                        error: err,
                    }
                }
            }).finally(() => {
                this.loading = false;
            });
        },
        templateSearch() {
            this.loadingTemplate = true;
            this.template = null;
            axios.post('/template/search-by-url', {
                url: this.url,
            }).then(({data}) => {
                this.priceQuery = data.price_query;
                this.priceQueryType = data.price_query_type;
                this.stockQuery = data.stock_query;
                this.stockQueryType = data.stock_query_type;
                this.client = data.client;
                this.stockText = data.stock_text;
                this.stockCondition = data.stock_condition;
                this.stockRequiresPrice = data.stock_requires_price;
                this.template = data;
                this.updateQueries = false;
                this.check();
            }).catch((err) => {
                console.log(err);
            }).finally(() => {
                this.loadingTemplate = false;
            });
        },
        update() {
            axios.put(`/watcher/${this.id}`, {
                id: this.id,
                name: this.name,
                interval_id: this.interval,
                region_id: this.region,
                url: this.url,
                price_query: this.priceQuery,
                price_query_type: this.priceQueryType,
                stock_query: this.stockQuery,
                stock_query_type: this.stockQueryType,
                alert_value: this.alertValue,
                client: this.client,
                stock_text: this.stockText,
                stock_alert: this.stockAlert,
                stock_condition: this.stockCondition,
                stock_requires_price: this.stockRequiresPrice,
                update_queries: this.updateQueries,
            }).then(() => {
                window.location = '/home';
            }).catch((err) => {
                if (err.response.status === 422) {
                    this.formErrors = err.response.data.errors;
                } else {
                    this.$buefy.toast.open({
                        duration: 5000,
                        message: err,
                        type: 'is-danger'
                    });
                    console.error(err);
                }
            });
        }
    }
}
</script>
