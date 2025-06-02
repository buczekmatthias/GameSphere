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

export interface Game {
    slug: string;
    title: string;
    description: string;
    thumbnail: string;
    media: { filename: string; path: string; type: string }[];
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
    ratings: object;
    is_verified: boolean;
    user: User & { is_email_verified: boolean };
    created_at: string;
}

export interface Discussion {
    slug: string;
    title: string;
    author: User;
    comments_count: number;
    created_at: string;
}
