<script setup lang="ts">
import DeleteActionLink from '@/components/app/DeleteActionLink.vue';
import UpdateDiscussionForm from '@/components/app/discussion/UpdateDiscussionForm.vue';
import FallbackContentAuthor from '@/components/app/FallbackContentAuthor.vue';
import FormActionTap from '@/components/app/FormActionTap.vue';
import ReportModal from '@/components/app/ReportModal.vue';
import TextLink from '@/components/TextLink.vue';
import { DiscussableGame, DiscussableGenre, Discussion } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Blocks, Calendar, Gamepad2, Lock, MessageCircle } from 'lucide-vue-next';

defineProps<{
    discussion: Discussion;
}>();
</script>

<template>
    <div class="dark:bg-card/70 flex flex-col gap-4 rounded-md px-3 py-2.5 shadow-lg dark:shadow-none">
        <FallbackContentAuthor :user="discussion.author" />
        <p class="col-span-full mb-2 text-2xl">{{ discussion.title }}</p>
        <div class="flex flex-wrap gap-3">
            <div class="flex flex-wrap items-center gap-1" v-if="discussion.is_locked">
                <Lock class="size-5" />
                <p>Discussion has been locked</p>
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <Calendar class="size-5" />
                <p>{{ discussion.created_at }}</p>
            </div>
            <div class="flex items-center gap-1">
                <MessageCircle class="size-5" />
                <p>{{ discussion.comments_count }}</p>
            </div>
            <div class="flex items-center gap-1">
                <template v-if="discussion.discussable">
                    <template v-if="discussion.discussable_type === 'game'">
                        <Gamepad2 class="size-5" />
                        <TextLink :href="route('games.show', { game: discussion.discussable.slug })">
                            {{ (discussion.discussable as DiscussableGame).title }}
                        </TextLink>
                    </template>
                    <template v-else>
                        <Blocks class="size-5" />
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
            <UpdateDiscussionForm v-if="discussion.permissions.update" :old-title="discussion.title" :slug="discussion.slug" />
            <DeleteActionLink :href="route('discussions.destroy', { discussion: discussion.slug })" v-if="discussion.permissions.destroy" />
            <Link
                :href="route('discussions.lock.toggle', { discussion: discussion.slug })"
                v-if="discussion.permissions.lock"
                method="patch"
                as="button"
            >
                <FormActionTap>
                    {{ discussion.is_locked ? 'Unlock' : 'Lock' }}
                </FormActionTap>
            </Link>
        </div>
    </div>
</template>
