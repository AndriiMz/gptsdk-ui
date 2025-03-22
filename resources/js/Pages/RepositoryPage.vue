<script setup>

import {InputText, Button, IftaLabel, Select, Message, useToast, ConfirmPopup, useConfirm, Dialog} from "primevue";

import {Head, useForm, usePage} from "@inertiajs/vue3";
import LayoutTitle from "../Common/LayoutTitle.vue";
import {computed, reactive} from "vue";
import Error from "../Common/Form/Error.vue";
import axios from "axios";

const page = usePage()
const toast = useToast()
const confirm = useConfirm();
const repository = computed(() => {
    return page.props.repository
})


const repositoryState = reactive({
    isTokenInstructionOpen: false
})

const form = useForm({
    type: 'github',
    token: '',
    url: repository.value?.url,
    _token: usePage().props.csrf_token
})

const saveRepository = () => {
    form.post(
        `/repository/${repository.value?.id ?? ''}`,
        {
            replace: false,
            onSuccess: () => {
                toast.add({
                    severity: 'info',
                    summary: 'Info',
                    detail: 'Repository has saved successfully!',
                    life: 2000
                });
            }
        }
    )
}

const deleteState = reactive({
    confirmationValue: ''
})

const deleteRepository = (event) => {
    confirm.require({
        target: event.currentTarget,
        group: 'headless',
        message: 'Are you sure you want to delete this repository? If yes, please type DELETE.',
        accept: () => {
            axios.delete(`/ui_api/repository/${repository.value.id}`).then(() => {
                toast.add({
                    severity:'info',
                    summary:'Info',
                    detail:'Repository deleted successfully!',
                    life: 3000
                });

                setTimeout(() => {
                    window.location.pathname = '/'
                }, 2000)
            })
        },
        reject: () => {}
    });
}


</script>

<template>
    <Head :title="repository?.name ?? 'New Repository'" />

    <LayoutTitle>
        <template v-if="repository">
            "{{repository.name}}" settings
        </template>
        <template v-else>
            New Repository
        </template>
    </LayoutTitle>


    <div class="flex flex-col gap-4 max-w-96">
        <IftaLabel>
            <Select
                :inputId="`repository-type`"
                :options="['github']"
                type="text"
                class="w-full"
                v-model="form.type" />
            <label :for="`repository-type`">Repository Type</label>
        </IftaLabel>


        <div class="flex flex-col gap-2">
            <IftaLabel>
                <label>GitHub Fine-grained Token</label>
                <InputText
                    size="small"
                    type="text"
                    class="w-full"
                    :invalid="form.errors.token"
                    v-model="form.token" />

                <Error :error="form.errors.token" />
            </IftaLabel>

            <Message
                icon="pi pi-info-circle"
                severity="secondary">
                Create new <a href="https://github.com/settings/personal-access-tokens/new"
                              class="text-blue-600"
                              target="_blank">GitHub Fine-grained Token</a>.
                <br/>

                Restrict access to your repository and select the following permissions: <b>Contents: Read and Write</b>.
                <a href="#" @click="repositoryState.isTokenInstructionOpen=true" class="link">Read More</a>.
            </Message>


            <Message
                icon="pi pi-lock"
                severity="success" >
                GitHub Fine-grained Token has been encrypted using AES-256
            </Message>
        </div>

        <div class="flex flex-col gap-2">
            <IftaLabel>
                <label>Github Repository URL</label>
                <InputText
                    size="small"
                    type="text"
                    class="w-full"
                    :invalid="form.errors.url"
                    v-model="form.url" />

                <Error :error="form.errors.url" />
            </IftaLabel>

            <Message
                icon="pi pi-info-circle"
                severity="secondary">
               Copy the repository URL from the browser address bar.
            </Message>
        </div>

        <Button type="submit" @click="saveRepository">Save</Button>
    </div>

    <div v-if="repository">
        <hr class="my-6 bg-gray-100 dark:bg-black border-0 h-px" />

        <LayoutTitle>
            Danger Zone
        </LayoutTitle>

        <div class="flex flex-col gap-2 max-w-96">
            <Message
                icon="pi pi-exclamation-circle"
                severity="error">
                This function removes the repository from the GptSdk, but the repository on GitHub remains untouched.
                The Stripe subscription will be cancelled automatically.
            </Message>

            <Button type="submit"
                    severity="danger"
                    @click="deleteRepository">Delete Repository</Button>
        </div>


        <!-- TODO: create component -->
        <ConfirmPopup group="headless">
            <template #container="{ message, acceptCallback, rejectCallback }">
                <div class="rounded p-4">
                    <div class="flex flex-col gap-2 max-w-80">
                        <span>{{ message.message }}</span>
                        <InputText v-model="deleteState.confirmationValue" />
                    </div>

                    <div class="flex items-center gap-2 mt-4">
                        <Button label="Save"
                                :disabled="deleteState.confirmationValue!='DELETE'"
                                @click="acceptCallback" size="small"></Button>
                        <Button label="Cancel"
                                outlined
                                @click="rejectCallback"
                                severity="secondary"
                                size="small"
                                text></Button>
                    </div>
                </div>
            </template>
        </ConfirmPopup>
    </div>


    <Dialog v-model:visible="repositoryState.isTokenInstructionOpen"
            modal
            header="How to Generate a GitHub Personal Access Token for GptSdk"
            :style="{ width: '50vw', maxHeight: '70vh' }"
            @keydown.enter="repositoryState.isTokenInstructionOpen=false">
        <div class="flex flex-col gap-2">
            <p>Follow these steps to create a <b>Personal Access Token (PAT)</b> with the required permissions for GptSdk:</p>

            <ol>
                <li class="pb-2">
                    <p class="caption">1. Open GitHub Token Page</p>
                    <a href="https://github.com/settings/tokens/new" class="link" target="_blank">Click here to generate a new token</a>
                </li>
                <li class="pb-2">
                    <p class="caption">2. Set Token Name & Expiration</p>
                    Name your token: "GptSdk Token"<br>
                    Set an expiration date (or choose "No expiration").
                </li>
                <li class="pb-2">
                    <p class="caption">3. Restrict Access to a Single Repository</p>
                    Under Repository access, select "Only select repositories".<br>
                    Choose the repository you want to use with GptSdk.
                </li>
                <li class="pb-2 text-red-500">
                    <p class="caption">4. Set Required Permissions</p>

                    Under Repository permissions, find <b>"Contents"</b>.<br>
                    Set Contents to <b>"Read and write"</b>.
                </li>
                <li class="pb-2">
                    <p class="caption">5. Generate & Save Token</p>

                    Click Generate token.<br>
                    Copy the token and keep it safe—you won’t see it again!
                </li>
                <li class="pb-2">
                    <p class="caption">6. Use the Token in GptSdk </p>
                    Paste the token into GptSdk when prompted.
                </li>
            </ol>

            <p>Done! 🎉 Your GitHub token is ready for use. 🚀</p>
        </div>
    </Dialog>
</template>
