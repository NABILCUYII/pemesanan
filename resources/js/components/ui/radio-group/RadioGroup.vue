<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed, type HTMLAttributes, provide, ref, watch } from 'vue';

interface RadioGroupProps {
  modelValue?: string;
  defaultValue?: string;
  orientation?: 'horizontal' | 'vertical';
  dir?: 'ltr' | 'rtl';
  name?: string;
  disabled?: boolean;
  required?: boolean;
  class?: HTMLAttributes['class'];
}

const props = defineProps<RadioGroupProps>();
const emits = defineEmits(['update:modelValue', 'update:defaultValue']);

const modelValue = ref(props.modelValue ?? props.defaultValue);

watch(() => props.modelValue, (val) => {
  modelValue.value = val;
});

watch(modelValue, (val) => {
  emits('update:modelValue', val);
});

provide('RadioGroupContext', {
  modelValue,
  changeModelValue: (val: string) => {
    modelValue.value = val;
  },
  orientation: computed(() => props.orientation),
  dir: computed(() => props.dir),
  name: computed(() => props.name),
  disabled: computed(() => props.disabled),
  required: computed(() => props.required),
});

const delegatedProps = computed(() => {
  const { class: _, modelValue: __, defaultValue: ___, ...delegated } = props;
  return delegated;
});
</script>

<template>
  <div
    role="radiogroup"
    v-bind="delegatedProps"
    :class="cn('grid gap-2', props.class)"
    :data-orientation="props.orientation"
    :dir="props.dir"
  >
    <slot />
  </div>
</template> 
