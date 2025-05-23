<script setup>

import {Button, InputGroup, InputGroupAddon, InputText, Message, SelectButton} from "primevue";
import {Head} from "@inertiajs/vue3";
import Error from "./Error.vue";
import {useFileForm} from "../../stores/useFileForm.js";
import {storeToRefs} from "pinia";
import {computed} from "vue";
import {getBackPath} from "../../helpers/pathHelper.ts";
import {useAiGenerate} from "../../stores/useAiGenerate.js";

const props = defineProps({
    saveFileToastMsg: {type: String, default: ''},
    fileExtension: {type: String, default: ''}
})

const fileFormStore = useFileForm()
const { pathProxy, fileForm, state } = storeToRefs(fileFormStore)
const { saveFile } = fileFormStore

const backPath = computed(() => {
    return '/repository/' + state.value.repositoryId + '/files/' + getBackPath(fileForm.value.path, 1)
})

const generate = () => {
    useAiGenerate().openModal({
        content: fileForm.value.content,
        path: fileForm.value.path
    })
}
</script>

<template>
    <Head :title="fileForm.path" />

    <div class="flex gap-4 justify-items-between py-2 items-center">
        <Button
            as="a"
            label="Back"
            icon="pi pi-chevron-left"
            variant="outlined"
            :href="backPath"
        />

        <div>
            <InputGroup>
                <InputText
                    type="text"
                    data-testid="Input.name"
                    :invalid="fileForm.errors.path"
                    v-model="pathProxy" />
                <InputGroupAddon>.{{ fileExtension }}</InputGroupAddon>
            </InputGroup>

            <Error :error="fileForm.errors.path" />
        </div>

        <Message
            icon="pi pi-verified"
            severity="success" >
            {{fileForm.sha}}
        </Message>

        <div class="flex gap-2 items-center">
            <slot name="modes"></slot>

            <form @submit.prevent="saveFile(saveFileToastMsg)">
                <Button label="Save"  icon="pi pi-save" type="submit" />
            </form>

            <Button label="Generate"
                    @click="generate"
                    icon="fa-solid fa-wand-magic-sparkles" />
        </div>

    </div>
</template>
