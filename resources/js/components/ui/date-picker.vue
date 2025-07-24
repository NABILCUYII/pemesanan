<template>
  <input
    type="date"
    :min="min"
    v-model="modelValueProxy"
    v-bind="$attrs"
  />
</template>
<script setup lang="ts">
import { computed } from 'vue';
const props = defineProps<{ modelValue: string, min?: string }>();
const emit = defineEmits(['update:modelValue']);
const modelValueProxy = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});
</script> 