<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue';
import Comment from '@/components/Partials/Discussion/Show/Comment.vue';
import UserInfo from '@/components/UserInfo.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, DiscussionComment } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, MessageCircle } from 'lucide-vue-next';

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
    <Head :title="comment.user ? `Comment by ${comment.user.name}` : 'Discussion comment'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-5xl flex-col gap-4">
            <Comment :comment :as-card="false" />

            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-4 rounded-md border p-3">
                    <Link class="mr-auto flex gap-3" :href="route('user.profile', { user: comment.user.username })" as="button" v-if="comment.user">
                        <UserInfo :show-username="true" :user="comment.user" />
                    </Link>
                    <p class="text-sm italic" v-else>Deleted user</p>
                    <Link :href="route('discussions.show', { discussion: comment.discussion.slug })">
                        <p class="text-xl">{{ comment.discussion.title }}</p>
                    </Link>
                    <div class="flex gap-5">
                        <div class="flex items-center gap-1.5">
                            <Calendar class="mt-0.5 size-4" />
                            <p>{{ comment.discussion.created_at }}</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <MessageCircle class="mt-0.5 size-4" />
                            <p>{{ comment.discussion.comments_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </MainContainer>
    </AppLayout>
</template>
