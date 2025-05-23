<script setup>
import LayoutTitle from "../Common/LayoutTitle.vue";
import {useAiApiKeys} from "../stores/useAiApiKeys.js";
import {storeToRefs} from "pinia";
import {computed, onMounted, ref} from "vue";
import {DataTable, Column, Button, IftaLabel, Select} from "primevue"
import {momentToElapsed} from "../helpers/dateHelper.js";
import {Head, usePage} from "@inertiajs/vue3";
import axios from "axios";

const store = useAiApiKeys()
const { state } = storeToRefs(store)


onMounted(() => {
    store.fetchAiApiKeys()
})

const page = usePage()
const useForGeneration = ref(page.props.useForGeneration?.id)
const updateUseForGeneration = (newId) => {
    axios.post(`/ai_api_key/${newId}`, {
        useForGeneration: true
    })
}

</script>
<template>
    <Head title="Api Keys" />

    <LayoutTitle>
        AI Api Keys
    </LayoutTitle>

    <template v-if="state.aiApiKeys.length">
        <div class="flex flex-col gap-2">
            <div>
                <Button
                    icon="pi pi-plus"
                    as="a"
                    severity="secondary"
                    @click.prevent="store.addKey"
                    label="Add AI Api Key"/>
            </div>

            <div class="max-w-4xl">
                <DataTable size="small" paginator :rows="10" :value="state.aiApiKeys" >
                    <Column field="aiVendor" header="AI"></Column>
                    <Column field="name" header="Name"></Column>
                    <Column field="defaultModel" header="Default Model"></Column>
                    <Column header="Created">
                        <template #body="slotProps">
                            {{momentToElapsed(slotProps.data.createdAt)}}
                        </template>
                    </Column>
                    <Column style="width: 20px">
                        <template #body="slotProps">
                            <div class="flex ">
                                <Button
                                    size="small"
                                    variant="text"
                                    @click.prevent="store.editApiKey(slotProps.data)"
                                    aria-label="Edit"
                                    icon="pi pi-pencil"
                                />

                                <Button
                                    size="small"
                                    severity="danger"
                                    variant="text"
                                    @click.prevent="store.deleteKey(slotProps.data.id)"
                                    aria-label="Delete"
                                    icon="pi pi-trash"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>

        </div>

        <hr class="my-6 bg-gray-100 dark:bg-black border-0 h-px" />

        <div class="flex flex-col gap-2">
            <LayoutTitle>
                Misc
            </LayoutTitle>
            <div class="max-w-96">
                <IftaLabel>
                    <Select
                        inputId="dd-ai-vendor"
                        size="small"
                        :options="state.aiApiKeys"
                        option-value="id"
                        option-label="name"
                        type="text"
                        v-model="useForGeneration"
                        @update:model-value="updateUseForGeneration"
                        class="w-full"/>
                    <label for="dd-ai-vendor">AI Generation Key</label>
                </IftaLabel>
            </div>
        </div>
    </template>
    <template v-else>
        <div class="max-w-4xl border-gray-50 dark:!border-gray-800 border rounded p-10 flex flex-col items-center gap-4">
            <div>
                <h3>You don't have any API keys yet.</h3>
            </div>
            <div>
                <Button
                    icon="pi pi-plus"
                    as="a"
                    severity="secondary"
                    @click.prevent="store.addKey"
                    label="Add AI Api Key"/>
            </div>
        </div>

    </template>


</template>
