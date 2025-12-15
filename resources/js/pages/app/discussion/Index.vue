<script setup lang="ts">
import Discussion from '@/components/Discussion.vue';
import MainContainer from '@/components/MainContainer.vue';
import PaginatedContent from '@/components/PaginatedContent.vue';
import { Skeleton } from '@/components/ui/skeleton';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Pagination } from '@/types';
import { Deferred, Head } from '@inertiajs/vue3';

defineProps<{
    discussions?: Pagination & { data: DiscussionType[] };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Discussions',
        href: '',
    },
];
</script>

<template>
    <Head title="Discussions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-4xl flex-col gap-4">
            <Deferred data="discussions">
                <template #fallback>
                    <Skeleton v-for="i in 10" :key="i" class="h-24 w-full" />
                </template>

                <PaginatedContent :pagination="getPaginationData(discussions!)" pagination-position="bottom">
                    <Discussion :discussion="discussion" v-for="discussion in discussions!.data" :key="discussion.slug" />
                </PaginatedContent>
            </Deferred>
        </MainContainer>
    </AppLayout>
</template>
