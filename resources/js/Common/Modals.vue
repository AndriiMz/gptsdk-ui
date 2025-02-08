<script setup>

import {ConfirmDialog} from "primevue";
import ValuesModal from "../Components/Modals/ValuesModal.vue";
import SubscriptionModal from "../Components/Modals/SubscriptionModal.vue";
import AddAiApiKeyModal from "../Components/Modals/AddAiApiKeyModal.vue";
</script>

<template>
    <ConfirmDialog />
    <AddAiApiKeyModal />
    <ValuesModal />
    <SubscriptionModal />
</template>

<style scoped>

</style>
