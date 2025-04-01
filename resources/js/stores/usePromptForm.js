import {defineStore} from "pinia";
import {reactive} from "vue";
import axios from "axios";

export const usePromptForm = defineStore('promptForm', () => {
    const state = reactive({
        focusYIndex: 0,
        focusXIndex: 0,

        mocks: {},
        mocksHashes: [],

        repositoryId: null,
        path: null
    })
    const setPromptMessageFocus = (x, y) => {
        state.focusXIndex = x
        state.focusYIndex = y
    }

    const loadMocks = () => {
        axios
            .get(`/ui_api/repository/${state.repositoryId}/prompt/mock/${state.path}`)
            .then(({data}) => {
                state.mocks = data.mocks
                state.mocksHashes = Object.fromEntries(Object.values(data.mocks).map(mock => [mock.hash, true]))
            })
    }

    const deleteMock = (hash) => {
        axios
            .delete(`/ui_api/repository/${state.repositoryId}/prompt/mock/${state.path}`, {params: {hash}})
            .then(({data}) => {
                usePromptForm().loadMocks()
            })
    }

    const createMock = (log) => {
        axios.post(
            `/ui_api/repository/${state.repositoryId}/prompt/mock/${state.path}`,
            {
                variableValues: log.variableValues,
                output: log.output
            }
        ).then(() => {
            usePromptForm().loadMocks()
        })
    }


    return {
        setPromptMessageFocus,
        loadMocks,
        deleteMock,
        createMock,

        state
    }
})
