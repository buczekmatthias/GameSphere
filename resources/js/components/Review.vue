<script setup lang="ts">
import DeleteActionLink from '@/components/DeleteActionLink.vue';
import ReportModal from '@/components/ReportModal.vue';
import TextLink from '@/components/TextLink.vue';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Card } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import UserInfo from '@/components/UserInfo.vue';
import UserRole from '@/components/UserRole.vue';
import { Review } from '@/types';
import { Link } from '@inertiajs/vue3';
import { MailCheck, Star } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        review: Review;
        withLink?: boolean;
        showUser?: boolean;
    }>(),
    {
        showUser: true,
        withLink: false,
    },
);
</script>

<template>
    <Card class="gap-4">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <TextLink
                    v-if="withLink && review.game"
                    as="button"
                    :href="route('games.show', { game: review.game.slug })"
                    class="flex items-center gap-2 self-start"
                >
                    {{ review.game.title }}
                </TextLink>
                <template v-if="showUser">
                    <template v-if="review.user">
                        <Link :href="route('user.profile', { user: review.user.username })" class="mr-auto flex gap-3">
                            <UserInfo :show-username="true" :user="review.user" />
                        </Link>
                        <UserRole v-if="review.user.role !== 'user'" :role="review.user.role" class="text-xs" />
                        <template v-if="review.user.is_email_verified">
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <MailCheck class="size-5" />
                                    </TooltipTrigger>
                                    <TooltipContent side="left">
                                        <p>Verified user</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </template>
                    </template>
                    <p class="text-lg" v-else>Posted by deleted user</p>
                </template>
            </div>
            <p class="text-sm text-slate-300">{{ review.created_at }}</p>
        </div>
        <p>{{ review.content }}</p>
        <div class="text-destructive flex gap-4 text-sm [&>*]:cursor-pointer">
            <DeleteActionLink :href="route('reviews.destroy', { review: review.slug })" v-if="review.permissions.destroy" />
            <ReportModal :contentId="review.slug" contentType="review" trigger-class="text-destructive text-sm" />
        </div>
        <Accordion type="single" class="w-full" collapsible>
            <AccordionItem value="rating">
                <AccordionTrigger class="cursor-pointer py-2 duration-150 hover:bg-slate-50/10 hover:px-2">
                    <div class="flex items-center gap-1.5">
                        <Star class="h-5" />
                        <span>{{ review.avg_rating }}</span>
                    </div>
                </AccordionTrigger>
                <AccordionContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1" v-for="[k, v] in Object.entries(review.ratings)" :key="k">
                            <p class="text-xs uppercase dark:text-slate-300/40">{{ k.replaceAll('_', ' ') }}</p>
                            <div class="flex items-center gap-1.5">
                                <Star class="mt-0.5 size-4" />
                                <p>{{ v }} / 5</p>
                            </div>
                        </div>
                    </div>
                </AccordionContent>
            </AccordionItem>
        </Accordion>
    </Card>
</template>
