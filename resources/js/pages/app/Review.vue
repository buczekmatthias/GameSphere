<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Review } from '@/types';
import { Head } from '@inertiajs/vue3';

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
</script>

<template>
    <Head :title="`Review: #${review.slug}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 md:p-4">
            {{ review }}
        </div>
    </AppLayout>
</template>
