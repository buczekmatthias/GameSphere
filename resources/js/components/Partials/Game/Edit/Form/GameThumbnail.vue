<script setup lang="ts">
import FormActionTap from '@/components/FormActionTap.vue';
import FormBox from '@/components/FormBox.vue';
import InputError from '@/components/InputError.vue';
import Modal from '@/components/Modal.vue';
import Preview from '@/components/Preview.vue';
import { Input } from '@/components/ui/input';
import { THUMBNAIL_ACCEPT_TYPE } from '@/constants';
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
        <Input :id="ID" type="file" @change="model = $event.target.files[0]" :accept="THUMBNAIL_ACCEPT_TYPE" :key="inputKey" />
        <div class="flex justify-between gap-2">
            <Modal>
                <template #trigger>
                    <FormActionTap>Show thumbnail</FormActionTap>
                </template>
                <template #title>
                    <p>Thumbnail</p>
                </template>
                <Preview type="image" :media="thumbnail" />
            </Modal>
            <FormActionTap v-if="model !== null" @click="discardNewThumbnail">Discard new thumbnail</FormActionTap>
        </div>
        <InputError :message="error" />
    </FormBox>
</template>
