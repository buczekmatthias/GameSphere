<script setup lang="ts">
import DatePicker from '@/components/DatePicker.vue';
import InputError from '@/components/InputError.vue';
import InputInfo from '@/components/InputInfo.vue';
import Modal from '@/components/Modal.vue';
import Preview from '@/components/Preview.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { transformDate } from '@/composables/useTransformDatePicker';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Game, Genre } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    game: Game;
    genres: Genre[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Game',
        href: route('games.index'),
    },
    {
        title: props.game.title,
        href: route('games.show', { game: props.game.slug }),
    },
    {
        title: 'Edit',
        href: '',
    },
];

const DESCRIPTION_MIN_LENGTH = 50;
const TOTAL_AVAILABLE_MEDIA_SLOT = 6;

const form = useForm({
    title: props.game.title,
    description: props.game.description,
    thumbnail: null,
    media: [] as File[],
    released_at: '' as any,
    genre: props.game.genre.slug,
    media_to_delete: [] as string[],
});

const charactersLeftTillRequired = computed(() => {
    const left = DESCRIPTION_MIN_LENGTH - form.description.length;

    return left > 0 ? left : 0;
});

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        released_at: transformDate(data.released_at),
    })).post(route('games.update', { game: props.game.slug }));
};

const toggleItem = (filename: string) => {
    if (form.media_to_delete.includes(filename)) {
        form.media_to_delete = form.media_to_delete.filter((i) => i !== filename);
    } else {
        form.media_to_delete.push(filename);
    }
};
</script>

<template>
    <Head :title="game.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="submitForm" class="main-container flex flex-col gap-4">
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
                <Input id="thumbnail" type="file" @change="form.thumbnail = $event.target.files[0]" accept="image/jpeg,image/png,image/webp" />
                <div class="flex justify-end gap-2">
                    <Modal>
                        <template #trigger>
                            <p class="cursor-pointer text-sm text-sky-600 dark:text-sky-400">Show thumbnail</p>
                        </template>
                        <template #title>
                            <p>Thumbnail</p>
                        </template>
                        <template #description>Thumbnail of "{{ game.title }}"</template>
                        <Preview type="image" :media="game.thumbnail" />
                    </Modal>
                </div>
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
                <InputInfo :message="`${game.media.length} of ${TOTAL_AVAILABLE_MEDIA_SLOT} slots used. Remove added media to free slots.`" />
                <InputInfo v-if="form.media_to_delete.length > 0" :message="`${form.media_to_delete.length} selected to delete.`" />
                <InputError :message="form.errors.media" />
                <template v-if="game.media.length > 0">
                    <div class="flex justify-end gap-2">
                        <Modal>
                            <template #trigger>
                                <p class="cursor-pointer text-sm text-sky-600 dark:text-sky-400">Show media</p>
                            </template>
                            <template #title>
                                <p>Game media</p>
                            </template>
                            <template #description>Media of "{{ game.title }}"</template>
                            <div class="flex max-h-[75vh] flex-col gap-6 overflow-y-auto">
                                <div v-for="item in game.media" :key="item.path" class="flex flex-col gap-2">
                                    <Preview :type="item.type" :media="item.path" />
                                    <div class="flex items-center gap-2">
                                        <Switch
                                            @update:model-value="toggleItem(item.filename)"
                                            :model-value="form.media_to_delete.includes(item.filename)"
                                        />
                                        <p class="text-sm">Remove this item</p>
                                    </div>
                                </div>
                            </div>
                        </Modal>
                    </div>
                </template>
            </div>

            <div class="form-box">
                <Label for="released_at">Released at</Label>
                <DatePicker v-model="form.released_at" />
                <InputInfo :message="game.released_at" />
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

            <Button type="submit" class="w-full" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Update
            </Button>
        </form>
    </AppLayout>
</template>
