<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { RadioGroupItem, type RadioGroupItemProps } from 'reka-ui';
import { Check } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

const props = defineProps<RadioGroupItemProps & { class?: HTMLAttributes['class'] }>();
</script>

<template>
  <RadioGroupItem
    v-bind="props"
    :class="cn(
      'aspect-square h-4 w-4 rounded-full border border-primary text-primary shadow focus:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
      props.class,
    )"
  >
    <slot>
      <div class="flex items-center justify-center">
        <Check class="h-3.5 w-3.5 fill-primary" />
      </div>
    </slot>
  </RadioGroupItem>
</template> 