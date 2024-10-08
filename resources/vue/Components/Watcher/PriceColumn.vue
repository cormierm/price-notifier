<template>
    <td class="py-2 px-4">
        <div class="flex flex-col items-center text-lg">
            {{ amount ? amount : '-' }}
            <span class="text-xs text-center">{{ dateFromNow }}</span>
        </div>
    </td>
</template>

<script setup>
import moment from "moment";
import {computed} from "vue";

const props = defineProps({
    amount: {
        type: String,
        required: false
    },
    date: {
        type: String,
        required: false
    }
})

const dateFromNow = computed(() => {
    return props.date ? moment.utc(props.date).fromNow() : ''
})
</script>
