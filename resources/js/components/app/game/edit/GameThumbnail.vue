<script setup lang="ts">
import FormActionTap from '@/components/app/FormActionTap.vue';
import FormBox from '@/components/app/FormBox.vue';
import Preview from '@/components/app/Preview.vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { constants } from '@/constants';
import { ref } from 'vue';

defineProps<{
    error?: string;
    thumbnail: string;
}>();

const model = defineModel<any>();

const inputKey = ref<number>(0);

const discardNewThumbnail = (): void => {
    inputKey.value++;
    model.value = null;
};

const ID = 'thumbnail';
</script>

<template>
    <FormBox :label="ID" :id="ID">
        <Input :id="ID" type="file" @change="model = $event.target.files[0]" :accept="constants.form.files.thumbnail.accept_type" :key="inputKey" />
        <div class="flex justify-between gap-2">
            <Preview :item="{ type: 'image', path: thumbnail, filename: 'Thumbnail' }">
                <FormActionTap>Show thumbnail</FormActionTap>
            </Preview>
            <FormActionTap class="self-end" v-if="model !== null" @click="discardNewThumbnail">Discard new thumbnail</FormActionTap>
        </div>
        <InputError :message="error" />
    </FormBox>
</template>
