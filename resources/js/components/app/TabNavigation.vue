<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { capitalize, nextTick, onMounted, ref } from 'vue';

export interface TabNavigationItem {
    name: string;
    route: string;
}

defineProps<{
    tabs: TabNavigationItem[];
    activeTab: string;
    reloadOnly: string[];
}>();

const scrollContainer = ref<HTMLDivElement | null>(null);

const scrollToActiveLink = () => {
    nextTick(() => {
        if (!scrollContainer.value) return;

        const container = scrollContainer.value;
        const activeLink = container.querySelector(`.active`) as HTMLElement;

        if (activeLink) {
            const containerWidth = container.clientWidth;
            const linkLeft = activeLink.offsetLeft;

            const scrollPosition = linkLeft - containerWidth / 5;

            container.scrollTo({
                left: scrollPosition,
                behavior: 'smooth',
            });
        }
    });
};

onMounted(() => {
    scrollToActiveLink();
});
</script>

<template>
    <div class="flex gap-6 overflow-x-auto" ref="scrollContainer">
        <Link
            as="button"
            v-for="tab in tabs"
            :key="tab.name"
            :href="tab.route"
            :only="reloadOnly"
            class="border-b-4 py-2 whitespace-nowrap"
            :class="
                activeTab === tab.name
                    ? 'border-b-primary active pointer-events-none dark:text-slate-50'
                    : 'text-muted-foreground border-transparent duration-150 hover:border-b-slate-200'
            "
        >
            {{ capitalize(tab.name.replace('_', ' ')) }}
        </Link>
    </div>
</template>
