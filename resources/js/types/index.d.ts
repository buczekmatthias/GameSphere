import { Constants } from '@/constants';
import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
    group?: string;
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
    can_user: {
        interact: boolean;
        use_special_permissions: boolean;
        add_game: boolean;
    };
    report_reasons: Record<number, string>;
    constants: Constants;
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

export interface Pagination {
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        to: number;
        total: number;
    };
    links: {
        prev: string;
        next: string;
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
    shortTitle: string;
    description: string;
    short_description: string;
    thumbnail: string;
    media: Media[];
    media_count: number;
    creator: User;
    genre: Genre;
    released_at: string;
    created_at: string;
    updated_at: string;
    lists: string[];
    score: number;
    reviews_count: number;
    permissions: Permissions;
    is_released: boolean;
    reviews: Pagination & { data: ReviewType[] };
    discussions: Pagination & { data: DiscussionType[] };
}

export interface Genre {
    slug: string;
    name: string;
    shortName: string;
    discussions_count: number;
    games_count: number;
    is_user_favorite: boolean;
}

export interface Review {
    slug: string;
    shortSlug: string;
    content: string;
    ratings: ReviewRatings;
    avg_rating: number;
    user: User & { is_email_verified: boolean };
    game: Game;
    created_at: string;
    permissions: Permissions;
    reports: Pagination & { data: Report[] };
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
    permissions: Permissions;
    is_locked: boolean;
}

export interface DiscussionComment {
    slug: string;
    shortSlug: string;
    content: string;
    media: Media[];
    user: User;
    created_at: string;
    discussion: Discussion;
    permissions: Permissions;
    reports: Pagination & { data: Report[] };
}

export interface DiscussableGame {
    slug: string;
    title: string;
}

export interface DiscussableGenre {
    slug: string;
    name: string;
}

export type ReportableType = 'comment' | 'discussion' | 'game' | 'user' | 'review';

export interface Report {
    slug: string;
    reason: string;
    status: string;
    user: User;
    reportable: string;
    reportable_type: ReportableType;
    created_at: string;
}

export interface Permissions {
    [key: string]: boolean;
}
