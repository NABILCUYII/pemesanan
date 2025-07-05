<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { X, CheckCircle, AlertCircle, Info, XCircle } from 'lucide-vue-next';

interface ToastProps {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  title: string;
  message?: string;
  duration?: number;
  onClose?: (id: string) => void;
}

const props = withDefaults(defineProps<ToastProps>(), {
  duration: 5000,
  message: ''
});

const emit = defineEmits<{
  close: [id: string];
}>();

const isVisible = ref(false);
const timeoutId = ref<number | null>(null);

const getIcon = () => {
  switch (props.type) {
    case 'success':
      return CheckCircle;
    case 'error':
      return XCircle;
    case 'warning':
      return AlertCircle;
    case 'info':
      return Info;
    default:
      return Info;
  }
};

const getIconColor = () => {
  switch (props.type) {
    case 'success':
      return 'text-green-500';
    case 'error':
      return 'text-red-500';
    case 'warning':
      return 'text-yellow-500';
    case 'info':
      return 'text-blue-500';
    default:
      return 'text-blue-500';
  }
};

const getBgColor = () => {
  switch (props.type) {
    case 'success':
      return 'bg-green-50 border-green-200';
    case 'error':
      return 'bg-red-50 border-red-200';
    case 'warning':
      return 'bg-yellow-50 border-yellow-200';
    case 'info':
      return 'bg-blue-50 border-blue-200';
    default:
      return 'bg-blue-50 border-blue-200';
  }
};

const close = () => {
  isVisible.value = false;
  if (timeoutId.value) {
    clearTimeout(timeoutId.value);
  }
  setTimeout(() => {
    emit('close', props.id);
  }, 300);
};

onMounted(() => {
  isVisible.value = true;
  if (props.duration > 0) {
    timeoutId.value = window.setTimeout(close, props.duration);
  }
});

onUnmounted(() => {
  if (timeoutId.value) {
    clearTimeout(timeoutId.value);
  }
});
</script>

<template>
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="transform translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="transform translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-show="isVisible"
      :class="[
        'max-w-sm w-full shadow-lg rounded-lg pointer-events-auto border',
        getBgColor()
      ]"
    >
      <div class="p-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <component
              :is="getIcon()"
              :class="['h-6 w-6', getIconColor()]"
            />
          </div>
          <div class="ml-3 w-0 flex-1 pt-0.5">
            <p class="text-sm font-medium text-gray-900">
              {{ title }}
            </p>
            <p v-if="message" class="mt-1 text-sm text-gray-500">
              {{ message }}
            </p>
          </div>
          <div class="ml-4 flex flex-shrink-0">
            <button
              @click="close"
              class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600"
            >
              <X class="h-5 w-5" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template> 