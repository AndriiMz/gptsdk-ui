<script setup>

import {Button, Dialog} from "primevue";
import {useSubscription} from "../../stores/useSubscription.js";
import {storeToRefs} from "pinia";

const subscriptionStore = useSubscription()
const { store } = storeToRefs(subscriptionStore)

</script>

<template>

    <Dialog v-model:visible="store.isSubscriptionModalOpen"
            modal
            header="Upgrade to Pro: Full Repository Access 🚀"
            :style="{ width: '50vw', maxHeight: '70vh' }">
        <div class="flex flex-col gap-4 pt-1">
            <div class="
                p-5
                rounded-lg divide-gray-200 dark:divide-gray-800
                ring-1 ring-gray-200 dark:ring-gray-800
                shadow bg-white dark:bg-gray-900
                flex flex-col gap-2
            ">

                <h3 class="text-2xl font-bold">$24.99/month</h3>
                <p>Unlock powerful features to supercharge your AI workflows:</p>
                <ul class="list-disc pl-5 mt-2">
                    <li>📂 <code class="code">.prompt</code> file management – organize and version prompts effortlessly</li>
                    <li>⚡ Dynamic prompt execution – real-time updates with zero latency</li>
                    <li>🛠️ Advanced prompt testing – run multiple datasets & refine results</li>
                    <li>🤝 Team collaboration – work together seamlessly on prompt development</li>
                    <li>📜 Full prompt history – track changes and revert anytime</li>
                </ul>
            </div>

            <Button
                as="a"
                severity="success"
                icon="pi pi-credit-card"
                label="Purchase Subscription"
                :href="`/repository/${store.repositoryId}/purchase`" />
        </div>


    </Dialog>
</template>
