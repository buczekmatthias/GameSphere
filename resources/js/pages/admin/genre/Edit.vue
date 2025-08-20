<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Genre } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ChevronLeft, LoaderCircle } from 'lucide-vue-next';

const props = defineProps<{
    genre: Genre;
}>();

const updateForm = useForm({
    name: props.genre.name,
});
</script>

<template>
    <Head title="Genres" />

    <AdminLayout>
        <div class="main-container flex flex-col gap-4">
            <TextLink :href="route('admin.genres.index')" class="flex items-center gap-1">
                <ChevronLeft class="mt-0.5 size-4" />
                <p>Back to all genres</p>
            </TextLink>
            <form @submit.prevent="updateForm.patch(route('admin.genres.update', { genre: genre.slug }))" class="flex flex-col gap-4">
                <p class="text-4xl">Edit genre</p>
                <p class="text-foreground/60 text-sm">#{{ genre.slug }}</p>
                <div class="flex flex-col gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" v-model="updateForm.name" placeholder="Genre name" />
                    <InputError :message="updateForm.errors.name" />
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="updateForm.processing">
                    <LoaderCircle v-if="updateForm.processing" class="h-4 w-4 animate-spin" />
                    Save changes
                </Button>
            </form>
        </div>
    </AdminLayout>
</template>
