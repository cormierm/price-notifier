<template>
    <div class="container">
        <div class="title-header">
            <h1 class="title">Profile {{ user.email }}</h1>
        </div>

        <div class="card">
            <header class="card-header">
                <p class="card-header-title">
                    User Settings
                </p>
            </header>
            <div class="card-content">
                <div class="content">
                    <b-field
                        label="Pushover User Key"
                        :type="formErrors['pushover_user_key'] ? 'is-danger' : 'is-default'"
                        :message="formErrors['pushover_user_key']"
                    >
                        <b-input
                            v-model="pushoverUserKey"
                            maxlength="30"
                        ></b-input>
                    </b-field>
                    <b-field
                        label="Pushover Api Token"
                        :type="formErrors['pushover_api_token'] ? 'is-danger' : 'is-default'"
                        :message="formErrors['pushover_api_token']"
                    >
                        <b-input
                            v-model="pushoverApiToken"
                            maxlength="30"
                        ></b-input>
                    </b-field>
                    <b-field
                        label="Watcher User Agent (Used for watcher fetches)"
                        :type="formErrors['user_agent'] ? 'is-danger' : 'is-default'"
                        :message="formErrors['user_agent']"
                    >
                        <b-input
                            v-model="userAgent"
                            maxlength="255"
                        ></b-input>
                    </b-field>
                    <b-field
                        label="User Api Key (Used for chrome extension)"
                        :type="formErrors['api_key'] ? 'is-danger' : 'is-default'"
                        :message="formErrors['api_key']"
                    >
                        <b-field>
                        <b-input
                            expanded
                            v-model="apiKey"
                            placeholder="5f7273b9-09eb-3070-a1fd-ce5a8fbaf2fa"
                        ></b-input>
                        <p class="control">
                            <button class="button is-primary" @click="generateUUID">Generate</button>
                        </p>
                        </b-field>
                    </b-field>
                    <div class="button-container">
                        <b-button :loading="loading" @click="updateProfile">Update</b-button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import { v4 as uuidv4 } from 'uuid';

export default {
    name: "ProfileIndex",
    props: {
        user: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            loading: false,
            pushoverApiToken: this.user.pushover_api_token,
            pushoverUserKey: this.user.pushover_user_key,
            apiKey: this.user.api_key,
            userAgent: this.user.user_agent,
            formErrors: {}
        }
    },
    methods: {
        generateUUID() {
            this.apiKey = uuidv4();
        },
        updateProfile() {
            this.formErrors = {};
            this.loading = true;
            axios.put(`/profile`, {
                pushover_user_key: this.pushoverUserKey,
                pushover_api_token: this.pushoverApiToken,
                user_agent: this.userAgent,
                api_key: this.apiKey
            }).then(() => {
                this.$buefy.toast.open({
                    duration: 5000,
                    message: 'Successfully updated profile.',
                    type: 'is-success'
                });
            }).catch((err) => {
                if (err.response.status === 422) {
                    this.formErrors = err.response.data.errors;
                } else{
                    this.$buefy.toast.open({
                        duration: 5000,
                        message: err,
                        type: 'is-danger'
                    });
                    console.error(err);
                }
            }).finally(() => {
                this.loading = false;
            });
        },
    }
}
</script>

<style scoped>
.card-header {
    background-color: #f8f8f8;
}
.title-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.button-container {
    width: 100%;
    display: flex;
    justify-content: flex-end;
}
</style>
