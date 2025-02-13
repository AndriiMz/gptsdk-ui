<script setup>

import {Select, DataTable, Column, Button, useToast} from 'primevue'
import {computed} from "vue";
import {Head, usePage} from "@inertiajs/vue3";
import {NodeType} from "../types/nodeType.ts";
import LayoutTitle from "../Common/LayoutTitle.vue";
import {SubscriptionStatus} from "../types/subscriptionStatus.ts";
import {useSubscription} from "../stores/useSubscription.js";
import axios from "axios"

const page = usePage()
const toast = useToast()
const subscriptionStore = useSubscription()

const repository = computed(() => {
    return page.props.repository
})
const branches = computed(() => {
    return page.props.branches
})
const selectedBranch = computed(() => {
    return page.props.branches[0]
})
const files = computed(() => {
    const files = page.props.files
    if (page.props.path.length) {
        files.unshift({
            name: '..',
            type: NodeType.BACK
        })
    }

    return files
})

const path = computed(() => page.props.path)
const backPath = computed(() => {
    if (path.value.length) {
        const split = path.value.split("/");

        return split.slice(0, split.length - 2).join("/") + "/";
    }

    return ''
})

const deletePrompt = ({path, sha}) => {
    axios.delete(
        `/ui_api/repository/${repository.value.id}/prompt/${path}?sha=${sha}`
    ).then(() => {
        toast.add({
            severity: 'info',
            summary: 'Info',
            detail: 'Prompt has deleted successfully!',
            life: 2000
        });

        window.location.reload()
    })
}

const isPaidRepository = computed(() => {
    return repository.value.subscriptionStatus === SubscriptionStatus.PAID
})



</script>

<template>
    <Head :title="repository.name" />

    <LayoutTitle>
        {{repository.name}}/{{page.props.path}}
    </LayoutTitle>
    <div>
        <div class="flex gap-4 justify-items-between items-center">
            <Select v-model="selectedBranch"
                    :options="branches"
                    optionLabel="name"
                    class="w-full md:w-56" />

            <div class="flex gap-2">
                <template v-if="isPaidRepository">
                    <Button
                        icon="pi pi-plus"
                        as="a"
                        severity="secondary"
                        label="Add Prompt"
                        :href="`/repository/${repository.id}/prompt/${path}${path.length ? '/' : ''}new`"/>
                </template>
                <template v-else>
                    <Button
                        icon="pi pi-plus"
                        as="a"
                        severity="secondary"
                        @click.prevent="subscriptionStore.openSubscriptionModal(repository.id)"
                        label="Add Prompt"/>
                </template>

                <Button
                    icon="pi pi-cog"
                    as="a"
                    severity="secondary"
                    label="Repository Settings"
                    :href="`/repository/${repository.id}/settings`"/>
            </div>
        </div>
        <div>
            <div class="card">
                <DataTable :value="files" tableStyle="min-width: 50rem">
                    <Column style="width: 20px">
                        <template #body="slotProps">
                            <i
                                v-if="slotProps.data.type === NodeType.DIR"
                                class="pi pi-folder"
                                style="font-size: 1rem"></i>

                            <i
                                v-if="slotProps.data.type === NodeType.BACK"
                                class="pi pi-angle-left"
                                style="font-size: 1rem"></i>
                        </template>
                    </Column>
                    <Column header="Name">
                        <template #body="slotProps">
                            <template v-if="slotProps.data.type === NodeType.DIR">
                                <a :href="`/repository/${repository.id}/prompts/${slotProps.data.path}`">
                                    {{slotProps.data.name}}
                                </a>
                            </template>
                            <template v-else-if="slotProps.data.type === NodeType.BACK">
                                <a :href="`/repository/${repository.id}/prompts/${backPath}`">
                                    {{slotProps.data.name}}
                                </a>
                            </template>
                            <template v-else>
                                {{slotProps.data.name}}
                            </template>
                        </template>
                    </Column>
                    <Column style="width: 20px">
                        <template #body="slotProps">
                            <template
                                v-if="slotProps.data.isEditable">
                                <div class="flex gap-2">
                                    <template v-if="isPaidRepository">
                                        <Button
                                            size="small"
                                            variant="text"
                                            as="a"
                                            data-testid="Action.editPrompt"
                                            :href="`/repository/${repository.id}/prompt/${slotProps.data.path}`"
                                            aria-label="Edit"
                                            icon="pi pi-pencil" />

                                        <Button
                                            @click="deletePrompt({
                                                path: slotProps.data.path,
                                                sha: slotProps.data.sha
                                            })"
                                            size="small"
                                            severity="danger"
                                            variant="text"
                                            aria-label="Delete"
                                            icon="pi pi-trash"
                                        />
                                    </template>
                                    <template v-else>
                                        <Button
                                            size="small"
                                            variant="text"
                                            as="a"
                                            @click.prevent="subscriptionStore.openSubscriptionModal(repository.id)"
                                            aria-label="Edit"
                                            icon="pi pi-pencil" />

                                        <Button
                                            size="small"
                                            severity="danger"
                                            variant="text"
                                            @click.prevent="subscriptionStore.openSubscriptionModal(repository.id)"
                                            aria-label="Delete"
                                            icon="pi pi-trash"
                                        />
                                    </template>
                                </div>

                            </template>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>
