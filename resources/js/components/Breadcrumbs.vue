<script setup lang="ts">
import {
    Breadcrumb,
    BreadcrumbEllipsis,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Link } from '@inertiajs/vue3';
import { useMediaQuery } from '@vueuse/core';

interface BreadcrumbItem {
    title: string;
    href?: string;
}

defineProps<{
    breadcrumbs: BreadcrumbItem[];
}>();

const isAboveLg = useMediaQuery('(min-width: 1024px)');
</script>

<template>
    <Breadcrumb>
        <BreadcrumbList>
            <template v-if="breadcrumbs.length > 3 && !isAboveLg">
                <BreadcrumbItem>
                    <BreadcrumbLink as-child>
                        <Link :href="breadcrumbs[0].href ?? '#'" as="button" class="truncate">{{ breadcrumbs[0].title }}</Link>
                    </BreadcrumbLink>
                </BreadcrumbItem>
                <BreadcrumbSeparator />
                <BreadcrumbItem>
                    <DropdownMenu>
                        <DropdownMenuTrigger class="flex items-center gap-1">
                            <BreadcrumbEllipsis class="h-4 w-4" />
                            <span class="sr-only">Toggle menu</span>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="start">
                            <template v-for="(item, index) in breadcrumbs" :key="index">
                                <DropdownMenuItem v-if="index !== 0 && index !== breadcrumbs.length - 1" as-child>
                                    <Link :href="item.href ?? '#'" as="button" class="w-full cursor-pointer truncate">{{ item.title }}</Link>
                                </DropdownMenuItem>
                            </template>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </BreadcrumbItem>
                <BreadcrumbSeparator />
                <BreadcrumbItem>
                    <BreadcrumbPage>
                        {{ breadcrumbs[breadcrumbs.length - 1].title }}
                    </BreadcrumbPage>
                </BreadcrumbItem>
            </template>
            <template v-else>
                <template v-for="(item, index) in breadcrumbs" :key="index">
                    <BreadcrumbItem>
                        <template v-if="index === breadcrumbs.length - 1">
                            <BreadcrumbPage>{{ item.title }}</BreadcrumbPage>
                        </template>
                        <template v-else>
                            <BreadcrumbLink as-child>
                                <Link :href="item.href ?? '#'" as="button" class="truncate">{{ item.title }}</Link>
                            </BreadcrumbLink>
                        </template>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator v-if="index !== breadcrumbs.length - 1" />
                </template>
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
