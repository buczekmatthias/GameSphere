<script setup lang="ts">
import LoginRequired from '@/components/LoginRequired.vue';
import MainContainer from '@/components/MainContainer.vue';
import NewCommentForm from '@/components/NewCommentForm.vue';
import PaginatedContent from '@/components/PaginatedContent.vue';
import Comment from '@/components/Partials/Discussion/Show/Comment.vue';
import DiscussionBaseInformation from '@/components/Partials/Discussion/Show/DiscussionBaseInformation.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Permissions } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    discussion: DiscussionType;
    permissions: Permissions;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Discussions',
        href: route('discussions.index'),
    },
    {
        title: props.discussion.shortTitle,
        href: route('discussions.show', { discussion: props.discussion.slug }),
    },
];
</script>

<template>
    <Head :title="discussion.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-5xl flex-col gap-6">
            <DiscussionBaseInformation :discussion :permissions />

            <div class="flex flex-col gap-4">
                <PaginatedContent :pagination="discussion.comments" pagination-position="bottom">
                    <Comment :comment v-for="comment in discussion.comments.data" :key="comment.slug" />
                </PaginatedContent>
                <LoginRequired>
                    <NewCommentForm :discussion-slug="discussion.slug" />
                </LoginRequired>
            </div>
        </MainContainer>
    </AppLayout>
</template>
