<script setup lang="ts">
import Modal from '@/components/Modal.vue';
import Preview from '@/components/Preview.vue';
import ReportModal from '@/components/ReportModal.vue';
import TextLink from '@/components/TextLink.vue';
import UpdateCommentForm from '@/components/UpdateCommentForm.vue';
import UserInfo from '@/components/UserInfo.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, DiscussionComment } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    comment: DiscussionComment;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Comment',
        href: '',
    },
    {
        title: props.comment.shortSlug,
        href: '',
    },
];
</script>

<template>
    <Head :title="`Comment: #${comment.slug}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-start gap-3 p-6 md:p-4">
            <Link class="mr-auto flex gap-3" :href="route('user.profile', { user: comment.user.username })" as="button">
                <UserInfo :show-username="true" :user="comment.user" />
            </Link>
            <p class="text-sm text-slate-300">{{ comment.created_at }}</p>
            <p>{{ comment.content }}</p>
            <div v-if="comment.media.length > 0">
                <Modal>
                    <template #trigger>
                        <p class="cursor-pointer text-sm text-sky-600 dark:text-sky-400">Show {{ comment.media.length }} media</p>
                    </template>
                    <template #title>
                        <p>Comment media</p>
                    </template>
                    <template #description>Media of "{{ comment.slug }}"</template>
                    <div class="flex max-h-[75vh] flex-col gap-6 overflow-y-auto">
                        <div v-for="media in comment.media" :key="media.path" class="flex flex-col gap-2">
                            <Preview :type="media.type" :media="media.path" />
                        </div>
                    </div>
                </Modal>
            </div>
            <div class="flex gap-4">
                <TextLink as="button" :href="route('discussions.show', { discussion: comment.discussion.slug })" class="cursor-pointer text-sm">
                    View discussion
                </TextLink>
                <UpdateCommentForm :old-content="comment.content" :media="comment.media" :slug="comment.slug" />
                <Link
                    as="button"
                    :href="route('comments.destroy', { comment: comment.slug, to_homepage: true })"
                    method="delete"
                    class="text-destructive cursor-pointer text-sm"
                >
                    Delete
                </Link>
                <ReportModal :contentId="comment.slug" contentType="comment" triggerClass="text-destructive cursor-pointer text-sm" />
            </div>
        </div>
    </AppLayout>
</template>
