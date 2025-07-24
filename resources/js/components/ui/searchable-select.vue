<template>
  <select v-model="selected" class="w-full border rounded p-2">
    <option value="" disabled>{{ placeholder }}</option>
    <option v-for="item in filteredOptions" :key="getValue(item)" :value="getValue(item)">
      {{ getLabel(item) }}
    </option>
  </select>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
const props = defineProps<{
  modelValue: string | number | null,
  options: any[],
  getLabel: (item: any) => string,
  getValue: (item: any) => string | number,
  placeholder?: string
}>();
const emit = defineEmits(['update:modelValue']);
const search = ref('');
const selected = ref(props.modelValue ?? '');

watch(() => props.modelValue, (val) => {
  selected.value = val ?? '';
});
watch(selected, (val) => {
  emit('update:modelValue', val);
});

const filteredOptions = computed(() => {
  if (!search.value) return props.options;
  return props.options.filter(item => props.getLabel(item).toLowerCase().includes(search.value.toLowerCase()));
});
</script> 