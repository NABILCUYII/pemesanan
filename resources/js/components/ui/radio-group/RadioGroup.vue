<script setup lang="ts">
import { useVModel } from '@vueuse/core';
import { provide } from 'vue';

interface RadioGroupRootProps {
  modelValue?: string;
  defaultValue?: string;
  name?: string;
  dir?: 'ltr' | 'rtl';
  orientation?: 'horizontal' | 'vertical';
  loop?: boolean;
  disabled?: boolean;
  required?: boolean;
}

const props = defineProps<RadioGroupRootProps>();

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
});

provide('RadioGroupContext', {
  modelValue,
  name: props.name,
  dir: props.dir,
  orientation: props.orientation,
  loop: props.loop,
  disabled: props.disabled,
  required: props.required,
});
</script>

<template>
  <div
    role="radiogroup"
    :aria-orientation="orientation"
    :data-orientation="orientation"
  >
    <slot />
  </div>
</template> 