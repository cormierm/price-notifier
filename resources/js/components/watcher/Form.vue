<template>
    <div>
        <b-message
            v-if="template && !testResults"
            :title="`Found domain query for ${template.domain}`"
            type="is-success"
            aria-close-label="Close message"
        >
            Checking for template and auto-filling form...
        </b-message>
        <div v-if="testResults" class="update-checkbox">
            <b-message v-if="testResults.error" title="Danger" type="is-danger" aria-close-label="Close message">
                {{ testResults.error }}
            </b-message>
            <div v-else>
                <b-message title="Check Results" type="is-success" aria-close-label="Close message">
                    {{ testResults.title }}<br><br>
                    Price: <strong>{{ testResults.value }}</strong>
                    <b-button v-if="testResults.title !== name" class="is-pulled-right" @click="autoFillName">
                        Update Name
                    </b-button>
                    <br>
                    Stock: {{ testResults.has_stock }}
                </b-message>
                <b-message v-if="showDebug" title="Debug Information" type="is-default"
                           aria-close-label="Close message">
                    Price InnerText: <strong>{{ testResults.debug.value_inner_text }}</strong><br><br>
                    Stock InnerText: <strong>{{ testResults.debug.stock_inner_text }}</strong><br><br>
                    Stock OuterHtml: <strong>{{ testResults.debug.stock_outer_html }}</strong>
                </b-message>
            </div>
        </div>

        <form class="watcher-form" action="">
            <b-field
                label="Url"
                :type="formErrors['url'] ? 'is-danger' : 'is-default'"
                :message="formErrors['url']"
            >
                <b-input
                    type="url"
                    v-model="url"
                    maxlength="255"
                    placeholder="https://www.example.com/product.html"
                    @input="autoFill"
                ></b-input>
            </b-field>

            <b-field
                label="Name"
                :type="formErrors['name'] ? 'is-danger' : 'is-default'"
                :message="formErrors['name']"
            >
                <b-input
                    v-model="name"
                    maxlength="255"
                    placeholder="Product Name"
                    :loading="loadingTemplate"
                ></b-input>
            </b-field>

            <b-field label="Price Query"></b-field>
            <b-field
                :type="formErrors['price_query_type'] ? 'is-danger' : 'is-default'"
                :message="formErrors['price_query_type']"
            >
                <div class="block">
                    <b-radio
                        v-model="priceQueryType"
                        name="price_query_type"
                        native-value="xpath"
                    >
                        XPath
                    </b-radio>
                    <b-radio
                        v-model="priceQueryType"
                        name="price_query_type"
                        native-value="selector"
                    >
                        Query Selector
                    </b-radio>
                    <b-radio
                        v-model="priceQueryType"
                        name="price_query_type"
                        native-value="regex"
                    >
                        Regex
                    </b-radio>
                </div>
            </b-field>
            <b-field
                :type="formErrors['price_query'] ? 'is-danger' : 'is-default'"
                :message="formErrors['price_query']"
            >
                <b-input
                    v-model="priceQuery"
                    maxlength="255"
                    :placeholder="pricePlaceholder"
                    @input="updateQueries = true"
                ></b-input>
            </b-field>

            <b-field label="Stock Query"></b-field>
            <b-field
                :type="formErrors['stock_query_type'] ? 'is-danger' : 'is-default'"
                :message="formErrors['stock_query_type']"
            >
                <div class="block">
                    <b-radio
                        v-model="stockQueryType"
                        name="stock_query_type"
                        native-value="xpath"
                    >
                        XPath
                    </b-radio>
                    <b-radio
                        v-model="stockQueryType"
                        name="stock_query_type"
                        native-value="selector"
                    >
                        Query Selector
                    </b-radio>
                    <b-radio
                        v-model="stockQueryType"
                        name="stock_query_type"
                        native-value="regex"
                    >
                        Regex
                    </b-radio>
                </div>
            </b-field>
            <b-field
                :type="formErrors['stock_query'] ? 'is-danger' : 'is-default'"
                :message="formErrors['stock_query']"
            >
                <b-input
                    v-model="stockQuery"
                    maxlength="255"
                    :placeholder="stockPlaceholder"
                    @input="updateQueries = true"
                ></b-input>
            </b-field>

            <b-field>
                <b-select placeholder="Select stock condition" v-model="stockCondition">
                    <option
                        v-for="option in stockConditions"
                        :value="option.value"
                        :key="option.value">
                        {{ option.label }}
                    </option>
                </b-select>
                <b-input
                    expanded
                    v-model="stockText"
                    maxlength="255"
                    placeholder="In Stock."
                    @input="updateQueries = true"
                ></b-input>
            </b-field>

            <b-field
                label="Alert Price"
                :type="formErrors['alert_value'] ? 'is-danger' : 'is-default'"
                :message="formErrors['alert_value']"
            >
                <b-input
                    v-model="alertValue"
                    type="number"
                    placeholder="5.00"
                ></b-input>
            </b-field>

            <b-field
                label="Alerts"
            >
                <div class="field">
                    <b-checkbox v-model="stockAlert">Stock</b-checkbox>
                </div>
            </b-field>

            <b-field
                label="Interval"
                :type="formErrors['interval_id'] ? 'is-danger' : 'is-default'"
                :message="formErrors['interval_id']"
            >
                <b-select placeholder="Select an interval" v-model="interval">
                    <option
                        v-for="option in intervals"
                        :value="option.id"
                        :key="option.id">
                        {{ option.name }}
                    </option>
                </b-select>
            </b-field>

            <b-field
                label="Html Client"
                :type="formErrors['client'] ? 'is-danger' : 'is-default'"
                :message="formErrors['client']"
            >
                <div class="block">
                    <b-radio
                        v-model="client"
                        name="client"
                        native-value="browsershot"
                    >
                        Browsershot
                    </b-radio>
                    <b-radio
                        v-model="client"
                        name="client"
                        native-value="curl"
                    >
                        Curl
                    </b-radio>
                    <b-radio
                        v-model="client"
                        name="client"
                        native-value="guzzle"
                    >
                        Guzzle
                    </b-radio>
                </div>
            </b-field>

            <b-field
                label="Region"
                :type="formErrors['region_id'] ? 'is-danger' : 'is-default'"
                :message="formErrors['region_id']"
            >
                <b-select placeholder="Select a region" v-model="region">
                    <option value="">None</option>
                    <option
                        v-for="option in regions"
                        :value="option.id"
                        :key="option.id">
                        {{ option.label }}
                    </option>
                </b-select>
            </b-field>
        </form>

        <div class="buttons">
            <div class="button-group">
                <b-button :loading="loadingCheck" @click="check">Check</b-button>
                <b-field>
                    <b-switch v-model="showDebug" class="switch-margin">
                        Show Debug
                    </b-switch>
                </b-field>
            </div>
            <div class="button-group">
                <b-checkbox v-model="updateQueries" class="switch-margin">
                    Update Domain Query
                </b-checkbox>
                <b-button type="is-info" @click="submit">{{ type }}</b-button>
            </div>
        </div>
    </div>
</template>

<script>
import debounce from 'lodash/debounce';

export default {
    name: "watcher-form",
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
                    return 'textBefore(.*?)textAfter';
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

<style scoped>
.watcher-form {
    display: flex;
    flex-direction: column;
}

.buttons {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
}

.button-group {
    display: flex;
    align-items: center;
}

.switch-margin {
    margin-bottom: 0.5rem;
}
</style>
