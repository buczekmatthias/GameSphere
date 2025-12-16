<script setup lang="ts">
import { SearchData } from '@/pages/app/game/Index.vue';
import { Diamond } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    total: number;
    query: SearchData;
}>();

const queriesCount = computed((): number => Object.keys(props.query).filter((key) => key !== 'per_page').length);
</script>

<template>
    <div class="col-span-full flex flex-col gap-2">
        <p class="text-2xl">{{ total }} {{ total === 1 ? 'result' : 'results' }}</p>
        <div class="text-muted-foreground flex flex-wrap items-center gap-2.5">
            <p v-if="query.title">Contains "{{ query.title }}"</p>
            <Diamond class="mt-0.5 size-2" v-if="query.title && queriesCount > 1" />
            <p v-if="query.genre">Genre: "{{ query.genre }}"</p>
            <Diamond class="mt-0.5 size-2" v-if="(query.released_after || query.released_before) && query.genre" />
            <template v-if="query.released_after || query.released_before">
                <p v-if="query.released_after && !query.released_before">After {{ query.released_after }}</p>
                <p v-else-if="!query.released_after && query.released_before">Before {{ query.released_before }}</p>
                <p v-else>Between {{ query.released_after }} and {{ query.released_before }}</p>
            </template>
        </div>
    </div>
</template>
