<script setup lang="ts">
import FormActionTap from '@/components/FormActionTap.vue';
import FormBox from '@/components/FormBox.vue';
import InputError from '@/components/InputError.vue';
import InputInfo from '@/components/InputInfo.vue';
import { Input } from '@/components/ui/input';
import { constants } from '@/constants';
import { ref } from 'vue';

defineProps<{
    error?: string;
}>();

const model = defineModel<any>();

const inputKey = ref<number>(0);

const ID = 'media';

const discardNewFiles = (): void => {
    inputKey.value++;
    model.value = [];
};

const handleFileChange = (event: any): void => {
    const files: File[] = Array.from(event.target.files);

    if (files.length > constants.value.form.files.media.max_files) {
        alert(`You can use up to ${constants.value.form.files.media.max_files} files.`);
        event.target.value = '';

        return;
    }

    if (files.some((file) => file.size > constants.value.form.files.media.max_size)) {
        alert(`At least one file exceeds ${constants.value.form.files.media.max_size_display}.`);
        event.target.value = '';

        return;
    }

    model.value = files;
};
</script>

<template>
    <FormBox :label="ID" :id="ID">
        <Input id="media" type="file" multiple @change="handleFileChange" :accept="constants.form.files.media.accept_type" :key="inputKey" />
        <div class="flex items-center justify-between gap-2">
            <InputInfo
                :message="`Up to ${constants.form.files.media.max_files} files can be uploaded. Max file size: ${constants.form.files.media.max_size_display}`"
            />
            <FormActionTap v-if="model.length > 0" @click="discardNewFiles" class="self-end">Discard files</FormActionTap>
        </div>
        <InputError :message="error" />
    </FormBox>
</template>
