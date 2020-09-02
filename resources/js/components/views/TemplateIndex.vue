<template>
    <div class="container">
        <h1 class="title">Domain Queries</h1>

        <b-table
            :data="templates"
        >
            <template slot-scope="props">
                <b-table-column field="domain" label="Domain" sortable>
                    {{ props.row.domain }}
                </b-table-column>
                <b-table-column field="xpath_value" label="XPath Price Query" sortable>
                    {{ props.row.xpath_value }}
                </b-table-column>
                <b-table-column field="client" label="Client" sortable>
                    {{ props.row.client }}
                </b-table-column>
                <b-table-column field="tools" centered>
                    <div class="tool-buttons">
                        <a :href="`/template/${props.row.id}/edit`">
                            <b-button type="is-default" icon-right="pencil"/>
                        </a>
                        <delete-button :template="props.row" @delete="reloadPage"></delete-button>
                    </div>
                </b-table-column>
            </template>
        </b-table>
    </div>
</template>

<script>
import DeleteButton from "../template/DeleteButton";

export default {
    name: "TemplateIndex",
    components: { DeleteButton },
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
