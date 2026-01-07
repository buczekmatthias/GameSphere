<script setup lang="ts">
import Comment from '@/components/app/comment/DiscussionComment.vue';
import Discussion from '@/components/app/Discussion.vue';
import Game from '@/components/app/Game.vue';
import Genre from '@/components/app/Genre.vue';
import PaginatedContent from '@/components/app/PaginatedContent.vue';
import Review from '@/components/app/Review.vue';
import type {
    DiscussionComment,
    Discussion as DiscussionType,
    Game as GameType,
    Genre as GenreType,
    Pagination,
    Review as ReviewType,
} from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    type: 'created_games' | 'games' | 'genres' | 'reviews' | 'discussions' | 'comments';
    content: Pagination & { data: GameType[] | GenreType[] | ReviewType[] | DiscussionType[] | DiscussionComment[] };
}>();

const reloadOnly: string[] = ['user'];

const gamesData = computed((): GameType[] => (props.type === 'created_games' || props.type === 'games' ? (props.content.data as GameType[]) : []));

const genresData = computed((): GenreType[] => (props.type === 'genres' ? (props.content.data as GenreType[]) : []));

const reviewsData = computed((): ReviewType[] => (props.type === 'reviews' ? (props.content.data as ReviewType[]) : []));

const discussionsData = computed((): DiscussionType[] => (props.type === 'discussions' ? (props.content.data as DiscussionType[]) : []));

const commentsData = computed((): DiscussionComment[] => (props.type === 'comments' ? (props.content.data as DiscussionComment[]) : []));
</script>

<template>
    <div class="flex flex-col gap-4">
        <template v-if="content.data.length > 0">
            <PaginatedContent :pagination="content" :reload-only>
                <template v-if="type === 'created_games'">
                    <div class="games-grid">
                        <Game v-for="game in gamesData" :key="game.title" :game />
                    </div>
                </template>

                <template v-if="type === 'games'">
                    <div class="games-grid">
                        <Game v-for="game in gamesData" :key="game.title" :game />
                    </div>
                </template>

                <template v-else-if="type === 'genres'">
                    <div class="flex flex-col gap-4">
                        <Genre v-for="genre in genresData" :key="genre.name" :genre />
                    </div>
                </template>

                <template v-else-if="type === 'reviews'">
                    <div class="flex flex-col gap-4">
                        <Review v-for="review in reviewsData" :key="review.slug" :review with-link :show-user="false" />
                    </div>
                </template>

                <template v-else-if="type === 'discussions'">
                    <div class="flex flex-col gap-4">
                        <Discussion v-for="discussion in discussionsData" :key="discussion.slug" :discussion :show-user="false" />
                    </div>
                </template>

                <template v-else-if="type === 'comments'">
                    <Comment v-for="comment in commentsData" :key="comment.slug" :comment />
                </template>
            </PaginatedContent>
        </template>

        <template v-else>
            <p class="my-10 max-md:self-center">There's nothing to be displayed here</p>
        </template>
    </div>
</template>
