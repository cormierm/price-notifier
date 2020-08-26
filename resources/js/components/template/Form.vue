<template>
    <div>
        <form class="template-form" action="">
            <b-field
                label="Domain"
                :type="formErrors['domain'] ? 'is-danger' : 'is-default'"
                :message="formErrors['domain']"
            >
                <b-input
                    v-model="domain"
                    maxlength="191"
                    placeholder="amazon.ca"
                ></b-input>
            </b-field>

            <b-field
                label="XPath Price Query"
                :type="formErrors['xpath_value'] ? 'is-danger' : 'is-default'"
                :message="formErrors['xpath_value']"
            >
                <b-input
                    v-model="xpathValue"
                    maxlength="191"
                    placeholder="//span[@id='price']"
                ></b-input>
            </b-field>

            <b-field
                label="XPath Name Query"
                :type="formErrors['xpath_name'] ? 'is-danger' : 'is-default'"
                :message="formErrors['xpath_name']"
            >
                <b-input
                    v-model="xpathName"
                    maxlength="191"
                    placeholder="//title"
                ></b-input>
            </b-field>

            <b-field
                label="Client"
                :type="formErrors['client'] ? 'is-danger' : 'is-default'"
                :message="formErrors['client']"
            >
                <div class="block">
                    <b-radio
                        v-model="client"
                        name="client"
                        native-value="browsershot"
                    >
                        Browsershot
                    </b-radio>
                    <b-radio
                        v-model="client"
                        name="client"
                        native-value="curl"
                    >
                        Curl
                    </b-radio>
                    <b-radio
                        v-model="client"
                        name="client"
                        native-value="guzzle"
                    >
                        Guzzle
                    </b-radio>
                </div>
            </b-field>
        </form>

        <div class="buttons">
            <b-button type="is-info" @click="submit" :loading="loading">{{ type }}</b-button>
        </div>
    </div>
</template>

<script>
export default {
    name: "template-form",
    props: {
        template: {
            type: Object,
            default: null,
        },
        type: {
            type: String,
            default: 'Create',
        }
    },
    mounted() {
        if (this.template) {
            this.id = this.template.id;
            this.domain = this.template.domain;
            this.xpathValue = this.template.xpath_value;
            this.xpathName = this.template.xpath_name;
            this.client = this.template.client;
        }
    },
    data() {
        return {
            loading: false,
            id: null,
            domain: '',
            xpathValue: '//span[@id="price"]',
            xpathName: '//title',
            client: 'browsershot',
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
            axios.post('/template', {
                domain: this.domain,
                xpath_value: this.xpathValue,
                xpath_name: this.xpathName,
                client: this.client
            }).then(() => {
                window.location = '/template';
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
            axios.put(`/template/${this.id}`, {
                id: this.id,
                domain: this.domain,
                xpath_value: this.xpathValue,
                xpath_name: this.xpathName,
                client: this.client
            }).then(() => {
                window.location = '/template';
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
.template-form {
    display: flex;
    flex-direction: column;
}

.buttons {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
}
</style>
