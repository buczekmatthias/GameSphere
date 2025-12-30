<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import UserRole from '@/components/app/user/UserRole.vue';
import { User } from '@/types';
import { Link } from '@inertiajs/vue3';
import { UserIcon } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        user: User | undefined;
        withIcon?: boolean;
        withRole?: boolean;
        fallbackText?: string;
    }>(),
    {
        withIcon: false,
        withRole: false,
        fallbackText: () => 'Deleted user',
    },
);
</script>

<template>
    <div class="flex gap-0.5">
        <UserIcon class="size-5" v-if="withIcon" />
        <template v-if="user">
            <Link class="mr-auto flex gap-3" :href="route('user.profile', { user: user.username })" as="button">
                <UserInfo :show-username="true" :user="user" />
            </Link>
            <UserRole :role="user.role" v-if="withRole" />
        </template>
        <p class="text-sm italic" v-else>{{ fallbackText }}</p>
    </div>
</template>
