<script setup>
import {Message, SelectButton} from "primevue"

import {usePage} from "@inertiajs/vue3";
import PromptBuilder from "../Components/Prompt/PromptBuilder.vue";
import PromptVariables from "../Components/Prompt/PromptVariables.vue";
import {onMounted, reactive} from "vue";
import {FormViewType} from "../types/formViewType.ts";
import PromptTest from "../Components/Prompt/PromptTest.vue";

import PromptMocks from "../Components/Prompt/PromptMocks.vue";
import {usePromptForm} from "../stores/usePromptForm.js";
import {storeToRefs} from "pinia";
import {useFileForm} from "../stores/useFileForm.js";
import FileFormHeader from "../Common/Form/FileFormHeader.vue";

const page = usePage()
const state = reactive({
    view: FormViewType.EDIT
})

const fileFormStore = useFileForm()
const { state: fileFormState, formErrors } = storeToRefs(fileFormStore)
const { fileForm } = fileFormStore

onMounted(() => {
    usePromptForm().loadMocks()
})

const onTryingToTest = (newValue) => {
    if (newValue === FormViewType.TEST) {

        fileForm
            .transform((data) => ({
                ...data,
                content: JSON.stringify(data.content)
            }))
            .post(
                `/repository/${fileFormState.value.repositoryId}/file/validate`,
                {
                    onError: () => {
                        state.view = FormViewType.EDIT
                    }
                },
            )
    }
}

const tabs = [
    {label: FormViewType.TEST, icon: 'pi pi-play'},
    {label:FormViewType.MOCKS, icon: 'pi pi-flag'},
    {label:FormViewType.EDIT, icon: 'pi pi-pencil'}
]

</script>

<template>
    <FileFormHeader file-extension="prompt" save-file-toast-msg="Prompt has saved successfully!">
        <template #modes>
            <SelectButton v-model="state.view"
                          data-testid="Action.testEdit"
                          :allow-empty="false"
                          @update:modelValue="onTryingToTest"
                          optionLabel="label"
                          optionValue="label"
                          :options="tabs" >
                <template #option="slotProps">
                    <i :class="slotProps.option.icon"></i>
                    {{slotProps.option.label}}
                </template>
            </SelectButton>
        </template>
    </FileFormHeader>

    <div v-if="page.props.isBrokenPromptFile">
        <Message
            icon="pi pi-info-circle"
            severity="secondary">
            Prompt file is invalid. Prompt form has been reset to defaults.
        </Message>
    </div>

    <KeepAlive>
        <template v-if="state.view === FormViewType.EDIT">
            <div class="flex gap-8">
                <div class="flex flex-col gap-8 flex-1 flex-grow">
                    <PromptBuilder
                        :errors="formErrors?.content?.messages"
                        v-model:prompt="fileForm.content.messages"
                    />
                </div>
                <PromptVariables
                    :errors="formErrors?.content?.variables"
                    v-model:messages="fileForm.content.messages"
                    v-model:variables="fileForm.content.variables"
                />
            </div>
        </template>
    </KeepAlive>

    <KeepAlive>
        <template v-if="state.view === FormViewType.TEST">
                <PromptTest />
        </template>
    </KeepAlive>

    <KeepAlive>
        <template v-if="state.view === FormViewType.MOCKS">
            <PromptMocks />
        </template>
    </KeepAlive>
</template>
