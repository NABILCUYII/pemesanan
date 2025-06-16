<script setup lang="ts">
import { SelectValue, type SelectValueProps } from 'reka-ui';
import { cn } from '@/lib/utils';

const props = defineProps<SelectValueProps & { class?: string }>();
</script>

<template>
  <SelectValue v-bind="props" :class="cn('placeholder:text-muted-foreground', props.class)">
    <slot />
  </SelectValue>
</template> 