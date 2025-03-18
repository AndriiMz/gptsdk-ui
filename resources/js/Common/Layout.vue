<script setup>

import { Button, Toast, Listbox, Avatar, ConfirmDialog } from "primevue";
import {usePage} from '@inertiajs/vue3'
import {computed, reactive} from "vue";
import Logo from "./Logo.vue";
import Modals from "./Modals.vue";
import GithubStar from "./Banner/GithubStar.vue";

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
    <div class="flex max-w-screen">
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

                <Button
                    as="a"
                    href="https://join.slack.com/t/saassdk/shared_invite/zt-2yg24cv91-IDyjYgoVgEeqUzuhF6qDYA"
                    icon="pi pi-question-circle"
                    variant="text"
                    target="_blank"
                    label="Help" />


                <GithubStar />

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
        <div style="width: calc(100vw - 260px); height: 100vh"
             class="flex flex-col gap-4 flex-grow-0 flex-shrink-0 p-2">
            <div class="rounded-lg border border-gray-100 bg-[#FEFEFE] dark:bg-[#202020] dark:border-b-gray-900  p-4 h-full overflow-auto">
                <slot />
            </div>

        </div>
    </div>

    <Toast />
    <Modals />

</template>

<style scoped>

</style>
