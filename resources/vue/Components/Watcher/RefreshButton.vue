<template>
    <button
        class="h-10 w-10 border rounded-md"
        :disabled="loading"
        @click="refresh(watcherId)"
    >
        <Spinner v-if="loading" class="ml-[8px]"/>
        <FontAwesomeIcon v-else :icon="faRotateRight" />
    </button>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import {faRotateRight} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Spinner from "@Components/Form/Spinner.vue";

const props = defineProps({
    watcherId: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['update']);

const loading = ref(false);

const refresh = async (id) => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/watcher/${id}/sync`);
        emit('update', data.watcher);
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};
</script>
