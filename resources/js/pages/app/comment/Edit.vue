<script setup lang="ts">
import FormButton from '@/components/FormButton.vue';
import GoBackLink from '@/components/GoBackLink.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import MainContainer from '@/components/MainContainer.vue';
import Preview from '@/components/Preview.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { useZiggy } from '@/composables/useZiggy';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, DiscussionComment, Ziggy } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ImageOff, PlayCircle } from 'lucide-vue-next';
import { computed } from 'vue';

interface ZiggyWithBackLink extends Ziggy {
    query: {
        back_to_discussion?: string;
    };
}

const props = defineProps<{
    comment: DiscussionComment;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Discussions',
        href: route('discussions.index'),
    },
    {
        title: props.comment.discussion.title,
        href: route('discussions.show', { discussion: props.comment.discussion.slug }),
    },
    {
        title: 'Comments',
        href: '',
    },
    {
        title: props.comment.slug,
        href: route('comments.show', { comment: props.comment.slug }),
    },
];

const ziggy: ZiggyWithBackLink = useZiggy().value;

const updateCommentForm = useForm({
    content: props.comment.content,
    media_to_delete: [] as string[],
});

const hasFormChanged = computed(() => updateCommentForm.content !== props.comment.content || updateCommentForm.media_to_delete.length > 0);
const isFormValid = computed(() => updateCommentForm.content.length > 0);

const submitForm = () => {
    if (isFormValid.value) {
        updateCommentForm.patch(route('comments.update', { comment: props.comment.slug, ...ziggy.query }), {
            preserveState: false,
            preserveScroll: true,
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

const getBackLinkHref = (): string =>
    Object.keys(ziggy.query).includes('back_to_discussion')
        ? route('discussions.show', { discussion: props.comment.discussion.slug })
        : route('comments.show', { comment: props.comment.slug });
</script>

<template>
    <Head :title="`Comment: #${comment.slug}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-5xl flex-col gap-6">
            <GoBackLink :href="getBackLinkHref()" />
            <div class="grid gap-2">
                <Label html-for="content">Content</Label>
                <Textarea id="content" v-model="updateCommentForm.content" :default-value="comment.content" class="h-36 resize-none" />
                <InputError :message="updateCommentForm.errors.content" />
            </div>
            <template v-if="comment.media.length > 0">
                <div class="flex flex-col justify-between gap-4">
                    <HeadingSmall title="Media to delete" :description="`${updateCommentForm.media_to_delete.length} selected`" />
                    <div v-for="item in comment.media" :key="item.path" class="flex items-center justify-between gap-3">
                        <Preview :item>
                            <template v-if="item.type === 'image'">
                                <Avatar class="size-16 cursor-pointer rounded-lg">
                                    <AvatarImage :src="item.path" :alt="item.filename" class="object-cover" />
                                    <AvatarFallback class="rounded-lg text-black dark:text-white">
                                        <ImageOff class="size-8" />
                                    </AvatarFallback>
                                </Avatar>
                            </template>
                            <template v-if="item.type === 'video'">
                                <div class="flex size-16 cursor-pointer items-center justify-center rounded-md bg-black">
                                    <PlayCircle class="size-8" />
                                </div>
                            </template>
                        </Preview>
                        <div class="flex items-center gap-2">
                            <p class="text-sm">Remove this item</p>
                            <Switch
                                @update:model-value="toggleItem(item.filename)"
                                :model-value="updateCommentForm.media_to_delete.includes(item.filename)"
                            />
                        </div>
                    </div>
                </div>
            </template>
            <FormButton
                label="Update comment"
                :is-processing="updateCommentForm.processing"
                :disabled="!isFormValid || !hasFormChanged"
                @click="submitForm"
            />
        </MainContainer>
    </AppLayout>
</template>
