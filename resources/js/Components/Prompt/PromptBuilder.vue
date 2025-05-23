<script setup>

import { Button } from "primevue"
import PromptMessage from "./PromptMessage.vue";
import CopyButton from "../../Common/Button/CopyButton.vue";
import PasteJsonModal from "../Modals/PasteJsonModal.vue";
import {computed, defineProps, defineEmits} from "vue"
import {usePromptForm} from "../../stores/usePromptForm.js";
import CopyRenderedPrompt from "./CopyRenderedPrompt.vue";


const props = defineProps({
    prompt: {type: Array},
    errors: {type: Array, default: []}
})
const emits = defineEmits(['update:prompt', 'onInput'])

const addMessage = () => {
    const newPrompt = []
    for (const promptIndex in props.prompt) {
        newPrompt.push({
            role: props.prompt[promptIndex].role,
            content: props.prompt[promptIndex].content
        })
    }

    newPrompt.push({
        role: 'user',
        content: ''
    })

    emits(
        'update:prompt',
        newPrompt
    )
}

const deleteMessage = (index) => {
    const newPrompt = [...props.prompt];
    newPrompt.splice(index, 1);

    emits(
        'update:prompt',
        newPrompt
    )
}

const updateMessage = (message, index) => {
    const newPrompt = []
    for (const promptIndex in props.prompt) {
        if (promptIndex == index) {
            newPrompt.push({
                role: message.role,
                content: message.content
            })
            continue;
        }

        newPrompt.push({
            role: props.prompt[promptIndex].role,
            content: props.prompt[promptIndex].content
        })
    }

    emits(
        'update:prompt',
        newPrompt
    )
}

const copiedValue = computed(() => {
    return JSON.stringify([...props.prompt])
})

</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex gap-2">
            <CopyButton label="Copy Json"
                        data-testid="Action.copyJson"
                        :value="copiedValue"/>
            <CopyRenderedPrompt />
            <PasteJsonModal
                data-testid="Action.pasteJson"
                @on-apply="(text) => emits('update:prompt', text)"/>
        </div>

        <PromptMessage v-for="(message, index) in prompt"
                       :model-value="message"
                       :key="index"
                       :index="index"
                       data-testid="ListItem.messages"
                       :errors="errors[index] ?? {}"
                       @update:modelValue="(m) => updateMessage(m, index)"
                       @on-change="usePromptForm().onMessageChange()"
                       @on-input="(e) => usePromptForm().setPromptMessageFocus(e.target.selectionStart, index)"
        >
            <template #actions>
                <Button
                    size="small"
                    severity="danger"
                    variant="text"
                   icon="pi pi-trash"
                   aria-label="Delete message"
                   @click.prevent="deleteMessage(index)" />
            </template>
        </PromptMessage>

        <div class="flex justify-end gap-2 items-center">
            <Button label="Add Message"
                    icon="pi pi-plus"
                    data-testid="Action.addMessage"
                    size="small"
                    @click.prevent="addMessage"/>
        </div>
    </div>
</template>
