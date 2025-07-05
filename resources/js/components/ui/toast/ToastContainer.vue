<script setup lang="ts">
import { ref, provide } from 'vue';
import Toast from './Toast.vue';

interface ToastNotification {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  title: string;
  message?: string;
  duration?: number;
}

const toasts = ref<ToastNotification[]>([]);

const addToast = (toast: Omit<ToastNotification, 'id'>) => {
  const id = Date.now().toString() + Math.random().toString(36).substr(2, 9);
  toasts.value.push({ ...toast, id });
};

const removeToast = (id: string) => {
  const index = toasts.value.findIndex(toast => toast.id === id);
  if (index > -1) {
    toasts.value.splice(index, 1);
  }
};

// Provide the addToast function globally
provide('addToast', addToast);

// Expose methods for external use
defineExpose({
  addToast,
  removeToast
});
</script>

<template>
  <div class="fixed top-4 right-4 z-50 space-y-4">
    <Toast
      v-for="toast in toasts"
      :key="toast.id"
      :id="toast.id"
      :type="toast.type"
      :title="toast.title"
      :message="toast.message"
      :duration="toast.duration"
      @close="removeToast"
    />
  </div>
</template> 