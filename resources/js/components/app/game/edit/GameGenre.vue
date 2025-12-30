<script setup lang="ts">
import FormActionTap from '@/components/app/FormActionTap.vue';
import FormBox from '@/components/app/FormBox.vue';
import { Button } from '@/components/ui/button';
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
} from '@/components/ui/combobox';
import { ScrollArea } from '@/components/ui/scroll-area';
import { cn } from '@/lib/utils';
import { Genre } from '@/types';
import { Check, ChevronsUpDown, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    error?: string;
    gameGenre: Genre;
    genres: Genre[];
}>();

const model = defineModel<any>();

const selectedGenre = ref(props.gameGenre);

const ID = 'genre';

watch(selectedGenre, () => (model.value = selectedGenre.value.slug));
</script>

<template>
    <FormBox :label="ID" :id="ID">
        <Combobox :id="ID" by="name" v-model="selectedGenre">
            <ComboboxAnchor class="w-full">
                <ComboboxTrigger as-child>
                    <Button variant="outline" class="h-full w-full justify-between" :class="{ 'py-[14.75px]': !selectedGenre }">
                        <template v-if="selectedGenre">{{ selectedGenre.name }}</template>
                        <template v-else> No genre selected </template>

                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                </ComboboxTrigger>
            </ComboboxAnchor>

            <ComboboxList align="center" class="w-[var(--radix-popper-anchor-width)]">
                <div class="relative w-full max-w-sm items-center">
                    <ComboboxInput class="h-10 rounded-none border-0 border-b pl-2 focus-visible:ring-0" placeholder="Select genres..." />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                        <Search class="text-muted-foreground size-4" />
                    </span>
                </div>

                <ComboboxEmpty> No genres found. </ComboboxEmpty>

                <ComboboxGroup>
                    <ScrollArea>
                        <div class="max-h-[35vh]">
                            <ComboboxItem v-for="genre in genres" :key="genre.slug" :value="genre">
                                {{ genre.name }}

                                <ComboboxItemIndicator>
                                    <Check :class="cn('ml-auto h-4 w-4')" />
                                </ComboboxItemIndicator>
                            </ComboboxItem>
                        </div>
                    </ScrollArea>
                </ComboboxGroup>
            </ComboboxList>
        </Combobox>

        <FormActionTap v-if="gameGenre.slug && gameGenre.slug !== model" @click="selectedGenre = gameGenre" class="self-end">
            Reset to original genre
        </FormActionTap>
    </FormBox>
</template>
