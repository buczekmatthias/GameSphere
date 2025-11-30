<script setup lang="ts">
import FormBox from '@/components/FormBox.vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { MEDIA_ACCEPT_TYPE, TOTAL_AVAILABLE_MEDIA_SLOT } from '@/constants';
import { ref } from 'vue';

defineProps<{
    error?: string;
}>();

const model = defineModel<any>();

const inputKey = ref<number>(0);

const ID = 'media';

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
        <InputError :message="error" />
    </FormBox>
</template>
