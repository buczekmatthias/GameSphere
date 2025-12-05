<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

defineProps<{
    href: string;
}>();
</script>

<template>
    <Link :href class="self-start" as="button">
        <ArrowLeft class="size-5" />
    </Link>
</template>
