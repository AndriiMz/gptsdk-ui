import {defineStore} from "pinia";
import {nextTick, reactive} from "vue";
import {useForm} from "@inertiajs/vue3";
import {useConfirm, useToast} from "primevue";
import _ from "lodash";
import {AiVendorType} from "../types/aiVendorType.ts";
import axios from "axios"

export const useAiApiKeys = defineStore('aiApiKeys', () => {
    const toast = useToast()
    const confirm = useConfirm()

    const state = reactive({
        aiApiKeys: [],
        aiApiKeysById: {},
        isApiKeyModalOpen: false,
        editApiKeyId: null,
    })

    const aiApiKeyForm = useForm({
        name: '',
        aiVendor: '',
        key: '',
        defaultModel: ''
    })

    const fetchAiApiKeys = (force = false) => {
        if (!state.aiApiKeys.length || force) {
            axios.get('/ui_api/ai_api_key').then(({data}) => {
                state.aiApiKeys = data.aiApiKeys
                state.aiApiKeysById = _.keyBy(state.aiApiKeys, 'id')
            })
        }
    }

    const editApiKey = (apiKeyData) => {
        state.editApiKeyId = apiKeyData.id
        Object.assign(aiApiKeyForm, apiKeyData)
        state.isApiKeyModalOpen = true
    }

    const addKey = () => {
        state.editApiKeyId = null
        Object.assign(aiApiKeyForm, {
            name: '',
            aiVendor: AiVendorType.OPENAI,
            key: '',
            defaultModel: ''
        })
        state.isApiKeyModalOpen = true
    }

    const saveKey = () => {
        if (aiApiKeyForm.processing) {
            return
        }

        aiApiKeyForm.post(
            state.editApiKeyId ? `/ai_api_key/${state.editApiKeyId}` : `/ai_api_key`,
            {
                replace: false,
                onSuccess: () => {
                    useAiApiKeys().fetchAiApiKeys(true)
                    state.isApiKeyModalOpen = false
                    state.editApiKeyId = null

                    toast.add({
                        severity: 'info',
                        summary: 'Info',
                        detail: 'Api Key has saved successfully!',
                        life: 2000
                    });
                }
            })
    }

    const setKeyName = async () => {
        await nextTick()
        if (!aiApiKeyForm.name.length && aiApiKeyForm.key.length > 10) {
            aiApiKeyForm.name = '***' + aiApiKeyForm.key.substring(
                aiApiKeyForm.key.length - 8,
                aiApiKeyForm.key.length
            )
        }
    }

    const deleteKey = (aiApiKeyId) => {
        confirm.require({
            message: 'Are you sure you want to delete Api Key? This will delete all related AI connectors.',
            header: 'Confirmation',
            icon: 'pi pi-exclamation-triangle',
            rejectProps: {
                label: 'Cancel',
                severity: 'secondary',
                outlined: true
            },
            acceptProps: {
                label: 'Yes'
            },
            accept: () => {
                axios.delete(`/ui_api/ai_api_key/${aiApiKeyId}`).then(() => {
                    toast.add({
                        severity: 'info',
                        summary: 'Info',
                        detail: 'Api Key has deleted successfully!',
                        life: 2000
                    });

                    fetchAiApiKeys(true)
                })
            }
        })
    }

    return {
        aiApiKeyForm,
        state,
        addKey,
        saveKey,
        editApiKey,
        deleteKey,
        fetchAiApiKeys,
        setKeyName
    }
})
