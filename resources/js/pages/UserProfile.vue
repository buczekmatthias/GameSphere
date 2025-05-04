<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    user: User;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile',
        href: '',
    },
    {
        title: props.user.username,
        href: '',
    },
];
</script>

<template>
    <Head :title="`${user.name}\'s profile`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 md:p-4">
            {{ user }}
        </div>
    </AppLayout>
</template>
