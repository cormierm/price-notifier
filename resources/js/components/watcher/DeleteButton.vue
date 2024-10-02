<template>
    <button
        class="w-10 h-10 border rounded text-center bg-red-500 text-white"
        @click="deleteConfirmation"
    >🗑</button>
</template>

<script>
export default {
    name: "DeleteButton",
    props: {
        watcher: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            loading: false,
        };
    },
    methods: {
        deleteConfirmation() {
            this.$buefy.dialog.confirm({
                title: 'Deleting watcher',
                message: `Are you sure you want to <b>delete</b> this watcher?<br><strong>${this.watcher.name}</strong><br>This action cannot be undone.`,
                confirmText: 'Delete Watcher',
                type: 'is-danger',
                hasIcon: true,
                onConfirm: () => this.delete()
            })
        },
        delete() {
            axios.delete(`/watcher/${this.watcher.id}`)
                .then(({data}) => {
                    this.$emit('delete', this.watcher)
                    this.$buefy.toast.open({
                        message: data.message,
                        type: 'is-success'
                    })
                })
                .catch((err) => {
                    this.$buefy.toast.open({
                        duration: 5000,
                        message: 'Error deleting watcher.',
                        type: 'is-danger'
                    });
                    console.log(err);
                });
        },
    }
}
</script>
