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
import { canInteract } from '@/composables/useCanInteract';
import { isLoggedIn } from '@/composables/useIsLoggedIn';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Game, Pagination as PaginationType, Review as ReviewType, Ziggy } from '@/types';
import { Deferred, Head, usePage, WhenVisible } from '@inertiajs/vue3';
import { Blocks, Calendar, LucideIcon, Rss, Star, User } from 'lucide-vue-next';
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

const tabs: { [key: string]: LucideIcon } = {
    reviews: Star,
    discussions: Rss,
};

const tab = computed(() => {
    const query = (usePage().props.ziggy as Ziggy & { query: { discussions_page?: number } }).query;

    return query?.discussions_page ? 'discussions' : 'reviews';
});
</script>

<template>
    <Head :title="game.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="main-container flex flex-col gap-4">
            <div
                class="grid gap-4"
                :class="isLoggedIn() ? 'ml:grid-cols-[auto_1fr_auto] grid-cols-[1fr_auto]' : 'grid-cols-1 md:grid-cols-[auto_1fr]'"
            >
                <Avatar class="h-80 w-64 overflow-hidden rounded-lg object-cover">
                    <AvatarImage :src="game.thumbnail" :alt="game.title" />
                    <AvatarFallback class="rounded-lg text-black dark:text-white" />
                </Avatar>
                <template v-if="isLoggedIn()">
                    <Deferred data="userLists">
                        <template #fallback>
                            <div class="flex flex-col gap-3">
                                <Skeleton class="h-9 w-12" />
                                <Skeleton class="h-9 w-12" />
                            </div>
                        </template>

                        <GameActionDropdown :game :lists="userLists" />
                    </Deferred>
                </template>
                <div class="ml:col-start-2 ml:row-start-1 flex flex-col gap-4">
                    <h1 class="text-3xl">{{ game.title }}</h1>
                    <div class="flex divide-x">
                        <div class="flex items-center gap-2.5 pr-3">
                            <Star class="mt-0.5 size-5" />
                            <p>{{ game.score }}</p>
                        </div>
                        <p class="pl-3">{{ game.reviews_count }} review(s)</p>
                    </div>
                    <p class="text-sm leading-[165%]">{{ game.description }}</p>
                    <div class="grid grid-cols-2 gap-4 lg:grid-cols-[repeat(3,auto)] lg:gap-6 lg:self-start">
                        <div class="flex items-center gap-2">
                            <Blocks class="mt-0.5 size-4" />
                            <TextLink :href="route('genres.show', { genre: game.genre.slug })">{{ game.genre.name }}</TextLink>
                        </div>
                        <div class="flex items-center gap-2">
                            <Calendar class="mt-0.5 size-4" />
                            <TextLink :href="route('games.index', { released_at: game.released_at })">{{ game.released_at }}</TextLink>
                        </div>
                        <div class="flex items-center gap-2 max-lg:col-span-full">
                            <User class="mt-0.5 size-4" />
                            <TextLink :href="route('user.profile', { user: game.creator.username })">{{ game.creator.name }}</TextLink>
                        </div>
                    </div>
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
                <TabsList class="h-auto w-full">
                    <TabsTrigger
                        v-for="[name, icon] in Object.entries(tabs)"
                        :value="name"
                        :key="name"
                        class="py-2 capitalize"
                        :class="{ 'cursor-pointer': tab !== name }"
                    >
                        <component :is="icon" class="mr-1"></component>
                        {{ name }}
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="reviews">
                    <WhenVisible data="reviews">
                        <template #fallback>
                            <ReviewSkeleton />
                        </template>

                        <NewReviewForm :slug="game.slug" v-if="canInteract()" />
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

                        <NewDiscussionForm :slug="game.slug" type="game" v-if="canInteract()" />
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
