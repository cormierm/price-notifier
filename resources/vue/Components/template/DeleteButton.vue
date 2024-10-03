<template>
    <b-button
        type="is-danger"
        icon-right="delete"
        @click="deleteConfirmation"
    />
</template>

<script>
export default {
    name: "DeleteButton",
    props: {
        template: {
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
                title: 'Deleting query template',
                message: `Are you sure you want to <b>delete</b> this template?<br><strong>${this.template.domain}</strong><br>This action cannot be undone.`,
                confirmText: 'Delete Template',
                type: 'is-danger',
                hasIcon: true,
                onConfirm: () => this.delete()
            })
        },
        delete() {
            axios.delete(`/template/${this.template.id}`)
                .then(({data}) => {
                    this.$emit('delete', this.template)
                    this.$buefy.toast.open({
                        message: data.message,
                        type: 'is-success'
                    })
                })
                .catch((err) => {
                    this.$buefy.toast.open({
                        duration: 5000,
                        message: 'Error deleting template.',
                        type: 'is-danger'
                    });
                    console.log(err);
                });
        },
    }
}
</script>
