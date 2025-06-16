<script setup lang="ts">
import { computed, inject, type Ref } from 'vue';
import { SelectContent, type SelectContentProps, SelectPortal } from 'reka-ui';
import { cn } from '@/lib/utils';

interface SelectContextType {
  modelValue: Ref<string | undefined>;
  name?: string;
  dir?: 'ltr' | 'rtl';
  autocomplete?: string;
  disabled?: boolean;
  required?: boolean;
  orientation?: 'horizontal' | 'vertical';
}

const props = defineProps<SelectContentProps & { class?: string }>();
const selectContext = inject<SelectContextType>('SelectContext');

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;
  return delegated;
});
</script>

<template>
  <SelectPortal>
    <SelectContent
      v-bind="delegatedProps"
      :class="cn(
        'relative z-50 max-h-96 min-w-[8rem] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2',
        props.class,
      )"
    >
      <div
        v-if="selectContext?.modelValue?.value"
        :class="cn(
          '-m-1 flex h-3.5 items-center justify-center',
          selectContext?.orientation === 'horizontal' ? 'w-full' : 'w-3.5',
        )"
      >
        <!-- Select Scroll Up Button (Placeholder, adjust as needed) -->
      </div>

      <slot />

      <div
        v-if="selectContext?.modelValue?.value"
        :class="cn(
          '-m-1 flex h-3.5 items-center justify-center',
          selectContext?.orientation === 'horizontal' ? 'w-full' : 'w-3.5',
        )"
      >
        <!-- Select Scroll Down Button (Placeholder, adjust as needed) -->
      </div>
    </SelectContent>
  </SelectPortal>
</template> 