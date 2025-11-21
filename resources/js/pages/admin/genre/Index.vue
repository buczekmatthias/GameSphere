<script setup lang="ts">
import Table from '@/components/Admin/Table.vue';
import FormButton from '@/components/FormButton.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { TableCell, TableRow } from '@/components/ui/table';
import { getPaginationData } from '@/composables/usePagination';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Genre, Pagination as PaginationType } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Ellipsis, Eye, Pen, Plus, Trash } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    genres: PaginationType & { data: Genre[] };
}>();

const tableHeaders = [
    { label: 'Slug' },
    { label: 'Name', is_sortable: true, column: 'name' },
    { label: 'Discussions', is_sortable: true, column: 'discussions' },
    { label: 'Games', is_sortable: true, column: 'games' },
];

const reloadOnly: string[] = ['genres'];

const openDialog = ref<boolean>(false);

const newGenreForm = useForm({
    name: '',
});

const submit = () => {
    newGenreForm.post(route('admin.genres.store'), {
        onFinish: () => (openDialog.value = false),
    });
};
</script>

<template>
    <Head title="Genres" />

    <AdminLayout>
        <div class="main-container flex flex-col gap-4">
            <div class="flex items-center justify-between gap-4">
                <p class="text-3xl">Genres</p>
                <Dialog v-model:open="openDialog">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="size-4" />
                            <span class="max-sm:hidden">Create genre</span>
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px]">
                        <DialogHeader>
                            <DialogTitle>New genre</DialogTitle>
                            <DialogDescription>New genre can be used with games and discussions</DialogDescription>
                        </DialogHeader>
                        <div>
                            <div class="flex flex-col gap-2">
                                <Label for="name">Name</Label>
                                <Input id="name" type="text" required autofocus :tabindex="1" v-model="newGenreForm.name" placeholder="Genre name" />
                            </div>
                        </div>
                        <DialogFooter>
                            <FormButton label="Store genre" @click="submit" :tabindex="4" :is-processing="newGenreForm.processing" />
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>

            <Table :reload-only :headers="tableHeaders">
                <TableRow v-for="genre in genres.data" :key="genre.slug">
                    <TableCell>{{ genre.slug }}</TableCell>
                    <TableCell>{{ genre.name }}</TableCell>
                    <TableCell class="text-center">{{ genre.discussions_count }}</TableCell>
                    <TableCell class="text-center">{{ genre.games_count }}</TableCell>
                    <TableCell>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline">
                                    <Ellipsis class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-56">
                                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link :href="route('genres.show', { genre: genre.slug })" as="button" class="w-full cursor-pointer">
                                        <Eye class="size-4" />
                                        View
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link :href="route('admin.genres.edit', { genre: genre.slug })" as="button" class="w-full cursor-pointer">
                                        <Pen class="size-4" />
                                        Edit
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link :href="route('admin.genres.destroy', { genre: genre.slug })" method="delete" class="w-full cursor-pointer">
                                        <Trash class="size-4" />
                                        Delete
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </TableCell>
                </TableRow>
            </Table>

            <Pagination :pagination="getPaginationData(genres)" :reload-only />
        </div>
    </AdminLayout>
</template>
