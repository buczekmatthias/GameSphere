<script setup lang="ts">
import Discussion from '@/components/Discussion.vue';
import Pagination from '@/components/Pagination.vue';
import TextLink from '@/components/TextLink.vue';
import { Skeleton } from '@/components/ui/skeleton';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, DiscussableGame, DiscussableGenre, Discussion as DiscussionType, Pagination as PaginationType } from '@/types';
import { Deferred, Head } from '@inertiajs/vue3';
import { Blocks, Gamepad2 } from 'lucide-vue-next';

defineProps<{
    discussions?: PaginationType & { data: DiscussionType[] };
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
        <div class="main-container flex flex-col gap-4">
            <Deferred data="discussions">
                <template #fallback>
                    <Skeleton v-for="i in 10" :key="i" class="h-24 w-full" />
                </template>

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
                <Pagination :pagination="getPaginationData(discussions!)" />
            </Deferred>
        </div>
    </AppLayout>
</template>
