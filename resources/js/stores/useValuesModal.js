import {defineStore} from "pinia";
import {reactive} from "vue";

export const SAVE_VALUES_ACTION = 'saveValues'

export const useValuesModal = defineStore('valuesModalStore', () => {
    const state = reactive({
        isModalOpen: false,
        fields: [],
        values: [],
        payload: [],
        modalHeader: ''
    })

    const saveValues = () => {
        state.isModalOpen = false
        return  {
            variables: state.variables,
            values: state.values,
            payload: state.payload
        }
    }

    const openValuesModal = (
        variables,
        values,
        payload,
        modalHeader
    ) => {
        state.variables = variables
        state.values = values
        state.payload = payload
        state.modalHeader = modalHeader
        state.isModalOpen = true
    }

    return {
        state,

        saveValues,
        openValuesModal
    }
})
