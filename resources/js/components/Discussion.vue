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
        <p class="truncate text-xl font-semibold">{{ discussion.title }}</p>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex gap-0.5">
                <Calendar class="h-5" />
                <p class="text-sm">{{ discussion.created_at }}</p>
            </div>
            <div class="flex gap-0.5" v-if="discussion.author">
                <User class="h-5" />
                <TextLink class="text-sm" :href="route('user.profile', { user: discussion.author.username })">{{ discussion.author.name }}</TextLink>
            </div>
            <div class="flex gap-0.5">
                <MessageCircle class="h-5" />
                <p class="text-sm">{{ discussion.comments_count }}</p>
            </div>
            <slot name="extra-items"></slot>
        </div>
        <TextLink :href="route('discussions.show', { discussion: discussion.slug })" class="self-start">Read discussion</TextLink>
    </div>
</template>
