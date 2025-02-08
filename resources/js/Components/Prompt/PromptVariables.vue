<script setup>
import { defineProps, defineEmits } from "vue";
import { Select, Textarea, IftaLabel, Button, InputText } from "primevue";
import {VariableType} from "../../types/variableType.ts";
import {usePromptForm} from "../../stores/usePromptForm.js";
import {storeToRefs} from "pinia";
import {useCloned} from "@vueuse/core";
import Error from "../../Common/Form/Error.vue";

const promptForm = usePromptForm()
const { state: promptFormState } = storeToRefs(promptForm)

const props = defineProps({
    variables: {type: Array},
    messages: {type: Array},
    errors: {type: Array}
})

const emits = defineEmits([
    'update:variables',
    'update:messages'
])

const addVariable = () => {
    emits(
        'update:variables',
        props.variables.concat(
            {
                name: '',
                type: VariableType.STRING,
                note: ''
            }
        )
    )
}

const deleteVariable = (index) => {
    emits(
        'update:variables',
        props.variables.filter((_, i) => i !== index)
    )
}

const insertVariable = ({name}) => {
    const oldContent = props.messages[promptFormState.value.focusYIndex].content
    const { cloned: newMessages } = useCloned(props.messages)
    newMessages.value[promptFormState.value.focusYIndex].content = [
        oldContent.slice(0, promptFormState.value.focusXIndex),
        `[[${name}]]`,
        oldContent.slice(promptFormState.value.focusXIndex)
    ].join('')

    emits('update:messages', newMessages.value);
}
</script>

<template>
<div class="flex-1 flex-grow flex flex-col gap-4">
    <h4 class="text-lg">Variables</h4>
    <div class="flex flex-col gap-2">
        <div class="border border-gray-100 dark:border-gray-800 p-2 rounded"
             data-testid="ListItem.variables"
             v-for="(variable, index) in props.variables">

            <div class="flex flex-col gap-2">
                <div class="flex gap-2">
                    <div>
                        <IftaLabel class="flex-1">
                            <label>Name</label>
                            <InputText
                                :invalid="errors?.[index]?.name"
                                size="small"
                                type="text"
                                class="w-full"
                                v-model="props.variables[index].name" />
                        </IftaLabel>

                        <Error :error="errors?.[index]?.name" />
                    </div>


                    <IftaLabel class="flex-1">
                        <Select
                            :inputId="`dd-type-${index}`"
                            size="small"
                            :options="[{name: 'String', key: VariableType.STRING}]"
                            optionValue="key"
                            optionLabel="name"
                            type="text"
                            class="w-full"
                            v-model="props.variables[index].type" />
                        <label :for="`dd-type-${index}`">Type</label>
                    </IftaLabel>
                </div>

                <IftaLabel>
                    <label>Note</label>
                    <Textarea
                        class="w-full"
                        size="small"
                        v-model="props.variables[index].note" />
                </IftaLabel>


                <div class="flex gap-2">
                    <Button
                        size="small"
                        variant="text"
                        icon="pi pi-file-import"
                        aria-label="Insert Variable"
                        @click.prevent="insertVariable({name: variable.name})" />

                    <Button
                        size="small"
                        variant="text"
                        severity="danger"
                        icon="pi pi-trash"
                        aria-label="Delete Variable"
                        @click.prevent="deleteVariable(index)" />
                </div>
            </div>
        </div>
    </div>
    <div>
        <Button label="Add Variable"
                icon="pi pi-plus"
                size="small"
                data-testid="Action.addVariable"
                @click.prevent="addVariable"/>
    </div>
</div>
</template>
