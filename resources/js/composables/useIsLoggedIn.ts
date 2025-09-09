import { SharedData, User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function isLoggedIn(): boolean {
    const user = computed(() => usePage<SharedData>().props.auth.user as User);

    return user.value.role !== 'guest';
}
