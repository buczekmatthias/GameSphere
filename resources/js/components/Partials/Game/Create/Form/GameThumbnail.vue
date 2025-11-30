<script setup lang="ts">
import FormBox from '@/components/FormBox.vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { THUMBNAIL_ACCEPT_TYPE } from '@/constants';
import { ref } from 'vue';

defineProps<{
    error?: string;
}>();

const model = defineModel<any>();

const inputKey = ref<number>(0);

const ID = 'thumbnail';
</script>

<template>
    <FormBox :label="ID" :id="ID">
        <Input :id="ID" type="file" @change="model = $event.target.files[0]" :accept="THUMBNAIL_ACCEPT_TYPE" :key="inputKey" />
        <InputError :message="error" />
    </FormBox>
</template>
