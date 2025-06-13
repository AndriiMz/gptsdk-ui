<script setup>
import { computed, defineProps } from 'vue'
import { useClipboard } from "@vueuse/core";
import { Button } from "primevue";

const props = defineProps({
    value: '',
    label: {type: String, default: 'Copy'},
})

const emit = defineEmits(['copyEvent'])
const { copy: copy, copied: copied, isSupported } = useClipboard()

const copyValue = () => {
    if (isSupported.value) {
        copy(props.value)
    } else {
        const input = document.createElement('input')
        input.value = props.value
        document.body.appendChild(input)
        input.select()
        document.execCommand('copy')
        document.body.removeChild(input)

        copied.value = true

        setTimeout(() => {
            copied.value = false
        }, 1000)
    }
}

const icon = computed(() => {
    return copied.value ? 'pi pi-check' : 'pi pi-copy'
})

const label = computed(() => {
    return copied.value ? 'Copied!' : props.label
})

</script>

<template>
    <Button href="#"
            size="small"
            variant="text"
            :icon="icon"
            :label="label"
            @click.prevent="copyValue"/>
</template>


