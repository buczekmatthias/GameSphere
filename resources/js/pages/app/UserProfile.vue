<script setup lang="ts">
import ReportModal from '@/components/ReportModal.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button, buttonVariants } from '@/components/ui/button';
import UserProfileTab from '@/components/UserProfileTab.vue';
import UserRole from '@/components/UserRole.vue';
import { useInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion, DiscussionComment, Game, Genre, Pagination as PaginationType, Review, User } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Settings } from 'lucide-vue-next';
import { capitalize, computed } from 'vue';

interface UserProfile extends User {
    created_games: PaginationType & { data: Game[] };
    games: PaginationType & { data: Game[] };
    genres: PaginationType & { data: Genre[] };
    reviews: PaginationType & { data: Review[] };
    discussions: PaginationType & { data: Discussion[] };
    comments: PaginationType & { data: DiscussionComment[] };
}

const props = defineProps<{
    user: UserProfile;
    isCurrentUserProfile: boolean;
    tabs: string[];
    activeTab: 'created_games' | 'games' | 'genres' | 'reviews' | 'discussions' | 'comments';
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
    {
        title: capitalize(props.activeTab.replace('_', ' ')),
        href: route('user.profile', { user: props.user.username, tab: props.activeTab }),
    },
];

const { getInitials } = useInitials();

const showAvatar = computed(() => props.user.avatar !== '');

router.on('before', () => {
    const div = document.getElementById('tabs');
    if (div) {
        localStorage.setItem('scrollPosition', `${div.scrollLeft}`);
    }
});

router.on('finish', () => {
    const div = document.getElementById('tabs');
    if (div) {
        const scrollPosition = localStorage.getItem('scrollPosition');
        if (scrollPosition) {
            div.scrollLeft = parseInt(scrollPosition);
            localStorage.removeItem('scrollPosition');
        }
    }
});
</script>

<template>
    <Head :title="`${user.name}\'s profile | ${capitalize(activeTab)}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="main-container flex flex-col gap-4">
            <div class="flex flex-col gap-3">
                <Avatar class="h-72 w-72 overflow-hidden rounded-lg">
                    <AvatarImage v-if="showAvatar" :src="user.avatar!" :alt="user.name" class="bg-gray-900 object-contain" />
                    <AvatarFallback class="rounded-lg text-4xl text-black dark:text-white">
                        {{ getInitials(user.name) }}
                    </AvatarFallback>
                </Avatar>
                <div class="flex justify-between gap-4">
                    <div>
                        <p class="mb-0.5 text-2xl">{{ user.name }}</p>
                        <p class="text-sm text-slate-400">@{{ user.username }}</p>
                    </div>
                    <UserRole :role="user.role" class="my-auto" v-if="user.role !== 'user'" />
                </div>
                <p>Joined {{ user.created_at }}</p>
            </div>
            <Button variant="outline" as-child v-if="isCurrentUserProfile">
                <Link :href="route('settings.profile.edit')" as="button">
                    <Settings class="mt-0.5 size-4" />
                    Profile settings
                </Link>
            </Button>
            <ReportModal :contentId="user.username" contentType="user" :triggerClass="buttonVariants({ variant: 'destructive' })" show-icon />
            <div class="flex gap-2 overflow-x-auto" id="tabs">
                <Link
                    v-for="tab in tabs"
                    :key="tab"
                    :href="route('user.profile', { user: user.username, tab: tab })"
                    :only="['activeTab', 'user']"
                    class="rounded-md px-4 py-1.5 whitespace-nowrap capitalize"
                    :class="activeTab === tab ? 'bg-primary/75 pointer-events-none text-slate-50' : 'hover:bg-primary/15 duration-150'"
                >
                    {{ tab.replace('_', ' ') }}
                </Link>
            </div>
            <UserProfileTab :type="activeTab" :content="user[activeTab]" />
        </div>
    </AppLayout>
</template>
