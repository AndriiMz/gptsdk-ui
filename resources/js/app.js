import '../css/app.css';
import 'primeicons/primeicons.css';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config';
import Layout from "./Common/Layout.vue";
import Aura from '@primevue/themes/aura';
import ToastService from 'primevue/toastservice';
import {createPinia} from "pinia";
import {ConfirmationService} from "primevue";
import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

createInertiaApp({
    title: (title) => {
        if (!title) {
            title = 'Prompt Management Tool'
        }

        return `GptSdk - ${title}`
    },
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        const page = pages[`./Pages/${name}.vue`]
        page.default.layout = page.default.layout || Layout

        return page
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .use(PrimeVue, {
                theme: {
                    preset: {
                        ...Aura,
                        options: {
                            darkModeSelector: 'system',
                        },
                        semantic: {
                            ...Aura.semantic,
                            primary: {
                                50: '{gray.50}',
                                100: '{gray.100}',
                                200: '{gray.200}',
                                300: '{gray.300}',
                                400: '{gray.400}',
                                500: '{gray.500}',
                                600: '{gray.600}',
                                700: '{gray.700}',
                                800: '{gray.800}',
                                900: '{gray.900}',
                                950: '{gray.950}'
                            }
                        }
                    },

                }
            })
            .use(ConfirmationService)
            .use(ToastService)
            .mount(el)
    },
    id: 'app',
})
