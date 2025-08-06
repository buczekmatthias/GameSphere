<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogIn, LogOut, Settings, UserRoundPlus } from 'lucide-vue-next';

interface Props {
    user: User;
}

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();
</script>

<template>
    <template v-if="user.role === 'guest'">
        <DropdownMenuLabel class="p-0 font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                <UserInfo :user="user" :show-username="user.role === 'guest' ? false : true" />
            </div>
        </DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuGroup>
            <DropdownMenuItem :as-child="true">
                <Link class="block w-full cursor-pointer" :href="route('security.login')" prefetch as="button">
                    <LogIn class="mr-2 h-4 w-4" />
                    Login
                </Link>
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem :as-child="true">
                <Link class="block w-full cursor-pointer" :href="route('security.register')" prefetch as="button">
                    <UserRoundPlus class="mr-2 h-4 w-4" />
                    Register
                </Link>
            </DropdownMenuItem>
        </DropdownMenuGroup>
    </template>
    <template v-else>
        <DropdownMenuItem :as-child="true">
            <Link :href="route('user.profile', { user: user.username })" class="block w-full cursor-pointer px-1 py-1.5" as="button">
                <UserInfo :user="user" :show-username="user.role === 'guest' ? false : true" />
            </Link>
        </DropdownMenuItem>
        <DropdownMenuSeparator />
        <DropdownMenuGroup>
            <DropdownMenuItem :as-child="true">
                <Link class="block w-full cursor-pointer" :href="route('settings.profile.edit')" prefetch as="button">
                    <Settings class="mr-2 h-4 w-4" />
                    Settings
                </Link>
            </DropdownMenuItem>
        </DropdownMenuGroup>
        <DropdownMenuSeparator />
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full cursor-pointer" method="post" :href="route('security.logout')" @click="handleLogout" as="button">
                <LogOut class="mr-2 h-4 w-4" />
                Log out
            </Link>
        </DropdownMenuItem>
    </template>
</template>
