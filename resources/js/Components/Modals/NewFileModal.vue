<script setup lang="ts">
import {Button, Dialog, InputGroup, InputGroupAddon, InputText} from "primevue";
import {storeToRefs} from "pinia";
import {useFiles} from "../../stores/useFiles.js";
import {useRepository} from "../../stores/useRepository.js";
import {computed} from "vue";

const filesStore = useFiles()
const { state } = storeToRefs(filesStore)

const repositoryStore = useRepository()

const { repository } = storeToRefs(repositoryStore)

const createFile = () => {
    window.location.pathname = `/repository/${repository.value.id}/file/${state.value.newFileForm.path}`
}


const pathProxy = computed({
    set: (newValue) => {
        if (!newValue.endsWith('.' + state.value.newFileForm.extension)) {
            newValue += '.' + state.value.newFileForm.extension
        }

        state.value.newFileForm.path = newValue;
    },
    get: () => {
        if (!state.value.newFileForm.path) {
            return ''
        }

        return state.value.newFileForm.path.replace(new RegExp('\\.' + state.value.newFileForm.extension + '$'), '')
    }
})

</script>

<template>
    <Dialog v-model:visible="state.newFileOpen"
            modal
            :style="{ width: '50vw', maxHeight: '70vh' }"
            header="New File">

        <InputGroup>
            <InputText
                type="text"
                data-testid="Input.name"
                v-model="pathProxy" />
            <InputGroupAddon>.{{ state.newFileForm.extension }}</InputGroupAddon>
        </InputGroup>



        <template #footer>
            <div class="text-left pt-2 flex gap-2 items-center">
                <Button
                    icon="pi pi-plus"
                    label="Create"
                    :disabled="!state.newFileForm.path.length"
                    @click.prevent="createFile" />
            </div>
        </template>
    </Dialog>

</template>
