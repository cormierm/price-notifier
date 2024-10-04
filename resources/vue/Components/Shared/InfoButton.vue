<template>
    <a :href="`/watcher/${watcherId}`">
        <button class="w-10 h-10 border rounded text-center">
            <FontAwesomeIcon :icon="faCircleInfo"/>
        </button>
    </a>
</template>

<script setup>
import {faCircleInfo} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    watcherId: {
        type: String,
        required: true
    }
})
</script>
