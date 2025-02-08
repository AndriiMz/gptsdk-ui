<script setup>
import {Tag} from 'primevue'
import {computed} from "vue";
import {StatusType} from "../../types/statusType.ts";

const props = defineProps({
    status: {type: String, default: ''},
    statusLabel: {type: String, default: null}
})

const severity = computed(() => {
    switch (props.status) {
        case StatusType.SUCCESS:
            return 'success'
        case StatusType.INFO:
            return 'info'
        case StatusType.ERROR:
            return 'danger'
    }

    return 'info'
})


</script>

<template>
    <Tag :severity="severity" :value="statusLabel ?? status" />
</template>
