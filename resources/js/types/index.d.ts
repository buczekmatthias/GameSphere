import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface Ziggy {
    current: string;
    ziggy: Config;
    location: string;
    query: object;
}

export interface SharedData extends PageProps {
    auth: Auth;
    ziggy: Ziggy;
    sidebarOpen: boolean;
}

export interface User {
    name: string;
    username: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    role: string;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface PaginationLink {
    url?: string;
    label: string;
    active: boolean;
}

export interface Pagination {
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        per_page: number;
        to: number;
        total: number;
    };
}

export interface Media {
    filename: string;
    path: string;
    type: string;
}

export interface Game {
    slug: string;
    title: string;
    description: string;
    thumbnail: string;
    media: Media[];
    creator: User;
    genre: Genre;
    released_at: string;
    created_at: string;
    updated_at: string;
}

export interface Genre {
    slug: string;
    name: string;
    discussions_count: number;
    games_count: number;
}

export interface Review {
    slug: string;
    content: string;
    ratings: ReviewRatings;
    is_verified: boolean;
    user: User & { is_email_verified: boolean };
    created_at: string;
}

export interface ReviewRatings {
    gameplay: number;
    graphics: number;
    storyline: number;
    replayability: number;
    sound_and_music: number;
    performance: number;
}

export interface Discussion {
    slug: string;
    shortTitle: string;
    title: string;
    author: User;
    discussable: DiscussableGame | DiscussableGenre;
    discussable_type: 'game' | 'genre';
    comments: Pagination & { data: DiscussionComment[] };
    comments_count: number;
    created_at: string;
}

export interface DiscussionComment {
    slug: string;
    content: string;
    media: Media[];
    user: User;
    created_at: string;
}

export interface DiscussableGame {
    slug: string;
    title: string;
}

export interface DiscussableGenre {
    slug: string;
    name: string;
}
