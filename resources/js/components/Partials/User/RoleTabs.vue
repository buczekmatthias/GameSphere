<script setup lang="ts">
import { ZiggyWithGamesQuery } from '@/pages/app/Users.vue';
import { Link } from '@inertiajs/vue3';
import { capitalize, computed } from 'vue';

const props = defineProps<{
    roles: string[];
    ziggy: ZiggyWithGamesQuery;
    users_count: number | string;
    total: number;
    reloadOnly: string[];
}>();

const queryWithoutRole = computed(() => (props.ziggy.query.contains ? { contains: props.ziggy.query.contains } : {}));
</script>

<template>
    <div class="flex gap-2.5 overflow-auto py-1.5 text-sm text-nowrap">
        <Link
            :href="route(ziggy.current, queryWithoutRole)"
            class="hover:bg-primary/30 rounded-md px-2 py-1.5 duration-150"
            :class="{ 'bg-primary': !ziggy.query.role }"
            :only="reloadOnly"
        >
            All
        </Link>
        <Link
            :href="route(ziggy.current, { ...ziggy.query, role: role })"
            class="hover:bg-primary/30 rounded-md px-2 py-1.5 duration-150"
            v-for="role in roles"
            :key="role"
            :class="{ 'bg-primary': ziggy.query.role === role }"
            :only="reloadOnly"
        >
            {{ capitalize(role.replace('_', ' ')) }}s
        </Link>
    </div>
</template>
