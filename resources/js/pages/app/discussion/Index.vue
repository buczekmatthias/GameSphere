<script setup lang="ts">
import Discussion from '@/components/Discussion.vue';
import MainContainer from '@/components/MainContainer.vue';
import PaginatedContent from '@/components/PaginatedContent.vue';
import TextLink from '@/components/TextLink.vue';
import { Skeleton } from '@/components/ui/skeleton';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, DiscussableGame, DiscussableGenre, Discussion as DiscussionType, Pagination } from '@/types';
import { Deferred, Head } from '@inertiajs/vue3';
import { Blocks, Gamepad2 } from 'lucide-vue-next';

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
                    <Discussion :discussion="discussion" v-for="discussion in discussions!.data" :key="discussion.slug">
                        <template #extra-items>
                            <div class="flex items-center gap-1 text-sm">
                                <template v-if="discussion.discussable_type === 'game'">
                                    <Gamepad2 class="h-5" />
                                    <TextLink :href="route('games.show', { game: discussion.discussable.slug })">
                                        {{ (discussion.discussable as DiscussableGame).title }}
                                    </TextLink>
                                </template>
                                <template v-else>
                                    <Blocks class="h-5" />
                                    <TextLink :href="route('genres.show', { genre: discussion.discussable.slug })">
                                        {{ (discussion.discussable as DiscussableGenre).name }}
                                    </TextLink>
                                </template>
                            </div>
                        </template>
                    </Discussion>
                </PaginatedContent>
            </Deferred>
        </MainContainer>
    </AppLayout>
</template>
