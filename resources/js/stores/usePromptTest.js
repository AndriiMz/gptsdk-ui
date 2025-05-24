import {defineStore, storeToRefs} from "pinia";
import {useAiConnectors} from "./useAiConnectors.js";
import {useVariableValues} from "./useVariableValues.js";
import {computed, reactive} from "vue";
import moment from "moment";
import axios from "axios";
import dot from "dot-object";
import {useFileForm} from "./useFileForm.js";
import {useLocalStorage} from "@vueuse/core";
import {ResultViewMode} from "../types/resultViewMode";

export const usePromptTest = defineStore('promptTest', () => {
    const aiConnectorsStore = useAiConnectors()
    const {state: aiConnectorsState} = storeToRefs(aiConnectorsStore)

    const variableValuesStore = useVariableValues()
    const {state: variableValuesState} = storeToRefs(variableValuesStore)

    const fileFormStore = useFileForm()
    const { fileForm } = fileFormStore
    const { state: fileFormState } = storeToRefs(fileFormStore)

    const repositoryId = computed(() => fileFormState.value.repositoryId)
    const path = computed(() => fileForm.path)
    const prompt = computed(() => fileForm.content)

    const state = reactive({
        errors: {},

        isLoading: false,

        logs: [],

        logsDateAfter: moment(),
        hasOldLogs: true,

        viewMode: useLocalStorage('viewMode', ResultViewMode.PRETTY)
    })

    const getPromptResults = () => {
        state.isLoading = true
        state.errors = {}
        axios.post(`/ui_api/repository/${repositoryId.value}/prompt/result/${path.value}`, {
            variableValues: variableValuesState.value.variableValues.map((item) => item.variableValues),
            aiConnectors: aiConnectorsState.value.aiConnectors,
            prompt: prompt.value
        }).then(({data}) => {
            state.logs = state.logs.concat(data.logs)
        }).catch((data) => {
            console.log(data)
            state.errors = dot.object(data?.response?.data?.errors)
        }).finally(() => {
            state.isLoading = false
        })
    }

    const removePromptResults = () => {
        state.logs =  []
        state.logsDateAfter = moment()
    }

    const hideLog = (id) => {
        state.logs = state.logs.filter(log => log.id !== id)
    }

    const loadOldResults = () => {
        state.isLoading = true
        axios.get(
            `/ui_api/repository/${repositoryId.value}/prompt/ai_logs/${path.value}`,
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

    return {
        loadOldResults,
        removePromptResults,
        getPromptResults,
        hideLog,

        state
    }
})
