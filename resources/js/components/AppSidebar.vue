<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { SharedData, type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Home, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: { [key: string]: NavItem[] } = {
    app: [
        {
            title: 'Home',
            href: route('home'),
            icon: Home,
        },
    ],
};

const footerNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '',
        icon: LayoutGrid,
    },
];

const page = usePage<SharedData>();
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
            <NavFooter :items="footerNavItems" v-if="['moderator', 'admin', 'developer'].includes(page.props.auth.user.role)" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
