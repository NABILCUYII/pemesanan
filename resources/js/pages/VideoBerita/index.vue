<template>
  <Head title="Video Berita" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#E6F3FF] to-[#F0FFF0] py-12 px-4 sm:px-6 lg:px-8">
      
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-[#2F4F4F] mb-2">Video Berita</h1>
            <p class="text-[#708090]">Kelola video berita untuk ditampilkan di dashboard</p>
          </div>
          <Link
            v-if="user?.role === 'admin'"
            :href="route('video-berita.create')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] text-white rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105"
          >
            <Plus class="h-4 w-4" />
            Tambah Video
          </Link>
        </div>
      </div>

      <!-- Video Grid -->
      <div v-if="videoBeritas.length === 0" class="text-center py-16">
        <div class="text-gray-500 mb-4">
          <Play class="h-16 w-16 mx-auto opacity-50" />
        </div>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum ada video berita</h3>
        <p class="text-gray-600 mb-6">Mulai dengan menambahkan video berita pertama Anda</p>
        <Link
          v-if="user?.role === 'admin'"
          :href="route('video-berita.create')"
          class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] text-white rounded-lg hover:shadow-lg transition-all duration-300"
        >
          <Plus class="h-4 w-4" />
          Tambah Video Pertama
        </Link>
      </div>

      <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <Card
          v-for="video in videoBeritas"
          :key="video.id"
          class="group relative overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-lg backdrop-blur-xl transition-all duration-300 hover:shadow-xl hover:scale-[1.02]"
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
            
            <!-- Status Badge -->
            <div class="absolute top-2 left-2">
              <span 
                :class="[
                  'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                  video.is_active 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800'
                ]"
              >
                {{ video.is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </div>
            
            <!-- Order Badge -->
            <div class="absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded">
              Urutan: {{ video.urutan }}
            </div>
          </div>
          
          <!-- Video Info -->
          <CardContent class="p-6">
            <h3 class="font-semibold text-[#2F4F4F] text-lg mb-2 line-clamp-2 group-hover:text-[#20B2AA] transition-colors duration-300">
              {{ video.judul }}
            </h3>
            
            <p v-if="video.deskripsi" class="text-sm text-[#708090] line-clamp-3 mb-4">
              {{ video.deskripsi }}
            </p>
            
            <div class="space-y-2 mb-4">
              <div class="flex items-center gap-2 text-xs text-[#B0C4DE]">
                <span v-if="video.sumber">Sumber: {{ video.sumber }}</span>
              </div>
              <div class="flex items-center gap-2 text-xs text-[#B0C4DE]">
                <span>Publish: {{ formatDate(video.tanggal_publish) }}</span>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
              <button 
                @click="playVideo(video)"
                class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] text-white rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105"
              >
                <Play class="h-4 w-4" />
                Putar
              </button>
              
              <Link
                v-if="user?.role === 'admin'"
                :href="route('video-berita.edit', video.id)"
                class="inline-flex items-center justify-center p-2 bg-[#98FB98] text-[#2F4F4F] rounded-lg hover:bg-[#90EE90] transition-colors duration-300"
              >
                <Edit class="h-4 w-4" />
              </Link>
              
              <button 
                v-if="user?.role === 'admin'"
                @click="deleteVideo(video)"
                class="inline-flex items-center justify-center p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors duration-300"
              >
                <Trash2 class="h-4 w-4" />
              </button>
            </div>
          </CardContent>
        </Card>
      </div>
      
      <!-- Video Modal -->
      <Dialog :open="showVideoModal" @update:open="showVideoModal = false">
        <DialogContent class="max-w-6xl sm:max-w-5xl w-full">
          <DialogHeader>
            <DialogTitle class="text-2xl">{{ selectedVideo?.judul }}</DialogTitle>
            <DialogDescription v-if="selectedVideo?.deskripsi" class="text-base">
              {{ selectedVideo.deskripsi }}
            </DialogDescription>
          </DialogHeader>
          <div class="space-y-6">
            <div class="aspect-video">
              <iframe
                v-if="selectedVideo?.embed_url"
                :src="selectedVideo.embed_url"
                class="w-full h-full rounded-xl"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>

            <div class="flex items-center justify-between text-sm text-gray-500">
              <span v-if="selectedVideo?.sumber">Sumber: {{ selectedVideo.sumber }}</span>
              <span>{{ formatDate(selectedVideo?.tanggal_publish) }}</span>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Play, Plus, Edit, Trash2 } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Video Berita',
    href: '/video-berita',
  },
];

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

const page = usePage();
const user = computed(() => page.props.auth?.user);

const showVideoModal = ref(false);
const selectedVideo = ref<VideoBerita | null>(null);

const playVideo = (video: VideoBerita) => {
  selectedVideo.value = video;
  showVideoModal.value = true;
};

const deleteVideo = (video: VideoBerita) => {
  if (confirm(`Apakah Anda yakin ingin menghapus video "${video.judul}"?`)) {
    router.delete(route('video-berita.destroy', video.id));
  }
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
  img.src = '/placeholder-video.jpg';
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 