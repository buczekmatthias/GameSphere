<script setup lang="ts">
import Discussion from '@/components/Discussion.vue';
import DiscussionSkeleton from '@/components/fallbacks/DiscussionSkeleton.vue';
import ReviewSkeleton from '@/components/fallbacks/ReviewSkeleton.vue';
import Review from '@/components/Review.vue';
import TextLink from '@/components/TextLink.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Game, Review as ReviewType } from '@/types';
import { Head, WhenVisible } from '@inertiajs/vue3';

const props = defineProps<{
    game: Game;
    reviews?: ReviewType[];
    discussions?: DiscussionType[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Games',
        href: route('games.index'),
    },
    {
        title: props.game.title,
        href: route('games.show', { game: props.game.slug }),
    },
];
</script>

<template>
    <Head :title="game.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-6 md:p-4">
            <div class="flex gap-4 max-md:flex-col">
                <Avatar class="h-80 w-64 overflow-hidden rounded-lg object-cover">
                    <AvatarImage :src="game.thumbnail" :alt="game.title" />
                    <AvatarFallback class="rounded-lg text-black dark:text-white" />
                </Avatar>
                <div class="flex flex-col gap-4">
                    <h1 class="text-3xl">{{ game.title }}</h1>
                    <p class="text-sm leading-[165%]">{{ game.description }}</p>
                    <p>
                        Release date: <TextLink :href="route('games.index', { released_at: game.released_at })">{{ game.released_at }}</TextLink>
                    </p>
                    <p>
                        Creator: <TextLink :href="route('user.profile', { user: game.creator.username })">{{ game.creator.name }}</TextLink>
                    </p>
                </div>
            </div>
            <div class="flex overflow-auto">
                <template v-for="media in game.media" :key="media.name">
                    <template v-if="media.type === 'image'">
                        <Avatar
                            class="hover:border-primary h-24 w-24 overflow-hidden rounded-lg border-2 border-solid border-transparent object-cover duration-150"
                        >
                            <AvatarImage :src="game.thumbnail" :alt="game.title" />
                            <AvatarFallback class="rounded-lg text-black dark:text-white" />
                        </Avatar>
                    </template>
                    <template v-else>
                        <div
                            class="hover:border-primary h-24 w-24 overflow-hidden rounded-lg border-2 border-solid border-transparent object-cover text-black duration-150 dark:text-white"
                        ></div>
                    </template>
                </template>
            </div>
            <Tabs default-value="reviews">
                <TabsList>
                    <TabsTrigger value="reviews">Reviews</TabsTrigger>
                    <TabsTrigger value="discussions">Discussions</TabsTrigger>
                </TabsList>
                <TabsContent value="reviews">
                    <WhenVisible data="reviews">
                        <template #fallback>
                            <ReviewSkeleton />
                        </template>

                        <template v-if="reviews!.length > 0">
                            <Review v-for="review in reviews" :key="review.slug" :review />
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

                        <template v-if="discussions!.length > 0">
                            <Discussion v-for="discussion in discussions" :key="discussion.slug" :discussion />
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
