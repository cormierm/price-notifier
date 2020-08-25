<template>
    <b-button
        type="is-default"
        icon-right="refresh"
        :loading="loading"
        @click="refresh(watcherId)"
    />
</template>

<script>
export default {
    name: "RefreshButton",
    props: {
        watcherId: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            loading: false,
        };
    },
    methods: {
        refresh(id) {
            this.loading = true;
            axios.get(`/watcher/${id}/sync`)
                .then(({data}) => {
                    this.$emit('update', data.watcher);
                })
                .catch((err) => {
                    console.log(err);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    }
}
</script>
