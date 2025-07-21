<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { ref, onMounted, onUnmounted } from 'vue';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

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

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        
        <!-- Jam Digital -->
        <div class="flex items-center gap-4">
            <div class="text-center">
                <div class="text-xl font-bold text-white bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] px-4 py-2 rounded-lg shadow-lg clock-animation">
                    {{ currentTime }}
                </div>
                <div class="text-sm text-[#708090] bg-white/80 px-3 py-1 rounded-lg mt-2 shadow-sm">
                    {{ currentDate }}
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
@keyframes clockGlow {
  0%, 100% {
    box-shadow: 0 0 10px rgba(32, 178, 170, 0.3);
  }
  50% {
    box-shadow: 0 0 20px rgba(32, 178, 170, 0.6);
  }
}

.clock-animation {
  animation: clockGlow 2s ease-in-out infinite;
  transition: all 0.3s ease;
}

.clock-animation:hover {
  transform: scale(1.05);
  box-shadow: 0 0 25px rgba(32, 178, 170, 0.8);
}
</style>

