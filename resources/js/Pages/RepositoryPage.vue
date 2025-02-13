<script setup>

import {InputText, Button, IftaLabel, Select, Message, useToast} from "primevue";

import {Head, useForm, usePage} from "@inertiajs/vue3";
import LayoutTitle from "../Common/LayoutTitle.vue";
import {computed} from "vue";
import Error from "../Common/Form/Error.vue";

const page = usePage()
const toast = useToast()
const repository = computed(() => {
    return page.props.repository
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
                Restrict access to your repository and select the following permissions: <b>Contents</b>.
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
</template>
