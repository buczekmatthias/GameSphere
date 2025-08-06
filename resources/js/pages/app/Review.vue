<script setup lang="ts">
import ReportModal from '@/components/ReportModal.vue';
import TextLink from '@/components/TextLink.vue';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import UserInfo from '@/components/UserInfo.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Review } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Gamepad2, MailCheck, Star } from 'lucide-vue-next';
import { capitalize, computed } from 'vue';

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
        <div class="main-container flex flex-col gap-4">
            <div class="flex items-center gap-3">
                <Link class="mr-auto flex gap-3" :href="route('user.profile', { user: review.user.username })">
                    <UserInfo :show-username="true" :user="review.user" />
                </Link>
                <p v-if="review.user.role !== 'user'" class="text-sm">{{ capitalize(review.user.role) }}</p>
                <template v-if="review.user.is_email_verified">
                    <TooltipProvider>
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <MailCheck />
                            </TooltipTrigger>
                            <TooltipContent side="left">
                                <p>Verified user</p>
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                </template>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <Star class="h-5" />
                    <span>{{ avgRating }}</span>
                </div>
                <p class="text-sm text-slate-300">{{ review.created_at }}</p>
            </div>
            <TextLink as="button" :href="route('games.show', { game: review.game.slug })" class="flex items-center gap-2 self-start">
                <Gamepad2 class="mt-1 h-5" />
                {{ review.game.title }}
            </TextLink>
            <p>{{ review.content }}</p>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1" v-for="[k, v] in Object.entries(review.ratings)" :key="k">
                    <p class="text-xs uppercase dark:text-slate-300/40">{{ k.replaceAll('_', ' ') }}</p>
                    <p class="">{{ v }}</p>
                </div>
            </div>
            <div class="text-destructive flex gap-4 text-sm [&>*]:cursor-pointer">
                <Link as="button" :href="route('reviews.destroy', { review: review.slug, to_homepage: true })" method="delete">Delete review</Link>
                <ReportModal :contentId="review.slug" contentType="review" />
            </div>
        </div>
    </AppLayout>
</template>
