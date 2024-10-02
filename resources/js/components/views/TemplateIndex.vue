<template>
    <div class="container">
        <h1 class="text-xl">Domain Queries</h1>

        <domain-queries-table :data="templates"/>
    </div>
</template>

<script>
import DomainQueriesTable from "@components/tables/DomainQueriesTable.vue";

export default {
    name: "TemplateIndex",
    components: {DomainQueriesTable},
    props: {
        templates: {
            type: Array,
            default: () => ([])
        }
    },

}
</script>
