<script setup>
import GithubButton from 'vue-github-button'
import { useLocalStorage } from '@vueuse/core'

const isBannerVisible = useLocalStorage('githubStarVisible', true)

</script>

<template>
    <div v-if="isBannerVisible"
         class="rounded p-3 bg-gray-50 dark:bg-gray-800 flex flex-col gap-2 relative">
        <i @click="isBannerVisible = false" class="cursor-pointer pi pi-times absolute top-3 end-3"></i>

        <p>If you find this library useful, please consider starring it on GitHub!</p>

        <!-- Place this tag where you want the button to render. -->
        <github-button href="https://github.com/saassdk/gptsdk-php" data-color-scheme="no-preference: light; light: light; dark: dark;" data-size="large" aria-label="Star saassdk/gptsdk-php on GitHub">Star</github-button>
    </div>
</template>
