<template>
    <div class="container">
        <h1 class="title">Domain Queries</h1>

        <b-table
            :data="templates"
        >
            <b-table-column field="domain" label="Domain" sortable v-slot="props">
                {{ props.row.domain }}
            </b-table-column>
            <b-table-column field="price_query_type" label="Price Query Type" v-slot="props">
                {{ props.row.price_query_type }}
            </b-table-column>
            <b-table-column field="price_query" label="Price Query" v-slot="props">
                {{ props.row.price_query }}
            </b-table-column>
            <b-table-column field="stock_query_type" label="Stock Query Type" v-slot="props">
                {{ props.row.stock_query_type }}
            </b-table-column>
            <b-table-column field="stock_query" label="Stock Query" v-slot="props">
                {{ props.row.stock_query }}
            </b-table-column>
            <b-table-column field="stock_condition" label="Condition" v-slot="props">
                {{ props.row.stock_condition }}
            </b-table-column>
            <b-table-column field="stock_text" label="Stock Text" v-slot="props">
                {{ props.row.stock_text }}
            </b-table-column>
            <b-table-column field="client" label="Client" sortable v-slot="props">
                {{ props.row.client }}
            </b-table-column>
            <b-table-column field="tools" centered v-slot="props">
                <div class="tool-buttons">
                    <a :href="`/template/${props.row.id}/edit`">
                        <b-button type="is-default" icon-right="pencil"/>
                    </a>
                    <delete-button :template="props.row" @delete="reloadPage"></delete-button>
                </div>
            </b-table-column>
        </b-table>
    </div>
</template>

<script>
import DeleteButton from "../template/DeleteButton.vue";

export default {
    name: "TemplateIndex",
    components: {DeleteButton},
    props: {
        templates: {
            type: Array,
            default: () => ([])
        }
    },
    methods: {
        reloadPage() {
            location.reload();
        }
    }
}
</script>

<style scoped>
.tool-buttons {
    display: flex;
}
</style>
