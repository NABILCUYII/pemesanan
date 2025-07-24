<template>
  <div class="bg-white rounded-lg shadow border mb-4">
    <div v-if="$slots.header" class="px-4 py-2 border-b font-semibold text-base">
      <slot name="header" />
    </div>
    <div class="px-4 py-3">
      <slot name="body" />
    </div>
  </div>
</template>
<script setup lang="ts">
// Minimal Card component
</script> 