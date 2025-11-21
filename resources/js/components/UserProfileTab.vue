<script setup lang="ts">
import Discussion from '@/components/Discussion.vue';
import Game from '@/components/Game.vue';
import Genre from '@/components/Genre.vue';
import Pagination from '@/components/Pagination.vue';
import Review from '@/components/Review.vue';
import TextLink from '@/components/TextLink.vue';
import { getPaginationData } from '@/composables/usePagination';
import type {
    DiscussionComment,
    Discussion as DiscussionType,
    Game as GameType,
    Genre as GenreType,
    Pagination as PaginationType,
    Review as ReviewType,
} from '@/types';

defineProps<{
    type: 'created_games' | 'games' | 'genres' | 'reviews' | 'discussions' | 'comments';
    content: PaginationType & { data: GameType[] | GenreType[] | ReviewType[] | DiscussionType[] | DiscussionComment[] };
}>();

const reloadOnly: string[] = ['user'];
</script>

<template>
    <div class="flex flex-col gap-4">
        <template v-if="content.data.length > 0">
            <Pagination :pagination="getPaginationData(content)" :reload-only />

            <template v-if="type === 'created_games'">
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                    <Game v-for="game in content.data as GameType[]" :key="game.title" :game />
                </div>
            </template>

            <template v-if="type === 'games'">
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                    <Game v-for="game in content.data as GameType[]" :key="game.title" :game />
                </div>
            </template>

            <template v-else-if="type === 'genres'">
                <div class="flex flex-col gap-4">
                    <Genre v-for="genre in content.data as GenreType[]" :key="genre.name" :genre />
                </div>
            </template>

            <template v-else-if="type === 'reviews'">
                <div class="flex flex-col gap-4">
                    <Review v-for="review in content.data as ReviewType[]" :key="review.slug" :review with-link :show-user="false" />
                </div>
            </template>

            <template v-else-if="type === 'discussions'">
                <div class="flex flex-col gap-4">
                    <Discussion v-for="discussion in content.data as DiscussionType[]" :key="discussion.slug" :discussion />
                </div>
            </template>

            <template v-else-if="type === 'comments'">
                <div
                    class="border-border flex flex-col items-start gap-3 rounded-md border border-solid p-2"
                    v-for="comment in content.data as DiscussionComment[]"
                    :key="comment.slug"
                >
                    <p class="text-sm text-slate-300">{{ comment.created_at }}</p>
                    <p>{{ comment.content }}</p>
                    <TextLink as="button" :href="route('comments.show', { comment: comment.slug })" class="cursor-pointer text-sm">
                        View comment
                    </TextLink>
                </div>
            </template>
        </template>

        <template v-else>
            <p class="my-10 max-md:self-center">There's nothing to be displayed here</p>
        </template>
    </div>
</template>
