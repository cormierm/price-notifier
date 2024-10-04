<template>
    <div class="bg-white p-4">
        <h1 class="text-xl mb-4">Domain Queries</h1>

        <DomainQueriesTable :templates="props.templates"/>
    </div>
</template>

<script setup>
import DomainQueriesTable from "@Components/Tables/DomainQueriesTable.vue";

const props = defineProps({
    templates: {
        type: Array,
        default: () => ([])
    }
});
</script>
