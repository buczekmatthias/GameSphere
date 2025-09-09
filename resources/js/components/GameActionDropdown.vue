<script setup lang="ts">
import ReportModal from '@/components/ReportModal.vue';
import { buttonVariants } from '@/components/ui/button';
import Button from '@/components/ui/button/Button.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuPortal,
    DropdownMenuSeparator,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Game } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Check, EllipsisVertical } from 'lucide-vue-next';

defineProps<{
    game: Game;
    lists: { [key: string]: boolean };
}>();
</script>

<template>
    <div class="flex flex-col gap-3">
        <ReportModal
            :contentId="game.slug"
            contentType="game"
            :trigger-class="buttonVariants({ variant: 'destructive' })"
            :show-text="false"
            show-icon
        />
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="outline">
                    <EllipsisVertical class="size-5" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent class="w-48">
                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuSub>
                    <DropdownMenuSubTrigger>
                        <span>Games collections</span>
                    </DropdownMenuSubTrigger>
                    <DropdownMenuPortal>
                        <DropdownMenuSubContent>
                            <DropdownMenuItem
                                v-for="[list, isInCollection] in Object.entries(lists)"
                                :key="list"
                                class="w-full cursor-pointer"
                                as-child
                            >
                                <Link
                                    :href="route('games.lists.toggle', { game: game.slug, list: list })"
                                    :only="['userLists']"
                                    method="post"
                                    as="button"
                                >
                                    <span class="capitalize">{{ list.replaceAll('_', ' ') }}</span>
                                    <Check class="ml-auto size-5" v-if="isInCollection" />
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuSubContent>
                    </DropdownMenuPortal>
                </DropdownMenuSub>
                <DropdownMenuSeparator />
                <DropdownMenuItem as-child>
                    <Link :href="route('games.edit', { game: game.slug })" as="button" class="w-full cursor-pointer"> Edit game </Link>
                </DropdownMenuItem>
                <DropdownMenuItem as-child>
                    <Link :href="route('games.destroy', { game: game.slug })" method="delete" class="w-full cursor-pointer" as="button">
                        Delete game
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
