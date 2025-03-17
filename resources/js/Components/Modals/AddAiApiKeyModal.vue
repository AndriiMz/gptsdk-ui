<script setup>

import {Button, Dialog, IftaLabel, InputText, Message, Select} from "primevue";
import {useAiApiKeys} from "../../stores/useAiApiKeys.js";
import {storeToRefs} from "pinia";
import {AiVendorType} from "../../types/aiVendorType.ts";
import Error from "../../Common/Form/Error.vue";

const store = useAiApiKeys()
const {state, aiApiKeyForm} = storeToRefs(store)
const { setKeyName, saveKey } = store

</script>

<template>
    <Dialog v-model:visible="state.isAddApiKeyModalOpen"
            modal
            :style="{ width: '50vw', maxHeight: '70vh' }"
            header="Create AI API Key">
        <div class="flex flex-col gap-4">
            <IftaLabel>
                <Select
                    inputId="dd-ai-vendor"
                    size="small"
                    :options="[AiVendorType.OPENAI, AiVendorType.ANTHROPIC]"
                    type="text"
                    class="w-full"
                    v-model="aiApiKeyForm.aiVendor" />
                <label for="dd-ai-vendor">AI</label>
            </IftaLabel>



            <div class="flex flex-col gap-1">
                <IftaLabel>
                    <label>Api Key</label>
                    <InputText
                        size="small"
                        type="text"
                        class="w-full"
                        :invalid="aiApiKeyForm.errors.key"
                        @update:modelValue="setKeyName"
                        v-model="aiApiKeyForm.key"/>
                </IftaLabel>

                <Error :error="aiApiKeyForm.errors.key" />

                <Message
                    icon="pi pi-lock"
                    severity="success" >
                    API key has been encrypted using AES-256
                </Message>
            </div>


            <IftaLabel>
                <label>Name</label>
                <InputText
                    size="small"
                    type="text"
                    class="w-full"
                    :invalid="aiApiKeyForm.errors.name"
                    v-model="aiApiKeyForm.name"/>

                <Error :error="aiApiKeyForm.errors.name" />
            </IftaLabel>

        </div>

        <template #footer>
            <div class="text-left">
                <Button
                    icon="pi pi-save"
                    label="Save"
                    @click="saveKey"
                    />
            </div>
        </template>

    </Dialog>
</template>

<style scoped>

</style>
