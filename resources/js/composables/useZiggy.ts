import { SharedData, Ziggy } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, ComputedRef } from 'vue';

export function useZiggy(): ComputedRef<Ziggy> {
    return computed(() => usePage<SharedData>().props.ziggy);
}
