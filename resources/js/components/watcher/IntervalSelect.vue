<template>
    <b-select
        class="interval-select"
        placeholder="None"
        :value="value"
        @input="updateInterval(watcherId, $event)"
        :loading="loading"
    >
        <option
            v-for="option in intervals"
            :value="option.id"
            :key="option.id">
            {{ option.name }}
        </option>
    </b-select>
</template>

<script>
export default {
    name: "interval-select",
    props: {
        intervals: {
            type: Array,
            required: true
        },
        watcherId: {
            type: Number,
            required: true,
        },
        value: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            loading: false,
        };
    },
    methods: {
        updateInterval(watcherId, intervalId) {
            this.loading = true;
            axios.put(`/watcher/${watcherId}`, {
                interval_id: intervalId,
            }).then(({data}) => {
                this.$emit('update', data.watcher);
            }).catch((err) => {
                this.$buefy.toast.open('Error updating watcher interval!');
                console.error(err);
            }).finally(() => {
                this.loading = false;
            });
        },
    }
}
</script>

<style scoped>

</style>
