<script setup>

const props = defineProps({
    title: {type: String, default: null}
})

</script>

<template>
    <div class="max-w-4xl border-gray-50 dark:!border-gray-800 border rounded p-10 flex flex-col items-center gap-4">
        <div>
            <h3>{{title}}</h3>
        </div>
        <div>
            <slot name="actions"></slot>
        </div>
    </div>
</template>
