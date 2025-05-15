<script setup>
import {Button, Dialog, InputGroup, InputGroupAddon, InputText, InputNumber} from "primevue";
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
        <div class="flex flex-col">
            <template v-for="(variable, index) in state.variables">
                <InputGroup>
                    <InputText class="!max-w-[150px]" v-model="state.variables[index].name" />
                    <template v-if="variable.type === VariableType.INT || variable.type === VariableType.FLOAT">
                        <InputNumber
                            :data-testid="`Input.value-${variable.name}`"
                            class="w-full"
                            v-model="state.values[index]" />
                    </template>
                    <template v-else>
                        <InputText
                            :data-testid="`Input.value-${variable.name}`"
                            class="w-full"
                            v-model="state.values[index]" />
                    </template>

                    <InputGroupAddon v-if="!state.isFieldsImmutable">
                        <Button icon="pi pi-times"
                                @click="state.variables.splice(index, 1)"
                                severity="danger" variant="text" />
                    </InputGroupAddon>
                </InputGroup>
            </template>

            <div v-if="!state.isFieldsImmutable">
                <Button icon="pi pi-plus"
                        @click="state.variables.push({name: ''})"
                        label="Add Property"
                        variant="text" />
            </div>
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
