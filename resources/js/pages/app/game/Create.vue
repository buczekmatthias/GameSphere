<script setup lang="ts">
import FormButton from '@/components/FormButton.vue';
import GameGenre from '@/components/Partials/Game/Create/Form/GameGenre.vue';
import GameMedia from '@/components/Partials/Game/Create/Form/GameMedia.vue';
import GameReleasedAt from '@/components/Partials/Game/Create/Form/GameReleasedAt.vue';
import GameThumbnail from '@/components/Partials/Game/Create/Form/GameThumbnail.vue';
import GameDescription from '@/components/Partials/Game/Edit/Form/GameDescription.vue';
import GameTitle from '@/components/Partials/Game/Edit/Form/GameTitle.vue';
import { transformDate } from '@/composables/useTransformDatePicker';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Genre } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    genres: Genre[];
}>();

const DESCRIPTION_MIN_LENGTH = 50;

const form = useForm({
    title: '',
    description: '',
    thumbnail: null,
    media: [],
    released_at: null,
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
        form.description.length >= DESCRIPTION_MIN_LENGTH &&
        form.thumbnail !== null &&
        form.released_at &&
        form.genre.length > 0
    );
};

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        released_at: transformDate(data.released_at),
    })).post(route('games.store'));
};
</script>

<template>
    <Head title="Add new game" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="submitForm" class="main-container flex flex-col gap-4">
            <GameTitle :error="form.errors.title" v-model="form.title" />

            <GameDescription :error="form.errors.description" v-model="form.description" />

            <GameThumbnail :error="form.errors.thumbnail" v-model="form.thumbnail" />

            <GameMedia :error="form.errors.media" v-model="form.media" />

            <GameReleasedAt :error="form.errors.released_at" v-model="form.released_at" />

            <GameGenre :error="form.errors.genre" v-model="form.genre" :genres />

            <FormButton label="Create game" :is-processing="form.processing" :disabled="!isFormValid()" />
        </form>
    </AppLayout>
</template>
