<template>
    <button
        class="w-10 h-10 border rounded text-center bg-red-500 text-white"
        @click="deleteConfirmation"
    >
        <FontAwesomeIcon :icon="faTrash"/>
    </button>
    <DeleteDialog
        v-model:isOpen="isOpen"
        label="Domain Query"
        :info="template.domain"
        @confirmed="deleteTemplate"
    ></DeleteDialog>
</template>

<script setup>
import {ref} from "vue";
import DeleteDialog from "@Components/Form/DeleteDialog.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {faTrash} from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    template: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['delete']);

const isOpen = ref(false);

const deleteConfirmation = () => {
    isOpen.value = true;
};

const deleteTemplate = () => {
    axios.delete(`/template/${props.template.id}`)
        .then(({data}) => {
            emit('delete', props.template)
        })
        .catch((err) => {
            console.log(err);
        });
};
</script>
