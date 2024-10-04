<template>
    <select
        class="rounded-md"
        :value="value"
        @input="updateInterval($event.target.value)"
        :disabled="loading"
    >
        <option
            v-for="option in intervals"
            :value="option.id"
            :key="option.id">
            {{ option.name }}
        </option>
    </select>
</template>

<script setup>
import {ref} from 'vue';
import axios from 'axios';

const emit = defineEmits();
const props = defineProps({
    intervals: {
        type: Array,
        required: true
    },
    watcherId: {
        type: Number,
        required: true,
    },
    value: {
        type: [Number, String],
        default: null
    }
});

const loading = ref(false);

const updateInterval = async (intervalId) => {
    loading.value = true;
    try {
        const { data } = await axios.put(`/watcher/${props.watcherId}`, {
            interval_id: intervalId,
        });
        emit('update', data.watcher);
    } catch (err) {
        console.error('Error updating watcher interval!', err);
    } finally {
        loading.value = false;
    }
};
</script>
