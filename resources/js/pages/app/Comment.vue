<script setup lang="ts">
import Modal from '@/components/Modal.vue';
import Preview from '@/components/Preview.vue';
import ReportModal from '@/components/ReportModal.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import UpdateCommentForm from '@/components/UpdateCommentForm.vue';
import UserInfo from '@/components/UserInfo.vue';
import { canInteract } from '@/composables/useCanInteract';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, DiscussionComment, Permissions } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Image, MessageCircle, Trash } from 'lucide-vue-next';

const props = defineProps<{
    comment: DiscussionComment;
    permissions: Permissions;
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
        <div class="main-container flex flex-col gap-3">
            <div class="flex items-center gap-4">
                <Link class="mr-auto flex gap-3" :href="route('user.profile', { user: comment.user.username })" as="button">
                    <UserInfo :show-username="true" :user="comment.user" />
                </Link>
                <div class="flex gap-3" v-if="canInteract() || permissions.update || permissions.destroy">
                    <UpdateCommentForm v-if="permissions.update" :old-content="comment.content" :media="comment.media" :slug="comment.slug" />
                    <Button variant="destructive" as-child>
                        <Link
                            as="button"
                            :href="route('comments.destroy', { comment: comment.slug, to_homepage: true })"
                            method="delete"
                            class="text-destructive cursor-pointer text-sm"
                            v-if="permissions.destroy"
                        >
                            <Trash class="size-4" />
                        </Link>
                    </Button>
                    <ReportModal
                        :contentId="comment.slug"
                        contentType="comment"
                        :show-text="false"
                        show-icon
                        :triggerClass="`cursor-pointer text-sm ${buttonVariants({ variant: 'destructive' })}`"
                    />
                </div>
            </div>
            <p>{{ comment.content }}</p>
            <div class="flex gap-4">
                <p class="text-sm text-slate-300">{{ comment.created_at }}</p>
                <div v-if="comment.media.length > 0">
                    <Modal trigger-class="flex gap-2 items-center">
                        <template #trigger>
                            <Image class="size-4" />
                            <p class="text-sm">{{ comment.media.length }}</p>
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
            </div>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-4 rounded-md border p-3">
                    <Link class="mr-auto flex gap-3" :href="route('user.profile', { user: comment.user.username })" as="button">
                        <UserInfo :show-username="true" :user="comment.discussion.author" />
                    </Link>
                    <Link :href="route('discussions.show', { discussion: comment.discussion.slug })">
                        <p class="text-xl">{{ comment.discussion.title }}</p>
                    </Link>
                    <div class="flex gap-3">
                        <p class="">{{ comment.discussion.created_at }}</p>
                        <div class="flex items-center gap-2">
                            <MessageCircle class="size-4" />
                            <p>{{ comment.discussion.comments_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
