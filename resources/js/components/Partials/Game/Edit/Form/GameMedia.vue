<script setup lang="ts">
import FormActionTap from '@/components/FormActionTap.vue';
import FormBox from '@/components/FormBox.vue';
import InputError from '@/components/InputError.vue';
import InputInfo from '@/components/InputInfo.vue';
import Modal from '@/components/Modal.vue';
import Preview from '@/components/Preview.vue';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { MEDIA_ACCEPT_TYPE, TOTAL_AVAILABLE_MEDIA_SLOT } from '@/constants';
import { Media } from '@/types';
import { computed, ref } from 'vue';

const props = defineProps<{
    error?: string;
    gameMedia: Media[];
}>();

const media = defineModel<any>('media');
const media_to_delete = defineModel<any>('media_to_delete');

const inputKey = ref<number>(0);

const ID = 'media';

const fileSlotsLeft = computed(() => TOTAL_AVAILABLE_MEDIA_SLOT - props.gameMedia.length);
const isDisabled = computed(() => props.gameMedia.length > TOTAL_AVAILABLE_MEDIA_SLOT);

const toggleItem = (filename: string) => {
    if (media_to_delete.value.includes(filename)) {
        media_to_delete.value = media_to_delete.value.filter((i: string) => i !== filename);
    } else {
        media_to_delete.value.push(filename);
    }
};

const discardNewFiles = (): void => {
    inputKey.value++;
    media.value = [];
};

const handleFileChange = (event: any): void => {
    const files = Array.from(event.target.files);

    if (files.length + props.gameMedia.length > TOTAL_AVAILABLE_MEDIA_SLOT) {
        alert(
            `You can use up to ${TOTAL_AVAILABLE_MEDIA_SLOT} files. Only ${fileSlotsLeft.value} files can be uploaded until you remove any of existing ones.`,
        );
        event.target.value = '';

        return;
    }

    media.value = files;
};
</script>

<template>
    <FormBox :label="ID" :id="ID">
        <Input id="media" type="file" multiple @change="handleFileChange" :accept="MEDIA_ACCEPT_TYPE" :disabled="isDisabled" :key="inputKey" />
        <div class="flex justify-between gap-2">
            <Modal v-if="gameMedia.length > 0">
                <template #trigger>
                    <FormActionTap class="self-end">Show media</FormActionTap>
                </template>
                <template #title>
                    <p>Game media</p>
                </template>
                <div class="flex max-h-[75vh] flex-col gap-6 overflow-y-auto">
                    <div v-for="item in gameMedia" :key="item.path" class="flex flex-col gap-2">
                        <Preview :type="item.type" :media="item.path" />
                        <div class="flex items-center gap-2">
                            <Switch @update:model-value="toggleItem(item.filename)" :model-value="media_to_delete.includes(item.filename)" />
                            <p class="text-sm">Remove this item</p>
                        </div>
                    </div>
                </div>
            </Modal>
            <FormActionTap v-if="media.length > 0" @click="discardNewFiles" class="self-end">Discard new files</FormActionTap>
        </div>
        <InputInfo :message="`${gameMedia.length} of ${TOTAL_AVAILABLE_MEDIA_SLOT} files used. ${fileSlotsLeft} more files can be uploaded`" />
        <InputInfo v-if="media_to_delete.length > 0" :message="`${media_to_delete.length} selected to delete.`" />
        <InputError :message="error" />
    </FormBox>
</template>
