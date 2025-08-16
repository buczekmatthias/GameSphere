<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Report } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    report: Report;
}>();

const isReportOpen = computed(() => props.report.status === 'open');
</script>

<template>
    <Head :title="`Report - #${report.slug}`" />

    <AdminLayout group="admin.reports">
        <div class="main-container flex flex-col gap-4">
            <TextLink :href="route('admin.reports.index')" class="flex items-center gap-1">
                <ChevronLeft class="mt-0.5 size-4" />
                <p>Back to all reports</p>
            </TextLink>
            <p class="text-4xl">Report</p>
            <p class="text-foreground/60 text-sm">#{{ report.slug }}</p>
            <p class="capitalize">Status: {{ report.status }}</p>
            <p class="">Reason: {{ report.reason }}</p>
            <p>
                Reported by: <TextLink :href="route('user.profile', { user: report.user.username })">{{ report.user.name }}</TextLink>
            </p>
            <p class="">Reported at: {{ report.created_at }}</p>
            <p class="">Type: {{ report.reportable_type }}</p>
            <TextLink :href="report.reportable" class="self-start">View entry</TextLink>
            <div class="flex flex-col gap-2" v-if="isReportOpen">
                <Button as-child variant="secondary">
                    <Link :href="route('admin.reports.update', { report: report.slug, status: 'closed' })" method="patch">Close</Link>
                </Button>
                <Button as-child variant="destructive">
                    <Link :href="route('admin.reports.update', { report: report.slug, status: 'rejected' })" method="patch">Reject</Link>
                </Button>
            </div>
            <Button as-child variant="destructive" class="" v-else>
                <Link :href="route('admin.reports.destroy', { report: report.slug })" method="delete">Delete</Link>
            </Button>
        </div>
    </AdminLayout>
</template>
