<template>
    <div>
        <p>Domain Query Templates</p>

        <b-table
            :columns="columns"
            :data="templates"
        ></b-table>
    </div>
</template>

<script>
export default {
    name: "TemplateIndex",
    props: {
        templates: {
            type: Array,
            default: () => ([])
        }
    },
    data() {
        return {
            columns: [
                {
                    field: 'domain',
                    label: 'Domain',
                },
                {
                    field: 'xpath_value',
                    label: 'XPath Price Query',
                },
                {
                    field: 'xpath_name',
                    label: 'XPath Name Query',
                },
                {
                    field: 'client',
                    label: 'Client',
                },
            ],
        }
    },
}
</script>

<style scoped>

</style>
