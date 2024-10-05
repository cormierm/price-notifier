<template>
    <div class="bg-white p-4">
        <h1 class="text-xl">{{ type }} Domain Query</h1>
        <form class="mt-8" action="">
            <FormInput
                class="mt-4"
                label="Domain"
                placeholder="Product Name"
                :errors="formErrors.domain"
                :disabled="type === 'Update'"
                v-model="domain"
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

<script setup>
import FormInput from "@Components/Form/FormInput.vue";
import {onMounted, ref} from "vue";
import QueryInput from "@Components/Form/QueryInput.vue";
import StockConditionsInput from "@Components/Form/StockConditionsInput.vue";

const props = defineProps({
    template: {
        type: Object,
        default: null,
    },
    type: {
        type: String,
        default: 'Create',
    }
});

onMounted(() => {
    if (props.template) {
        id.value = props.template.id;
        domain.value = props.template.domain;
        priceQuery.value = props.template.price_query;
        priceQueryType.value = props.template.price_query_type;
        client.value = props.template.client;
        stockQuery.value = props.template.stock_query;
        stockQueryType.value = props.template.stock_query_type;
        stockText.value = props.template.stock_text;
        stockCondition.value = props.template.stock_condition;
    }
});

const loading = ref(false);
const id = ref(null);
const domain = ref('');
const priceQuery = ref('//span[@id="price"]');
const priceQueryType = ref('xpath');
const client = ref('browsershot');
const formErrors = ref({});
const stockQuery = ref('');
const stockQueryType = ref('xpath');
const stockCondition = ref('contains_text');
const stockText = ref('');

const submit = () => {
    loading.value = true;

    if (id.value) {
        update();
    } else {
        create();
    }

    loading.value = false;
};

const create = () => {
    axios.post('/template', {
        domain: domain.value,
        price_query: priceQuery.value,
        price_query_type: priceQueryType.value,
        client: client.value
    }).then(() => {
        window.location = '/template';
    }).catch((err) => {
        if (err.response.status === 422) {
            formErrors.value = err.response.data.errors;
        } else {
            console.error(err);
        }
    });
};

const update = () => {
    axios.put(`/template/${id.value}`, {
        id: id.value,
        domain: domain.value,
        price_query: priceQuery.value,
        price_query_type: priceQueryType.value,
        client: client.value,
        stock_query: stockQuery.value,
        stock_query_type: stockQueryType.value,
        stock_text: stockText.value,
        stock_condition: stockCondition.value,
    }).then(() => {
        window.location = '/template';
    }).catch((err) => {
        if (err.response.status === 422) {
            formErrors.value = err.response.data.errors;
        } else {
            console.error(err);
        }
    });
};
</script>
