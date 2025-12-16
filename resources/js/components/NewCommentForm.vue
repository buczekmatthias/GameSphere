<script setup lang="ts">
import FormBox from '@/components/FormBox.vue';
import FormButton from '@/components/FormButton.vue';
import InputError from '@/components/InputError.vue';
import LazyAvatar from '@/components/LazyAvatar.vue';
import CommentMedia from '@/components/Partials/Game/Create/Form/GameMedia.vue';
import { Textarea } from '@/components/ui/textarea';
import { useInitials } from '@/composables/useInitials';
import { constants } from '@/constants';
import { SharedData, User } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    discussionSlug: string;
}>();

const { getInitials } = useInitials();

const user = computed(() => usePage<SharedData>().props.auth.user as User);

const newCommentForm = useForm({
    discussion_slug: props.discussionSlug,
    content: '',
    media: [],
});

const isFormValid = () => {
    return newCommentForm.content.length > 0 && newCommentForm.media.length <= constants.value.form.files.media.max_files;
};

const submitForm = () => {
    newCommentForm
        .transform((data) => ({ ...data }))
        .post(route('comments.store'), {
            preserveScroll: true,
            onSuccess: () => newCommentForm.reset(),
        });
};
</script>

<template>
    <form @submit.prevent="submitForm" class="flex flex-col gap-2">
        <div class="flex items-center gap-2">
            <LazyAvatar :src="user.avatar" :alt="user.name" class="h-8 w-8" :fallback="getInitials(user.name)" />
            <span class="truncate font-medium">{{ user.name }}</span>
        </div>
        <FormBox>
            <Textarea id="description" required v-model="newCommentForm.content" class="h-48 resize-none" placeholder="Example content" />
            <InputError :message="newCommentForm.errors.content" />
        </FormBox>
        <CommentMedia :error="newCommentForm.errors.media" v-model="newCommentForm.media" />
        <FormButton label="Post comment" :is-processing="newCommentForm.processing" :disabled="!isFormValid()" />
    </form>
</template>
