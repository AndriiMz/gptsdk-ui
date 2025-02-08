<script setup>

import JsonViewerRow from "./JsonViewerRow.vue";
import {defineStore} from "pinia";
import {reactive} from "vue";

const props = defineProps({
    json: {type: Object},
    id: {type: String}
})

const store = defineStore(`jsonViewer.${props.id}`, () => {
    const state = reactive({
        collapsedPath: {}
    })

    const togglePath = (path) => {
        state.collapsedPath[path] = !state.collapsedPath[path]
    }

    return {
        togglePath,

        state
    }
})()

</script>

<template>
    <div class="flex flex-col">
        <JsonViewerRow :rows="json" :store="store" />
    </div>
</template>
