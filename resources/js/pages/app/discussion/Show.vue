<script setup lang="ts">
import LoginRequired from '@/components/LoginRequired.vue';
import MainContainer from '@/components/MainContainer.vue';
import Modal from '@/components/Modal.vue';
import NewCommentForm from '@/components/NewCommentForm.vue';
import Pagination from '@/components/Pagination.vue';
import Preview from '@/components/Preview.vue';
import ReportModal from '@/components/ReportModal.vue';
import TextLink from '@/components/TextLink.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import UpdateCommentForm from '@/components/UpdateCommentForm.vue';
import UpdateDiscussionForm from '@/components/UpdateDiscussionForm.vue';
import UserInfo from '@/components/UserInfo.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, DiscussableGame, DiscussableGenre, Discussion as DiscussionType, Permissions } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Blocks, Calendar, Gamepad2, MessageCircle, Trash, User } from 'lucide-vue-next';

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
        <MainContainer class="flex flex-col gap-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-[auto_auto_auto_auto] lg:self-start">
                <p class="col-span-full mb-2 text-2xl">{{ discussion.title }}</p>
                <div class="flex gap-3">
                    <div class="flex items-center gap-1">
                        <Calendar class="h-5" />
                        <p>{{ discussion.created_at }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <MessageCircle class="h-5" />
                        <p>{{ discussion.comments_count }}</p>
                    </div>
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
                <div class="flex gap-3">
                    <ReportModal
                        show-icon
                        :show-text="false"
                        :contentId="discussion.slug"
                        contentType="discussion"
                        :triggerClass="buttonVariants({ variant: 'destructive' })"
                    />
                    <UpdateDiscussionForm v-if="permissions.update" :old-title="discussion.title" :slug="discussion.slug" />
                    <Button v-if="permissions.destroy" variant="destructive" as-child>
                        <Link :href="route('discussions.destroy', { discussion: discussion.slug })" method="delete" as="button">
                            <Trash class="size-4" />
                        </Link>
                    </Button>
                </div>
            </div>
            <div class="flex flex-col gap-4">
                <div
                    class="border-border flex flex-col items-start gap-3 rounded-md border border-solid p-2"
                    v-for="comment in discussion.comments.data"
                    :key="comment.slug"
                >
                    <Link class="mr-auto flex gap-3" :href="route('user.profile', { user: comment.user.username })" as="button" v-if="comment.user">
                        <UserInfo :show-username="true" :user="comment.user" />
                    </Link>
                    <p class="text-sm italic" v-else>Deleted user</p>
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
                    <div class="flex w-full items-center gap-3">
                        <p class="mr-auto text-sm text-slate-300">{{ comment.created_at }}</p>
                        <UpdateCommentForm
                            :old-content="comment.content"
                            :media="comment.media"
                            :slug="comment.slug"
                            v-if="comment.permissions.update"
                        />
                        <Button variant="destructive" as-child v-if="comment.permissions.destroy">
                            <Link
                                as="button"
                                :href="route('comments.destroy', { comment: comment.slug })"
                                :preserve-scroll="true"
                                method="delete"
                                class="text-destructive cursor-pointer text-sm"
                            >
                                <Trash class="size-4" />
                            </Link>
                        </Button>
                        <ReportModal
                            :contentId="comment.slug"
                            contentType="comment"
                            :show-text="false"
                            show-icon
                            :triggerClass="`${buttonVariants({ variant: 'destructive' })} cursor-pointer text-sm`"
                        />
                    </div>
                </div>
                <Pagination :pagination="discussion.comments" />
                <LoginRequired>
                    <NewCommentForm :discussion-slug="discussion.slug" />
                </LoginRequired>
            </div>
        </MainContainer>
    </AppLayout>
</template>
