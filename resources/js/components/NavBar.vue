<template>
    <b-navbar>
        <template slot="brand">
            <b-navbar-item class="logo" href="/home">
                PriceWatcher
            </b-navbar-item>
        </template>
        <template slot="start">
            <b-navbar-item href="/watcher">
                Watchers
            </b-navbar-item>
            <b-navbar-item href="/template">
                Domain Queries
            </b-navbar-item>
        </template>

        <template slot="end">
            <b-navbar-dropdown :label="username">
                <b-navbar-item href="/profile" class="border-bottom">
                    Profile
                </b-navbar-item>
                <b-navbar-item @click="logout">
                    Logout
                </b-navbar-item>
            </b-navbar-dropdown>
        </template>
    </b-navbar>
</template>

<script>
export default {
    name: "NavBar",
    props: {
        username: {
            type: String,
            required: true
        }
    },
    methods: {
        logout() {
            axios.post('/logout').then(() => {
                window.location = '/';
            })
        }
    }
}
</script>

<style scoped>
    .logo {
        font-size: 1.2em;
        font-weight: 500;
    }

    .border-bottom {
        border-bottom: 1px solid #ececec;
    }
</style>
