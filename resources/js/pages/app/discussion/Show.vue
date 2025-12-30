<script setup lang="ts">
import Comment from '@/components/app/comment/DiscussionComment.vue';
import DiscussionBaseInformation from '@/components/app/discussion/DiscussionBaseInformation.vue';
import NewCommentForm from '@/components/app/discussion/NewCommentForm.vue';
import LockedDiscussion from '@/components/app/LockedDiscussion.vue';
import LoginRequired from '@/components/app/LoginRequired.vue';
import MainContainer from '@/components/app/MainContainer.vue';
import PaginatedContent from '@/components/app/PaginatedContent.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    discussion: DiscussionType;
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
            <DiscussionBaseInformation :discussion />

            <div class="flex flex-col gap-4">
                <PaginatedContent :pagination="discussion.comments" pagination-position="bottom">
                    <Comment :comment v-for="comment in discussion.comments.data" :key="comment.slug" back-to-discussion />
                </PaginatedContent>
                <LoginRequired>
                    <LockedDiscussion :is-locked="discussion.is_locked && !discussion.permissions.lock">
                        <NewCommentForm :discussion-slug="discussion.slug" />
                    </LockedDiscussion>
                </LoginRequired>
            </div>
        </MainContainer>
    </AppLayout>
</template>
