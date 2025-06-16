<script setup lang="ts">
import { useVModel } from '@vueuse/core';
import { provide } from 'vue';

interface SelectRootProps {
  modelValue?: string;
  defaultValue?: string;
  name?: string;
  dir?: 'ltr' | 'rtl';
  autocomplete?: string;
  disabled?: boolean;
  required?: boolean;
}

const props = defineProps<SelectRootProps>();

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | undefined): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
});

provide('SelectContext', {
  modelValue,
  name: props.name,
  dir: props.dir,
  autocomplete: props.autocomplete,
  disabled: props.disabled,
  required: props.required,
});
</script>

<template>
  <slot />
</template> 