<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { TableHead } from '@/components/ui/table';
import { ArrowDownZA, ArrowUpDown, ArrowUpZA } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        withSorting?: boolean;
        label: string;
    }>(),
    {
        withSorting: false,
    },
);

defineEmits(['order-choice']);
</script>

<template>
    <TableHead>
        <span class="my-auto">
            {{ label }}
        </span>
        <DropdownMenu v-if="withSorting">
            <DropdownMenuTrigger as-child>
                <Button variant="outline"><ArrowUpDown class="size-4" /></Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent class="w-56">
                <DropdownMenuLabel>Sorting</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem @click="$emit('order-choice', 'asc')" class="w-full cursor-pointer">
                    <ArrowUpZA class="size-4" />
                    Ascending
                </DropdownMenuItem>
                <DropdownMenuItem @click="$emit('order-choice', 'desc')" class="w-full cursor-pointer">
                    <ArrowDownZA class="size-4" />
                    Descending
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </TableHead>
</template>
