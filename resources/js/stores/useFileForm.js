import {defineStore} from "pinia";
import {useForm, usePage} from "@inertiajs/vue3";
import {computed, reactive, watch} from "vue";
import dot from "dot-object";
import {NEW_MD, NEW_PROMPT} from "../types/defaults";
import {useToast} from "primevue";

export const useFileForm = defineStore('fileForm', () => {
    const page = usePage()
    const toast = useToast()

    const fileForm = useForm({
        sha: page.props.file?.sha ?? null,
        content: page.props.file?.content,
        path: page.props.path
    })

    watch(() => page.props.file, (newValue) => {
        fileForm.sha = newValue.sha
    })

    const state = reactive({
        repositoryId: page.props.repository.id,
        extension: page.props.path ? page.props.path.split('.').pop() : ''
    })

    const formErrors = computed(() => {
        return dot.object(
            fileForm.errors
        );
    })

    const saveFile = (toastMsg) => {
        fileForm
            .transform((data) => ({
                ...data,
                content: JSON.stringify(data.content)
            }))
            .post(
            `/repository/${state.repositoryId}/file`,
            {
                replace: false,
                onSuccess: () => {
                    toast.add({
                        severity: 'info',
                        summary: 'Info',
                        detail: toastMsg,
                        life: 2000
                    });
                }
            }
        )
    }


    const pathProxy = computed({
        set: (newValue) => {
            if (!newValue.endsWith('.' + state.extension)) {
                newValue += '.' + state.extension
            }

            fileForm.path = newValue;
        },
        get: () => {
            if (!fileForm.path) {
                return ''
            }

            return fileForm.path.replace(new RegExp('\\.' + state.extension + '$'), '')
        }
    })

    const paste = (content) => {
        console.log(content)
        if (fileForm.path.endsWith('.prompt')) {
            fileForm.content = fileForm.content.messages[0].content = content
        } else {
            fileForm.content = content;
        }
    }

    return {
        pathProxy,

        formErrors,
        fileForm,
        state,

        paste,
        saveFile
    }
})
