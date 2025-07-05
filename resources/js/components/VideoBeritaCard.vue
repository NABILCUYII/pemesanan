<template>
  <div class="video-berita-card">
    <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl animate-slide-up">
      <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] p-6 animate-gradient-x">
        <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
          <Play class="h-5 w-5" />
          Video Berita Terbaru
        </CardTitle>
      </CardHeader>
      <CardContent class="p-6">
        <div v-if="videoBeritas.length === 0" class="text-center py-8">
          <div class="text-gray-500 mb-4">
            <Play class="h-12 w-12 mx-auto opacity-50" />
          </div>
          <p class="text-sm text-gray-600">Belum ada video berita tersedia</p>
        </div>
        
        <div v-else class="space-y-4">
          <div 
            v-for="(video, index) in videoBeritas" 
            :key="video.id"
            class="group relative overflow-hidden rounded-xl border border-[#B0C4DE] bg-white/60 transition-all duration-300 hover:shadow-lg hover:scale-[1.02] animate-fade-in"
            :style="{ animationDelay: `${index * 200}ms` }"
          >
            <!-- Video Thumbnail -->
            <div class="relative aspect-video overflow-hidden">
              <img 
                :src="video.thumbnail_url || video.youtube_thumbnail" 
                :alt="video.judul"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                @error="handleImageError"
              />
              <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors duration-300"></div>
              
              <!-- Play Button Overlay -->
              <div class="absolute inset-0 flex items-center justify-center">
                <button 
                  @click="playVideo(video)"
                  class="bg-white/90 hover:bg-white text-red-600 rounded-full p-3 shadow-lg transition-all duration-300 hover:scale-110 group-hover:bg-white"
                >
                  <Play class="h-6 w-6" />
                </button>
              </div>
              
              <!-- Video Duration Badge -->
              <div class="absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded">
                Berita
              </div>
            </div>
            
            <!-- Video Info -->
            <div class="p-4">
              <h3 class="font-semibold text-[#2F4F4F] text-sm line-clamp-2 mb-2 group-hover:text-[#20B2AA] transition-colors duration-300">
                {{ video.judul }}
              </h3>
              
              <p v-if="video.deskripsi" class="text-xs text-[#708090] line-clamp-2 mb-3">
                {{ video.deskripsi }}
              </p>
              
              <div class="flex items-center justify-between text-xs text-[#B0C4DE]">
                <span v-if="video.sumber">{{ video.sumber }}</span>
                <span>{{ formatDate(video.tanggal_publish) }}</span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- View All Button -->
        <div v-if="videoBeritas.length > 0" class="mt-6 text-center">
          <button 
            @click="viewAllVideos"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] text-white rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105"
          >
            <Eye class="h-4 w-4" />
            Lihat Semua Video
          </button>
        </div>
      </CardContent>
    </Card>
    
    <!-- Video Modal -->
    <Dialog :open="showVideoModal" @update:open="showVideoModal = false">
      <DialogContent class="max-w-4xl">
        <DialogHeader>
          <DialogTitle>{{ selectedVideo?.judul }}</DialogTitle>
          <DialogDescription v-if="selectedVideo?.deskripsi">
            {{ selectedVideo.deskripsi }}
          </DialogDescription>
        </DialogHeader>
        <div class="space-y-4">
          <div class="aspect-video">
            <template v-if="selectedVideo?.embed_url">
              <iframe
                :src="selectedVideo.embed_url"
                class="w-full h-full rounded-lg"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              />
            </template>
            <template v-else>
              <div class="flex items-center justify-center h-full text-center text-gray-500">
                <span>Video tidak dapat diputar. Pastikan link video valid dan publik.<br />
                Untuk Google Drive, pastikan file dapat diakses publik dan gunakan format link yang benar.</span>
              </div>
            </template>
          </div>

          <div class="flex items-center justify-between text-xs text-gray-500">
            <span v-if="selectedVideo?.sumber">Sumber: {{ selectedVideo.sumber }}</span>
            <span>{{ formatDate(selectedVideo?.tanggal_publish) }}</span>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Play, Eye } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

interface VideoBerita {
  id: number;
  judul: string;
  deskripsi?: string;
  video_url: string;
  thumbnail_url?: string;
  sumber?: string;
  tanggal_publish: string;
  is_active: boolean;
  urutan: number;
  youtube_id?: string;
  embed_url?: string;
  youtube_thumbnail?: string;
}

interface Props {
  videoBeritas: VideoBerita[];
}

const props = defineProps<Props>();

const showVideoModal = ref(false);
const selectedVideo = ref<VideoBerita | null>(null);

// Debug: Log video data
console.log('VideoBeritaCard - videoBeritas:', props.videoBeritas);

const playVideo = (video: VideoBerita) => {
  selectedVideo.value = video;
  showVideoModal.value = true;
};

const viewAllVideos = () => {
  router.visit('/video-berita');
};

const formatDate = (dateString: string | undefined) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement;
  img.src = '/placeholder-video.jpg'; // Fallback image
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-slide-up {
  animation: slide-up 0.8s ease-out;
}

@keyframes gradient-x {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

.animate-gradient-x {
  animation: gradient-x 15s ease infinite;
  background-size: 200% 200%;
}
</style> 