import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';

export function canInteract(): boolean {
    return usePage<SharedData>().props.can_interact;
}

export function hasSpecialPermissions(): boolean {
    return usePage<SharedData>().props.has_special_permissions;
}

export function isCurrentUserTheAuthor(username: string): boolean {
    if (!username) return false;

    return usePage<SharedData>().props.auth.user.username === username;
}
