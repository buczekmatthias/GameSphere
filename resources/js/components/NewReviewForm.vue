<script lang="ts" setup>
import CanInteract from '@/components/CanInteract.vue';
import FormActionTap from '@/components/FormActionTap.vue';
import FormButton from '@/components/FormButton.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
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
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Textarea } from '@/components/ui/textarea';
import { ReviewRatings } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { createReusableTemplate, useMediaQuery } from '@vueuse/core';
import { Plus, Star } from 'lucide-vue-next';
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
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <CanInteract>
        <UseTemplate>
            <ScrollArea>
                <div class="max-h-[35vh] md:max-h-[55vh]">
                    <form class="grid items-start gap-4 px-4">
                        <div class="grid gap-2">
                            <Label html-for="content">Content</Label>
                            <Textarea id="content" v-model="newReviewForm.content" class="h-48 resize-none" />
                            <InputError :message="newReviewForm.errors.content" />
                        </div>
                        <div class="flex flex-col gap-4">
                            <p class="text-lg">Ratings</p>
                            <div class="grid grid-cols-[1fr_auto] gap-2" v-for="(k, ind) in Object.keys(newReviewForm.ratings)" :key="k">
                                <p>{{ capitalize(k.replaceAll('_', ' ')) }}</p>
                                <div class="flex gap-3">
                                    <Star
                                        v-for="i in 5"
                                        :key="i"
                                        @click="newReviewForm.ratings[k as keyof ReviewRatings] = i"
                                        class="size-5 cursor-pointer"
                                        :class="{
                                            'text-amber-400': i <= newReviewForm.ratings[k as keyof ReviewRatings],
                                        }"
                                    />
                                </div>
                                <InputError :message="newReviewForm.errors.ratings?.[ind]" />
                            </div>
                        </div>
                    </form>
                </div>
            </ScrollArea>
        </UseTemplate>

        <Dialog v-if="isDesktop" v-model:open="isOpen">
            <DialogTrigger as-child>
                <FormActionTap> <Plus class="size-4" /> Create review </FormActionTap>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Create review</DialogTitle>
                    <DialogDescription> Share your opinion about the game </DialogDescription>
                </DialogHeader>
                <GridForm />
                <DialogFooter class="pt-2">
                    <FormButton label="Add review" :is-processing="newReviewForm.processing" @click="submitForm()" />
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Drawer v-else v-model:open="isOpen">
            <DrawerTrigger as-child>
                <FormActionTap> <Plus class="size-4" /> Create review </FormActionTap>
            </DrawerTrigger>
            <DrawerContent>
                <DrawerHeader class="text-left">
                    <DrawerTitle>Create review</DrawerTitle>
                    <DrawerDescription> Share your opinion about the game </DrawerDescription>
                </DrawerHeader>
                <GridForm />
                <DrawerFooter class="pt-2">
                    <FormButton label="Add review" :is-processing="newReviewForm.processing" @click="submitForm()" />
                    <DrawerClose as-child>
                        <Button variant="outline"> Cancel </Button>
                    </DrawerClose>
                </DrawerFooter>
            </DrawerContent>
        </Drawer>
    </CanInteract>
</template>
