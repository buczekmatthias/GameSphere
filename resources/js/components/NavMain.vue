<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { capitalize, computed } from 'vue';

defineProps<{
    items: { [key: string]: NavItem[] };
}>();

const currentPage = computed(() => usePage<SharedData>().props.ziggy.location);
</script>

<template>
    <SidebarGroup class="px-2 py-0" v-for="[group, i] in Object.entries(items)" :key="group">
        <SidebarGroupLabel>{{ capitalize(group) }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in i" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.href === currentPage" :tooltip="item.title">
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
