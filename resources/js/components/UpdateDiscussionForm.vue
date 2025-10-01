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
import { useForm } from '@inertiajs/vue3';
import { createReusableTemplate, useMediaQuery } from '@vueuse/core';
import { LoaderCircle, Pen } from 'lucide-vue-next';
import { ref } from 'vue';

// Reuse `form` section
const [UseTemplate, GridForm] = createReusableTemplate();
const isDesktop = useMediaQuery('(min-width: 768px)');

const isOpen = ref(false);

const props = defineProps<{
    oldTitle: string;
    slug: string;
}>();

const updateDiscussionForm = useForm({
    title: props.oldTitle,
});

const submitForm = () => {
    if (updateDiscussionForm.title.length > 0) {
        updateDiscussionForm.patch(route('discussions.update', { discussion: props.slug }), {
            onSuccess: () => (isOpen.value = false),
        });
    }
};
</script>

<template>
    <UseTemplate>
        <form class="grid items-start gap-4 px-4" @submit.prevent="submitForm">
            <div class="grid gap-2">
                <Label html-for="title">Title</Label>
                <Input id="title" v-model="updateDiscussionForm.title" :default-value="oldTitle" />
                <InputError :message="updateDiscussionForm.errors.title" />
            </div>
            <Button type="submit" class="sticky bottom-0" :disabled="updateDiscussionForm.processing">
                <LoaderCircle v-if="updateDiscussionForm.processing" class="h-4 w-4 animate-spin" />
                Update discussion
            </Button>
        </form>
    </UseTemplate>

    <Dialog v-if="isDesktop" v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="outline" class="w-full"> <Pen class="size-4" /> Update</Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Update discussion</DialogTitle>
                <DialogDescription> Give discussion new title </DialogDescription>
            </DialogHeader>
            <GridForm />
        </DialogContent>
    </Dialog>

    <Drawer v-else v-model:open="isOpen">
        <DrawerTrigger as-child>
            <Button variant="outline" class="w-full"> <Pen class="size-4" /> Update</Button>
        </DrawerTrigger>
        <DrawerContent>
            <DrawerHeader class="text-left">
                <DrawerTitle>Update discussion</DrawerTitle>
                <DrawerDescription> Give discussion new title </DrawerDescription>
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
