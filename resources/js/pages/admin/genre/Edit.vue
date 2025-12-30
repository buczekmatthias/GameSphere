<script setup lang="ts">
import FormBox from '@/components/FormBox.vue';
import FormButton from '@/components/FormButton.vue';
import GoBackLink from '@/components/GoBackLink.vue';
import InputError from '@/components/InputError.vue';
import MainContainer from '@/components/MainContainer.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Genre } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

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
        <MainContainer class="flex flex-col gap-4">
            <GoBackLink :href="route('admin.genres.index')" />
            <form @submit.prevent="updateForm.patch(route('admin.genres.update', { genre: genre.slug }))" class="flex flex-col gap-4">
                <p class="text-4xl">Edit genre</p>
                <p class="text-foreground/60 text-sm">#{{ genre.slug }}</p>
                <FormBox>
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" v-model="updateForm.name" placeholder="Genre name" />
                    <InputError :message="updateForm.errors.name" />
                </FormBox>

                <FormButton label="Save changes" :tabindex="4" :is-processing="updateForm.processing" />
            </form>
        </MainContainer>
    </AdminLayout>
</template>
