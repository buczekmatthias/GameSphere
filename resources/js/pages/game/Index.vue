<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Game } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Games',
        href: route('games.index'),
    },
];

defineProps<{
    games: Game[];
}>();
</script>

<template>
    <Head title="Games" />

    <AppLayout :breadcrumbs="breadcrumbs"></AppLayout>
</template>
