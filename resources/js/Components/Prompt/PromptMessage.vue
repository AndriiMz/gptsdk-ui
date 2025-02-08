<script setup>
import { Select, Textarea } from 'primevue';
import { computed } from "vue";
import _ from "lodash";
import Error from "../../Common/Form/Error.vue";

const roles = [
  {name: 'System', role: 'system'},
  {name: 'Assistant', role: 'assistant'},
  {name: 'User', role: 'user'},
]
const props = defineProps({
    modelValue: {type: Object},
    index: {type: Number, default: 0},
    errors: {type: Object}
})

const emits = defineEmits([
    'update:modelValue',
    'onInput'
])

const setRole = (role) => {
    emits('update:modelValue', {
        role: role.role,
        content: props.modelValue.content
    })
}

const setContent = (content) => {
    emits('update:modelValue', {
        role: props.modelValue.role,
        content: content
    })
}

const selectedRole = computed(() => {
  return _.keyBy(roles, 'role')[props.modelValue.role] ?? {}
})

</script>

<template>
    <div class="flex gap-4 flex-col shadow-sm border border-gray-100 dark:border-gray-800 p-4 rounded">
        <div class="flex justify-between items-center">
            <div class="w-fit">
                <Select
                    :model-value="selectedRole"
                    @update:model-value="(role) => setRole(role)"
                    :options="roles"
                    optionLabel="name"
                    placeholder="Role"
                    :invalid="errors?.role"
                    />

                <Error :error="errors?.role" />
            </div>
            <slot name="actions"></slot>
        </div>

      <div class="grow w-full">
        <Textarea
            :invalid="errors?.content"
            autoResize
            :name="`prompt-message-${index}`"
            class="w-full textarea textarea-bordered"
            :model-value="modelValue.content"
           @input="(e) => emits('onInput', e)"
           @mousedown="(e) => emits('onInput', e)"
           @update:model-value="(m) => setContent(m)"
           rows="5" />
        <Error :error="errors?.content" />
        <div></div>
      </div>
    </div>
</template>
