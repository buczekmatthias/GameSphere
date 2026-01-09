<script setup lang="ts">
import { cn } from '@/lib/utils';
import { ImageOff, LoaderCircle } from 'lucide-vue-next';
import { computed, getCurrentInstance, HTMLAttributes, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps<{
    src?: string;
    alt: string;
    fallback?: string;
    fallbackTextSize?: string;
    class?: HTMLAttributes['class'];
}>();

const imageLoaded = ref<boolean>(false);
const imageError = ref<boolean>(false);

const hasValidSrc = computed(() => {
    console.log(typeof props.src, props.src);
    return props.src && props.src.trim() !== '' && props.src !== undefined;
});

const instance = getCurrentInstance();
const lazyload = instance?.appContext.config.globalProperties.$Lazyload;

const onLoaded = (listener: any) => {
    if (listener.src === props.src) {
        imageLoaded.value = true;
        imageError.value = false;
    }
};

const onError = (listener: any) => {
    if (listener.src === props.src) {
        imageLoaded.value = false;
        imageError.value = true;
    }
};

const onLoading = (listener: any) => {
    if (listener.src === props.src) {
        imageLoaded.value = false;
        imageError.value = false;
    }
};

onMounted(() => {
    if (lazyload) {
        lazyload.$on('loaded', onLoaded);
        lazyload.$on('error', onError);
        lazyload.$on('loading', onLoading);
    }

    if (!hasValidSrc.value) {
        imageError.value = true;
    }
});

onUnmounted(() => {
    if (lazyload) {
        lazyload.$off('loaded', onLoaded);
        lazyload.$off('error', onError);
        lazyload.$off('loading', onLoading);
    }
});
</script>

<template>
    <div :class="cn('relative shrink-0 overflow-hidden rounded-lg', props.class)">
        <img v-lazy="src" :alt="alt" class="h-full w-full object-cover" v-if="hasValidSrc && !imageError" :key="`avatar-${alt}-${src}`" />
        <!-- Fallback -->
        <div v-if="!imageLoaded || imageError" class="bg-muted text-muted-foreground flex h-full w-full items-center justify-center">
            <span v-if="fallback" :class="fallbackTextSize">{{ fallback }}</span>
            <ImageOff class="size-1/3" v-else />
        </div>
        <!-- Loader -->
        <div v-if="!imageLoaded && !imageError" class="bg-muted absolute inset-0 flex items-center justify-center">
            <LoaderCircle class="size-1/3 animate-spin" />
        </div>
    </div>
</template>
