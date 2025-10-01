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
import { LoaderCircle, Plus } from 'lucide-vue-next';
import { ref } from 'vue';

// Reuse `form` section
const [UseTemplate, GridForm] = createReusableTemplate();
const isDesktop = useMediaQuery('(min-width: 768px)');

const isOpen = ref(false);

const props = defineProps<{
    slug: string;
    type: string;
}>();

const newDiscussionForm = useForm({
    title: '',
    slug: props.slug,
    type: props.type,
});

const submitForm = () => {
    if (newDiscussionForm.title.length > 0) {
        newDiscussionForm.post(route('discussions.store'), {
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
                <Input id="title" v-model="newDiscussionForm.title" />
                <InputError :message="newDiscussionForm.errors.title" />
            </div>
            <Button type="submit" class="sticky bottom-0" :disabled="newDiscussionForm.processing">
                <LoaderCircle v-if="newDiscussionForm.processing" class="h-4 w-4 animate-spin" />
                Start discussion
            </Button>
        </form>
    </UseTemplate>

    <Dialog v-if="isDesktop" v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="outline" class="mb-4 w-full py-5"> <Plus class="mt-0.5" /> Create discussion </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Create discussion</DialogTitle>
                <DialogDescription> Start new discussion </DialogDescription>
            </DialogHeader>
            <GridForm />
        </DialogContent>
    </Dialog>

    <Drawer v-else v-model:open="isOpen">
        <DrawerTrigger as-child>
            <Button variant="outline" class="mb-4 w-full py-5"> <Plus class="mt-0.5" /> Create discussion </Button>
        </DrawerTrigger>
        <DrawerContent>
            <DrawerHeader class="text-left">
                <DrawerTitle>Create discussion</DrawerTitle>
                <DrawerDescription> Start new discussion </DrawerDescription>
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
