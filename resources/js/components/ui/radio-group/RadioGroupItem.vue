<script setup lang="ts">
import { inject, computed, type Ref } from 'vue';
import { cn } from '@/lib/utils';

interface RadioGroupItemProps {
  value: string;
  disabled?: boolean;
}

interface RadioGroupContextType {
  modelValue: Ref<string | undefined>;
  name?: string;
  dir?: 'ltr' | 'rtl';
  orientation?: 'horizontal' | 'vertical';
  loop?: boolean;
  disabled?: boolean;
  required?: boolean;
}

const props = defineProps<RadioGroupItemProps>();

const radioGroupContext = inject<RadioGroupContextType>('RadioGroupContext');

const isChecked = computed(() => radioGroupContext?.modelValue?.value === props.value);

const handleClick = () => {
  if (props.disabled) return;
  if (radioGroupContext?.modelValue) {
    radioGroupContext.modelValue.value = props.value;
  }
};
</script>

<template>
  <button
    type="button"
    role="radio"
    :aria-checked="isChecked"
    :data-state="isChecked ? 'checked' : 'unchecked'"
    :data-disabled="disabled"
    :disabled="disabled"
    :value="value"
    @click="handleClick"
    :class="cn(
      'aspect-square h-4 w-4 rounded-full border border-primary text-primary shadow focus:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
      'data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground',
      'data-[state=unchecked]:bg-background data-[state=unchecked]:text-foreground',
    )"
  >
    <span :class="cn(
      'flex items-center justify-center rounded-full',
      'data-[state=checked]:scale-100',
      'data-[state=unchecked]:scale-0',
    )">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-2.5 w-2.5 fill-current"><circle cx="12" cy="12" r="10"/><path d="M12 6V18M6 12h12"/></svg>
    </span>
  </button>
</template> 