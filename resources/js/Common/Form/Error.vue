<script setup>

const props = defineProps({
    error: {type: String, default: null}
})
</script>

<template>
    <div class="text-red-500" v-if="error">
        {{ error }}
    </div>
</template>
