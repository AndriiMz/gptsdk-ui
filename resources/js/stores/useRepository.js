import {defineStore} from "pinia";
import {computed, reactive} from "vue";
import {usePage} from "@inertiajs/vue3";

export const useRepository = defineStore('useRepository', () => {
    const page = usePage()

    const repository = computed(() => {
        return page.props.repository
    })


    return {
        repository
    }
})
