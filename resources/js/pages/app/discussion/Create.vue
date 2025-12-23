<script setup lang="ts">
import FormBox from '@/components/FormBox.vue';
import FormButton from '@/components/FormButton.vue';
import GoBackLink from '@/components/GoBackLink.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import MainContainer from '@/components/MainContainer.vue';
import CommentMedia from '@/components/Partials/Game/Create/Form/GameMedia.vue';
import CommentTitle from '@/components/Partials/Game/Edit/Form/GameTitle.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Game, Genre } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { capitalize, computed } from 'vue';

const props = defineProps<{
    type: 'game' | 'genre';
    item: Game | Genre;
}>();

const isGameType = computed(() => props.type === 'game');
const computedItem = computed(() =>
    isGameType.value
        ? { display: (props.item as Game).title, route: route('games.show', { game: props.item.slug }) }
        : { display: (props.item as Genre).name, route: route('genres.show', { genre: props.item.slug }) },
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: capitalize(`${props.type}s`),
        href: route(isGameType.value ? 'games.index' : 'genres.index'),
    },
    {
        title: computedItem.value.display,
        href: computedItem.value.route,
    },
    {
        title: 'Create discussion',
        href: route('discussions.create', { type: props.type, slug: props.item.slug }),
    },
];

const newDiscussionForm = useForm({
    title: '',
    slug: props.item.slug,
    type: props.type,
    content: '',
    media: [],
});

const isFormValid = computed(() => newDiscussionForm.title.length > 0 && newDiscussionForm.content.length > 0);

const submitForm = () => {
    if (isFormValid.value) {
        newDiscussionForm.post(route('discussions.store'));
    }
};
</script>

<template>
    <Head title="Create discussion" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-3xl flex-col gap-5">
            <GoBackLink :href="computedItem.route" />
            <HeadingSmall title="Create new discussion" :description="`${capitalize(type)}: ${computedItem.display} (${item.slug})`" />

            <CommentTitle :error="newDiscussionForm.errors.title" v-model="newDiscussionForm.title" />

            <FormBox id="description" label="Content">
                <Textarea id="description" required v-model="newDiscussionForm.content" class="h-48 resize-none" placeholder="Example content" />
                <InputError :message="newDiscussionForm.errors.content" />
            </FormBox>
            <CommentMedia :error="newDiscussionForm.errors.media" v-model="newDiscussionForm.media" />
            <FormButton label="Start discussion" :is-processing="newDiscussionForm.processing" :disabled="!isFormValid" @click="submitForm" />
        </MainContainer>
    </AppLayout>
</template>
