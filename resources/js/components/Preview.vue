<script setup lang="ts">
import { Media } from '@/types';
import { X } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    item: Media;
}>();

const isPreviewOpen = ref<boolean>(false);
</script>

<template>
    <button @click="isPreviewOpen = !isPreviewOpen">
        <slot />
    </button>

    <Teleport to="body">
        <div
            class="bg-preview fixed top-0 left-0 z-[999] grid h-dvh w-dvw grid-cols-1 grid-rows-[auto_1fr] gap-2"
            id="file-preview"
            v-if="isPreviewOpen"
            @click.stop
        >
            <div class="grid grid-cols-[1fr_auto] border-b">
                <p class="truncate p-3.5">{{ item.filename }}</p>
                <button @click="isPreviewOpen = false" class="cursor-pointer border-l p-3.5">
                    <X class="size-6" />
                </button>
            </div>
            <div class="flex items-center justify-center overflow-hidden p-2">
                <template v-if="item.type === 'image'">
                    <img :src="item.path" :alt="item.filename" class="max-h-full max-w-full object-contain" />
                </template>
                <template v-if="item.type === 'video'">
                    <video controls :src="item.path" class="max-h-full max-w-full"></video>
                </template>
            </div>
        </div>
    </Teleport>
</template>
