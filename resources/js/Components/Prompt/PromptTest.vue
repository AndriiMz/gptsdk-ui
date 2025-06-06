<script setup>

import {computed, onMounted} from "vue";
import {
    Badge,
    Button,
    IftaLabel,
    Panel,
    ProgressSpinner,
    Select,
    SelectButton,
    Splitter,
    SplitterPanel,
    Tag
} from "primevue";
import {useAiApiKeys} from "../../stores/useAiApiKeys.js";
import {storeToRefs} from "pinia";
import {useAiConnectors} from "../../stores/useAiConnectors.js";
import JsonViewer from "../Json/JsonViewer.vue";
import {SAVE_VALUES_ACTION, useValuesModal} from "../../stores/useValuesModal.js";
import TimeTag from "../../Common/Tags/TimeTag.vue";
import StatusTag from "../../Common/Tags/StatusTag.vue";
import Error from "../../Common/Form/Error.vue";
import {usePromptForm} from "../../stores/usePromptForm.js";
import {useVariableValues} from "../../stores/useVariableValues.js";
import {toValuesModalVariables} from "../../types/aiVendorOptionsTemplates";
import {usePromptTest} from "../../stores/usePromptTest.js";
import {ResultViewMode} from "../../types/resultViewMode";
import PromptResultViewer from "./PromptResultViewer.vue";
import {useFileForm} from "../../stores/useFileForm.js";

const aiApiKeysStore = useAiApiKeys()
const { state: aiApiKeysState } = storeToRefs(aiApiKeysStore)


const aiConnectorsStore = useAiConnectors()
const {state: aiConnectorsState} = storeToRefs(aiConnectorsStore)
const { addAiConnector, deleteAiConnector, saveAiConnector } = aiConnectorsStore

const variableValuesStore = useVariableValues()
const {state: variableValuesState} = storeToRefs(variableValuesStore)
const { saveVariableValues, deleteVariableValues } = variableValuesStore

const { openValuesModal, $onAction: onValuesAction } = useValuesModal()

const VARIABLE_VALUES_TARGET = 'variableValues';
const AI_CONNECTOR_TARGET = 'aiConnector';

onValuesAction(
    ({name, after}) => {
        if (name === SAVE_VALUES_ACTION) {
            after(({values, payload}) => {
                if (payload.target === VARIABLE_VALUES_TARGET) {
                    saveVariableValues(payload.index, values)
                } else if (payload.target === AI_CONNECTOR_TARGET) {
                    saveAiConnector(payload.index, values)
                }
            })
        }
    }
)

const promptTestStore = usePromptTest()
const { state: promptTestState } = storeToRefs(promptTestStore)
const logs = computed(() => {
    return promptTestState.value.logs.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
})

const { loadOldResults, removePromptResults, getPromptResults, hideLog } = promptTestStore

const promptFormStore = usePromptForm()
const { state: promptFormState } = storeToRefs(promptFormStore)

const { fileForm } = useFileForm()

onMounted(() => {
    aiApiKeysStore.fetchAiApiKeys()
    aiConnectorsStore.fetchAiConnectors()
    variableValuesStore.fetchVariableValues()
})


const resultViewModes = [
    {label: ResultViewMode.RAW, icon: 'pi pi-align-justify'},
    {label: ResultViewMode.PRETTY, icon: 'pi pi-align-left'},
]

</script>

<template>
    <div class="flex flex-col gap-4">
        <Panel toggleable>
            <template #header>
                <div class="flex justify-between items-center w-full">
                    <span class="p-panel-title">
                        Variable Values
                        <Badge v-if="variableValuesState.variableValues.length" :value="variableValuesState.variableValues.length" />
                    </span>
                    <Button label="Add Variable Values"
                            icon="pi pi-plus"
                            size="small"
                            data-testid="Action.addVariableValues"
                            variant="text"
                            @click.prevent="openValuesModal({
                                variables: fileForm.content.variables,
                                values: {},
                                payload: {target: VARIABLE_VALUES_TARGET},
                                modalHeader: 'Add Variable Values'
                            })"/>
                </div>
            </template>

            <div class="flex flex-col gap-2">
                <div class="flex gap-2 overflow-x-auto">
                    <div v-if="!variableValuesState.variableValues.length">
                        No Variable Values
                    </div>
                    <div v-for="(variableValues, index) in variableValuesState.variableValues"
                         data-testid="ListItem.variableValues"
                         class="border border-gray-100 dark:border-gray-800 p-2 rounded flex flex-col gap-2 min-w-60 flex-shrink-0">
                        <JsonViewer :json="variableValues.variableValues" />
                        <div class="flex gap-2">
                            <Button
                                size="small"
                                variant="text"
                                icon="pi pi-pencil"
                                aria-label="Edit Variable Values"
                                @click.prevent="openValuesModal({
                                    variables: fileForm.content.variables,
                                    values: variableValues.variableValues,
                                    payload: {index, target: VARIABLE_VALUES_TARGET},
                                    modalHeader: 'Edit Variable Values'
                                })" />

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
                                :invalid="promptTestState.errors?.aiConnectors?.[index]?.aiApiKeyId"
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

                        <Error :error="promptTestState.errors?.aiConnectors?.[index]?.aiApiKeyId" />
                    </div>
                    <div>
                        <JsonViewer
                            data-testid="Input.llmOptions"
                            v-if="aiConnectorsState.aiConnectors[index].aiApiKeyId"
                            :json="aiConnectorsState.aiConnectors[index].llmOptions" />

                        <Error :error="promptTestState.errors?.aiConnectors?.[index]?.llmOptions" />
                    </div>

                    <div>
                        <Button
                            v-if="aiConnectorsState.aiConnectors[index].aiApiKeyId"
                            size="small"
                            variant="text"
                            icon="pi pi-pencil"
                            aria-label="Edit Variable Values"
                            @click.prevent="openValuesModal({
                                 variables: toValuesModalVariables(
                                     aiConnectorsState.aiConnectors[index].llmOptions
                                 ),
                                 values: aiConnectorsState.aiConnectors[index].llmOptions,
                                 payload: {index, target: AI_CONNECTOR_TARGET},
                                 modalHeader: 'Edit Connector Options',
                                 isFieldsImmutable: false
                            })" />

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
                            v-if="promptTestState.isLoading"
                            style="width: 15px; height: 15px" strokeWidth="4"
                            fill="transparent" />
                    </span>
                    <div class="flex gap-2">

                        <SelectButton v-model="promptTestState.viewMode"
                                      :allow-empty="false"
                                      optionLabel="label"
                                      optionValue="label"
                                      :options="resultViewModes" >
                            <template #option="slotProps">
                                <i :class="slotProps.option.icon"></i>
                                {{slotProps.option.label}}
                            </template>
                        </SelectButton>


                        <Button label="Get Results"
                                icon="pi pi-play"
                                size="small"
                                @click.prevent="getPromptResults"/>

                        <Button label="Get Older Results"
                                v-if="promptTestState.hasOldLogs"
                                icon="pi pi-history"
                                size="small"
                                variant="text"
                                @click.prevent="loadOldResults"/>

                        <Button label="Clear Results"
                                icon="pi pi-times"
                                size="small"
                                variant="text"
                                severity="danger"
                                @click.prevent="removePromptResults"/>
                    </div>
                </div>
            </template>

            <div v-if="!logs.length">
                No Results yet
            </div>

            <div class="flex flex-col gap-2">
                <template v-for="(log, index) in logs" :key="log.id">
                    <Splitter>
                        <SplitterPanel :size="25" :minSize="20" class="flex flex-col gap-2 p-2">
                            <div class="flex gap-2">
                                <StatusTag :status="log.status" />
                                <TimeTag :time="log.createdAt" />
                            </div>

                            <JsonViewer :json="log.variableValues" />
                            <JsonViewer :json="log.llmOptions" />
                        </SplitterPanel>
                        <SplitterPanel :size="75">
                            <PromptResultViewer
                                :id="log.id"
                                :output="log.output"
                                :ai-vendor="log.aiVendor"
                                :view-mode="promptTestState.viewMode"
                            >
                                <template #actions>
                                    <Button
                                        v-if="promptFormState.mocksHashes[log.hash] === undefined"
                                        icon="pi pi-flag"
                                        v-tooltip.left="`Save as Mock`"
                                        size="small"
                                        variant="text"
                                        @click="promptFormStore.createMock(log)"
                                        class="w-full" />
                                    <Tag v-else severity="info"
                                         class="!p-2 w-full"
                                         icon="pi pi-flag"
                                         value="Mocked"  />

                                    <Button
                                        @click="hideLog(log.id)"
                                        icon="pi pi-times"
                                        size="small"
                                        variant="text"
                                        v-tooltip.left="`Remove from Compare`"
                                        severity="danger"/>
                                </template>
                            </PromptResultViewer>

                        </SplitterPanel>
                    </Splitter>
                </template>
            </div>

        </Panel>

    </div>
</template>
