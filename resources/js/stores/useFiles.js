import {defineStore} from "pinia";
import {reactive} from "vue";
import axios from "axios";
import {usePage} from "@inertiajs/vue3";
import {NodeType} from "../types/nodeType";

export const useFiles = defineStore('useFiles', () => {
    const page = usePage()
    const state = reactive({
        repositoryId: page.props.repository.id,
        path: page.props.path,

        fileOptions: [],

        newFileOpen: false,
        newFileForm: {
            extension: '',
            path: ''
        }
    })

    const addNewFile = ({path, extension}) => {
        state.newFileOpen = true
        state.newFileForm = {
            extension: extension,
            path: path
        }

        console.log(state)
    }

    const loadFiles = async (path) => {
        return await axios.get(`/ui_api/repository/${state.repositoryId}/files/${path}`)
    }

    const createFileOption = (file) => {
        return {
            key: file.path,
            label: file.name,
            leaf: file.type !== NodeType.DIR,
            loading: false,
            children: null
        }
    }

    const initFileOptions = async () => {
        const {data} = await loadFiles('/')

        state.fileOptions = data.files.map((file) => createFileOption(file))
    }

    const loadFilesByNode = async (node) => {
        if (!node.children) {
            node.loading = true;
            const pathSegments = node.key.split('/').filter(Boolean);
            const {data} = await loadFiles(node.key);

            let currentLevel = state.fileOptions;
            // Traverse the path segments to find the correct nesting level
            for (let i = 0; i < pathSegments.length; i++) {
                const segment = pathSegments[i];
                const targetNode = currentLevel.find(item => item.label === segment);
                if (targetNode) {
                    if (i === pathSegments.length - 1) {
                        targetNode.children = data.files.map((file) => createFileOption(file));
                    } else {
                        currentLevel = targetNode.children;
                    }
                }
            }

            node.loading = false;
        }
    }


    return {
        loadFilesByNode,
        initFileOptions,
        addNewFile,

        state
    }
})
