<script setup>

import {Dialog, Textarea, Button, InputText} from "primevue";
import {reactive, ref} from "vue";

const visible = ref(false)
const emits = defineEmits(['onApply'])
const props = defineProps({
    dataTestid: {type: String, default: ''}
})

const jsonData = reactive({
    json: "",
    parsedJson: {},
    error: null
})
const pasteJson = () => {
    visible.value = true

}
const applyJson = (json) => {
    try {
        jsonData.parsedJson = JSON.parse(json)
        if (
            !jsonData.parsedJson[0].content ||
            !jsonData.parsedJson[0].role
        ) {
            throw new Error('Invalid JSON. JSON input should contain array of object with role, content keys.')
        }


        jsonData.error = null
        emits('onApply', jsonData.parsedJson)
        visible.value = false
    } catch (error) {
        jsonData.error = error
    }

}

</script>

<template>
    <slot name="button" >
        <Button icon="pi pi-file-import"
                size="small"
                variant="text"
                :data-testid="dataTestid"
                label="Paste JSON"
                @click.prevent="pasteJson"
        />
    </slot>

    <Dialog v-model:visible="visible" modal header="Paste JSON"
            :style="{ width: '50vw', maxHeight: '70vh' }"
            @keydown.enter="applyJson(jsonData.json)">
        <div class="flex flex-col gap-4">
            <Textarea data-testid="Input.json" label="Json" v-model="jsonData.json"/>
        </div>

        <template #footer>
            <div class="text-left">
                <Button
                    icon="pi pi-check"
                    label="Apply"
                    data-testid="Action.paste"
                    @click.prevent="applyJson(jsonData.json)" />
            </div>
        </template>

    </Dialog>
</template>

