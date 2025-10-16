<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { User } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    user: User;
}>();

const pfpInput = ref<InstanceType<typeof Input> | null>(null);

const updateProfilePictureForm = useForm({
    pfp: null,
});

const submitProfilePictureUpdateForm = () => {
    updateProfilePictureForm.post(route('settings.profile.update.picture'), {
        preserveScroll: true,
        onSuccess: () => {
            updateProfilePictureForm.reset();
            if (pfpInput.value?.input) {
                pfpInput.value.input.value = '';
            }
        },
    });
};
</script>

<template>
    <div class="flex flex-col space-y-6">
        <HeadingSmall title="Profile picture" description="Update or remove your profile picture" />

        <form @submit.prevent="submitProfilePictureUpdateForm" class="gap-4">
            <div class="form-box">
                <Label for="profile_picture">Profile picture</Label>
                <Input
                    class="cursor-pointer"
                    id="profile_picture"
                    type="file"
                    ref="pfpInput"
                    required
                    @change="updateProfilePictureForm.pfp = $event.target.files[0]"
                    accept="image/jpeg,image/png,image/webp"
                />
                <InputError :message="updateProfilePictureForm.errors.pfp" />
            </div>

            <div class="mt-3 flex items-center gap-4">
                <Button :disabled="updateProfilePictureForm.processing">Update</Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-show="updateProfilePictureForm.recentlySuccessful" class="text-sm text-neutral-600">Updated.</p>
                </Transition>

                <Link
                    :href="
                        route('settings.profile.update.picture', {
                            delete_pfp: true,
                        })
                    "
                    preserve-scroll
                    method="post"
                    as="button"
                    class="ml-auto text-sm text-red-700 dark:text-red-500"
                    v-if="user.avatar"
                >
                    Delete profile picture
                </Link>
            </div>
        </form>
    </div>
</template>
