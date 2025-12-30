<script setup lang="ts">
import FormBox from '@/components/app/FormBox.vue';
import DatePicker from '@/components/app/game/DatePicker.vue';
import InputError from '@/components/InputError.vue';

defineProps<{
    error?: string;
}>();

const model = defineModel<any>();

const ID = 'released_at';
</script>

<template>
    <FormBox label="released at" :id="ID">
        <DatePicker :id="ID" v-model="model" trigger-button-class="w-full" />
        <InputError :message="error" />
    </FormBox>
</template>
