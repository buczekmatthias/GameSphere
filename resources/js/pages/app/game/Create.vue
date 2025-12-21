<script setup lang="ts">
import FormButton from '@/components/FormButton.vue';
import MainContainer from '@/components/MainContainer.vue';
import GameGenre from '@/components/Partials/Game/Create/Form/GameGenre.vue';
import GameMedia from '@/components/Partials/Game/Create/Form/GameMedia.vue';
import GameReleasedAt from '@/components/Partials/Game/Create/Form/GameReleasedAt.vue';
import GameThumbnail from '@/components/Partials/Game/Create/Form/GameThumbnail.vue';
import GameDescription from '@/components/Partials/Game/Edit/Form/GameDescription.vue';
import GameTitle from '@/components/Partials/Game/Edit/Form/GameTitle.vue';
import { constants } from '@/constants';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Genre } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { getLocalTimeZone, today } from '@internationalized/date';

defineProps<{
    genres: Genre[];
}>();

const form = useForm({
    title: '',
    description: '',
    thumbnail: null,
    media: [],
    released_at: today(getLocalTimeZone()).toString(),
    genre: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Games',
        href: route('games.index'),
    },
    {
        title: 'Add new game',
        href: route('games.create'),
    },
];

const isFormValid = () => {
    return (
        form.title.length > 0 &&
        form.description.length >= constants.value.form.description.min_length &&
        form.thumbnail !== null &&
        form.released_at &&
        form.genre.length > 0
    );
};

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        released_at: data.released_at.toString(),
    })).post(route('games.store'));
};
</script>

<template>
    <Head title="Add new game" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-6xl flex-col gap-6">
            <GameTitle :error="form.errors.title" v-model="form.title" />

            <GameDescription :error="form.errors.description" v-model="form.description" />

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <GameThumbnail :error="form.errors.thumbnail" v-model="form.thumbnail" />

                <GameMedia :error="form.errors.media" v-model="form.media" />
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <GameReleasedAt :error="form.errors.released_at" v-model="form.released_at" />

                <GameGenre :error="form.errors.genre" v-model="form.genre" :genres />
            </div>

            <FormButton label="Create game" :is-processing="form.processing" :disabled="!isFormValid()" @click="submitForm" />
        </MainContainer>
    </AppLayout>
</template>
