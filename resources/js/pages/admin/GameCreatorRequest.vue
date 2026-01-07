<script setup lang="ts">
import GameCreatorRequestTable from '@/components/admin/GameCreatorRequestTable.vue';
import MainContainer from '@/components/app/MainContainer.vue';
import PaginatedContent from '@/components/app/PaginatedContent.vue';
import Heading from '@/components/Heading.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Pagination, User as UserType } from '@/types';
import { Head } from '@inertiajs/vue3';

export interface GameCreatorRequest {
    slug: string;
    user: UserType;
    created_at: string;
}

defineProps<{
    requests: Pagination & { data: GameCreatorRequest[] };
}>();

const reloadOnly: string[] = ['requests'];
</script>

<template>
    <Head title="Requests to become game creator" />

    <AdminLayout>
        <MainContainer class="flex flex-col gap-4">
            <Heading title="Requests to become game creator" />

            <PaginatedContent :pagination="requests" :reload-only pagination-position="bottom">
                <GameCreatorRequestTable :requests="requests.data" />
            </PaginatedContent>
        </MainContainer>
    </AdminLayout>
</template>
