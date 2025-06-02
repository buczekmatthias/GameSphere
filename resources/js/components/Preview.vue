<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

defineProps<{
    type: string;
    media: string;
}>();
</script>

<template>
    <template v-if="type === 'image'">
        <Avatar class="bg-input h-auto max-h-[40vh] w-full overflow-hidden rounded-lg">
            <AvatarImage :src="media" :alt="media" class="object-contain" />
            <AvatarFallback class="rounded-lg text-black dark:text-white">
                {{ media }}
            </AvatarFallback>
        </Avatar>
    </template>
    <template v-if="type === 'video'"></template>
</template>
