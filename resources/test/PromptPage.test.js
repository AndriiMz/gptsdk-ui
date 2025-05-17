import {describe, expect, test, vi, beforeEach} from 'vitest'
import {mount, config} from '@vue/test-utils'
import PromptPage from "../js/Pages/PromptPage.vue";
import {createPinia, setActivePinia} from "pinia";
import {nextTick, reactive} from "vue";
import {vueWrapperOptions} from "./helper.js";
import axios from "axios";
import Modals from "../js/Common/Modals.vue";
import { createHeadManager } from '@inertiajs/core';

vi.mock('axios');

setActivePinia(createPinia())


const internalState = reactive({errors: {}})
axios.get = () => {
    return new Promise((resolve) => resolve({data: {
        aiApiKeys: [{name: 'My Key', id: 1, aiVendor: 'openai'}],
        aiConnectors:[],
        variableValues: []
    }}))
}
axios.delete = vi.fn()
axios.post = vi.fn(() => Promise.resolve({ data: {} }))

const mockedHeadManager = createHeadManager(
    false,
    () => '',
    () => '',
);
config.global.mocks.$headManager = mockedHeadManager;

vi.mock('@inertiajs/vue3',async (importOriginal) => ({
    __esModule: true,
    ...await importOriginal('@inertiajs/vue3'),
    useForm: (data) => {
        for (const key in data) {
            internalState[key] = data[key]
        }

        internalState.post = () => {}

        return internalState
    },
    usePage: () => ({
        props: {
            prompt: {
                sha: '',
                content: {
                    messages: [{role: 'User', content: 'Hello {{who}}!'}],
                    variables: [{name: 'who', type: 'string'}]
                }
            },
            path: 'prompt1.prompt',
            repository: {
                id: 1
            }
        },

    })
}))

beforeEach(() => {
    // Create a new Pinia instance and set it as active
    const pinia = createPinia();
    setActivePinia(pinia);

    const modals = mount(Modals, vueWrapperOptions)
});

describe('Prompt Page', () => {
    test('Prompt Page > Add Variable', async () => {
        const wrapper = mount(PromptPage, vueWrapperOptions)
        await nextTick()

        const addVariableButton = wrapper.find('[data-testid="Action.addVariable"]')
        expect(wrapper.text()).toContain('Variables')

        expect(
            wrapper.findAll('[data-testid="ListItem.variables"]').length
        ).toBe(1)

        addVariableButton.trigger('click')
        await nextTick()

        expect(
            wrapper.findAll('[data-testid="ListItem.variables"]').length
        ).toBe(2)
    })

    test('Prompt Page > Change Name', async () => {
        const wrapper = mount(PromptPage, vueWrapperOptions)
        await nextTick()

        const nameInput = wrapper.find('[data-testid="Input.name"]');
        expect(nameInput.attributes('value')).toBe('prompt1')
        nameInput.setValue('prompt2')
        await nextTick()
        expect(nameInput.attributes('value')).toBe('prompt2')
    })

    test('Prompt Page > Prompt Builder > Add Message', async () => {
        const wrapper = mount(PromptPage, vueWrapperOptions)
        await nextTick()

        const addMessageButton = wrapper.find('[data-testid="Action.addMessage"]')
        addMessageButton.trigger('click')
        await nextTick()
        expect(
            wrapper.findAll('[data-testid="ListItem.messages"]').length
        ).toBe(2)
    })

    test('Prompt Page > Prompt Builder > Paste/Copy Json', async () => {
        const wrapper = mount(PromptPage, vueWrapperOptions)
        await nextTick()

        const copyJsonButton = wrapper.find('[data-testid="Action.copyJson"]')
        copyJsonButton.trigger('click')
        await nextTick()
        const clipboardText = await navigator.clipboard.readText()
        expect(clipboardText).toBe(JSON.stringify([{role: 'User', content: 'Hello {{who}}!'}]))


        wrapper.find('[data-testid="Action.pasteJson"]').trigger('click')
        await nextTick()


        const inputJson =
            await vi.waitUntil(() => {
                return document.querySelector('[data-testid="Input.json"]')
            })
        inputJson.value = JSON.stringify([{role: 'User', content: 'Hello motherfucker!'}])
        inputJson.dispatchEvent(new Event('input'))
        await nextTick()


        const pasteButton = document.querySelector('[data-testid="Action.paste"]')
        pasteButton.click()
        await nextTick()

        copyJsonButton.trigger('click')
        await nextTick()
        const clipboardTextAfterPaste = await navigator.clipboard.readText()
        expect(clipboardTextAfterPaste).toBe(JSON.stringify([{role: 'User', content: 'Hello motherfucker!'}]))
    })

    test('Prompt Page > Prompt Test > Add Variable Value', async () => {
        const wrapper = mount(PromptPage, vueWrapperOptions)
        await nextTick()

        wrapper.find('[data-testid="Action.testEdit"] button:first-child').trigger('click')
        await nextTick()

        wrapper.find('[data-testid="Action.addVariableValues"]').trigger('click')
        await nextTick()


        const html = wrapper.html()
        const whoInput =
            await vi.waitUntil(() => {
                return document.querySelector('[data-testid="Input.value-who"]')
            })
        whoInput.value = 'Me';
        whoInput.dispatchEvent(new Event('input'))
        await nextTick()
        document.querySelector('[data-testid="Action.applyValues"]').click()
        await nextTick()

        expect(wrapper.findAll('[data-testid="ListItem.variableValues"]').length).toBe(1)
    })


    test('Prompt Page > Prompt Test > Add Connector', async () => {

        const wrapper = mount(PromptPage, vueWrapperOptions)
        await nextTick()

        wrapper.find('[data-testid="Action.testEdit"] button:first-child').trigger('click')
        await nextTick()

        wrapper.find('[data-testid="Action.addAiConnector"]').trigger('click')
        await nextTick()


        expect(
            wrapper.findAll('[data-testid="ListItem.aiConnectors"]').length
        ).toBe(1)

        //TODO: solve problem with select
        // wrapper.find('[data-testid="ListItem.aiConnectors"] [data-testid="Input.aiApiKey"] select').setValue(1)
        // await nextTick()

        // expect(
        //     wrapper.find('[data-testid="ListItem.aiConnectors"] [data-testid="Input.llmOptions"]').html()
        // ).toContain('model')
    })
})
