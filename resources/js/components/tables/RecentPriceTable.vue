<template>
    <div class="recent-price-table">
        <h2>{{ title }}</h2>

        <b-table
            :data="tableData"
        >
            <template slot-scope="props">
                <b-table-column field="created_at" label="Date">
                    {{ props.row.created_at_formatted }}
                </b-table-column>
                <b-table-column field="watcher.name" label="Watcher">
                    <div class="name-field">
                        <div>
                            <a :href="`/watcher/${props.row.watcher.id}`">{{ props.row.watcher.name }}</a>
                            <a :href="props.row.watcher.url"><b-icon icon="link"/></a>
                        </div>
                        <span>{{ props.row.watcher.url_domain }}</span>
                    </div>
                </b-table-column>
                <b-table-column field="price" label="Price" width="100">
                    {{ props.row.price }}
                </b-table-column>
            </template>
        </b-table>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: "RecentPriceTable",
    props: {
        title: {
            type: String,
            default: 'Most Recent Price Changes',
        },
        priceChanges: {
            type: Array,
            default: () => ([])
        },
    },
    computed: {
        tableData() {
            return this.priceChanges.map((change) => {
                return {
                    ...change,
                    created_at_formatted: change.created_at
                        ? moment(change.created_at).format('YYYY-MM-DD HH:mm:ss')
                        : '',
                }
            });
        }
    },
}
</script>

<style lang="scss" scoped>
h2 {
    font-weight: bold;
}

.name-field {
    display: flex;
    flex-direction: column;

    span {
        color: #666;
        font-size: 0.7em;
    }
}
</style>
