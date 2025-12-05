<script setup lang="ts">
import DeleteActionLink from '@/components/DeleteActionLink.vue';
import ReportModal from '@/components/ReportModal.vue';
import TextLink from '@/components/TextLink.vue';
import UpdateDiscussionForm from '@/components/UpdateDiscussionForm.vue';
import { DiscussableGame, DiscussableGenre, Discussion, Permissions } from '@/types';
import { Blocks, Calendar, Gamepad2, MessageCircle, User } from 'lucide-vue-next';

defineProps<{
    discussion: Discussion;
    permissions: Permissions;
}>();
</script>

<template>
    <div class="bg-card/70 flex flex-col gap-4 rounded-md px-3 py-2.5">
        <p class="col-span-full mb-2 text-2xl">{{ discussion.title }}</p>
        <div class="flex gap-3">
            <div class="flex flex-wrap items-center gap-1">
                <Calendar class="h-5" />
                <p>{{ discussion.created_at }}</p>
            </div>
            <div class="flex items-center gap-1">
                <MessageCircle class="h-5" />
                <p>{{ discussion.comments_count }}</p>
            </div>
            <div class="flex items-center gap-1">
                <User class="h-5" />
                <TextLink :href="route('user.profile', { user: discussion.author.username })" class="truncate" v-if="discussion.author">
                    {{ discussion.author.name }}
                </TextLink>
                <p class="text-sm italic" v-else>Deleted user</p>
            </div>
            <div class="flex items-center gap-1">
                <template v-if="discussion.discussable">
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
                </template>
                <p class="text-sm italic" v-else>Deleted {{ discussion.discussable_type }}</p>
            </div>
        </div>
        <div class="flex gap-5">
            <ReportModal trigger-class="text-destructive text-sm" :contentId="discussion.slug" contentType="discussion" />
            <UpdateDiscussionForm v-if="permissions.update" :old-title="discussion.title" :slug="discussion.slug" />
            <DeleteActionLink :href="route('discussions.destroy', { discussion: discussion.slug })" v-if="permissions.destroy">
                Delete
            </DeleteActionLink>
        </div>
    </div>
</template>
