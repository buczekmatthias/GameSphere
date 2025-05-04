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

export interface Routes {
    current: string;
    ziggy: Config;
    location: string;
}

export interface SharedData extends PageProps {
    auth: Auth;
    routes: Routes;
    sidebarOpen: boolean;
}

export interface User {
    id: number;
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

export interface Game {
    id: number;
    slug: string;
    title: string;
    description: string;
    thumbnail: string;
    media: array;
    creator: User;
    released_at: string;
    created_at: string;
    updated_at: string;
}

export interface Genre {
    id: number;
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
}

export interface Discussion {
    slug: string;
    title: string;
    author: User;
    comments_count: number;
}
