import { SharedData, User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, ComputedRef } from 'vue';

export function useCurrentUser(): ComputedRef<User> {
    return computed(() => usePage<SharedData>().props.auth.user as User);
}
