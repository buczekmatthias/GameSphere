<script setup lang="ts">
import ReportModal from '@/components/ReportModal.vue';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Card } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import UserInfo from '@/components/UserInfo.vue';
import { Review } from '@/types';
import { Link } from '@inertiajs/vue3';
import { MailCheck, Star } from 'lucide-vue-next';
import { capitalize, computed } from 'vue';

const props = defineProps<{
    review: Review;
}>();

const avgRating = computed(() =>
    (Object.values(props.review.ratings).reduce((a, b) => a + b, 0) / Object.keys(props.review.ratings).length).toFixed(1),
);
</script>

<template>
    <Card>
        <div class="flex items-center gap-3">
            <UserInfo :show-username="true" :user="review.user" />
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
        <p class="text-sm text-slate-300">{{ review.created_at }}</p>
        <p>{{ review.content }}</p>
        <div class="text-destructive flex gap-4 text-sm [&>*]:cursor-pointer">
            <Link as="button" :href="route('reviews.destroy', { review: review.slug })" method="delete"> Delete review </Link>
            <ReportModal :contentId="review.slug" contentType="review" />
        </div>
        <Accordion type="single" class="w-full" collapsible>
            <AccordionItem value="rating">
                <AccordionTrigger class="cursor-pointer">
                    <div class="flex items-center gap-1.5">
                        <Star class="h-5" />
                        <span>{{ avgRating }}</span>
                    </div>
                </AccordionTrigger>
                <AccordionContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1" v-for="[k, v] in Object.entries(review.ratings)" :key="k">
                            <p class="text-xs uppercase dark:text-slate-300/40">{{ k.replaceAll('_', ' ') }}</p>
                            <p class="">{{ v }}</p>
                        </div>
                    </div>
                </AccordionContent>
            </AccordionItem>
        </Accordion>
    </Card>
</template>
