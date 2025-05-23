<script setup>

import JsonViewer from "../Json/JsonViewer.vue";
import {ResultViewMode} from "../../types/resultViewMode";
import {computed} from "vue";
import {prettifyAiResponse} from "../../helpers/aiResponse.ts";
import CopyButton from "../../Common/Button/CopyButton.vue";
import {Button} from "primevue";


const props = defineProps({
    id: {type: Number},
    output: {type: Object},
    viewMode: {type: String},
    aiVendor: {type: String}
})



const prettyView = computed(() => prettifyAiResponse(props.output, props.aiVendor))
const copyValue = computed(() => {
    if (props.viewMode === ResultViewMode.PRETTY) {
        return prettyView.value
    }

    return props.output
})
</script>

<template>
    <div class="relative">
        <div class="absolute end-1 top-1 flex gap-2 bg-gray-50 dark:bg-gray-800 border rounded p-1 border-gray-100 dark:border-gray-900">
            <CopyButton
                v-tooltip.left="`Copy Result`"
                :value="copyValue"
                label="" />
            <slot name="actions"></slot>
        </div>
    </div>

    <div class="rounded bg-gray-50 dark:bg-gray-800 p-2 flex-1 pt-5" v-if="viewMode === ResultViewMode.RAW">
        <JsonViewer :json="output" :id="`log-${id}`" />
    </div>
    <div class="rounded bg-gray-50 dark:bg-gray-800 p-2 flex-1 markdown-body pt-7"
         v-if="viewMode === ResultViewMode.PRETTY"
         v-html="prettyView"></div>

</template>

<style scoped>

</style>
