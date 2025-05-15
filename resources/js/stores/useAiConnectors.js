import {defineStore} from "pinia";
import {reactive} from "vue";
import axios from "axios"
import {AiVendorOptionsTemplates} from "../types/aiVendorOptionsTemplates.ts";
import {useAiApiKeys} from "./useAiApiKeys.js";

export const useAiConnectors = defineStore('aiConnectors', () => {
    const state = reactive({
        aiConnectors: []
    })

    const addAiConnector = () => {
        //TODO: set option template here
        state.aiConnectors.push({
            aiApiKeyId: null,
            llmOptions: {}
        })
    }

    const saveAiConnector = (index, values) => {
        if (
            !Object.values(state.aiConnectors[index].llmOptions).length &&
            state.aiConnectors[index].aiApiKeyId
        ) {
            const llmOptions = {}

            const optionTemplate = AiVendorOptionsTemplates[
                useAiApiKeys().state.aiApiKeysById[
                    state.aiConnectors[index].aiApiKeyId
                ].aiVendor
            ] ?? []

            for (const option of optionTemplate) {
                llmOptions[option.name] = ""
            }


            state.aiConnectors[index].llmOptions = llmOptions;
        } else if (values !== null) {
            state.aiConnectors[index].llmOptions = values;
        }

        axios.post(`/ui_api/ai_connector/${state.aiConnectors[index].id ?? ''}`, state.aiConnectors[index]).then(() => {
            if (!state.aiConnectors[index].id) {
                fetchAiConnectors(true)
            }
        })
    }

    const deleteAiConnector = (index) => {
        if (state.aiConnectors[index].id) {
            axios.delete(`/ui_api/ai_connector/${state.aiConnectors[index].id}`).then(() => {
                fetchAiConnectors(true)
            })
        } else {
            state.aiConnectors.splice(index, 1)
        }
    }

    const fetchAiConnectors = (force = false) => {
        if (!state.aiConnectors.length || force) {
            axios.get('/ui_api/ai_connector').then(({data}) => {
                state.aiConnectors = data.aiConnectors
            })
        }
    }

    return {
        state,

        fetchAiConnectors,
        addAiConnector,
        saveAiConnector,
        deleteAiConnector
    }
})
