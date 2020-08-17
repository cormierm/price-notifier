<template>
    <div>
        <div v-if="testResults">
            <b-message v-if="testResults.error" title="Danger" type="is-danger" aria-close-label="Close message">
                {{ testResults.error }}
            </b-message>
            <b-message v-else title="Found results" type="is-success" aria-close-label="Close message">
                {{ testResults.title }}<br>
                Value: {{ testResults.value }}
            </b-message>
        </div>

        <form class="wrapper-form" action="">
            <b-field
                label="Url"
                :type="formErrors['url'] ? 'is-danger' : 'is-default'"
                :message="formErrors['url']"
            >
                <b-input
                    v-model="url"
                    placeholder="https://www.example.com/product.html"
                    @input="autoFill"
                    :loading="loadingTemplate"
                ></b-input>
            </b-field>

            <b-field
                label="Name"
                :type="formErrors['name'] ? 'is-danger' : 'is-default'"
                :message="formErrors['name']"
            >
                <b-input
                    v-model="name"
                    placeholder="Product Name"
                    :loading="loadingTemplate"
                ></b-input>
            </b-field>

            <b-field
                label="Value XPath Query"
                :type="formErrors['query'] ? 'is-danger' : 'is-default'"
                :message="formErrors['query']"
            >
                <b-input v-model="xpathValue"></b-input>
            </b-field>

            <b-field
                label="Title XPath Query"
                :type="formErrors['xpath_title'] ? 'is-danger' : 'is-default'"
                :message="formErrors['xpath_title']"
            >
                <b-input v-model="xpathTitle"></b-input>
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
                label="Alert Value"
                :type="formErrors['alert_value'] ? 'is-danger' : 'is-default'"
                :message="formErrors['alert_value']"
            >
                <b-input v-model="alertValue" placeholder="5.00"></b-input>
            </b-field>

            <b-field
                label="Initial Value"
                :type="formErrors['initial_value'] ? 'is-danger' : 'is-default'"
                :message="formErrors['initial_value']"
            >
                <b-input v-model="initialValue" placeholder="0.00"></b-input>
            </b-field>
        </form>

        <div class="buttons">
            <b-button :loading="loading || loadingTemplate" @click="check">Check</b-button>
            <b-button type="is-info" @click="submit" :loading="loading">{{ type }}</b-button>
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
            this.xpathValue = this.watcher.query;
            this.url = this.watcher.url;
            this.alertValue = this.watcher.alert_value;
            this.initialValue = this.watcher.initial_value;
        }
    },
    data() {
        return {
            testResults: null,
            loading: false,
            loadingTemplate: false,
            id: null,
            name: '',
            interval: null,
            initialValue: '',
            alertValue: '',
            xpathValue: '//span[@id="price"]',
            xpathTitle: '//span[@class="title"]',
            url: '',
            formErrors: {},
        };
    },
    methods: {
        autoFill: debounce(function () {
            if (!this.id) {
                this.testResults = null;
                this.templateSearch();
            }
        }, 300),
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
                url: this.url,
                query: this.xpathValue,
                xpath_name: this.xpathTitle,
                initial_value: this.initialValue,
                alert_value: this.alertValue
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
            this.initialValue = '';
            axios.post('/watcher/check', {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                url: this.url,
                xpath_value: this.xpathValue,
                xpath_name: this.xpathTitle,
            }).then(({data}) => {
                this.testResults = data;
                this.name = this.testResults.title;
                this.initialValue = this.testResults.value;
            }).catch((err) => {
                if (err.response.status === 400) {
                    this.testResults = err.response.data;
                } else {
                    this.testResults = {
                        error:  err,
                    }
                }
            }).finally(() => {
                this.loadingTemplate = false;
            });
        },
        templateSearch() {
            this.testResults = null;
            this.loadingTemplate = true;
            axios.post('/template/search-by-url', {
                url: this.url,
            }).then(({data}) => {
                this.xpathValue = data.xpath_value;
                this.xpathTitle = data.xpath_name;
                this.check();
            }).catch((err) => {
                console.log(err);
                this.loadingTemplate = false;
            });
        },
        update() {
            axios.put(`/watcher/${this.id}`, {
                id: this.id,
                name: this.name,
                interval_id: this.interval,
                url: this.url,
                query: this.xpathValue,
                xpath_title: this.xpathTitle,
                initial_value: this.initialValue,
                alert_value: this.alertValue
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
    .wrapper-form {
        display: flex;
        flex-direction: column;
    }

    .buttons {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }
</style>
