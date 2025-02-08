import {defineStore} from "pinia";
import {reactive} from "vue";

export const useSubscription = defineStore('subscription', () => {
    const store = reactive({
        isSubscriptionModalOpen: false,
        repositoryId: null
    })

    const openSubscriptionModal = (repositoryId) => {
        store.isSubscriptionModalOpen = true
        store.repositoryId = repositoryId
    }

    return {
        openSubscriptionModal,

        store
    }
})
