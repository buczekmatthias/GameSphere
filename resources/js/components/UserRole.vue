<script setup lang="ts">
import { computed } from 'vue';

defineProps<{
    role: string;
}>();

const formatRole = computed(() => (role: string) => role.replaceAll('_', ' '));
</script>

<template>
    <p
        class="self-start rounded-md px-3 py-2 text-white capitalize"
        :class="{
            'bg-slate-800': role === 'user',
            'bg-amber-800': role === 'game_creator',
            'bg-rose-800': role === 'moderator',
            'bg-emerald-800': role === 'admin',
            'bg-fuchsia-800': role === 'developer',
        }"
    >
        {{ formatRole(role) }}
    </p>
</template>
