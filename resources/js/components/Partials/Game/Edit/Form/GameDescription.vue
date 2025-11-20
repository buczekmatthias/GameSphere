<script setup lang="ts">
import FormBox from '@/components/FormBox.vue';
import InputError from '@/components/InputError.vue';
import InputInfo from '@/components/InputInfo.vue';
import { Textarea } from '@/components/ui/textarea';
import { DESCRIPTION_MIN_LENGTH } from '@/constants';
import { computed } from 'vue';

defineProps<{
    error?: string;
}>();

const model = defineModel<any>();

const charactersLeftTillRequired = computed(() => {
    const left = DESCRIPTION_MIN_LENGTH - model.value.length;

    return left > 0 ? left : 0;
});

const ID = 'description';
</script>

<template>
    <FormBox :label="ID" :id="ID">
        <Textarea :id="ID" required v-model="model" class="h-64 resize-none" placeholder="Example description" />
        <InputInfo
            :message="`At least ${DESCRIPTION_MIN_LENGTH} characters long. ${charactersLeftTillRequired === 0 ? '' : charactersLeftTillRequired + ' characters to go'}`"
        />
        <InputError :message="error" />
    </FormBox>
</template>
