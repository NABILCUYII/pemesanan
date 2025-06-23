<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <Head title="Welcome" />
    <div class="container relative min-h-screen flex-col items-center justify-center grid lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col bg-muted p-10 text-white lg:flex dark:border-r">
            <div class="absolute inset-0 bg-zinc-900" />
            <div class="relative z-20 flex items-center text-lg font-medium">
                <img src="/puskesmas-logo.png" alt="Puskesmas Logo" class="size-9 object-contain" />
            </div>
            <div class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <p class="text-lg">
                        &ldquo;This library has saved me countless hours of work and helped me deliver stunning designs to my clients faster than ever before.&rdquo;
                    </p>
                    <footer class="text-sm">Sofia Davis</footer>
                </blockquote>
            </div>
        </div>
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-2xl font-semibold tracking-tight">Create an account</h1>
                    <p class="text-sm text-muted-foreground">Enter your email below to create your account</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>

