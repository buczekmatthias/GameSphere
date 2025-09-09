<script setup lang="ts">
import Discussion from '@/components/Discussion.vue';
import DiscussionSkeleton from '@/components/fallbacks/DiscussionSkeleton.vue';
import ReviewSkeleton from '@/components/fallbacks/ReviewSkeleton.vue';
import GameActionDropdown from '@/components/GameActionDropdown.vue';
import Modal from '@/components/Modal.vue';
import NewDiscussionForm from '@/components/NewDiscussionForm.vue';
import NewReviewForm from '@/components/NewReviewForm.vue';
import Pagination from '@/components/Pagination.vue';
import Preview from '@/components/Preview.vue';
import Review from '@/components/Review.vue';
import TextLink from '@/components/TextLink.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Skeleton } from '@/components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { isLoggedIn } from '@/composables/useIsLoggedIn';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Game, Pagination as PaginationType, Review as ReviewType, Ziggy } from '@/types';
import { Deferred, Head, usePage, WhenVisible } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        game: Game;
        userLists?: { [key: string]: boolean };
        reviews?: PaginationType & { data: ReviewType[] };
        discussions?: PaginationType & { data: DiscussionType[] };
    }>(),
    {
        userLists: () => ({}),
    },
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Games',
        href: route('games.index'),
    },
    {
        title: props.game.shortTitle,
        href: route('games.show', { game: props.game.slug }),
    },
];

const tab = computed(() => {
    const query = (usePage().props.ziggy as Ziggy & { query: { discussions_page?: number } }).query;

    return query?.discussions_page ? 'discussions' : 'reviews';
});
</script>

<template>
    <Head :title="game.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="main-container flex flex-col gap-4">
            <div class="grid grid-cols-[1fr_auto] gap-4 md:grid-cols-[auto_1fr_auto]">
                <Avatar class="h-80 w-64 overflow-hidden rounded-lg object-cover">
                    <AvatarImage :src="game.thumbnail" :alt="game.title" />
                    <AvatarFallback class="rounded-lg text-black dark:text-white" />
                </Avatar>
                <template v-if="isLoggedIn()">
                    <Deferred data="userLists">
                        <template #fallback>
                            <Skeleton class="h-24 w-12 self-end" />
                        </template>

                        <GameActionDropdown :game :lists="userLists" />
                    </Deferred>
                </template>
                <div class="flex flex-col gap-4 md:col-start-2 md:row-start-1">
                    <h1 class="text-3xl">{{ game.title }}</h1>
                    <p class="text-sm leading-[165%]">{{ game.description }}</p>
                    <p>
                        Genre: <TextLink :href="''">{{ game.genre.name }}</TextLink>
                    </p>
                    <p>
                        Release date: <TextLink :href="route('games.index', { released_at: game.released_at })">{{ game.released_at }}</TextLink>
                    </p>
                    <p>
                        Creator: <TextLink :href="route('user.profile', { user: game.creator.username })">{{ game.creator.name }}</TextLink>
                    </p>
                    <div v-if="game.media.length > 0">
                        <Modal>
                            <template #trigger>
                                <p class="cursor-pointer text-sm text-sky-600 dark:text-sky-400">Show {{ game.media.length }} media</p>
                            </template>
                            <template #title>
                                <p>Game media</p>
                            </template>
                            <template #description>Media of "{{ game.title }}"</template>
                            <div class="flex max-h-[75vh] flex-col gap-6 overflow-y-auto">
                                <div v-for="media in game.media" :key="media.path" class="flex flex-col gap-2">
                                    <Preview :type="media.type" :media="media.path" />
                                </div>
                            </div>
                        </Modal>
                    </div>
                </div>
            </div>
            <Tabs :default-value="tab">
                <TabsList class="w-full">
                    <TabsTrigger
                        v-for="i in ['reviews', 'discussions']"
                        :value="i"
                        :key="i"
                        class="capitalize"
                        :class="{ 'cursor-pointer': tab !== i }"
                    >
                        {{ i }}
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="reviews">
                    <WhenVisible data="reviews">
                        <template #fallback>
                            <ReviewSkeleton />
                        </template>

                        <NewReviewForm :slug="game.slug" />
                        <template v-if="reviews!.data.length > 0">
                            <div class="flex flex-col gap-4">
                                <Pagination page-name="reviews_page" :pagination="getPaginationData(reviews!)" />
                                <Review v-for="review in reviews!.data" :key="review.slug" :review />
                            </div>
                        </template>
                        <template v-else>
                            <p>Nothing to show</p>
                        </template>
                    </WhenVisible>
                </TabsContent>
                <TabsContent value="discussions">
                    <WhenVisible data="reviews">
                        <template #fallback>
                            <DiscussionSkeleton />
                        </template>

                        <NewDiscussionForm :slug="game.slug" type="game" />
                        <template v-if="discussions!.data.length > 0">
                            <div class="flex flex-col gap-4">
                                <Pagination page-name="discussions_page" :pagination="getPaginationData(discussions!)" />
                                <Discussion v-for="discussion in discussions!.data" :key="discussion.slug" :discussion />
                            </div>
                        </template>
                        <template v-else>
                            <p>Nothing to show</p>
                        </template>
                    </WhenVisible>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
