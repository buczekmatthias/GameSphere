<script setup lang="ts">
import FormBox from '@/components/FormBox.vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';

defineProps<{
    error?: string;
}>();

const model = defineModel<any>();

const ID = 'title';
</script>

<template>
    <FormBox :label="ID" :id="ID">
        <Input :id="ID" type="text" required autofocus v-model="model" placeholder="Example title" />
        <InputError :message="error" />
    </FormBox>
</template>
