<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';

defineProps<{
    role: string;
    appliedToBecomeGameCreator: boolean;
}>();
</script>

<template>
    <div class="flex flex-col space-y-6">
        <HeadingSmall title="Game creator" description="Become game creator and get access to add new games" />

        <div class="flex flex-col items-start space-y-0.5 rounded-md border border-emerald-400/20 bg-emerald-700/10 p-4" v-if="role === 'user'">
            <p class="font-medium">Join now!</p>
            <p class="text-sm">Become game creator and gain access to adding new games</p>

            <Button as-child class="mt-4 bg-emerald-900" v-if="!appliedToBecomeGameCreator">
                <Link :href="route('game.creator.join')" :only="['appliedToBecomeGameCreator']" preserve-scroll method="post">
                    Become game creator
                </Link>
            </Button>
            <p v-else class="mt-4 text-sm">You have applied to become game creator.</p>
        </div>
        <div class="bg-primary/10 border-primary/20 flex flex-col items-start space-y-0.5 rounded-md border p-4" v-else>
            <p class="font-medium">Success!</p>
            <p class="text-sm">You are a game creator!</p>

            <Button as-child class="mt-4">
                <Link :href="route('games.create')">Create game</Link>
            </Button>
        </div>
    </div>
</template>
