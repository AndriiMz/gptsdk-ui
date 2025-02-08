<script setup>

import {computed, defineProps} from "vue";
import {storeToRefs} from "pinia";

const props = defineProps({
    rows: {type: Object},
    depth: {type: Number, default: 0},
    store: {type: Object, default: 0},
    path: {type: String, default: ''}
})

const { state } = storeToRefs(props.store)
const { togglePath } = props.store

</script>

<template>
    <div class="flex flex-col gap-1 border border-gray-200 dark:border-gray-600 p-1 rounded">
        <div v-for="(childs, key) in rows"
             :class="{
                'pl-3': depth > 0,
                'flex-col': (typeof childs === 'object' && childs != null) || Array.isArray(childs)
             }"
             class="flex gap-2 items-start">
            <div v-if="!Array.isArray(rows)"
                 @click="togglePath(`${path}${path !== ''?'.':''}${key}`)"
                 class="flex items-center cursor-pointer">
                <template v-if="typeof childs === 'object' && childs !== null">
                    <i class="pi" :class="{
                        'pi-angle-right': state.collapsedPath[`${path}${path !== ''?'.':''}${key}`],
                        'pi-angle-down': !state.collapsedPath[`${path}${path !== ''?'.':''}${key}`]
                    }"/>
                </template>
                <b>{{key}}</b>
            </div>


            <div v-if="childs === null" class="text-red-400">
                NULL
            </div>
            <div v-else-if="typeof childs !== 'object' && !Array.isArray(childs)">
                {{childs}}
            </div>
            <JsonViewerRow :path="`${path}${path !== ''?'.':''}${key}`"
                           :rows="childs"
                           v-else-if="state.collapsedPath[`${path}${path !== ''?'.':''}${key}`] || Array.isArray(rows)"
                           :depth="depth + 1"
                           :store="store"/>
        </div>
    </div>
</template>
