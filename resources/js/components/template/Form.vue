<template>
    <div>
        <form class="template-form" action="">
            <b-field
                label="Domain"
                :type="formErrors['domain'] ? 'is-danger' : 'is-default'"
                :message="formErrors['domain']"
            >
                <b-input
                    v-model="domain"
                    readonly
                    disabled
                ></b-input>
            </b-field>

            <b-field
                label="XPath Price Query"
                :type="formErrors['xpath_value'] ? 'is-danger' : 'is-default'"
                :message="formErrors['xpath_value']"
            >
                <b-input
                    v-model="xpathValue"
                    maxlength="191"
                    placeholder="//span[@id='price']"
                ></b-input>
            </b-field>

            <b-field
                label="XPath Query for Stock"
                :type="formErrors['xpath_stock'] ? 'is-danger' : 'is-default'"
                :message="formErrors['xpath_stock']"
            >
                <b-input
                    v-model="xpathStock"
                    maxlength="191"
                    placeholder="//span[@id='stock']"
                ></b-input>
            </b-field>

            <b-field
                label="Stock Condition"
                :type="formErrors['stock_condition'] ? 'is-danger' : 'is-default'"
                :message="formErrors['stock_condition']"
            >
                <b-select placeholder="Select stock condition" v-model="stockCondition">
                    <option
                        v-for="option in stockConditions"
                        :value="option.value"
                        :key="option.value">
                        {{ option.label }}
                    </option>
                </b-select>
            </b-field>

            <b-field
                label="Stock Text Match"
                :type="formErrors['stock_text'] ? 'is-danger' : 'is-default'"
                :message="formErrors['stock_text']"
            >
                <b-input
                    v-model="stockText"
                    maxlength="191"
                    placeholder="In Stock."
                ></b-input>
            </b-field>

            <b-field
                label="Client"
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
        </form>

        <div class="buttons">
            <b-button type="is-info" @click="submit" :loading="loading">{{ type }}</b-button>
        </div>
    </div>
</template>

<script>
export default {
    name: "template-form",
    props: {
        template: {
            type: Object,
            default: null,
        },
        type: {
            type: String,
            default: 'Create',
        }
    },
    mounted() {
        if (this.template) {
            this.id = this.template.id;
            this.domain = this.template.domain;
            this.xpathValue = this.template.xpath_value;
            this.client = this.template.client;
            this.xpathStock = this.template.xpath_stock;
            this.stockText = this.template.stock_text;
            this.stockCondition = this.template.stock_condition;
        }
    },
    data() {
        return {
            loading: false,
            id: null,
            domain: '',
            xpathValue: '//span[@id="price"]',
            client: 'browsershot',
            formErrors: {},
            xpathStock: '',
            stockCondition: 'contains_text',
            stockText: '',
            stockConditions: [
                {label: 'Contains Text', value: 'contains_text'},
                {label: 'Missing Text', value: 'missing_text'},
                {label: 'Contains Html', value: 'contains_html'},
                {label: 'Missing Html', value: 'missing_html'},
            ],
        };
    },
    methods: {
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
            axios.post('/template', {
                domain: this.domain,
                xpath_value: this.xpathValue,
                client: this.client
            }).then(() => {
                window.location = '/template';
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
        update() {
            axios.put(`/template/${this.id}`, {
                id: this.id,
                domain: this.domain,
                xpath_value: this.xpathValue,
                client: this.client,
                xpath_stock: this.xpathStock,
                stock_text: this.stockText,
                stock_condition: this.stockCondition,
            }).then(() => {
                window.location = '/template';
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
.template-form {
    display: flex;
    flex-direction: column;
}

.buttons {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
}
</style>
