<script setup lang="ts">
import LazyAvatar from '@/components/LazyAvatar.vue';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';

withDefaults(
    defineProps<{
        user: User;
        showUsername?: boolean;
    }>(),
    {
        showUsername: false,
    },
);

const { getInitials } = useInitials();
</script>

<template>
    <LazyAvatar :src="user.avatar" :alt="user.name" :fallback="getInitials(user.name)" class="size-8 lg:row-span-2" />

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ user.name }}</span>
        <span v-if="showUsername" class="text-muted-foreground truncate text-xs">@{{ user.username }}</span>
    </div>
</template>
