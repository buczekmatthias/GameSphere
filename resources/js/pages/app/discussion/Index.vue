<script setup lang="ts">
import Discussion from '@/components/Discussion.vue';
import MainContainer from '@/components/MainContainer.vue';
import PaginatedContent from '@/components/PaginatedContent.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { getPaginationData } from '@/composables/usePagination';
import { useZiggy } from '@/composables/useZiggy';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Pagination, Ziggy } from '@/types';
import { Deferred, Head, useForm } from '@inertiajs/vue3';
import { ComputedRef } from 'vue';

interface ZiggyWithGamesQuery extends Ziggy {
    query: {
        title?: string;
    };
}

defineProps<{
    discussions?: Pagination & { data: DiscussionType[] };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Discussions',
        href: '',
    },
];

const ziggy: ComputedRef<ZiggyWithGamesQuery> = useZiggy();

const searchForm = useForm({
    title: ziggy.value.query.title || '',
});
</script>

<template>
    <Head title="Discussions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-4xl flex-col gap-4">
            <Deferred data="discussions">
                <template #fallback>
                    <div class="flex gap-2">
                        <Skeleton class="h-9 w-[calc(100%-5rem)]"></Skeleton>
                        <Skeleton class="h-9 w-20"></Skeleton>
                    </div>
                    <Skeleton v-for="i in 10" :key="i" class="h-24 w-full" />
                </template>

                <form
                    @submit.prevent="searchForm.get(route(ziggy.current), { only: ['discussions', 'ziggy'] })"
                    class="grid grid-cols-[1fr_auto] gap-2"
                >
                    <Input v-model="searchForm.title" placeholder="Search by title..." />
                    <Button>Search</Button>
                </form>
                <PaginatedContent :pagination="getPaginationData(discussions!)" pagination-position="bottom">
                    <Discussion :discussion="discussion" v-for="discussion in discussions!.data" :key="discussion.slug" />
                </PaginatedContent>
            </Deferred>
        </MainContainer>
    </AppLayout>
</template>
