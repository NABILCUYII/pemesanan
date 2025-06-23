<script setup lang="ts">
import { Link, usePage, Head } from '@inertiajs/vue3';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <Head title="Welcome" />
    <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
        <div class="flex items-center justify-center bg-sidebar p-4 lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <div class="flex flex-col space-y-2 text-center">
                    <div class="flex items-center justify-center">
                        <img src="/puskesmas-logo.png" alt="Puskesmas Logo" class="mr-2 size-8 object-contain" />
                    </div>
                    <h1 class="text-2xl font-semibold tracking-tight">Welcome back</h1>
                    <p class="text-sm text-muted-foreground">Enter your email to sign in to your account</p>
                </div>
                <slot />
            </div>
        </div>
        <div class="hidden bg-muted lg:block">
            <slot name="aside" />
        </div>
    </div>
</template>

