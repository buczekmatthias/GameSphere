import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';

export function canInteract(): boolean {
    return usePage<SharedData>().props.can_interact;
}
