import {ConfirmationService, useConfirm, useToast} from "primevue";
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";

export const vueWrapperOptions = {
    global: {
        components: {
            useToast,
            useConfirm
        },
        plugins: [PrimeVue, ToastService, ConfirmationService],
    }
}
