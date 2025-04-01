<script setup>

import { Head } from '@inertiajs/vue3'
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";
import {Button, Message} from "primevue";


const page = usePage()
const repositoryId = computed(() => {
    return page.props.repositoryId
})



</script>

<template>
    <Head title="expired" />

    <div class="flex flex-col gap-2 max-w-96">

        <Message icon="pi pi-calendar-times" severity="error">
            The repository key has expired.
            Please visit the repository settings to set a new key and restore access.
        </Message>

        <div>
            <Button
                icon="pi pi-cog"
                as="a"
                severity="secondary"
                label="Repository Settings"
                :href="`/repository/${repositoryId}/settings`"/>
        </div>
    </div>
</template>
