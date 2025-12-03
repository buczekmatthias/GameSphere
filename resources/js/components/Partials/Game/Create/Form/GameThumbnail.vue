<script setup lang="ts">
import FormBox from '@/components/FormBox.vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { constants } from '@/constants';
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
        <Input :id="ID" type="file" @change="model = $event.target.files[0]" :accept="constants.form.files.thumbnail.accept_type" :key="inputKey" />
        <InputError :message="error" />
    </FormBox>
</template>
