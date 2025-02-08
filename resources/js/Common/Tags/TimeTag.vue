<script setup>
import {Tag} from "primevue";
import moment from "moment";
import {momentToElapsed} from "../../helpers/dateHelper.js";
import {computed} from "vue";

const props = defineProps({
    time: {type: String}
});

const elapsedTime = computed(() => {
    return momentToElapsed(moment(props.time))
});

</script>

<template>
    <Tag :value="elapsedTime" />
</template>
