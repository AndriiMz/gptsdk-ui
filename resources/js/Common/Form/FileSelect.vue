<script setup>
import {TreeSelect} from "primevue";
import {computed, onMounted, ref} from "vue";
import {storeToRefs} from "pinia";
import {useFiles} from "../../stores/useFiles";

const props = defineProps({
    modelValue: {type: String, default: ''},
})

const emits = defineEmits(['update:modalValue'])

const modelValueProxy = computed({
    get: () => {
        return {[props.modelValue]: true}
    },
    set: (val) => {
        emits(
            'update:modelValue',
            Object.keys(val)[0]

        )
    }
})

const filesStore = useFiles()
const {state} = storeToRefs(filesStore)
const { loadFilesByNode } = filesStore

onMounted(() => {
    useFiles().initFileOptions()
})

</script>


<template>
    <TreeSelect v-model="modelValueProxy"
                loadingMode="icon"
                :options="state.fileOptions"
                filter
                filterMode="lenient"
                @node-expand="loadFilesByNode"
                placeholder="Select File"
                class="md:w-80 w-full" />
</template>
