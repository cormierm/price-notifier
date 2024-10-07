<template>
    <a href="/watcher/create" title="Add a new watcher">
        <button class="w-8 h-8 border rounded border-transparent text-white bg-blue-500 hover:bg-blue-600">
            <FontAwesomeIcon :icon="faPlus"/>
        </button>
    </a>
</template>
<script setup>
import {faPlus} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
</script>
