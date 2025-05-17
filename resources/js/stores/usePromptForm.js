import {defineStore} from "pinia";
import {reactive} from "vue";
import axios from "axios";
import {useForm, usePage} from "@inertiajs/vue3";

export const usePromptForm = defineStore('promptForm', () => {
    const page = usePage()

    const state = reactive({
        focusYIndex: 0,
        focusXIndex: 0,

        mocks: {},
        mocksHashes: [],

        repositoryId: page.props.repository.id,
        path: page.props.path
    })


    const promptForm = useForm({
        sha: page.props.prompt.sha ?? null,
        content: page.props.prompt.content,
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
                state.mocksHashes = data.mocks ?
                    Object.fromEntries(Object.values(data.mocks).map(mock => [mock.hash, true])) :
                    []
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

    const renderPrompt = async (values) => {
        const { data } = await axios.post(
            `/ui_api/repository/${state.repositoryId}/prompt/render/${state.path}`,
            {
                variableValues: values,
                prompt: promptForm.content.messages
            }
        )

        return data.prompt
    }

    return {setPromptMessageFocus,
        loadMocks,
        deleteMock,
        createMock,

        renderPrompt,

        state,
        promptForm
    }
})
