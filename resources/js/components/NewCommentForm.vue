<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { useInitials } from '@/composables/useInitials';
import { useCurrentUser } from '@/composables/useUser';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    discussionSlug: string;
}>();

const { getInitials } = useInitials();

const user = useCurrentUser();

const newCommentForm = useForm({
    discussion_slug: props.discussionSlug,
    content: '',
    media: [],
});

const fileInput = ref<null | HTMLInputElement>(null);

const isFormValid = () => {
    return newCommentForm.content.length > 0 && newCommentForm.media.length < 5;
};

const submitForm = () => {
    newCommentForm.post(route('comments.store'), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => newCommentForm.reset(),
    });
};
</script>

<template>
    <form @submit.prevent="submitForm" class="flex flex-col gap-2">
        <div class="flex items-center gap-2">
            <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
                <AvatarImage :src="user.avatar!" :alt="user.name" />
                <AvatarFallback class="rounded-lg text-black dark:text-white">
                    {{ getInitials(user.name) }}
                </AvatarFallback>
            </Avatar>
            <span class="truncate font-medium">{{ user.name }}</span>
        </div>
        <div class="form-box">
            <Textarea id="description" required v-model="newCommentForm.content" class="h-48 resize-none" placeholder="Example content" />
            <InputError :message="newCommentForm.errors.content" />
        </div>
        <div class="form-box">
            <Input
                id="media"
                type="file"
                multiple
                @change="newCommentForm.media = $event.target.files"
                accept="image/jpeg,image/png,image/webp,video/mp4"
                ref="fileInput"
            />
            <InputError :message="newCommentForm.errors.media" />
        </div>
        <Button type="submit" class="w-full" :disabled="newCommentForm.processing || !isFormValid()">
            <LoaderCircle v-if="newCommentForm.processing" class="h-4 w-4 animate-spin" />
            Post comment
        </Button>
    </form>
</template>
