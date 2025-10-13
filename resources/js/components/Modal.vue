<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';

defineProps<{
    triggerClass?: string;
}>();
</script>

<template>
    <Dialog>
        <DialogTrigger :class="triggerClass">
            <slot name="trigger" />
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    <slot name="title" />
                </DialogTitle>
                <DialogDescription>
                    <slot name="description" />
                </DialogDescription>
            </DialogHeader>
            <slot />
            <DialogFooter>
                <slot name="footer" />
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
