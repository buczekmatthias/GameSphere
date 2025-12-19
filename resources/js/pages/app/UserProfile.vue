<script setup lang="ts">
import LazyAvatar from '@/components/LazyAvatar.vue';
import MainContainer from '@/components/MainContainer.vue';
import ReportModal from '@/components/ReportModal.vue';
import TabNavigation, { TabNavigationItem } from '@/components/TabNavigation.vue';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import UserProfileTab from '@/components/UserProfileTab.vue';
import UserRole from '@/components/UserRole.vue';
import { userCanInteract } from '@/composables/useCanInteract';
import { useInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion, DiscussionComment, Game, Genre, Pagination, Review, User } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, Settings } from 'lucide-vue-next';
import { capitalize, computed } from 'vue';

interface UserProfile extends User {
    created_games: Pagination & { data: Game[] };
    games: Pagination & { data: Game[] };
    genres: Pagination & { data: Genre[] };
    reviews: Pagination & { data: Review[] };
    discussions: Pagination & { data: Discussion[] };
    comments: Pagination & { data: DiscussionComment[] };
}

const props = defineProps<{
    user: UserProfile;
    isCurrentUserProfile: boolean;
    tabs: TabNavigationItem[];
    activeTab: 'created_games' | 'games' | 'genres' | 'reviews' | 'discussions' | 'comments';
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: route('users.index'),
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
</script>

<template>
    <Head :title="`${user.name}\'s profile | ${capitalize(activeTab)}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-5xl flex-col gap-4">
            <div
                class="grid gap-4"
                :class="userCanInteract() ? 'ml:grid-cols-[auto_1fr_auto] grid-cols-[1fr_auto]' : 'grid-cols-1 md:grid-cols-[auto_1fr]'"
            >
                <LazyAvatar
                    :src="user.avatar"
                    :alt="user.name"
                    v-if="showAvatar"
                    :fallback="getInitials(user.name)"
                    class="h-56 w-44 lg:row-span-2"
                />
                <div class="flex flex-col gap-3 lg:col-start-3 lg:row-end-1">
                    <ReportModal :contentId="user.username" contentType="user" show-icon :show-text="false" />
                    <Button variant="outline" as-child v-if="isCurrentUserProfile">
                        <Link :href="route('settings.profile.edit')" as="button">
                            <Settings class="size-4" />
                        </Link>
                    </Button>
                </div>
                <div class="max-ml:col-span-full ml:col-start-2 ml:row-end-1 flex flex-col gap-4">
                    <div class="ml:flex-col flex justify-between gap-4">
                        <div>
                            <div class="mb-0.5 flex items-center gap-2">
                                <p class="text-2xl">{{ user.name }}</p>
                                <TooltipProvider v-if="user.email_verified_at">
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <CheckCircle class="text-sidebar-ring mt-1 size-5" />
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>Verified user</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </div>
                            <p class="text-sm text-slate-400">@{{ user.username }}</p>
                        </div>
                        <UserRole :role="user.role" class="ml:order-first my-auto" v-if="user.role !== 'user'" />
                    </div>
                    <p>Joined {{ user.created_at }}</p>
                </div>
            </div>
            <TabNavigation :tabs="tabs" :active-tab :reload-only="['activeTab', 'user']" />
            <UserProfileTab :type="activeTab" :content="user[activeTab]" />
        </MainContainer>
    </AppLayout>
</template>
