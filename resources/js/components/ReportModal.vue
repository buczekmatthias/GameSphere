<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { canInteract } from '@/composables/useCanInteract';
import { ReportableType, SharedData } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { AlertTriangle, LoaderCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const reportReasons = computed((): Record<number, string> => usePage<SharedData>().props.report_reasons);

const props = withDefaults(
    defineProps<{
        contentId: string;
        contentType: ReportableType;
        triggerClass?: string;
        showIcon?: boolean;
        showText?: boolean;
    }>(),
    {
        showIcon: false,
        showText: true,
    },
);

const reportForm = useForm({
    reason: '',
    id: props.contentId,
    type: props.contentType,
});

const handleSubmit = (e: Event) => {
    e.preventDefault();
    reportForm.post(route('reports.store'), {
        preserveScroll: true,
        onFinish: () => {
            reportForm.reason = '';
            isOpen.value = false;
        },
    });
};

const isOpen = ref<boolean>(false);
</script>

<template>
    <Dialog :open="isOpen" @update:open="isOpen = $event" v-if="canInteract()">
        <DialogTrigger :class="triggerClass">
            <AlertTriangle class="mt-0.5 size-4" v-if="showIcon" />
            <span v-if="showText">Report</span>
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
                <Button @click="handleSubmit" variant="destructive" :disabled="reportForm.reason.length === 0 || reportForm.processing">
                    <LoaderCircle v-if="reportForm.processing" class="h-4 w-4 animate-spin" />
                    Submit report
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
