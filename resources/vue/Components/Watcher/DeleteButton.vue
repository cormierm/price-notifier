<template>
    <button
        class="w-10 h-10 border rounded text-center bg-red-500 text-white"
        @click="deleteConfirmation"
    >
        <FontAwesomeIcon :icon="faTrash" />
    </button>
    <delete-watcher-dialog
        v-model:isOpen="isOpen"
        :watcher="watcher"
        @confirmed="deleteWatcher"
    ></delete-watcher-dialog>
</template>

<script setup>
import DeleteWatcherDialog from "@Components/Watcher/DeleteWatcherDialog.vue";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faTrash } from '@fortawesome/free-solid-svg-icons';
import {defineEmits, ref} from "vue";

const emit = defineEmits(['delete']);

const props = defineProps({
    watcher: {
        type: Object,
        required: true
    }
})

const isOpen = ref(false);

const deleteConfirmation = () => {
    isOpen.value = true;
};

const deleteWatcher = () => {
    axios.delete(`/watcher/${props.watcher.id}`)
        .then(({data}) => {
            emit('delete', props.watcher)
            // this.$buefy.toast.open({
            //     message: data.message,
            //     type: 'is-success'
            // })
        })
        .catch((err) => {
            // this.$buefy.toast.open({
            //     duration: 5000,
            //     message: 'Error deleting watcher.',
            //     type: 'is-danger'
            // });
            console.log(err);
        });
};
</script>
