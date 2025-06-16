<script setup lang="ts">
import { computed, inject, type Ref } from 'vue';
import { SelectItem, type SelectItemProps } from 'reka-ui';
import { cn } from '@/lib/utils';

interface SelectContextType {
  modelValue: Ref<string | undefined>;
  // Tambahkan properti lain dari SelectContext jika diperlukan di SelectItem
}

const props = defineProps<SelectItemProps & { class?: string }>();
const selectContext = inject<SelectContextType>('SelectContext');

const isChecked = computed(() => selectContext?.modelValue?.value === props.value);

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;
  return delegated;
});
</script>

<template>
  <SelectItem
    v-bind="delegatedProps"
    :class="cn(
      'relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-8 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
      props.class,
    )"
  >
    <span class="absolute right-2 flex h-3.5 w-3.5 items-center justify-center">
      <svg v-if="isChecked" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12l5 5l10 -10"/></svg>
    </span>
    <slot />
  </SelectItem>
</template> 