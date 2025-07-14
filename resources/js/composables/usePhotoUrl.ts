import { computed } from 'vue';

export function usePhotoUrl() {
    const getPhotoUrl = (photoPath: string | null | undefined): string | null => {
        if (!photoPath) return null;
        
        // If it's already a full URL, return as is
        if (photoPath.startsWith('http://') || photoPath.startsWith('https://')) {
            return photoPath;
        }
        
        // If it's a relative path, prepend /storage/
        if (photoPath.startsWith('/')) {
            return photoPath;
        }
        
        // Otherwise, assume it's a storage path
        return `/storage/${photoPath}`;
    };

    const hasPhoto = (photoPath: string | null | undefined): boolean => {
        return !!photoPath;
    };

    return {
        getPhotoUrl,
        hasPhoto
    };
} 