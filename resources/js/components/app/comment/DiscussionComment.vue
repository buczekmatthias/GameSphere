<script setup lang="ts">
import DeleteActionLink from '@/components/app/DeleteActionLink.vue';
import FallbackContentAuthor from '@/components/app/FallbackContentAuthor.vue';
import FormActionTap from '@/components/app/FormActionTap.vue';
import MediaPreview from '@/components/app/MediaPreview.vue';
import ReportModal from '@/components/app/ReportModal.vue';
import TextLink from '@/components/TextLink.vue';
import { DiscussionComment } from '@/types';
import { Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        comment: DiscussionComment;
        asCard?: boolean;
        backToDiscussion?: boolean;
    }>(),
    {
        asCard: true,
        backToDiscussion: false,
    },
);
</script>

<template>
    <div class="flex flex-col gap-4" :class="{ 'bg-card/70 rounded-md px-3 py-2.5': asCard }">
        <div class="grid grid-cols-[1fr_auto] gap-2">
            <FallbackContentAuthor :user="comment.user" v-if="'user' in comment" />
            <p class="self-center text-sm text-slate-300">{{ comment.created_at }}</p>
            <TextLink :href="route('discussions.show', { discussion: comment.discussion.slug })" v-if="'discussion' in comment">
                View discussion
            </TextLink>
        </div>
        <p>{{ comment.content }}</p>

        <MediaPreview :media="comment.media" v-if="comment.media.length > 0" />

        <div class="flex w-full items-center gap-5">
            <ReportModal :contentId="comment.slug" contentType="comment" triggerClass="text-destructive text-sm" />
            <Link
                as="button"
                :href="route('comments.edit', backToDiscussion ? { comment: comment.slug, back_to_discussion: true } : { comment: comment.slug })"
                v-if="comment.permissions.update"
            >
                <FormActionTap>Edit</FormActionTap>
            </Link>
            <DeleteActionLink :href="route('comments.destroy', { comment: comment.slug })" v-if="comment.permissions.destroy" />
        </div>
    </div>
</template>
