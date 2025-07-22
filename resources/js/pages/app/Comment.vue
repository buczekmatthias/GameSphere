<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, DiscussionComment } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    comment: DiscussionComment;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Comment',
        href: '',
    },
    {
        title: props.comment.shortSlug,
        href: '',
    },
];
</script>

<template>
    <Head :title="`Comment: #${comment.slug}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 md:p-4">
            {{ comment }}
        </div>
    </AppLayout>
</template>
