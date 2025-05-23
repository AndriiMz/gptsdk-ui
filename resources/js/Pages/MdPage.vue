<script setup>
import {useFileForm} from "../stores/useFileForm.js";
import {storeToRefs} from "pinia";
import FileFormHeader from "../Common/Form/FileFormHeader.vue";

import {EditorContent, useEditor} from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import {onUnmounted, watch} from "vue";
import {Markdown} from 'tiptap-markdown';
import markdownit from 'markdown-it'


const fileFormStore = useFileForm()
const { state: fileFormState, formErrors, fileForm } = storeToRefs(fileFormStore)

const editor = useEditor({
    content: fileForm.value.content,
    extensions: [
        StarterKit,
        Markdown,
    ],
    onUpdate: ({ editor }) => {
        fileForm.value.content = editor.storage.markdown.getMarkdown();
    }
})


watch(() => fileForm.value.content, (newContent) => {
    if (editor.value && editor.value.storage.markdown.getMarkdown() !== newContent) {
        editor.value.commands.setContent(newContent);
    }
});



onUnmounted(() => {
    editor.value.destroy()
})

//https://github.com/sindresorhus/generate-github-markdown-css
</script>

<template>
    <FileFormHeader file-extension="md" save-file-toast-msg="File has saved successfully!" />

    <div class="rounded-lg border border-gray-100 dark:border-gray-900 dark:bg-gray-900 p-4">
        <EditorContent
            class="markdown-body"
            :editor="editor"
            ref="richTextarea"/>
    </div>

</template>

<style lang="scss">
.ProseMirror {
    min-height: 80vh;
}

.ProseMirror-focused {
    outline: none;
}
</style>
