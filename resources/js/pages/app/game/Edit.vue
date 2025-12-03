<script setup lang="ts">
import FormButton from '@/components/FormButton.vue';
import GoBackLink from '@/components/GoBackLink.vue';
import MainContainer from '@/components/MainContainer.vue';
import GameCreator from '@/components/Partials/Game/Edit/Form/GameCreator.vue';
import GameDescription from '@/components/Partials/Game/Edit/Form/GameDescription.vue';
import GameGenre from '@/components/Partials/Game/Edit/Form/GameGenre.vue';
import GameMedia from '@/components/Partials/Game/Edit/Form/GameMedia.vue';
import GameReleasedAt from '@/components/Partials/Game/Edit/Form/GameReleasedAt.vue';
import GameThumbnail from '@/components/Partials/Game/Edit/Form/GameThumbnail.vue';
import GameTitle from '@/components/Partials/Game/Edit/Form/GameTitle.vue';
import { transformDate } from '@/composables/useTransformDatePicker';
import { constants } from '@/constants';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Game, Genre, User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    game: Game;
    genres: Genre[];
    users?: User[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Game',
        href: route('games.index'),
    },
    {
        title: props.game.title,
        href: route('games.show', { game: props.game.slug }),
    },
    {
        title: 'Edit',
        href: '',
    },
];

const form = useForm({
    title: props.game.title,
    description: props.game.description,
    thumbnail: null,
    media: [] as File[],
    released_at: props.game.released_at,
    genre: props.game.genre.slug,
    media_to_delete: [] as string[],
    creator: props.game.creator.username,
});

const hasFormChanged = (): boolean => {
    return (
        props.game.title !== form.title ||
        props.game.description !== form.description ||
        form.thumbnail !== null ||
        form.media.length > 0 ||
        props.game.released_at !== transformDate(form.released_at) ||
        props.game.genre.slug !== form.genre ||
        form.media_to_delete.length > 0 ||
        props.game.creator.username !== form.creator
    );
};

// TODO: Add validation
const submitForm = (): void => {
    if (form.title.length > 0 && form.description.length >= constants.value.form.description.min_length) {
        form.transform((data) => ({
            ...data,
            released_at: transformDate(data.released_at),
        })).post(route('games.update', { game: props.game.slug }));
    }
};
</script>

<template>
    <Head :title="game.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-6xl flex-col gap-4">
            <GoBackLink :href="route('games.show', { game: props.game.slug })" />

            <GameTitle :error="form.errors.title" v-model="form.title" />

            <GameDescription :error="form.errors.description" v-model="form.description" />

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <GameThumbnail :error="form.errors.thumbnail" v-model="form.thumbnail" :thumbnail="game.thumbnail" />

                <GameMedia
                    :error="form.errors.media"
                    v-model:media="form.media"
                    v-model:media_to_delete="form.media_to_delete"
                    :game-media="game.media"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_2fr]">
                <GameReleasedAt :error="form.errors.released_at" v-model="form.released_at" :released-at="game.released_at" />

                <GameCreator v-if="users" :error="form.errors.creator" v-model="form.creator" :game-creator="game.creator" :users />
            </div>

            <GameGenre :error="form.errors.genre" v-model="form.genre" :genres :game-genre="game.genre" />

            <FormButton :is-processing="form.processing" :disabled="!hasFormChanged()" label="Update" @click="submitForm" />
        </MainContainer>
    </AppLayout>
</template>
