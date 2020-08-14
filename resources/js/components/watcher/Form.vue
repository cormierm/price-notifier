<template>
    <form class="wrapper-form" action="">
        <b-field
            label="Name"
            :type="formErrors['name'] ? 'is-danger' : 'is-default'"
            :message="formErrors['name']"
        >
            <b-input v-model="name" placeholder="Product Name"></b-input>
        </b-field>

        <b-field
            label="Url"
            :type="formErrors['url'] ? 'is-danger' : 'is-default'"
            :message="formErrors['url']"
        >
            <b-input v-model="url" placeholder="https://www.example.com/product.html"></b-input>
        </b-field>

        <b-field
            label="XPath Query"
            :type="formErrors['query'] ? 'is-danger' : 'is-default'"
            :message="formErrors['query']"
        >
            <b-input v-model="query"></b-input>
        </b-field>

        <b-button type="is-info" @click="submit" :loading="loading">{{ type }}</b-button>
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
            this.url = this.watcher.url;
        }
    },
    data() {
        return {
            loading: false,
            id: null,
            name: '',
            query: '//div[@id="price"]',
            url: '',
            formErrors: {},
        };
    },
    methods: {
        submit() {
            this.loading = true;

            if (this.id) {
                this.update();
            } else {
                this.create();
            }

            this.loading = false;
        },
        create() {
            axios.post('/watcher', {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                name: this.name,
                url: this.url,
                query: this.query,
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
            axios.put(`/watcher/${this.id}`, {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                id: this.id,
                name: this.name,
                url: this.url,
                query: this.query,
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
