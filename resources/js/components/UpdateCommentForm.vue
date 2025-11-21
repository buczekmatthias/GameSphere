<script lang="ts" setup>
import FormButton from '@/components/FormButton.vue';
import InputError from '@/components/InputError.vue';
import Modal from '@/components/Modal.vue';
import Preview from '@/components/Preview.vue';
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
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Media } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { createReusableTemplate, useMediaQuery } from '@vueuse/core';
import { Pen } from 'lucide-vue-next';
import { ref } from 'vue';

// Reuse `form` section
const [UseTemplate, GridForm] = createReusableTemplate();
const isDesktop = useMediaQuery('(min-width: 768px)');

const isOpen = ref(false);

const props = defineProps<{
    oldContent: string;
    media: Media[];
    slug: string;
}>();

const updateCommentForm = useForm({
    content: props.oldContent,
    media_to_delete: [] as string[],
});

const submitForm = () => {
    if (updateCommentForm.content.length > 0) {
        updateCommentForm.patch(route('comments.update', { comment: props.slug }), {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => (isOpen.value = false),
        });
    }
};

const toggleItem = (filename: string) => {
    if (updateCommentForm.media_to_delete.includes(filename)) {
        updateCommentForm.media_to_delete = updateCommentForm.media_to_delete.filter((i) => i !== filename);
    } else {
        updateCommentForm.media_to_delete.push(filename);
    }
};
</script>

<template>
    <UseTemplate>
        <form class="grid items-start gap-4 px-4" @submit.prevent="submitForm">
            <div class="grid gap-2">
                <Label html-for="content">Content</Label>
                <Textarea id="content" v-model="updateCommentForm.content" :default-value="oldContent" class="h-36 resize-none" />
                <InputError :message="updateCommentForm.errors.content" />
            </div>
            <template v-if="media.length > 0">
                <div class="flex justify-between gap-2">
                    <p class="">{{ updateCommentForm.media_to_delete.length }} selected</p>
                    <Modal>
                        <template #trigger>
                            <p class="cursor-pointer text-sm text-sky-600 dark:text-sky-400">Show media</p>
                        </template>
                        <template #title>
                            <p>Comment media</p>
                        </template>
                        <template #description>Media of "{{ slug }}"</template>
                        <div class="flex max-h-[75vh] flex-col gap-6 overflow-y-auto">
                            <div v-for="item in media" :key="item.path" class="flex flex-col gap-2">
                                <Preview :type="item.type" :media="item.path" />
                                <div class="flex items-center gap-2">
                                    <Switch
                                        @update:model-value="toggleItem(item.filename)"
                                        :model-value="updateCommentForm.media_to_delete.includes(item.filename)"
                                    />
                                    <p class="text-sm">Remove this item</p>
                                </div>
                            </div>
                        </div>
                    </Modal>
                </div>
            </template>
            <FormButton label="Update comment" :is-processing="updateCommentForm.processing" />
        </form>
    </UseTemplate>

    <Dialog v-if="isDesktop" v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="secondary">
                <Pen class="size-4" />
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Update comment</DialogTitle>
                <DialogDescription> Give comment new content & remove unneeded files </DialogDescription>
            </DialogHeader>
            <GridForm />
        </DialogContent>
    </Dialog>

    <Drawer v-else v-model:open="isOpen">
        <DrawerTrigger as-child>
            <Button variant="secondary">
                <Pen class="size-4" />
            </Button>
        </DrawerTrigger>
        <DrawerContent>
            <DrawerHeader class="text-left">
                <DrawerTitle>Update comment</DrawerTitle>
                <DrawerDescription> Give comment new content & remove unneeded files </DrawerDescription>
            </DrawerHeader>
            <GridForm />
            <DrawerFooter class="pt-2">
                <DrawerClose as-child>
                    <Button variant="outline">Cancel</Button>
                </DrawerClose>
            </DrawerFooter>
        </DrawerContent>
    </Drawer>
</template>
