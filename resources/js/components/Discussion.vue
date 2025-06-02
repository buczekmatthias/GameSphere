<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Discussion } from '@/types';
import { Calendar, MessageCircle, User } from 'lucide-vue-next';

defineProps<{
    discussion: Discussion;
}>();
</script>

<template>
    <div class="border-border flex flex-col gap-3 rounded-md border border-solid p-3">
        <p class="text-xl font-semibold">{{ discussion.title }}</p>
        <div class="flex gap-2">
            <div class="flex gap-0.5">
                <Calendar class="h-5" />
                <p class="text-sm">{{ discussion.created_at }}</p>
            </div>
            <div class="flex gap-0.5">
                <User class="h-5" />
                <p class="truncate text-sm">{{ discussion.author }}</p>
            </div>
            <div class="flex gap-0.5">
                <MessageCircle class="h-5" />
                <p class="text-sm">{{ discussion.comments_count }}</p>
            </div>
        </div>
        <TextLink :href="route('discussions.show', { discussion: discussion.slug })" class="self-start">Read discussion</TextLink>
    </div>
</template>
