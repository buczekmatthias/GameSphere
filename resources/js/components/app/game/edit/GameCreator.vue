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
import UserInfo from '@/components/UserInfo.vue';
import { cn } from '@/lib/utils';
import { User } from '@/types';
import { Check, ChevronsUpDown, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    error?: string;
    gameCreator: User;
    users: User[];
}>();

const model = defineModel<any>();

const ID = 'creator';

const selectedCreator = ref(props.gameCreator);

watch(selectedCreator, () => (model.value = selectedCreator.value.username));
</script>

<template>
    <FormBox :label="ID" :id="ID">
        <Combobox :id="ID" by="username" v-model="selectedCreator">
            <ComboboxAnchor class="w-full">
                <ComboboxTrigger as-child>
                    <Button variant="outline" class="h-full w-full justify-between" :class="{ 'py-[14.75px]': !gameCreator.username }">
                        <UserInfo :user="selectedCreator" show-username v-if="selectedCreator.username" />
                        <template v-else> No creator selected </template>

                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                </ComboboxTrigger>
            </ComboboxAnchor>

            <ComboboxList align="center" class="w-[var(--radix-popper-anchor-width)]">
                <div class="relative w-full max-w-sm items-center">
                    <ComboboxInput class="h-10 rounded-none border-0 border-b pl-2 focus-visible:ring-0" placeholder="Select creator..." />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                        <Search class="text-muted-foreground size-4" />
                    </span>
                </div>

                <ComboboxEmpty> No users found. </ComboboxEmpty>

                <ComboboxGroup>
                    <ScrollArea>
                        <div class="max-h-[35vh]">
                            <ComboboxItem v-for="user in users" :key="user.username" :value="user">
                                <UserInfo :user show-username />

                                <ComboboxItemIndicator>
                                    <Check :class="cn('ml-auto h-4 w-4')" />
                                </ComboboxItemIndicator>
                            </ComboboxItem>
                        </div>
                    </ScrollArea>
                </ComboboxGroup>
            </ComboboxList>
        </Combobox>

        <FormActionTap
            class="self-end"
            v-if="gameCreator.username && gameCreator.username !== selectedCreator.username"
            @click="selectedCreator = gameCreator"
        >
            Reset to original creator
        </FormActionTap>
    </FormBox>
</template>
