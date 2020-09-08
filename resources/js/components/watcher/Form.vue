<template>
    <div>
        <b-message v-if="template && !testResults" :title="`Found domain query for ${template.domain}`"
                   type="is-success" aria-close-label="Close message">
            Auto filling xpath query and checking...
        </b-message>
        <div v-if="testResults">
            <b-message v-if="testResults.error" title="Danger" type="is-danger" aria-close-label="Close message">
                {{ testResults.error }}
            </b-message>
            <b-message v-else title="XPath query and title results" type="is-success" aria-close-label="Close message">
                {{ testResults.title }}<br><br>
                Value: <strong>{{ testResults.value }}</strong>
                <b-button v-if="testResults.title !== name" class="is-pulled-right" @click="autoFillName">Update Name
                </b-button>
            </b-message>
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
                    maxlength="191"
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
                    maxlength="191"
                    placeholder="Product Name"
                    :loading="loadingTemplate"
                ></b-input>
            </b-field>

            <b-field
                label="XPath Query for Price"
                :type="formErrors['query'] ? 'is-danger' : 'is-default'"
                :message="formErrors['query']"
            >
                <b-input
                    v-model="xpathValue"
                    maxlength="191"
                    placeholder="//span[@id='price']"
                ></b-input>
            </b-field>

            <b-field
                label="Alert Value"
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
            <b-button :loading="loadingCheck" @click="check">Check</b-button>
            <b-button type="is-info" @click="submit">{{ type }}</b-button>
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
            this.xpathValue = this.watcher.query;
            this.url = this.watcher.url;
            this.alertValue = this.watcher.alert_value;
            this.client = this.watcher.client;
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
            xpathValue: '//span[@id="price"]',
            url: '',
            formErrors: {},
            template: null,
            client: 'browsershot',
        };
    },
    computed: {
        loadingCheck() {
            return this.loading || this.loadingTemplate;
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
        submit() {
            this.loading = true;

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
                query: this.xpathValue,
                alert_value: this.alertValue,
                client: this.client
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
            this.testResults = null;
            this.loading = true;
            axios.post('/watcher/check', {
                url: this.url,
                xpath_value: this.xpathValue,
                client: this.client
            }).then(({data}) => {
                this.testResults = data;
                if (!this.name) {
                    this.name = this.testResults.title;
                }
                if (!this.alertValue) {
                    this.alertValue = this.testResults.value;
                }
            }).catch((err) => {
                if (err.response.status === 400) {
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
                this.xpathValue = data.xpath_value;
                this.client = data.client
                this.template = data;
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
                query: this.xpathValue,
                alert_value: this.alertValue,
                client: this.client
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
</style>
