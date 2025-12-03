<script setup lang="ts">
import FormActionTap from '@/components/FormActionTap.vue';
import FormBox from '@/components/FormBox.vue';
import InputError from '@/components/InputError.vue';
import InputInfo from '@/components/InputInfo.vue';
import { Input } from '@/components/ui/input';
import { MEDIA_ACCEPT_TYPE, TOTAL_AVAILABLE_MEDIA_SLOT } from '@/constants';
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
    const files = Array.from(event.target.files);

    if (files.length > TOTAL_AVAILABLE_MEDIA_SLOT) {
        alert(`You can use up to ${TOTAL_AVAILABLE_MEDIA_SLOT} files.`);
        event.target.value = '';

        return;
    }

    model.value = files;
};
</script>

<template>
    <FormBox :label="ID" :id="ID">
        <Input id="media" type="file" multiple @change="handleFileChange" :accept="MEDIA_ACCEPT_TYPE" :key="inputKey" />
        <div class="flex items-center justify-between gap-2">
            <InputInfo :message="`Up to ${TOTAL_AVAILABLE_MEDIA_SLOT} files can be uploaded`" />
            <FormActionTap v-if="model.length > 0" @click="discardNewFiles" class="self-end">Discard files</FormActionTap>
        </div>
        <InputError :message="error" />
    </FormBox>
</template>
