<script setup>
import {Button, Dialog, IftaLabel, InputText, Textarea} from "primevue";
import {useValuesModal} from "../../stores/useValuesModal.js";
import {storeToRefs} from "pinia";


const store = useValuesModal()
const { saveValues } = store
const { state } = storeToRefs(store)

</script>

<template>
    <Dialog v-model:visible="state.isModalOpen"
            modal
            :header="state.modalHeader"
            :style="{ width: '50vw', maxHeight: '70vh' }"
            @keydown.enter="saveValues">
        <div class="flex flex-col gap-4">
            <IftaLabel v-for="variable in state.variables">
                <label>{{variable.name}}</label>
                <InputText
                    size="small"
                    :data-testid="`Input.value-${variable.name}`"
                    type="text"
                    class="w-full"
                    v-model="state.values[variable.name]" />
            </IftaLabel>

        </div>

        <template #footer>
            <div class="text-left">
                <Button
                    icon="pi pi-check"
                    label="Apply"
                    data-testid="Action.applyValues"
                    @click.prevent="saveValues" />
            </div>
        </template>
    </Dialog>
</template>
