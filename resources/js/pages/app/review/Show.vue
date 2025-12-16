<script setup lang="ts">
import DeleteActionLink from '@/components/DeleteActionLink.vue';
import Heading from '@/components/Heading.vue';
import LazyAvatar from '@/components/LazyAvatar.vue';
import MainContainer from '@/components/MainContainer.vue';
import ReportModal from '@/components/ReportModal.vue';
import UserInfo from '@/components/UserInfo.vue';
import UserRole from '@/components/UserRole.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Review } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, CheckCircle, Star } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    review: Review;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Review',
        href: '',
    },
    {
        title: props.review.shortSlug,
        href: '',
    },
];

const avgRating = computed(() =>
    (Object.values(props.review.ratings).reduce((a, b) => a + b, 0) / Object.keys(props.review.ratings).length).toFixed(1),
);
</script>

<template>
    <Head :title="`Review: #${review.slug}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-4xl flex-col gap-6">
            <div class="flex items-center gap-3">
                <Link class="flex gap-3" :href="route('user.profile', { user: review.user.username })">
                    <UserInfo :show-username="true" :user="review.user" />
                </Link>
                <UserRole v-if="review.user.role !== 'user'" :role="review.user.role" class="text-sm" :show-user-role-tag="false" />
            </div>
            <div class="flex flex-wrap gap-4 lg:gap-6">
                <div class="flex items-center gap-1.5">
                    <Star class="size-5" />
                    <span>{{ avgRating }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <Calendar class="size-5" />
                    <span>{{ review.created_at }}</span>
                </div>
                <div class="flex items-center gap-1.5" v-if="review.user.is_email_verified">
                    <CheckCircle class="size-5" />
                    <span>Verified user</span>
                </div>
            </div>
            <Link as="button" class="hover:bg-card/50 duration-150" :href="route('games.show', { game: review.game.slug })">
                <div class="flex gap-4 rounded-md border p-2">
                    <LazyAvatar :src="review.game.thumbnail" :alt="review.game.title" class="h-48 w-40" />
                    <Heading class="text-left" :title="review.game.title" :description="review.game.description" />
                </div>
            </Link>
            <p>{{ review.content }}</p>
            <div class="grid grid-cols-2 gap-4 lg:flex lg:justify-between">
                <div class="flex flex-col gap-1" v-for="[k, v] in Object.entries(review.ratings)" :key="k">
                    <p class="text-xs uppercase dark:text-slate-300/40">{{ k.replaceAll('_', ' ') }}</p>
                    <p class="">{{ v }} / 5</p>
                </div>
            </div>
            <div class="text-destructive flex gap-6 text-sm [&>*]:cursor-pointer">
                <DeleteActionLink :href="route('reviews.destroy', { review: review.slug, to_route: 'home' })" v-if="review.permissions.destroy" />
                <ReportModal :contentId="review.slug" contentType="review" trigger-class="text-destructive text-sm" />
            </div>
        </MainContainer>
    </AppLayout>
</template>
