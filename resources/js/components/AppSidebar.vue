<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { SharedData, User, type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Blocks, Gamepad2, Home, MessageCircle, Plus } from 'lucide-vue-next';
import { computed, onBeforeMount } from 'vue';

const mainNavItems: { [key: string]: NavItem[] } = {
    app: [
        {
            title: 'Home',
            href: route('home'),
            icon: Home,
        },
    ],
    games: [
        {
            title: 'List of games',
            href: route('games.index'),
            icon: Gamepad2,
        },
    ],
    discussions: [
        {
            title: 'List of discussions',
            href: route('discussions.index'),
            icon: MessageCircle,
        },
    ],
    genres: [
        {
            title: 'List of genres',
            href: route('genres.index'),
            icon: Blocks,
        },
    ],
};

const user = computed(() => usePage<SharedData>().props.auth.user as User);

onBeforeMount(() => {
    if (['game_creator', 'moderator', 'admin', 'developer'].includes(user.value.role)) {
        mainNavItems['games'].push({
            title: 'Add new game',
            href: route('games.create'),
            icon: Plus,
        });
    }
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('home')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser :user="user" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
