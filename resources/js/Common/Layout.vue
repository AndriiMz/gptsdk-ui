<script setup>

import { Button, Toast, Listbox, Avatar, ConfirmDialog } from "primevue";
import {usePage} from '@inertiajs/vue3'
import {computed, reactive} from "vue";
import AddAiApiKeyModal from "../Components/Modals/AddAiApiKeyModal.vue";
import ValuesModal from "../Components/Modals/ValuesModal.vue";
import SubscriptionModal from "../Components/Modals/SubscriptionModal.vue";
import Logo from "./Logo.vue";
import Modals from "./Modals.vue";

const page = usePage()
const repositories = computed(() => page.props.repositories)

const state = reactive({
    selectedRepositoryId: page.props?.repository?.id
})

const user = computed(() => {
    return page.props.user
})

const goToRepository = (repositoryId) => {
    if (!repositoryId) {
        repositoryId = state.selectedRepositoryId
    }

    window.location.pathname = `/repository/${repositoryId}/prompts`
}
</script>

<template>
    <div class="flex gap-4 max-w-screen">
        <div class="flex flex-col justify-between gap-2 p-3 w-[250px] h-screen flex-grow-0 flex-shrink-0">
            <div class="flex flex-col gap-2">
                <div class="flex justify-center">
                    <a href="/">
                        <Logo />
                    </a>
                </div>

                <Listbox :model-value="state.selectedRepositoryId"
                         :options="repositories"
                         optionLabel="name"
                         optionValue="id"
                         checkmark

                         @update:modelValue="goToRepository"
                         :highlightOnSelect="false" />

                <Button
                    as="a"
                    href="/repository/new"
                    icon="pi pi-plus"
                    label="Add Repository" />
            </div>

            <div class="flex flex-col gap-2">
                <Button
                    as="a"
                    href="/ai_api_key"
                    icon="pi pi-key"
                    variant="text"
                    label="AI Api Keys" />

                <Button
                    as="a"
                    href="/billing"
                    variant="text"
                    icon="pi pi-credit-card"
                    label="Billing" />


                <div class="rounded p-3 bg-gray-50 dark:bg-gray-800 flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <div>
                            <Avatar :image="`https://www.gravatar.com/avatar/${user.gravatar}?d=mp`"
                                    class="flex items-center justify-center mr-2"
                                    size="large"
                                    shape="circle" />
                        </div>

                        <div class="text-ellipsis overflow-hidden text-sm">
                            {{user.name}}
                            {{user.email}}
                        </div>
                    </div>

                    <Button
                        as="a"
                        href="/logout"
                        variant="outlined"
                        class="w-full"
                        icon="pi pi-sign-out"
                        label="Logout" />
                </div>
            </div>
        </div>
        <div style="width: calc(100vw - 290px);" class="flex flex-col gap-4 flex-grow-0 flex-shrink-0">
            <slot />
        </div>
    </div>

    <Toast />
    <Modals />

</template>

<style scoped>

</style>
