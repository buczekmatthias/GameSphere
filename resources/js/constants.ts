import { SharedData } from '@/types';
import { Constants } from '@/types/constants';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const constants = computed((): Constants => usePage<SharedData>().props.constants);

export const DESCRIPTION_MIN_LENGTH = constants.value.form.description.min_length;

export const MEDIA_ACCEPT_TYPE = constants.value.form.files.media.accept_type;
export const TOTAL_AVAILABLE_MEDIA_SLOT = constants.value.form.files.media.max_files;

export const THUMBNAIL_ACCEPT_TYPE = constants.value.form.files.thumbnail.accept_type;
