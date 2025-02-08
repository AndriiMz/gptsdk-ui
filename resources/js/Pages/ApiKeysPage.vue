<script setup>
import LayoutTitle from "../Common/LayoutTitle.vue";
import {useAiApiKeys} from "../stores/useAiApiKeys.js";
import {storeToRefs} from "pinia";
import {onMounted} from "vue";
import {DataTable, Column, Button} from "primevue"
import {momentToElapsed} from "../helpers/dateHelper.js";

const store = useAiApiKeys()
const { state } = storeToRefs(store)


onMounted(() => {
    store.fetchAiApiKeys()
})
</script>
<template>
    <LayoutTitle>
        AI Api Keys
    </LayoutTitle>

    <template v-if="state.aiApiKeys.length">
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
