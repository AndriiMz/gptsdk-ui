import {describe, expect, test, vi} from 'vitest'
import {config, mount} from '@vue/test-utils'
import {createPinia, setActivePinia} from "pinia";
import {nextTick} from "vue";
import {vueWrapperOptions} from "./helper.js";
import FilesPage from "../js/Pages/FilesPage.vue";
import {createHeadManager} from '@inertiajs/core';

vi.mock('axios');

setActivePinia(createPinia())

const mockedHeadManager = createHeadManager(
    false,
    () => '',
    () => '',
);
config.global.mocks.$headManager = mockedHeadManager;

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


describe('Files Page', () => {
    test('Files Page > Paid Repository > Edit Prompt', async () => {
        const wrapper = mount(FilesPage, vueWrapperOptions)
        await nextTick()

        expect(wrapper.findAll('[data-testid="Action.editPrompt"]').length).toBe(1)
    })
})

