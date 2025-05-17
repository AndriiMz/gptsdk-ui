<script setup>



import {Button} from "primevue";
import {useClipboard} from "@vueuse/core";
import {computed} from "vue";
import {usePromptForm} from "../../stores/usePromptForm.js";
import {SAVE_VALUES_ACTION, useValuesModal} from "../../stores/useValuesModal.js";

const promptFormStore = usePromptForm()
const { promptForm, renderPrompt } = promptFormStore

const { openValuesModal, $onAction: onValuesAction } = useValuesModal()

const { copy: copy, copied: copied } = useClipboard()

onValuesAction(
    ({name, after}) => {
        if (name === SAVE_VALUES_ACTION) {
            after(async ({values}) => {
                copy(JSON.stringify(await renderPrompt(values)))
            })
        }
    }
)

const icon = computed(() => {
    return copied.value ? 'pi pi-check' : 'pi pi-copy'
})

const label = computed(() => {
    return copied.value ? 'Copied!' : 'Copy Rendered Prompt'
})


</script>

<template >
    <Button href="#"
            size="small"
            variant="text"
            :icon="icon"
            :label="label"
            @click.prevent="openValuesModal({
                variables: promptForm.content.variables,
                values: [],
                payload: {},
                modalHeader: 'Set Variable Values'
            })"/>
</template>
