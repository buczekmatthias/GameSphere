<script setup lang="ts">
import CanInteract from '@/components/app/CanInteract.vue';
import ContentWithFallback from '@/components/app/ContentWithFallback.vue';
import FormActionTap from '@/components/app/FormActionTap.vue';
import PaginatedContent from '@/components/app/PaginatedContent.vue';
import { Discussion, Pagination, Review } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { capitalize } from 'vue';

defineProps<{
    data: 'review' | 'discussion';
    actionHref: string;
    pagination?: Pagination & { data: Review[] | Discussion[] };
}>();
</script>

<template>
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
</template>
