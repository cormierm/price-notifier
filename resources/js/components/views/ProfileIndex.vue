<template>
    <div class="container">
        <div class="title-header">
            <h1 class="title">Profile {{ user.email }}</h1>
        </div>

        <div class="card">
            <header class="card-header">
                <p class="card-header-title">
                    Pushover Settings
                </p>
            </header>
            <div class="card-content">
                <div class="content">
                    <b-field
                        label="User Key"
                        :type="formErrors['pushover_user_key'] ? 'is-danger' : 'is-default'"
                        :message="formErrors['pushover_user_key']"
                    >
                        <b-input
                            v-model="pushoverUserKey"
                            maxlength="191"
                        ></b-input>
                    </b-field>
                    <b-field
                        label="Api Token"
                        :type="formErrors['pushover_api_token'] ? 'is-danger' : 'is-default'"
                        :message="formErrors['pushover_api_token']"
                    >
                        <b-input
                            v-model="pushoverApiToken"
                            maxlength="191"
                        ></b-input>
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
            pushoverApiToken: this.user.pushover_user_key,
            pushoverUserKey: this.user.pushover_api_token,
            formErrors: {}
        }
    },
    methods: {
        updateProfile() {
            this.loading = true;
            axios.put(`/profile`, {
                pushover_user_key: this.pushoverUserKey,
                pushover_api_token: this.pushoverApiToken,
            }).then(() => {
                this.$buefy.toast.open({
                    duration: 5000,
                    message: 'Successfully updated profile.',
                    type: 'is-success'
                });
            }).catch((err) => {
                if (err.response.status !== 422) {
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
