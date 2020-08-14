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

        <b-button type="is-info" @click="submit">{{ type }}</b-button>
    </form>
</template>

<script>
export default {
    name: "watcher-form",
    props: {
        watcher: {
            type: Object,
            default: null,
        },
        type: {
            type: String,
            default: 'Create',
        }
    },
    mounted() {
        if (this.watcher) {
            this.id = this.watcher.id;
            this.name = this.watcher.name;
            this.query = this.watcher.query;
            this.queryType = this.watcher.query_type;
            this.url = this.watcher.url;
        }
    },
    data() {
        return {
            loading: false,
            id: null,
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

            if (this.id) {
                this.create();
            } else {
                this.update();
            }

            this.loading = false;
        },
        create() {
            axios.post('/watcher', {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                name: this.name,
                url: this.url,
                query: this.query,
                query_type: this.queryType,
            }).then(() => {
                window.location = '/home';
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
        },
        update() {
            axios.post(`/watcher/${this.id}`, {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                id: this.id,
                name: this.name,
                url: this.url,
                query: this.query,
                query_type: this.queryType,
            }).then(() => {
                window.location = '/home';
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
        }
    }
}
</script>

<style scoped>
    .wrapper-form {
        display: flex;
        flex-direction: column;
    }
</style>
