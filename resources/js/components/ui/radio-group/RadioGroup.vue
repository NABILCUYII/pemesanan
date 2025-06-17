<script setup lang="ts">
import { RadioGroup, type RadioGroupProps } from 'reka-ui';
import { cn } from '@/lib/utils';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<RadioGroupProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;
  return delegated;
});
</script>

<template>
  <RadioGroup
    v-bind="delegatedProps"
    :class="cn('grid gap-2', props.class)"
  >
    <slot />
  </RadioGroup>
</template> 
