<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';

import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ReportableType, SharedData } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const reportReasons = computed((): Record<number, string> => usePage<SharedData>().props.report_reasons);

const props = defineProps<{
    contentId: string;
    contentType: ReportableType;
    triggerClass?: string;
    triggerContent: string;
}>();

const reportForm = useForm({
    reason: '',
    id: props.contentId,
    type: props.contentType,
});

const handleSubmit = (e: Event) => {
    e.preventDefault();
    reportForm.post(route('reports.store'));
};
</script>

<template>
    <Dialog>
        <DialogTrigger :class="triggerClass">
            {{ triggerContent }}
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Report content</DialogTitle>
                <DialogDescription>Choose the reason and let us know something's wrong</DialogDescription>
            </DialogHeader>

            <Select v-model="reportForm.reason">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select report reason" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="reason in reportReasons" :key="reason" :value="reason"> {{ reason }} </SelectItem>
                </SelectContent>
            </Select>

            <DialogFooter>
                <Button @click="handleSubmit" variant="destructive" :disabled="reportForm.reason.length === 0"> Submit report </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
