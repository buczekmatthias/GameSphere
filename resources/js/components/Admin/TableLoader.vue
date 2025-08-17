<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const show = ref<boolean>(false);

router.on('start', () => {
    show.value = true;
});

router.on('finish', () => {
    show.value = false;
});
</script>
<template>
    <div v-if="show" class="bg-background/70 fixed top-0 left-0 z-50 flex h-screen w-full items-center justify-center">
        <LoaderCircle class="size-18 animate-spin" />
    </div>
</template>
