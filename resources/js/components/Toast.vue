<template>
    <div class="fixed top-4 right-4 z-50 space-y-2">
        <TransitionGroup name="toast">
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-lg border"
                :class="{
                    'border-green-200 bg-green-50': toast.type === 'success',
                    'border-red-200 bg-red-50': toast.type === 'error',
                    'border-yellow-200 bg-yellow-50': toast.type === 'warning',
                    'border-blue-200 bg-blue-50': toast.type === 'info'
                }"
            >
                <div class="flex items-center flex-1">
                    <div class="flex-shrink-0">
                        <CheckCircle v-if="toast.type === 'success'" class="w-5 h-5 text-green-600" />
                        <XCircle v-else-if="toast.type === 'error'" class="w-5 h-5 text-red-600" />
                        <AlertTriangle v-else-if="toast.type === 'warning'" class="w-5 h-5 text-yellow-600" />
                        <Info v-else class="w-5 h-5 text-blue-600" />
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium" :class="{
                            'text-green-800': toast.type === 'success',
                            'text-red-800': toast.type === 'error',
                            'text-yellow-800': toast.type === 'warning',
                            'text-blue-800': toast.type === 'info'
                        }">
                            {{ toast.title }}
                        </p>
                        <p class="text-sm" :class="{
                            'text-green-700': toast.type === 'success',
                            'text-red-700': toast.type === 'error',
                            'text-yellow-700': toast.type === 'warning',
                            'text-blue-700': toast.type === 'info'
                        }">
                            {{ toast.message }}
                        </p>
                    </div>
                </div>
                <button
                    @click="removeToast(toast.id)"
                    class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600"
                >
                    <X class="w-4 h-4" />
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup lang="ts">
import { CheckCircle, XCircle, AlertTriangle, Info, X } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const { toasts, removeToast } = useToast();
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

.toast-move {
    transition: transform 0.3s ease;
}
</style> 