import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';

export function userCanInteract(): boolean {
    return usePage<SharedData>().props.can_user.interact;
}

export function userHasSpecialPermissions(): boolean {
    return usePage<SharedData>().props.can_user.use_special_permissions;
}

export function userCanAddGame(): boolean {
    return usePage<SharedData>().props.can_user.add_game;
}
