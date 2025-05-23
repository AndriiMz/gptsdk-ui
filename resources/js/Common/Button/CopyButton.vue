<script setup>
import { computed, defineProps } from 'vue'
import { useClipboard } from "@vueuse/core";
import { Button } from "primevue";

const props = defineProps({
    value: '',
    label: {type: String, default: 'Copy'},
})

const emit = defineEmits(['copyEvent'])
const { copy: copy, copied: copied } = useClipboard()

const copyValue = () => {
    copy(props.value)
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


