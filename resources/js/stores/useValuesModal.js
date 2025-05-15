import {defineStore} from "pinia";
import {reactive} from "vue";

export const SAVE_VALUES_ACTION = 'saveValues'

export const useValuesModal = defineStore('valuesModalStore', () => {
    const state = reactive({
        isModalOpen: false,
        isFieldsImmutable: true,

        variables: [],
        values: [],
        payload: [],
        modalHeader: ''
    })

    const saveValues = () => {
        state.isModalOpen = false

        const values = {}
        for (const index in state.variables) {
            values[state.variables[index].name] = state.values[index] ?? null
        }

        return  {
            variables: state.variables,
            values: values,
            payload: state.payload
        }
    }

    const openValuesModal = ({
         variables,
         values,
         payload,
         modalHeader,
         isFieldsImmutable
     }) => {
        state.variables = variables
        state.values = {}
        for (const index in variables) {
            state.values[index] = values[variables[index].name] ?? null
        }
        state.payload = payload
        state.modalHeader = modalHeader
        state.isModalOpen = true
        state.isFieldsImmutable = isFieldsImmutable ?? true
    }

    return {
        state,

        saveValues,
        openValuesModal
    }
})
