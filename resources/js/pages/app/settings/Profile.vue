<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useCurrentUser } from '@/composables/useUser';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: '',
    },
    {
        title: 'Profile settings',
        href: route('settings.profile.edit'),
    },
];

const user = useCurrentUser();

const updateProfileForm = useForm({
    name: user.value.name,
    email: user.value.email,
});

const submitProfileUpdateForm = () => {
    updateProfileForm.patch(route('settings.profile.update'), {
        preserveScroll: true,
    });
};

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
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form @submit.prevent="submitProfileUpdateForm" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            v-model="updateProfileForm.name"
                            required
                            autocomplete="name"
                            placeholder="Full name"
                        />
                        <InputError class="mt-2" :message="updateProfileForm.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="updateProfileForm.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="updateProfileForm.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="text-muted-foreground -mt-4 text-sm">
                            Your email address is unverified.
                            <Link
                                :href="route('security.verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="updateProfileForm.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="updateProfileForm.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

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
                    <Link
                        :href="
                            route('settings.profile.update.picture', {
                                delete_pfp: true,
                            })
                        "
                        preserve-scroll
                        method="post"
                        as="button"
                        class="text-sm text-red-700 dark:text-red-500"
                        v-if="user.avatar"
                    >
                        Delete profile picture
                    </Link>

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
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
