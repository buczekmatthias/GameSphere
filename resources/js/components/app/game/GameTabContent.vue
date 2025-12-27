<script setup lang="ts">
import CanInteract from '@/components/CanInteract.vue';
import ContentWithFallback from '@/components/ContentWithFallback.vue';
import DiscussionSkeleton from '@/components/fallbacks/DiscussionSkeleton.vue';
import ReviewSkeleton from '@/components/fallbacks/ReviewSkeleton.vue';
import FormActionTap from '@/components/FormActionTap.vue';
import PaginatedContent from '@/components/PaginatedContent.vue';
import { Discussion, Pagination, Review } from '@/types';
import { Link, WhenVisible } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { capitalize } from 'vue';

defineProps<{
    data: 'review' | 'discussion';
    actionHref: string;
    pagination?: Pagination & { data: Review[] | Discussion[] };
}>();
</script>

<template>
    <WhenVisible :data="`${data}s`">
        <template #fallback>
            <ReviewSkeleton v-if="data === 'review'" />
            <DiscussionSkeleton v-else />
        </template>

        <div class="mb-4 flex w-full items-center justify-between gap-4 border-y py-3">
            <p class="text-xl">{{ capitalize(data) }}s</p>
            <CanInteract>
                <Link :href="actionHref">
                    <FormActionTap>
                        <Plus class="size-4" />
                        Create {{ data }}
                    </FormActionTap>
                </Link>
            </CanInteract>
        </div>

        <ContentWithFallback :has-value="pagination!.data.length > 0">
            <PaginatedContent :page-name="`${data}s_page`" :pagination="pagination!">
                <slot />
            </PaginatedContent>
        </ContentWithFallback>
    </WhenVisible>
</template>
