<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { DiscussableGame, DiscussableGenre, Discussion } from '@/types';
import { Blocks, Calendar, Gamepad2, MessageCircle, User } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        discussion: Discussion;
        showUser?: boolean;
    }>(),
    {
        showUser: true,
    },
);
</script>

<template>
    <div class="border-border flex flex-col gap-3 rounded-md border border-solid p-3">
        <p class="truncate text-xl font-semibold">{{ discussion.title }}</p>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex gap-0.5">
                <Calendar class="h-5" />
                <p class="text-sm">{{ discussion.created_at }}</p>
            </div>
            <div class="flex gap-0.5" v-if="showUser">
                <User class="h-5" />
                <TextLink class="text-sm" :href="route('user.profile', { user: discussion.author.username })" v-if="discussion.author">
                    {{ discussion.author.name }}
                </TextLink>
                <p class="text-sm italic" v-else>Deleted user</p>
            </div>
            <div class="flex gap-0.5">
                <MessageCircle class="h-5" />
                <p class="text-sm">{{ discussion.comments_count }}</p>
            </div>
            <div class="flex items-center gap-1 text-sm" v-if="discussion.discussable_type">
                <template v-if="discussion.discussable_type === 'game'">
                    <Gamepad2 class="h-5" />
                    <TextLink :href="route('games.show', { game: discussion.discussable.slug })">
                        {{ (discussion.discussable as DiscussableGame).title }}
                    </TextLink>
                </template>
                <template v-else>
                    <Blocks class="h-5" />
                    <TextLink :href="route('genres.show', { genre: discussion.discussable.slug })">
                        {{ (discussion.discussable as DiscussableGenre).name }}
                    </TextLink>
                </template>
            </div>
        </div>
        <TextLink :href="route('discussions.show', { discussion: discussion.slug })" class="self-start">Read discussion</TextLink>
    </div>
</template>
