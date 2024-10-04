<template>
    <a :href="`/watcher/${watcherId}/edit`">
        <button class="w-10 h-10 border rounded text-center">
            <FontAwesomeIcon :icon="faPenToSquare"/>
        </button>
    </a>
</template>
<script setup>
import {faPenToSquare} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    watcherId: {
        type: String,
        required: true
    }
})
</script>
