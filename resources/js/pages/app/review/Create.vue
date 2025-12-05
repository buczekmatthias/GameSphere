<script setup lang="ts">
import FormButton from '@/components/FormButton.vue';
import GoBackLink from '@/components/GoBackLink.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import MainContainer from '@/components/MainContainer.vue';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Game, ReviewRatings } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Star } from 'lucide-vue-next';
import { capitalize, computed } from 'vue';

const props = defineProps<{
    game: Game;
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
    {
        title: 'Create review',
        href: route('reviews.create', { game: props.game.slug }),
    },
];

const newReviewForm = useForm({
    content: '',
    ratings: {
        gameplay: 0,
        graphics: 0,
        storyline: 0,
        replayability: 0,
        sound_and_music: 0,
        performance: 0,
    },
    game_slug: props.game.slug,
});

const isFormValid = computed(() => newReviewForm.content.length > 0 && Object.values(newReviewForm.ratings).every((i) => i > 0));

const submitForm = () => {
    if (isFormValid.value) {
        newReviewForm.post(route('reviews.store'));
    }
};
</script>

<template>
    <Head title="Create review" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-3xl flex-col gap-6">
            <GoBackLink :href="route('games.show', { game: game.slug })" />
            <HeadingSmall title="Create new review" :description="`Game: ${game.title} (${game.slug})`" />
            <div class="grid gap-2">
                <Label html-for="content">Content</Label>
                <Textarea id="content" v-model="newReviewForm.content" class="h-48 resize-none" />
                <InputError :message="newReviewForm.errors.content" />
            </div>
            <div class="flex flex-col gap-6">
                <p class="text-lg">Ratings</p>
                <div class="grid grid-cols-[1fr_auto] gap-2" v-for="(k, ind) in Object.keys(newReviewForm.ratings)" :key="k">
                    <p class="text-sm">{{ capitalize(k.replaceAll('_', ' ')) }}</p>
                    <div class="flex gap-3">
                        <Star
                            v-for="i in 5"
                            :key="i"
                            @click="newReviewForm.ratings[k as keyof ReviewRatings] = i"
                            class="size-5 cursor-pointer"
                            :class="{
                                'text-amber-400': i <= newReviewForm.ratings[k as keyof ReviewRatings],
                            }"
                        />
                    </div>
                    <InputError :message="newReviewForm.errors.ratings?.[ind]" />
                </div>
            </div>

            <FormButton label="Add review" :is-processing="newReviewForm.processing" :disabled="!isFormValid" @click="submitForm" />
        </MainContainer>
    </AppLayout>
</template>
