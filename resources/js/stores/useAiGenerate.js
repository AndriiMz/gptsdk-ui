import {defineStore} from "pinia";
import {computed, reactive} from "vue";
import axios from "axios";
import {useFileForm} from "./useFileForm.js";
import {prettifyAiResponse} from "../helpers/aiResponse";

export const useAiGenerate = defineStore('aiGenerate', () => {
    const { fileForm, paste } = useFileForm()

    const state = reactive({
        isOpen: false,
        context: null,
        form: {
            aiApiKey: null,
            variables: [],
            values: {},
        },
        result: null,
        isLoading: false,
        pasteTooltip: ''
    })

    const variables = computed(() => {
        return state.form.variables.filter(
            (variable) => !['filePath', 'content'].includes(variable.name)
        )
    })

    const openModal = (context) => {
        state.isOpen = true
        state.context = context

        axios.get('/ui_api/ai_generate').then(({data}) => {
            state.form = Object.assign(data, {values: {}})
        })
    }


    const generate = () => {
        if (state.isLoading) {
            state.isLoading = false
        }

        state.isLoading = true

        axios.post(
            '/ui_api/ai_generate',
            {
                values:  Object.assign(
                    {},
                    state.form.values,
                    {
                        filePath: fileForm.path,
                        content: JSON.stringify(fileForm.content)
                    }
                )
            }
        ).then(({data}) => {
            state.result = prettifyAiResponse(
                data.response,
                state.form.aiApiKey.aiVendor
            )
        }).finally(() => {
            state.isLoading = false
        })
    }


    return {
        state,
        variables,
        generate,
        paste,

        openModal
    }
})

