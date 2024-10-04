<template>
    <button
        class="w-10 h-10 border rounded text-center bg-red-500 text-white"
        @click="deleteConfirmation"
    >
        <FontAwesomeIcon :icon="faTrash" />
    </button>
    <DeleteDialog
        v-model:isOpen="isOpen"
        label="watcher"
        :info="watcher.name"
        @confirmed="deleteWatcher"
    ></DeleteDialog>
</template>

<script setup>
import DeleteDialog from "@Components/Form/DeleteDialog.vue";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faTrash } from '@fortawesome/free-solid-svg-icons';
import {ref} from "vue";

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
        })
        .catch((err) => {
            console.log(err);
        });
};
</script>
