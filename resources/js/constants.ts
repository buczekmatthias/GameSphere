import { SharedData } from '@/types';
import { Constants } from '@/types/constants';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export const constants = computed((): Constants => usePage<SharedData>().props.constants);
