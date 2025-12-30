<script setup lang="ts">
import FormBox from '@/components/app/FormBox.vue';
import DatePicker from '@/components/app/game/DatePicker.vue';
import InputError from '@/components/InputError.vue';
import { getLocalTimeZone, today } from '@internationalized/date';

const props = defineProps<{
    error?: string;
    releasedAt: string;
}>();

const model = defineModel<any>();

const ID = 'released_at';

const calendarPreset = [
    [today(getLocalTimeZone()), 'Today'],
    [props.releasedAt, 'Old value'],
];
</script>

<template>
    <FormBox label="released at" :id="ID">
        <DatePicker :id="ID" v-model="model" :preset="calendarPreset" trigger-button-class="w-full" />
        <InputError :message="error" />
    </FormBox>
</template>
