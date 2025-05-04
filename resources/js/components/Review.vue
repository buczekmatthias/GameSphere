<script setup lang="ts">
import { Review } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    review: Review;
}>();

const avgRating = computed(() =>
    (Object.values(props.review.ratings).reduce((a, b) => a + b, 0) / Object.keys(props.review.ratings).length).toFixed(1),
);
</script>

<template>
    <div>
        {{ avgRating }}
        {{ review }}
    </div>
</template>
