<template>
  <div class="text-center">
    <div class="text-2xl font-bold text-white mb-2">
      {{ currentTime }}
    </div>
    <div class="text-lg text-white/90">
      {{ currentDate }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

const currentTime = ref('');
const currentDate = ref('');

let interval: number | null = null;

const updateDateTime = () => {
  const now = new Date();
  
  // Format time
  currentTime.value = now.toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false
  });
  
  // Format date
  currentDate.value = now.toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

onMounted(() => {
  updateDateTime();
  interval = setInterval(updateDateTime, 1000);
});

onUnmounted(() => {
  if (interval) {
    clearInterval(interval);
  }
});
</script> 