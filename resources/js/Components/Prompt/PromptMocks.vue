<script setup>

import {usePromptForm} from "../../stores/usePromptForm.js";
import {onMounted} from "vue";
import {storeToRefs} from "pinia";
import {Button, Panel, Splitter, SplitterPanel} from "primevue";
import JsonViewer from "../Json/JsonViewer.vue";

const promptFormStore = usePromptForm()
const {state} = storeToRefs(promptFormStore)
</script>

<template>
    <Panel header="" toggleable>
        <template #header>
            <div class="flex justify-between items-center w-full">
                <span class="p-panel-title">
                    <i class="pi pi-flag"></i>
                    Mocks
<!--                    <ProgressSpinner-->
<!--                        v-if="state.isLoading"-->
<!--                        style="width: 15px; height: 15px" strokeWidth="4"-->
<!--                        fill="transparent" />-->
                </span>
            </div>
        </template>

        <div v-if="!Object.values(state.mocks).length">
            No Mocks yet
        </div>

        <div class="flex flex-col gap-2">
            <template v-for="(mock, hash) in state.mocks">

                <Splitter>
                    <SplitterPanel :size="25" :minSize="20" class="flex flex-col gap-2 p-2">
                        <div>
                            <Button
                                icon="pi pi-trash"
                                label="Delete Mock"
                                severity="danger"
                                variant="outlined"
                                size="small"
                                @click="promptFormStore.deleteMock(hash)"
                                class="w-full" />
                        </div>

                        <JsonViewer :json="mock.variableValues" />
                    </SplitterPanel>
                    <SplitterPanel :size="75">
                        <div class="rounded bg-gray-50 dark:bg-gray-800 p-2 flex-1">
                            <JsonViewer :json="mock.output" :id="`mock-${hash}`" />
                        </div>
                    </SplitterPanel>
                </Splitter>
            </template>
        </div>

    </Panel>

</template>
