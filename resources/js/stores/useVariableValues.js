import {defineStore, storeToRefs} from "pinia";
import {reactive} from "vue";
import axios from "axios";
import {usePromptForm} from "./usePromptForm.js";

export const useVariableValues = defineStore('variableValues', () => {
    const { state: promptFormState } = storeToRefs(usePromptForm())

    const state = reactive({
        variableValues: [],
    });

    const saveVariableValues = (index, values) => {
        if (!index) {
            index = state.variableValues.length
            state.variableValues.push({variableValues: values});
        }

        if (values !== null) {
            state.variableValues[index].variableValues = values;
        }

        axios.post(
            `/ui_api/repository/${promptFormState.value.repositoryId}/variable_values/${state.variableValues[index].id ?? ''}`,
            {
                path: promptFormState.value.path,
                variableValues: state.variableValues[index].variableValues
            }
        ).then(() => {
            if (!state.variableValues[index].id) {
                fetchVariableValues(true);
            }
        });
    };

    const deleteVariableValues = (index) => {
        if (state.variableValues[index].id) {
            axios
                .delete(`/ui_api/repository/${promptFormState.value.repositoryId}/variable_values/${state.variableValues[index].id}`)
                .then(() => {
                    fetchVariableValues(true);
                });
        } else {
            state.variableValues.splice(index, 1);
        }
    };

    const fetchVariableValues = (force = false) => {
        if (!state.variableValues.length || force) {
            axios.get(`/ui_api/repository/${promptFormState.value.repositoryId}/variable_values/${promptFormState.value.path}`).then(({data}) => {
                state.variableValues = data.variableValues;
            });
        }
    };

    return {
        state,
        fetchVariableValues,
        saveVariableValues,
        deleteVariableValues
    };
});
