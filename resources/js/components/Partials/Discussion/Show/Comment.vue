<script setup lang="ts">
import DeleteActionLink from '@/components/DeleteActionLink.vue';
import FormActionTap from '@/components/FormActionTap.vue';
import MediaPreview from '@/components/MediaPreview.vue';
import ReportModal from '@/components/ReportModal.vue';
import UserInfo from '@/components/UserInfo.vue';
import { DiscussionComment } from '@/types';
import { Link } from '@inertiajs/vue3';

defineProps<{
    comment: DiscussionComment;
}>();
</script>

<template>
    <div class="bg-card/70 flex flex-col gap-4 rounded-md px-3 py-2.5">
        <div class="grid grid-cols-[1fr_auto] gap-2">
            <Link class="mr-auto flex gap-3" :href="route('user.profile', { user: comment.user.username })" as="button" v-if="comment.user">
                <UserInfo :show-username="true" :user="comment.user" />
            </Link>
            <p class="text-sm italic" v-else>Deleted user</p>
            <p class="mr-auto self-center text-sm text-slate-300">{{ comment.created_at }}</p>
        </div>
        <p>{{ comment.content }}</p>

        <MediaPreview :media="comment.media" v-if="comment.media.length > 0" />

        <div class="flex w-full items-center gap-5">
            <ReportModal :contentId="comment.slug" contentType="comment" triggerClass="text-destructive text-sm" />
            <Link as="button" :href="route('comments.edit', { comment: comment.slug, back_to_discussion: true })">
                <FormActionTap>Edit</FormActionTap>
            </Link>
            <DeleteActionLink :href="route('comments.destroy', { comment: comment.slug })" v-if="comment.permissions.destroy">
                Delete
            </DeleteActionLink>
        </div>
    </div>
</template>
