<script setup lang="ts">
import Preview from '@/components/Preview.vue';
import { Media } from '@/types';
import { useScroll } from '@vueuse/core';
import { ArrowLeft, ArrowRight } from 'lucide-vue-next';
import { onMounted, ref, useTemplateRef } from 'vue';

defineProps<{
    media: Media[];
}>();

const SCROLL_MOVE_VALUE = 100;

const container = useTemplateRef('container');

const { x } = useScroll(container, { behavior: 'smooth' });

const hasHorizontalOverflow = ref<boolean>(false);
const isAtLeftEdge = ref<boolean>(true);
const isAtRightEdge = ref<boolean>(false);

onMounted(() => {
    updateOverflowStatus();
});

const updateOverflowStatus = (): void => {
    hasHorizontalOverflow.value = container.value!.scrollWidth > container.value!.clientWidth;

    isAtLeftEdge.value = container.value!.scrollLeft === 0;

    isAtRightEdge.value = container.value!.scrollLeft + container.value!.offsetWidth >= container.value!.scrollWidth;
};

window.addEventListener('resize', updateOverflowStatus);
</script>

<template>
    <div class="flex gap-1.5 overflow-auto" ref="container" @scroll="updateOverflowStatus">
        <button
            class="bg-input sticky left-0 z-10 cursor-pointer px-1.5 duration-300"
            :class="{ 'shadow-right': !isAtLeftEdge }"
            v-if="hasHorizontalOverflow"
            @click="x -= SCROLL_MOVE_VALUE"
        >
            <ArrowLeft class="size-4" />
        </button>
        <template v-for="item in media" :key="item.filename">
            <Preview :item />
        </template>
        <button
            class="bg-input sticky right-0 z-10 cursor-pointer px-1.5 duration-300"
            :class="{ 'shadow-left': !isAtRightEdge }"
            v-if="hasHorizontalOverflow"
            @click="x += SCROLL_MOVE_VALUE"
        >
            <ArrowRight class="size-4" />
        </button>
    </div>
</template>
