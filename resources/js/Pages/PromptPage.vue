<script setup>
import {InputText, Button, useToast, SelectButton, Message, InputGroupAddon, InputGroup} from "primevue"


import {useForm, usePage} from "@inertiajs/vue3";
import PromptBuilder from "../Components/Prompt/PromptBuilder.vue";
import PromptVariables from "../Components/Prompt/PromptVariables.vue";
import {computed, onMounted, reactive} from "vue";
import {FormViewType} from "../types/formViewType.ts";
import PromptTest from "../Components/Prompt/PromptTest.vue";
import Error from "../Common/Form/Error.vue";

import dot from "dot-object"

const page = usePage()
const toast = useToast()
const state = reactive({
    view: FormViewType.EDIT
})

const promptForm = useForm({
    sha: page.props.prompt.sha ?? null,
    content: page.props.prompt.content,
    path: null
})

const savePrompt = () => {
    promptForm.post(
        `/repository/${page.props.repository.id}/prompt`,
        {
            replace: false,
            onSuccess: () => {
                toast.add({
                    severity: 'info',
                    summary: 'Info',
                    detail: 'Prompt has saved successfully!',
                    life: 2000
                });
            }
        }
    )
}

const promptPathProxy = computed({
    set: (newValue) => {
        if (!newValue.endsWith('.prompt')) {
            newValue += '.prompt';
        }
        promptForm.path = newValue;
    },
    get: () => {
        if (!promptForm.path) {
            return ''
        }

        return promptForm.path.replace(/\.prompt$/, '')
    }
})

const formErrors = computed(() => {
    return dot.object(
        promptForm.errors
    );
})

const repositoryId = computed(() => {
    return page.props.repository.id
})

onMounted(() => {
    // Triggers .prompt completion
    promptPathProxy.value = page.props.path
})

const onTryingToTest = (newValue) => {
    if (newValue === FormViewType.TEST) {
        promptForm.post(
            `/repository/${repositoryId.value}/prompt/validate`,
            {
                onError: () => {
                    state.view = FormViewType.EDIT
                }
            }
        )
    }
}

</script>

<template>

    <div class="flex gap-4 justify-items-between py-2 items-center">
        <div>
            <InputGroup>
                <InputText
                    type="text"
                    data-testid="Input.name"
                    :invalid="promptForm.errors.path"
                    v-model="promptPathProxy" />
                <InputGroupAddon>.prompt</InputGroupAddon>
            </InputGroup>

            <Error :error="promptForm.errors.path" />
        </div>
        <div class="flex gap-2 items-center">
            <SelectButton v-model="state.view"
                          data-testid="Action.testEdit"
                          :allow-empty="false"
                          @update:modelValue="onTryingToTest"
                          :options="[FormViewType.TEST, FormViewType.EDIT]" />

            <form @submit.prevent="savePrompt">
                <Button label="Save"  icon="pi pi-save" type="submit" />
            </form>
        </div>
    </div>

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
                        v-model:prompt="promptForm.content.messages"
                    />
                </div>
                <PromptVariables
                    :errors="formErrors?.content?.variables"
                    v-model:messages="promptForm.content.messages"
                    v-model:variables="promptForm.content.variables"
                />
            </div>
        </template>
    </KeepAlive>

    <KeepAlive>
        <template v-if="state.view === FormViewType.TEST">
                <PromptTest
                    :path="promptForm.path"
                    :repository-id="repositoryId"
                    :prompt="promptForm.content"/>
        </template>
    </KeepAlive>
</template>
