import { ref } from 'vue';

export interface Toast {
    id: string;
    type: 'success' | 'error' | 'warning' | 'info';
    title: string;
    message: string;
    duration?: number;
}

const toasts = ref<Toast[]>([]);

export function useToast() {
    const addToast = (toast: Omit<Toast, 'id'>) => {
        const id = Math.random().toString(36).substr(2, 9);
        const newToast: Toast = {
            id,
            duration: 5000,
            ...toast
        };
        
        toasts.value.push(newToast);
        
        // Auto remove after duration
        setTimeout(() => {
            removeToast(id);
        }, newToast.duration || 5000);
    };

    const removeToast = (id: string) => {
        const index = toasts.value.findIndex(toast => toast.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const clearToasts = () => {
        toasts.value = [];
    };

    return {
        toasts,
        addToast,
        removeToast,
        clearToasts
    };
} 