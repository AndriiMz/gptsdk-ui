<script setup>

import {Button, Dialog, IftaLabel, InputText, Textarea, Message, ProgressSpinner} from "primevue";
import {storeToRefs} from "pinia";
import {useAiGenerate} from "../../stores/useAiGenerate.js";
import {VariableType} from "../../types/variableType";
import CopyButton from "../../Common/Button/CopyButton.vue";

const aiGenerateStore = useAiGenerate()
const { state, variables } = storeToRefs(aiGenerateStore)

</script>

<template>
    <Dialog v-model:visible="state.isOpen"
            modal
            :style="{ width: '50vw', maxHeight: '70vh' }"
            header="AI Generate">


        <div v-if="!state.form.aiApiKey">
            <Message
                icon="pi pi-exclamation-circle"
                severity="error">
                You don't have an API key to run this action.
                Visit <a href="/ai_api_key">AI API Keys</a> to set up the keys you want to use for AI generation.
            </Message>
        </div>

        <div v-else class="py-2 flex flex-col gap-2">
            <template v-for="(variable) in variables" v-if="!state.result">
                <IftaLabel>
                    <template v-if="variable.type === VariableType.TEXT">
                        <Textarea
                            :data-testid="`Input.value-${variable.name}`"
                            class="w-full"
                            rows="20"
                            :id="`generate-${variable.name}`"
                            v-model="state.form.values[variable.name]" />
                    </template>
                    <template v-else>
                        <InputText
                            :data-testid="`Input.value-${variable.name}`"
                            class="w-full"
                            :id="`generate-${variable.name}`"
                            v-model="state.form.values[variable.name]" />
                    </template>

                    <label :for="`generate-${variable.name}`">{{variable.name}}</label>
                </IftaLabel>
            </template>

            <template v-if="state.result">
                <div class="markdown-body"
                     v-html="state.result"></div>
            </template>
        </div>

        <template #footer>
            <div class="text-left pt-2 flex gap-2 items-center">
                <template v-if="state.result">
                    <CopyButton :value="state.result" />
                    <Button icon="pi pi-file-import"
                            size="small"
                            variant="text"
                            label="Paste"
                            v-tooltip.top="state.pasteTooltip"
                            @click.prevent="aiGenerateStore.paste(state.result)"
                    />
                </template>


                <Message
                    v-if="state.form.aiApiKey"
                    v-tooltip.top="`Api Key For AI Generation`"
                    icon="pi pi-verified"
                    severity="success" >
                    {{state.form.aiApiKey.name}} ({{state.form.aiApiKey.defaultModel}})
                </Message>

                <ProgressSpinner
                    v-if="state.isLoading"
                    style="width: 15px; height: 15px" strokeWidth="4"
                    fill="transparent" />

                <Button
                    icon="fa-solid fa-wand-magic-sparkles"
                    label="Generate"
                    @click.prevent="aiGenerateStore.generate()" />
            </div>
        </template>
    </Dialog>
</template>
