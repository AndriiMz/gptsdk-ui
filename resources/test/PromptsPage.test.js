import {describe, expect, test, vi} from 'vitest'
import {mount} from '@vue/test-utils'
import PromptPage from "../js/Pages/PromptPage.vue";
import {createPinia, setActivePinia} from "pinia";
import {nextTick, reactive} from "vue";
import {vueWrapperOptions} from "./helper.js";
import axios from "axios";
import ValuesModal from "../js/Components/Modals/ValuesModal.vue";
import Modals from "../js/Common/Modals.vue";
import PromptsPage from "../js/Pages/PromptsPage.vue";

vi.mock('axios');

setActivePinia(createPinia())

vi.mock('@inertiajs/vue3',async (importOriginal) => ({
    __esModule: true,
    ...await importOriginal('@inertiajs/vue3'),
    usePage: () => ({
        props: {
            path: '/',
            files: [
                {type: 'file', name: 'prompt1.prompt', isEditable: true, path: '/prompt1.prompt', sha: ''},
                {type: 'dir', name: 'prompts', isEditable: false, path: '/prompts', sha: ''}
            ],
            branches: [{name: 'main'}],
            repository: {
                id: 1,
                name: 'Repo',
                subscriptionStatus: 'paid'
            }
        },

    })
}))


describe('Prompts Page', () => {
    test('Prompts Page > Paid Repository > Edit Prompt', async () => {
        const wrapper = mount(PromptsPage, vueWrapperOptions)
        await nextTick()

        expect(wrapper.findAll('[data-testid="Action.editPrompt"]').length).toBe(1)
    })
})

