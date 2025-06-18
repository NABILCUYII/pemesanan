import { defineComponent, ref, watch, provide, inject } from 'vue';
import { cn } from '@/lib/utils';

const TabsContextKey = Symbol('TabsContext');

export const Tabs = defineComponent({
    name: 'Tabs',
    props: {
        modelValue: {
            type: String,
            required: true
        },
        class: {
            type: String,
            default: ''
        }
    },
    emits: ['update:modelValue'],
    setup(props, { emit, slots }) {
        const activeTab = ref(props.modelValue);

        watch(() => props.modelValue, (newValue) => {
            activeTab.value = newValue;
        });

        watch(activeTab, (newValue) => {
            emit('update:modelValue', newValue);
        });

        provide(TabsContextKey, {
            activeTab,
            setActiveTab: (value: string) => {
                activeTab.value = value;
                emit('update:modelValue', value);
            }
        });

        return () => slots.default?.();
    }
});

export const TabsList = defineComponent({
    name: 'TabsList',
    props: {
        class: {
            type: String,
            default: ''
        }
    },
    template: `
        <div :class="['inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground', class]">
            <slot />
        </div>
    `
});

export const TabsTrigger = defineComponent({
    name: 'TabsTrigger',
    props: {
        value: {
            type: String,
            required: true
        },
        class: {
            type: String,
            default: ''
        }
    },
    setup(props) {
        const context = inject(TabsContextKey) as { activeTab: { value: string }, setActiveTab: (value: string) => void };
        const isActive = () => context.activeTab.value === props.value;

        return {
            isActive,
            handleClick: () => context.setActiveTab(props.value)
        };
    },
    template: `
        <button
            :class="[
                'inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
                isActive() ? 'bg-background text-foreground shadow-sm' : 'hover:bg-background hover:text-foreground',
                class
            ]"
            @click="handleClick"
        >
            <slot />
        </button>
    `
});

export const TabsContent = defineComponent({
    name: 'TabsContent',
    props: {
        value: {
            type: String,
            required: true
        },
        class: {
            type: String,
            default: ''
        }
    },
    setup(props) {
        const context = inject(TabsContextKey) as { activeTab: { value: string } };
        const isActive = () => context.activeTab.value === props.value;

        return {
            isActive
        };
    },
    template: `
        <div
            v-show="isActive()"
            :class="[
                'mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2',
                class
            ]"
        >
            <slot />
        </div>
    `
}); 