<script setup lang="ts">
import ReportModal from '@/components/ReportModal.vue';
import { buttonVariants } from '@/components/ui/button';
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
        href: route('user.profile', { user: props.user.username }),
    },
];
</script>

<template>
    <Head :title="`${user.name}\'s profile`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="main-container">
            <ReportModal :contentId="user.username" contentType="user" :triggerClass="buttonVariants({ variant: 'destructive' })" />
            <p>{{ user }}</p>
        </div>
    </AppLayout>
</template>
