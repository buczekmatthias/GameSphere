<script setup lang="ts">
import GameCreatorRequestTable from '@/components/Admin/GameCreatorRequestTable.vue';
import Pagination from '@/components/Pagination.vue';
import { getPaginationData } from '@/composables/usePagination';
import { GameCreatorRequest } from '@/pages/admin/GameCreatorRequest.vue';
import type { Pagination as PaginationType } from '@/types';

defineProps<{
    pending_requests: PaginationType & { data: GameCreatorRequest[] };
}>();
</script>

<template>
    <div class="bg-card col-span-full">
        <div class="border-b-card-foreground/20 flex items-center justify-between gap-4 border-b border-solid px-3 py-4">
            <p class="text-xl">Pending requests to become game creator</p>
            <Pagination :show-counter="false" :pagination="getPaginationData(pending_requests)" :reload-only="['pending_requests']" />
        </div>
        <div :class="pending_requests.data.length > 0 ? 'm-1' : 'm-3'">
            <GameCreatorRequestTable :requests="pending_requests.data" />
        </div>
    </div>
</template>
