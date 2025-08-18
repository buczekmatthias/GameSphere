<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Separator } from '@/components/ui/separator';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import UserInfo from '@/components/UserInfo.vue';
import { getPaginationData } from '@/composables/usePagination';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Pagination as PaginationType, Report, Review } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Ban, ChevronLeft, Circle, CircleX, Ellipsis, Gamepad2, Trash } from 'lucide-vue-next';
import { capitalize } from 'vue';

defineProps<{
    review: Review;
    reports: PaginationType & { data: Report[] };
}>();
</script>

<template>
    <Head :title="`Review - ${review.slug}`" />

    <AdminLayout>
        <div class="main-container flex flex-col gap-4">
            <TextLink :href="route('admin.reviews.index')" class="flex items-center gap-1">
                <ChevronLeft class="mt-0.5 size-4" />
                <p>Back to all reviews</p>
            </TextLink>
            <p class="text-4xl">Review</p>
            <p class="text-foreground/60 text-sm">#{{ review.slug }}</p>
            <Button variant="destructive" as-child>
                <Link as="button" :href="route('reviews.destroy', { review: review.slug, to_route: 'admin.reviews.index' })" method="delete">
                    Delete review
                </Link>
            </Button>
            <div class="flex items-center gap-3">
                <Link class="mr-auto flex gap-3" :href="route('user.profile', { user: review.user.username })">
                    <UserInfo :show-username="true" :user="review.user" />
                </Link>
                <p v-if="review.user.role !== 'user'" class="text-sm">{{ capitalize(review.user.role) }}</p>
            </div>
            <p>{{ review.content }}</p>
            <p>Created at: {{ review.created_at }}</p>
            <TextLink as="button" :href="route('games.show', { game: review.game.slug })" class="flex items-center gap-2 self-start">
                <Gamepad2 class="mt-1 h-5" />
                {{ review.game.title }}
            </TextLink>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1" v-for="[k, v] in Object.entries(review.ratings)" :key="k">
                    <p class="text-xs uppercase dark:text-slate-300/40">{{ k.replaceAll('_', ' ') }}</p>
                    <p class="">{{ v }}</p>
                </div>
            </div>
            <Separator class="my-4" />
            <template v-if="reports.data.length > 0">
                <p class="text-3xl">Reports</p>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-80">Slug</TableHead>
                            <TableHead class="w-20">Status</TableHead>
                            <TableHead>User</TableHead>
                            <TableHead>Reason</TableHead>
                            <TableHead class="w-32">Created at</TableHead>
                            <TableHead class="w-16"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="report in reports.data" :key="report.slug">
                            <TableCell>{{ report.slug }}</TableCell>
                            <TableCell class="capitalize">{{ report.status }}</TableCell>
                            <TableCell>
                                <TextLink :href="route('user.profile', { user: review.user.username })">{{ review.user.name }}</TextLink>
                            </TableCell>
                            <TableCell>{{ report.reason }}</TableCell>
                            <TableCell>{{ report.created_at }}</TableCell>
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
                                        <template v-if="report.status !== 'open'">
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="route('admin.reports.update', { report: report.slug, status: 'open' })"
                                                    method="patch"
                                                    as="button"
                                                    class="w-full cursor-pointer"
                                                >
                                                    <Circle class="size-4" />
                                                    Open
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="route('admin.reports.destroy', { report: report.slug })"
                                                    method="delete"
                                                    as="button"
                                                    class="w-full cursor-pointer"
                                                >
                                                    <Trash class="size-4" />
                                                    Delete
                                                </Link>
                                            </DropdownMenuItem>
                                        </template>
                                        <template v-else>
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="route('admin.reports.update', { report: report.slug, status: 'closed' })"
                                                    method="patch"
                                                    as="button"
                                                    class="w-full cursor-pointer"
                                                >
                                                    <CircleX class="size-4" />
                                                    Close
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="route('admin.reports.update', { report: report.slug, status: 'rejected' })"
                                                    method="patch"
                                                    as="button"
                                                    class="w-full cursor-pointer"
                                                >
                                                    <Ban class="size-4" />
                                                    Reject
                                                </Link>
                                            </DropdownMenuItem>
                                        </template>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <Pagination :pagination="getPaginationData(reports)" :reload-only="['reports']" />
            </template>
            <template v-else>
                <p>No reports to display</p>
            </template>
        </div>
    </AdminLayout>
</template>
