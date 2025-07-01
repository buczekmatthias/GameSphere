<script lang="ts" setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import {
    Drawer,
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerFooter,
    DrawerHeader,
    DrawerTitle,
    DrawerTrigger,
} from '@/components/ui/drawer';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ReviewRatings } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { createReusableTemplate, useMediaQuery } from '@vueuse/core';
import { LoaderCircle, Star } from 'lucide-vue-next';
import { capitalize, ref } from 'vue';

// Reuse `form` section
const [UseTemplate, GridForm] = createReusableTemplate();
const isDesktop = useMediaQuery('(min-width: 768px)');

const isOpen = ref(false);

const props = defineProps<{
    slug: string;
}>();

const newReviewForm = useForm({
    content: '',
    ratings: {
        gameplay: 0,
        graphics: 0,
        storyline: 0,
        replayability: 0,
        sound_and_music: 0,
        performance: 0,
    },
    game_slug: props.slug,
});

const submitForm = () => {
    if (newReviewForm.content.length > 0 && Object.values(newReviewForm.ratings).every((i) => i > 0)) {
        newReviewForm.post(route('reviews.store'), {
            onSuccess: () => (isOpen.value = false),
        });
    }
};
</script>

<template>
    <UseTemplate>
        <form class="grid items-start gap-4 px-4" @submit.prevent="submitForm">
            <div class="grid gap-2">
                <Label html-for="content">Content</Label>
                <Textarea id="content" v-model="newReviewForm.content" class="h-48 resize-none" />
                <InputError :message="newReviewForm.errors.content" />
            </div>
            <div class="grid gap-2">
                <Label html-for="slug">Game slug</Label>
                <Input id="slug" :default-value="slug" disabled />
                <InputError :message="newReviewForm.errors.game_slug" />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <p class="col-span-full">Ratings</p>
                <div class="grid gap-2" v-for="(k, ind) in Object.keys(newReviewForm.ratings)" :key="k">
                    <p>{{ capitalize(k.replaceAll('_', ' ')) }}</p>
                    <div class="flex gap-1.5">
                        <Star
                            v-for="i in 5"
                            :key="i"
                            @click="newReviewForm.ratings[k as keyof ReviewRatings] = i"
                            class="cursor-pointer"
                            :class="{
                                'text-amber-400': i <= newReviewForm.ratings[k as keyof ReviewRatings],
                            }"
                        />
                    </div>
                    <InputError :message="newReviewForm.errors.ratings?.[ind]" />
                </div>
            </div>
            <Button type="submit" class="sticky bottom-0" :disabled="newReviewForm.processing">
                <LoaderCircle v-if="newReviewForm.processing" class="h-4 w-4 animate-spin" />
                Add review
            </Button>
        </form>
    </UseTemplate>

    <Dialog v-if="isDesktop" v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="outline" class="mb-4 w-full"> Create review </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Create review</DialogTitle>
                <DialogDescription> Share your opinion about the game </DialogDescription>
            </DialogHeader>
            <GridForm />
        </DialogContent>
    </Dialog>

    <Drawer v-else v-model:open="isOpen">
        <DrawerTrigger as-child>
            <Button variant="outline" class="mb-4 w-full"> Create review </Button>
        </DrawerTrigger>
        <DrawerContent>
            <DrawerHeader class="text-left">
                <DrawerTitle>Create review</DrawerTitle>
                <DrawerDescription> Share your opinion about the game </DrawerDescription>
            </DrawerHeader>
            <GridForm />
            <DrawerFooter class="pt-2">
                <DrawerClose as-child>
                    <Button variant="outline"> Cancel </Button>
                </DrawerClose>
            </DrawerFooter>
        </DrawerContent>
    </Drawer>
</template>
