<script setup lang="ts">
import ActiveReports from '@/components/admin/ActiveReports.vue';
import CustomChartTooltip from '@/components/admin/CustomChartTooltip.vue';
import PendingGameCreatorRequests from '@/components/admin/PendingGameCreatorRequests.vue';
import Trend from '@/components/admin/Trend.vue';
import MainContainer from '@/components/app/MainContainer.vue';
import Heading from '@/components/Heading.vue';
import { AreaChart } from '@/components/ui/chart-area';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { GameCreatorRequest } from '@/pages/admin/GameCreatorRequest.vue';
import type { Report } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Entries {
    games: { this_month: number; last_month: number };
    users: { this_month: number; last_month: number };
    reports: { this_month: number; last_month: number };
    discussions: { this_month: number; last_month: number };
}

interface ChartData {
    month: string;
    games: number;
    users: number;
    discussions: number;
    reports: number;
}

defineProps<{
    new_entries: Entries;
    chart_data: ChartData[];
    active_reports: Report[];
    pending_requests: GameCreatorRequest[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <MainContainer class="grid grid-cols-1 gap-4 lg:grid-cols-[2.5fr_1fr]">
            <Heading title="Overview" description="Stats update every 45 minutes" class="col-span-full !mb-0 text-2xl" />
            <div class="grid grid-cols-1 grid-rows-4 gap-4 lg:col-start-2 lg:row-start-2">
                <Trend v-for="[label, entry] in Object.entries(new_entries)" :key="label" :label="label" :entry="entry" />
            </div>
            <div class="bg-card h-[30rem] rounded-md p-3">
                <AreaChart
                    index="month"
                    :data="chart_data"
                    :categories="['games', 'users', 'discussions', 'reports']"
                    :colors="['yellow', 'green', 'cyan', 'red']"
                    :custom-tooltip="CustomChartTooltip"
                />
            </div>
            <ActiveReports :active_reports />
            <PendingGameCreatorRequests :pending_requests />
        </MainContainer>
    </AdminLayout>
</template>
