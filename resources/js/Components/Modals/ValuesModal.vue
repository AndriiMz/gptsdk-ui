<script setup>
import {Button, Dialog, IftaLabel, InputText, InputNumber, Textarea} from "primevue";
import {useValuesModal} from "../../stores/useValuesModal.js";
import {storeToRefs} from "pinia";
import {VariableType} from "../../types/variableType.ts";


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
                <template v-if="variable.type === VariableType.INT || variable.type === VariableType.FLOAT">
                    <InputNumber
                        size="small"
                        :input-id="`Input.value-${variable.name}`"
                        :data-testid="`Input.value-${variable.name}`"
                        class="w-full"
                        v-model="state.values[variable.name]" />
                </template>
                <template v-else>
                    <InputText
                        size="small"
                        :data-testid="`Input.value-${variable.name}`"
                        class="w-full"
                        v-model="state.values[variable.name]" />
                </template>
                <label :for="`Input.value-${variable.name}`">{{variable.name}}</label>
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
