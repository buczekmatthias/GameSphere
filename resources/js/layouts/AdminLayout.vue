<script setup lang="ts">
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            {
                title: 'Dashboard',
                href: route('admin.dashboard'),
            },
            ...breadcrumbs,
        ]"
    >
        <slot />
    </AppLayout>
</template>
