<script setup lang="ts">
import GameCreatorRequestTable from '@/components/admin/GameCreatorRequestTable.vue';
import FormActionTap from '@/components/FormActionTap.vue';
import { GameCreatorRequest } from '@/pages/admin/GameCreatorRequest.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    pending_requests: GameCreatorRequest[];
}>();
</script>

<template>
    <div class="bg-card col-span-full">
        <div class="border-b-card-foreground/20 flex items-center justify-between gap-4 border-b border-solid px-3 py-4">
            <p class="text-xl">Pending requests to become game creator</p>
            <Link :href="route('admin.game.creator.join')" v-if="pending_requests.length > 0">
                <FormActionTap>View all</FormActionTap>
            </Link>
        </div>
        <div :class="pending_requests.length > 0 ? 'm-1' : 'm-3'">
            <GameCreatorRequestTable :requests="pending_requests" />
        </div>
    </div>
</template>
