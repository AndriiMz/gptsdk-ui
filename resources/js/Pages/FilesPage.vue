<script setup>

import {Button, Column, DataTable, Message, Select, SplitButton, useToast} from 'primevue'
import {computed, onMounted} from "vue";
import {Head, router, usePage} from "@inertiajs/vue3";
import {NodeType} from "../types/nodeType.ts";
import LayoutTitle from "../Common/LayoutTitle.vue";
import {SubscriptionStatus} from "../types/subscriptionStatus.ts";
import {useSubscription} from "../stores/useSubscription.js";
import axios from "axios"
import {getBackPath} from "../helpers/pathHelper.ts";
import {useFiles} from "../stores/useFiles.js";
import {useRepository} from "../stores/useRepository.js";
import {storeToRefs} from "pinia";
import _ from "lodash";
import Stub from "../Common/Stub.vue";

const page = usePage()
const toast = useToast()
const filesStore = useFiles()
const repositoryStore = useRepository()

const { repository } = storeToRefs(repositoryStore)

const subscriptionStore = useSubscription()

const branches = computed(() => {
    return page.props.branches
})
const selectedBranch = computed(() => {
    return page.props.branches[0]
})

const originalFiles = computed(() => {
    return page.props.files
})
const files = computed(() => {
    const files = _.clone(originalFiles.value)
    if (page.props.path.length) {
        files.unshift({
            name: '..',
            type: NodeType.BACK
        })
    }

    return files.sort((a, b) => {
        if (a.type === NodeType.BACK) return -1
        if (b.type === NodeType.BACK) return 1
        if (a.type === NodeType.DIR && b.type !== NodeType.DIR) return -1
        if (b.type === NodeType.DIR && a.type !== NodeType.DIR) return 1
        return a.name.localeCompare(b.name)
    })
})

const path = computed(() => page.props.path)
const backPath = computed(() => {
    return getBackPath(path.value, 2)
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

const createOptions = [
    {
        label: 'Add MD',
        icon: 'pi pi-plus',
        command: () => {
            filesStore.addNewFile({
                path: path.value,
                extension: 'md'
            })
        }
    }
]

const addPrompt = () => {
    filesStore.addNewFile({
        path: path.value,
        extension: 'prompt'
    })
}
</script>

<template>
    <Head :title="repository.name" />

    <LayoutTitle>
        {{repository.name}}/{{page.props.path}}
    </LayoutTitle>
    <div class="flex flex-col gap-2">
        <template v-if="originalFiles.length">
            <div class="flex gap-2 justify-items-between items-center">
                <Select v-model="selectedBranch"
                        :options="branches"
                        optionLabel="name"
                        class="w-full md:w-56" />

                <Message
                    icon="pi pi-verified"
                    severity="success" >
                    {{selectedBranch.commitSha}}
                </Message>

                <div class="flex gap-2">
                    <template v-if="isPaidRepository">
                        <SplitButton
                            :model="createOptions"
                            icon="pi pi-plus"
                            as="a"
                            severity="secondary"
                            label="Add Prompt"
                            @click="addPrompt"/>
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
                    <DataTable class="rounded" :value="files" tableStyle="min-width: 50rem" >
                        <Column style="width: 20px">
                            <template #body="slotProps">
                                <template v-if="slotProps.data.type === NodeType.DIR">
                                    <a :href="`/repository/${repository.id}/files/${slotProps.data.path}`">
                                        <i class="pi pi-folder" style="font-size: 1rem"></i>
                                    </a>
                                </template>

                                <template v-if="slotProps.data.type === NodeType.BACK">
                                    <a :href="`/repository/${repository.id}/files/${backPath}`">
                                        <i class="pi pi-angle-left" style="font-size: 1rem"></i>
                                    </a>
                                </template>
                            </template>
                        </Column>
                        <Column header="Name" class="dark:hover:bg-[#202020] hover:bg-slate-100">
                            <template #body="slotProps">
                                <template v-if="slotProps.data.type === NodeType.DIR">
                                    <a :href="`/repository/${repository.id}/files/${slotProps.data.path}`" class="block w-full">
                                        {{slotProps.data.name}}
                                    </a>
                                </template>
                                <template v-else-if="slotProps.data.type === NodeType.BACK">
                                    <a :href="`/repository/${repository.id}/files/${backPath}`" class="block w-full">
                                        {{slotProps.data.name}}
                                    </a>
                                </template>
                                <template v-else>
                                    <template v-if="isPaidRepository">
                                        <a class="block w-full" :href="`/repository/${repository.id}/file/${slotProps.data.path}`">
                                            {{slotProps.data.name}}
                                        </a>
                                    </template>
                                    <template v-else>
                                        {{slotProps.data.name}}
                                    </template>
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
                                                :href="`/repository/${repository.id}/file/${slotProps.data.path}`"
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
        </template>
        <template v-else>
            <Stub title="You don't have any files yet.">
                <template #actions>
                    <template v-if="isPaidRepository">
                        <SplitButton
                            :model="createOptions"
                            icon="pi pi-plus"
                            as="a"
                            severity="secondary"
                            label="Add Prompt"
                            @click="addPrompt"/>
                    </template>
                    <template v-else>
                        <Button
                            icon="pi pi-plus"
                            as="a"
                            severity="secondary"
                            @click.prevent="subscriptionStore.openSubscriptionModal(repository.id)"
                            label="Add Prompt"/>
                    </template>
                </template>
            </Stub>
        </template>
    </div>
</template>
