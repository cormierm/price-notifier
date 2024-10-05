<template>
    <button
        class="w-10 h-10 border rounded text-center bg-red-500 text-white"
        @click="deleteConfirmation"
    >
        <FontAwesomeIcon :icon="faTrash" />
    </button>
    <DeleteDialog
        v-model:isOpen="isOpen"
        :label="modelName"
        :info="dialogInfo"
        @confirmed="deleteModel"
    ></DeleteDialog>
</template>

<script setup>
import {ref} from "vue";
import DeleteDialog from "@Components/Shared/DeleteDialog.vue";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faTrash } from '@fortawesome/free-solid-svg-icons';

const emit = defineEmits(['delete']);

const props = defineProps({
    modelName: {
        type: String,
        required: true
    },
    model: {
        type: Object,
        required: true
    },
    dialogInfo: {
        type: String,
        required: true
    },
    pathName: {
        type: String,
        required: true
    }
})

const isOpen = ref(false);

const deleteConfirmation = () => {
    isOpen.value = true;
};

const deleteModel = () => {
    axios.delete(`/${props.pathName}/${props.model.id}`)
        .then(({data}) => {
            emit('delete', props.model)
        })
        .catch((err) => {
            console.log(err);
        });
};
</script>
