<template>
    <div class="bg-white p-4">
        <h1 class="text-xl">{{type}} Domain Query</h1>
        <form class="mt-8" action="">
            <form-input
                class="mt-4"
                label="Domain"
                placeholder="Product Name"
                :errors="formErrors['domain']"
                :disabled="type === 'Update'"
                v-model="domain"
            />

            <form-input
                class="mt-8"
                label="Price Query"
                placeholder="//span[@id='price']"
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
                class="mt-8"
                label="Stock Query"
                placeholder="//span[@id='stock']"
                :errors="formErrors['xpath_stock']"
                v-model="xpathStock"
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
        </form>

        <div class="flex justify-end">
            <button class="border rounded py-2 px-4 hover:border-gray-300" @click="submit">{{ type }}</button>
        </div>
    </div>
</template>

<script>
import FormInput from "../Form/FormInput.vue";

export default {
    name: "template-form",
    components: {FormInput},
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
            this.priceQuery = this.template.price_query;
            this.priceQueryType = this.template.price_query_type;
            this.client = this.template.client;
            this.xpathStock = this.template.stock_query;
            this.stockQueryType = this.template.stock_query_type;
            this.stockText = this.template.stock_text;
            this.stockCondition = this.template.stock_condition;
        }
    },
    data() {
        return {
            loading: false,
            id: null,
            domain: '',
            priceQuery: '//span[@id="price"]',
            priceQueryType: 'xpath',
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
                price_query: this.priceQuery,
                price_query_type: this.priceQueryType,
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
                price_query: this.priceQuery,
                price_query_type: this.priceQueryType,
                client: this.client,
                xpath_stock: this.xpathStock,
                stock_query_type: this.stockQueryType,
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
