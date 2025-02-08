import {defineStore} from "pinia";
import {reactive} from "vue";

export const usePromptForm = defineStore('promptForm', () => {
    const state = reactive({
        focusYIndex: 0,
        focusXIndex: 0,
    })

    const setPromptMessageFocus = (x, y) => {
        state.focusXIndex = x
        state.focusYIndex = y
    }

    return {
        setPromptMessageFocus,

        state
    }
})
