<script setup lang="ts">
import { Separator } from '@/components/ui/separator';
import { SearchData } from '@/pages/app/game/Index.vue';
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
        <div class="text-muted-foreground flex flex-wrap items-center gap-3">
            <p v-if="query.title">Contains "{{ query.title }}"</p>
            <Separator class="max-h-7" orientation="vertical" v-if="query.title && queriesCount > 1" />
            <p v-if="query.genre">Genre: "{{ query.genre }}"</p>
            <Separator class="max-h-7" orientation="vertical" v-if="(query.released_after || query.released_before) && query.genre" />
            <template v-if="query.released_after || query.released_before">
                <p v-if="query.released_after && !query.released_before">After {{ query.released_after }}</p>
                <p v-else-if="!query.released_after && query.released_before">Before {{ query.released_before }}</p>
                <p v-else>Between {{ query.released_after }} and {{ query.released_before }}</p>
            </template>
        </div>
    </div>
</template>
