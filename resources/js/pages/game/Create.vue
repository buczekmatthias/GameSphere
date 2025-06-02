<script setup lang="ts">
import DatePicker from '@/components/DatePicker.vue';
import InputError from '@/components/InputError.vue';
import InputInfo from '@/components/InputInfo.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { transformDate } from '@/composables/useTransformDatePicker';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Genre } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{
    genres: Genre[];
}>();

const DESCRIPTION_MIN_LENGTH = 50;

const form = useForm({
    title: '',
    description: '',
    thumbnail: null,
    media: [],
    released_at: '' as any,
    genre: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Games',
        href: route('games.index'),
    },
    {
        title: 'Add new game',
        href: route('games.create'),
    },
];

const charactersLeftTillRequired = computed(() => {
    const left = DESCRIPTION_MIN_LENGTH - form.description.length;

    return left > 0 ? left : 0;
});

const isFormValid = () => {
    return form.title.length > 0 && form.description.length > 0 && form.thumbnail !== null && form.released_at && form.genre.length > 0;
};

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        released_at: transformDate(data.released_at),
    })).post(route('games.store'));
};
</script>

<template>
    <Head title="Add new game" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="submitForm" class="flex flex-col gap-4 p-6 md:p-4">
            <div class="form-box">
                <Label for="title">Title</Label>
                <Input id="title" type="text" required autofocus v-model="form.title" placeholder="Example title" />
                <InputError :message="form.errors.title" />
            </div>

            <div class="form-box">
                <Label for="description">Description</Label>
                <Textarea id="description" required v-model="form.description" class="h-64 resize-none" placeholder="Example description" />
                <InputInfo
                    :message="`At least 50 characters long. ${charactersLeftTillRequired === 0 ? '' : charactersLeftTillRequired + ' characters to go'}`"
                />
                <InputError :message="form.errors.description" />
            </div>

            <div class="form-box">
                <Label for="thumbnail">Thumbnail</Label>
                <Input
                    id="thumbnail"
                    type="file"
                    required
                    @change="form.thumbnail = $event.target.files[0]"
                    accept="image/jpeg,image/png,image/webp"
                />
                <InputError :message="form.errors.thumbnail" />
            </div>

            <div class="form-box">
                <Label for="media">Media</Label>
                <Input
                    id="media"
                    type="file"
                    multiple
                    @change="form.media = $event.target.files"
                    accept="image/jpeg,image/png,image/webp,video/mp4"
                />
                <InputError :message="form.errors.media" />
            </div>

            <div class="form-box">
                <Label for="released_at">Released at</Label>
                <DatePicker v-model="form.released_at" />
                <InputError :message="form.errors.released_at" />
            </div>

            <div class="form-box">
                <label>Genre</label>
                <Select v-model="form.genre" required>
                    <SelectTrigger class="w-full cursor-pointer">
                        <SelectValue placeholder="Select genre" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="genre.slug" v-for="genre in genres" :key="genre.slug" class="cursor-pointer">
                            {{ genre.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing || !isFormValid()">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Create
            </Button>
        </form>
    </AppLayout>
</template>
