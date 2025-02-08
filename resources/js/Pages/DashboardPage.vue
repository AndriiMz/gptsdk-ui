<script setup>

import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";
import LayoutTitle from "../Common/LayoutTitle.vue";
import {Card, Button} from 'primevue'
import StatusTag from "../Common/Tags/StatusTag.vue";
import {StatusType} from "../types/statusType.ts";

const page = usePage()
const user = computed(() => {
    return page.props.user
})

const repositories = computed(() => page.props.repositories)
</script>

<template>
    <LayoutTitle>
        Hello {{user.name.split(' ')[0]}} 👋
    </LayoutTitle>

    <div class="flex flex-col gap-4" v-if="repositories.length">
        <h2 class="text-xl pt-3">
            Your repositories
        </h2>

        <div>
            <Card class="max-w-96" v-for="repository in repositories">
                <template #title>
                    {{repository.name}}
                </template>
                <template #content>
                    <div class="flex flex-col gap-2">
                        <p class="m-0 flex gap-2 items-center">
                            <i class="pi pi-user"></i>
                            <span>{{repository.owner}}</span>
                        </p>
                        <p class="flex gap-2 items-center">
                            <i class="pi pi-link"></i>
                            <span>{{repository.url}}</span>
                        </p>
                        <p>
                            <StatusTag
                                :status-label="repository.subscriptionStatus"
                                :status="repository.subscriptionStatus === 'paid' ? StatusType.SUCCESS : StatusType.INFO" />
                        </p>
                    </div>
                </template>
                <template #footer>
                    <div class="flex gap-4 mt-1">
                        <Button as="a"
                                :href="`repository/${repository.id}/prompts/`"
                                icon="pi pi-arrow-down-right"
                                label="Open" class="w-full" />

                        <Button as="a"

                                :href="`repository/${repository.id}/settings`"
                                icon="pi pi-pencil"
                                label="Edit" class="w-full" />
                    </div>
                </template>

            </Card>
        </div>

    </div>

    <div v-if="!repositories.length">
        <div class="max-w-4xl border-gray-50 dark:!border-gray-800 border rounded p-10 flex flex-col items-center gap-4">
            <div>
                <h3>You don't have any Repositories yet.</h3>
            </div>
            <div>
                <Button
                    as="a"
                    href="/repository/new"
                    icon="pi pi-plus"
                    label="Add Repository" />
            </div>
        </div>
    </div>
</template>
