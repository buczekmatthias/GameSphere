<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
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
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarImage :src="user.avatar!" :alt="user.name" />
        <AvatarFallback class="rounded-lg text-black dark:text-white">
            {{ getInitials(user.name) }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ user.name }}</span>
        <span v-if="showUsername" class="text-muted-foreground truncate text-xs">@{{ user.username }}</span>
    </div>
</template>
