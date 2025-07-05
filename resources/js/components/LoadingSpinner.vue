<template>
  <div v-if="show" class="loading-overlay" :class="{ 'fullscreen': fullscreen }">
    <div class="loading-content">
      <!-- Spinner -->
      <div class="spinner-container">
        <div class="spinner"></div>
        <div v-if="text" class="loading-text">{{ text }}</div>
      </div>
      
      <!-- Progress bar (optional) -->
      <div v-if="showProgress && progress !== null" class="progress-container">
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: `${progress}%` }"></div>
        </div>
        <div class="progress-text">{{ Math.round(progress) }}%</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  show?: boolean
  text?: string
  fullscreen?: boolean
  showProgress?: boolean
  progress?: number | null
}

withDefaults(defineProps<Props>(), {
  show: false,
  text: 'Memuat...',
  fullscreen: false,
  showProgress: false,
  progress: null
})
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.loading-overlay:not(.fullscreen) {
  position: absolute;
}

.loading-content {
  text-align: center;
  padding: 2rem;
  background: white;
  border-radius: 1rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.spinner-container {
  margin-bottom: 1rem;
}

.spinner {
  width: 3rem;
  height: 3rem;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

.loading-text {
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 500;
}

.progress-container {
  margin-top: 1rem;
}

.progress-bar {
  width: 200px;
  height: 8px;
  background-color: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
  margin: 0 auto 0.5rem;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #1d4ed8);
  border-radius: 4px;
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: 500;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Variants */
.loading-overlay.dark {
  background-color: rgba(0, 0, 0, 0.8);
}

.loading-overlay.dark .loading-content {
  background: #1f2937;
  color: white;
}

.loading-overlay.dark .loading-text {
  color: #d1d5db;
}

.loading-overlay.dark .progress-text {
  color: #d1d5db;
}

/* Size variants */
.spinner.small {
  width: 1.5rem;
  height: 1.5rem;
  border-width: 2px;
}

.spinner.large {
  width: 4rem;
  height: 4rem;
  border-width: 6px;
}
</style> 