<script setup lang="ts">
import DeleteActionLink from '@/components/app/DeleteActionLink.vue';
import FallbackContentAuthor from '@/components/app/FallbackContentAuthor.vue';
import FormActionTap from '@/components/app/FormActionTap.vue';
import MediaPreview from '@/components/app/MediaPreview.vue';
import ReportModal from '@/components/app/ReportModal.vue';
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
    <div class="flex flex-col gap-4" :class="{ 'dark:bg-card/70 rounded-md px-3 py-2.5 shadow-md dark:shadow-none': asCard }">
        <FallbackContentAuthor :user="comment.user" v-if="'user' in comment" />
        <p>{{ comment.content }}</p>

        <MediaPreview :media="comment.media" v-if="comment.media.length > 0" />

        <div class="flex w-full items-center gap-5">
            <p class="text-muted-foreground text-sm">{{ comment.created_at }}</p>
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
