import {defineStore, storeToRefs} from "pinia";
import {computed, reactive, watch} from "vue";
import axios from "axios";
import {useForm, usePage} from "@inertiajs/vue3";
import {useFileForm} from "./useFileForm.js";

export const usePromptForm = defineStore('promptForm', () => {
    const fileFormStore = useFileForm()
    const { fileForm } = fileFormStore
    const { state: fileFormState } = storeToRefs(fileFormStore)

    const state = reactive({
        focusYIndex: 0,
        focusXIndex: 0,

        mocks: {},
        mocksHashes: [],
    })


    const variableByName = computed(() => {
        const variableByName = { }

        for (const variable of fileForm.content.variables) {
            variableByName[variable.name] = variable
        }

        return variableByName
    })

    const onMessageChange = () => {
        for (const message of fileForm.content.messages) {
            const matches = message.content.match(/\[\[(.*?)\]\]/g) || [];
            for (const match of matches) {
                const variableName = match.slice(2, -2).trim();
                if (variableName.length && !variableByName.value[variableName]) {
                    fileForm.content.variables.push({
                        name: variableName,
                        value: ''
                    });
                }
            }
        }
    }

    const setPromptMessageFocus = (x, y) => {
        state.focusXIndex = x
        state.focusYIndex = y
    }

    const loadMocks = () => {
        axios
            .get(`/ui_api/repository/${fileFormState.value.repositoryId}/prompt/mock/${fileForm.path}`)
            .then(({data}) => {
                state.mocks = data.mocks
                state.mocksHashes = data.mocks ?
                    Object.fromEntries(Object.values(data.mocks).map(mock => [mock.hash, true])) :
                    []
            })
    }

    const deleteMock = (hash) => {
        axios
            .delete(`/ui_api/repository/${fileFormState.value.repositoryId}/prompt/mock/${fileForm.path}`, {params: {hash}})
            .then(({data}) => {
                usePromptForm().loadMocks()
            })
    }

    const createMock = (log) => {
        axios.post(
            `/ui_api/repository/${fileFormState.value.repositoryId}/prompt/mock/${fileForm.path}`,
            {
                variableValues: log.variableValues,
                output: log.output
            }
        ).then(() => {
            usePromptForm().loadMocks()
        })
    }

    const renderPrompt = async (values) => {
        const { data } = await axios.post(
            `/ui_api/repository/${fileFormState.value.repositoryId}/prompt/render/${fileForm.path}`,
            {
                variableValues: values,
                prompt: fileForm.content
            }
        )

        return data.prompt
    }

    return {
        setPromptMessageFocus,
        onMessageChange,

        loadMocks,
        deleteMock,
        createMock,

        renderPrompt,

        state,
    }
})
