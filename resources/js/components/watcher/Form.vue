<template>
    <form class="wrapper-form" action="">
        <b-field
            label="Name"
            :type="formErrors['name'] ? 'is-danger' : 'is-default'"
            :message="formErrors['name']"
        >
            <b-input v-model="name"></b-input>
        </b-field>

        <b-field
            label="Url"
            :type="formErrors['url'] ? 'is-danger' : 'is-default'"
            :message="formErrors['url']"
        >
            <b-input v-model="url"></b-input>
        </b-field>

        <b-field
            label="Query Type"
            :type="formErrors['query_type'] ? 'is-danger' : 'is-default'"
            :message="formErrors['query_type']"
        >
            <b-select placeholder="Select a query type" v-model="queryType">
                <option
                    v-for="option in queryTypes"
                    :value="option.value"
                    :key="option.value">
                    {{ option.name }}
                </option>
            </b-select>
        </b-field>

        <b-field
            label="Query"
            :type="formErrors['query'] ? 'is-danger' : 'is-default'"
            :message="formErrors['query']"
        >
            <b-input v-model="query"></b-input>
        </b-field>

        <b-button type="is-info" @click="submit">Create</b-button>
    </form>
</template>

<script>
export default {
    name: "wrapper-form",
    data() {
        return {
            loading: false,
            name: '',
            query: '',
            queryType: null,
            url: '',
            formErrors: {},
            queryTypes: [
                {
                    name: 'Class',
                    value: 'class'
                },
                {
                    name: 'Id',
                    value: 'id'
                },
                {
                    name: 'Query',
                    value: 'query'
                },
            ],
        };
    },
    methods: {
        submit() {
            this.loading = true;

            axios.post('/watcher', {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                name: this.name,
                url: this.url,
                query: this.query,
                query_type: this.queryType,
            }).then(() => {
                window.location = '/';
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

            this.loading = false;
        },
    }
}
</script>

<style scoped>
    .wrapper-form {
        display: flex;
        flex-direction: column;
    }
</style>
