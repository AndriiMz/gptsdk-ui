<script setup>

import {ConfirmDialog} from "primevue";
import ValuesModal from "../Components/Modals/ValuesModal.vue";
import SubscriptionModal from "../Components/Modals/SubscriptionModal.vue";
import AiApiKeyModal from "../Components/Modals/AiApiKeyModal.vue";
import AiGenerationModal from "../Components/Modals/AiGenerationModal.vue";
import NewFileModal from "../Components/Modals/NewFileModal.vue";
</script>

<template>
    <ConfirmDialog />
    <AiApiKeyModal />
    <ValuesModal />
    <SubscriptionModal />
    <AiGenerationModal />
    <NewFileModal />
</template>

<style scoped>

</style>
