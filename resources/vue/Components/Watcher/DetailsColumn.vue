<template>
    <td class="py-2 px-4">
        <div class="min-w-96">
            <div class="text-blue-600 dark:text-blue-300 space-x-1">
                <a :href="`/watcher/${watcher.id}`">{{ watcher.name }}</a>
                <a :href="watcher.url" target="_blank">
                    <FontAwesomeIcon :icon="faLink"/>
                </a>
            </div>
            <span class="text-sm text-gray-500 dark:text-gray-300">{{ watcher.url_domain }}</span>
        </div>
    </td>
</template>

<script setup>
import {faLink} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    watcher: {
        type: Object,
        required: true
    }
})
</script>
