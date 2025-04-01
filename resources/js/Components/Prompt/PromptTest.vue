<script setup>

import {onMounted, reactive} from "vue";
import {Button, IftaLabel, Panel, Select, Badge, ProgressSpinner, Splitter, SplitterPanel, Tag} from "primevue";
import {useAiApiKeys} from "../../stores/useAiApiKeys.js";
import {storeToRefs} from "pinia";
import {useAiConnectors} from "../../stores/useAiConnectors.js";
import JsonViewer from "../Json/JsonViewer.vue";
import {useValuesModal, SAVE_VALUES_ACTION} from "../../stores/useValuesModal.js";
import {AiVendorOptionsTemplates} from "../../types/aiVendorOptionsTemplates.ts";
import moment from "moment";
import TimeTag from "../../Common/Tags/TimeTag.vue";
import StatusTag from "../../Common/Tags/StatusTag.vue";
import axios from "axios"

import dot from "dot-object"
import Error from "../../Common/Form/Error.vue";
import {usePromptForm} from "../../stores/usePromptForm.js";

const props = defineProps({
    prompt: {type: Object},
    repositoryId: {type: Number},
    path: {type: String}
})

const aiApiKeysStore = useAiApiKeys()
const { state: aiApiKeysState } = storeToRefs(aiApiKeysStore)

const aiConnectorsStore = useAiConnectors()
const {state: aiConnectorsState} = storeToRefs(aiConnectorsStore)
const { addAiConnector, deleteAiConnector, saveAiConnector } = aiConnectorsStore

const VARIABLE_VALUES_TARGET = 'variableValues';
const AI_CONNECTOR_TARGET = 'aiConnector';

const { openValuesModal, $onAction: onValuesAction } = useValuesModal()
onValuesAction(
    ({name, after}) => {
        if (name === SAVE_VALUES_ACTION) {
            after(({values, payload}) => {
                if (payload.index && payload.target === VARIABLE_VALUES_TARGET) {
                    state.variableValues[payload.index] = values
                } else if (payload.target === VARIABLE_VALUES_TARGET) {
                    state.variableValues.push(values)
                } else if (payload.target === AI_CONNECTOR_TARGET) {
                    saveAiConnector(payload.index)
                }
            })
        }
    }
)


const promptFormStore = usePromptForm()
const {state: promptFormState} = storeToRefs(promptFormStore)

const state = reactive({
    errors: {},

    variableValues: [],

    isLoading: false,

    logs: [],

    logsDateAfter: moment(),
    hasOldLogs: true
})

const deleteVariableValues = (index) => {
    state.variableValues.splice(index, 1)
}

const getPromptResults = () => {
    state.isLoading = true
    state.errors = {}
    axios.post(`/ui_api/repository/${props.repositoryId}/prompt/result/${props.path}`, {
        variableValues: state.variableValues,
        aiConnectors: aiConnectorsState.value.aiConnectors,
        prompt: props.prompt.messages
    }).then(({data}) => {
        state.logs = state.logs.concat(data.logs)
    }).catch((data) => {
        state.errors = dot.object(data?.response?.data?.errors)
    }).finally(() => {
        state.isLoading = false
    })
}

const loadOldResults = () => {
    state.isLoading = true
    axios.get(
        `/ui_api/repository/${props.repositoryId}/prompt/ai_logs/${props.path}`,
        {
            params: {
                date_after: state.logsDateAfter.format('YYYY-MM-DD HH:mm:ss')
            }
        }
    ).then(({data}) => {
        if (!data.logs.length) {
            state.hasOldLogs = false
        }

        state.logs.unshift(...data.logs)

        if (state.logs.length) {
            state.logsDateAfter = moment(state.logs[0].createdAt)
        }
    }).finally(() => {
        state.isLoading = false
    })
}

onMounted(() => {
    aiApiKeysStore.fetchAiApiKeys()
    aiConnectorsStore.fetchAiConnectors()
})

</script>

<template>
    <div class="flex flex-col gap-4">
        <Panel toggleable>
            <template #header>
                <div class="flex justify-between items-center w-full">
                    <span class="p-panel-title">
                        Variable Values
                        <Badge v-if="state.variableValues.length"
                               :value="state.variableValues.length" />
                    </span>
                    <Button label="Add Variable Values"
                            icon="pi pi-plus"
                            size="small"
                            data-testid="Action.addVariableValues"
                            variant="text"
                            @click.prevent="openValuesModal(
                                prompt.variables,
                                {},
                                {target: VARIABLE_VALUES_TARGET},
                                'Add Variable Values'
                            )"/>
                </div>
            </template>

            <div class="flex flex-col gap-2">
                <div class="flex gap-2 overflow-x-auto">
                    <div v-if="!state.variableValues.length">
                        No Variable Values
                    </div>
                    <div v-for="(variableValues, index) in state.variableValues"
                         data-testid="ListItem.variableValues"
                         class="border border-gray-100 dark:border-gray-800 p-2 rounded flex flex-col gap-2 min-w-60 flex-shrink-0">
                        <JsonViewer :json="variableValues" />
                        <div class="flex gap-2">
                            <Button
                                size="small"
                                variant="text"
                                icon="pi pi-pencil"
                                aria-label="Edit Variable Values"
                                @click.prevent="openValuesModal(
                                    prompt.variables,
                                     variableValues,
                                     {index, target: VARIABLE_VALUES_TARGET},
                                     'Edit Variable Values'
                                )" />

                            <Button
                                size="small"
                                variant="text"
                                severity="danger"
                                icon="pi pi-trash"
                                aria-label="Delete Variable Values"
                                @click.prevent="deleteVariableValues(index)" />
                        </div>
                    </div>
                </div>
            </div>
        </Panel>

        <Panel toggleable>
            <template #header>
                <div class="flex justify-between items-center w-full">
                    <span class="p-panel-title">
                        Ai Connectors
                       <Badge v-if="aiConnectorsState.aiConnectors.length"
                              :value="aiConnectorsState.aiConnectors.length" />
                    </span>
                    <Button label="Add Ai Connector"
                            icon="pi pi-plus"
                            data-testid="Action.addAiConnector"
                            size="small"
                            variant="text"
                            @click.prevent="addAiConnector"/>
                </div>
            </template>

            <div class="flex gap-2 overflow-x-auto">
                <div v-if="!aiConnectorsState.aiConnectors.length">
                    No Ai Connectors
                </div>

                <div v-for="(aiConnector, index) in aiConnectorsState.aiConnectors"
                     data-testid="ListItem.aiConnectors"
                     class="border border-gray-100 dark:border-gray-800 p-2 rounded flex flex-col gap-2 min-w-60 flex-shrink-0">
                    <div>
                        <IftaLabel>
                            <Select
                                :invalid="state.errors?.aiConnectors?.[index]?.aiApiKeyId"
                                :inputId="`dd-api-key-${index}`"
                                size="small"
                                :options="aiApiKeysState.aiApiKeys"
                                optionValue="id"
                                optionLabel="name"
                                type="text"
                                class="w-full"
                                data-testid="Input.aiApiKey"
                                @change="saveAiConnector(index)"
                                v-model="aiConnectorsState.aiConnectors[index].aiApiKeyId">
                                <template #footer>
                                    <div class="p-1">
                                        <Button label="Add New"
                                                @click="aiApiKeysStore.addKey()"
                                                fluid severity="secondary"
                                                text size="small"
                                                icon="pi pi-plus" />
                                    </div>
                                </template>
                            </Select>
                            <label :for="`dd-api-key-${index}`">Api Key</label>
                        </IftaLabel>

                        <Error :error="state.errors?.aiConnectors?.[index]?.aiApiKeyId" />
                    </div>
                    <div>
                        <JsonViewer
                            data-testid="Input.llmOptions"
                            v-if="aiConnectorsState.aiConnectors[index].aiApiKeyId"
                            :json="aiConnectorsState.aiConnectors[index].llmOptions" />

                        <Error :error="state.errors?.aiConnectors?.[index]?.llmOptions" />
                    </div>


                    <div>
                        <Button
                            v-if="aiConnectorsState.aiConnectors[index].aiApiKeyId"
                            size="small"
                            variant="text"
                            icon="pi pi-pencil"
                            aria-label="Edit Variable Values"
                            @click.prevent="openValuesModal(
                                 AiVendorOptionsTemplates[
                                     aiApiKeysState.aiApiKeysById[aiConnectorsState.aiConnectors[index].aiApiKeyId].aiVendor
                                 ],
                                 aiConnectorsState.aiConnectors[index].llmOptions,
                                 {index, target: AI_CONNECTOR_TARGET},
                                 'Edit Connector Options'
                            )" />

                        <Button
                            size="small"
                            variant="text"
                            severity="danger"
                            icon="pi pi-trash"
                            aria-label="Delete Ai Connector"
                            @click.prevent="deleteAiConnector(index)" />
                    </div>

                </div>
            </div>
        </Panel>
        <Panel header="" toggleable>
            <template #header>
                <div class="flex justify-between items-center w-full">
                    <span class="p-panel-title">
                        Results
                        <ProgressSpinner
                            v-if="state.isLoading"
                            style="width: 15px; height: 15px" strokeWidth="4"
                            fill="transparent" />
                    </span>
                    <div class="flex gap-2">
                        <Button label="Get Results"
                                icon="pi pi-play"
                                size="small"
                                @click.prevent="getPromptResults"/>

                        <Button label="Get Older Results"
                                v-if="state.hasOldLogs"
                                icon="pi pi-history"
                                size="small"
                                variant="text"
                                @click.prevent="loadOldResults"/>
                    </div>
                </div>
            </template>

            <div v-if="!state.logs.length">
                No Results yet
            </div>

            <div class="flex flex-col gap-2">
                <template v-for="(log, index) in state.logs">

                    <Splitter>
                        <SplitterPanel :size="25" :minSize="20" class="flex flex-col gap-2 p-2">
                            <div class="flex gap-2">
                                <StatusTag :status="log.status" />
                                <TimeTag :time="log.createdAt" />

                            </div>
                            <div>
                                <Button
                                    v-if="promptFormState.mocksHashes[log.hash] === undefined"
                                    icon="pi pi-flag"
                                        label="Save as Mock"
                                        size="small"
                                        @click="promptFormStore.createMock(log)"
                                        class="w-full" />
                                <Tag v-else severity="info" class="!p-2 w-full" icon="pi pi-flag" value="Mocked"  />
                            </div>

                            <JsonViewer :json="log.variableValues" />
                            <JsonViewer :json="log.llmOptions" />
                        </SplitterPanel>
                        <SplitterPanel :size="75">
                            <div class="rounded bg-gray-50 dark:bg-gray-800 p-2 flex-1">
                                <JsonViewer :json="log.output" :id="`log-${log.id}`" />
                            </div>
                        </SplitterPanel>
                    </Splitter>
                </template>
            </div>

        </Panel>

    </div>
</template>
