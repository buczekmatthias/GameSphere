<script setup lang="ts">
import ReportModal from '@/components/app/ReportModal.vue';
import Button from '@/components/ui/button/Button.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Game } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Check, ListChecks, Pen, Trash } from 'lucide-vue-next';
import { capitalize } from 'vue';

defineProps<{
    game: Game;
    lists: { [key: string]: boolean };
}>();
</script>

<template>
    <div class="flex flex-col gap-3 lg:col-start-3 lg:row-end-1">
        <ReportModal :contentId="game.slug" contentType="game" :show-text="false" show-icon />
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="outline">
                    <ListChecks class="size-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent class="w-48" side="left" align="start">
                <DropdownMenuLabel>Games collections</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem as-child v-for="[list, isInCollection] in Object.entries(lists)" :key="list" class="w-full cursor-pointer">
                    <Link :href="route('games.lists.toggle', { game: game.slug, list: list })" :only="['userLists']" method="post" as="button">
                        <span>{{ capitalize(list.replaceAll('_', ' ')) }}</span>
                        <Check class="ml-auto size-5" v-if="isInCollection" />
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
        <Button variant="outline" as-child>
            <Link :href="route('games.edit', { game: game.slug })" as="button" v-if="game.permissions.update">
                <Pen class="size-4" />
            </Link>
        </Button>
        <Button variant="destructive" as-child>
            <Link :href="route('games.destroy', { game: game.slug })" method="delete" v-if="game.permissions.destroy" as="button">
                <Trash class="size-4" />
            </Link>
        </Button>
    </div>
</template>
