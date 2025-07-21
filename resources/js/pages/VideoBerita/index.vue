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
            :href="route('video-berita.create')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] text-white rounded-lg hover:from-[#FF5252] hover:to-[#26A69A] transition-all duration-300"
          >
            <Plus class="h-4 w-4" />
            Tambah Video
          </Link>
        </div>
      </div>

      <!-- Video Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card
          v-for="video in videoBeritas.data"
          :key="video.id"
          class="group relative overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-lg backdrop-blur-xl transition-all duration-300 hover:scale-105 hover:shadow-xl"
        >
          <!-- Thumbnail -->
          <div class="relative aspect-video bg-black overflow-hidden">
            <img
              v-if="video.thumbnail_url || video.youtube_thumbnail"
              :src="video.thumbnail_url || video.youtube_thumbnail"
              :alt="video.judul"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
            />
            <div v-else class="w-full h-full bg-gradient-to-br from-[#20B2AA] to-[#87CEEB] flex items-center justify-center">
              <Play class="w-16 h-16 text-white/80" />
            </div>
            
            <!-- Play Button Overlay -->
            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <div class="bg-black/50 rounded-full p-4">
                <Play class="w-8 h-8 text-white" />
              </div>
            </div>

            <!-- Status Badge -->
            <div class="absolute top-2 right-2">
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
          </div>

          <!-- Content -->
          <CardContent class="p-6">
            <h3 class="text-lg font-bold text-[#2F4F4F] mb-2 line-clamp-2">{{ video.judul }}</h3>
            <p v-if="video.deskripsi" class="text-sm text-[#708090] mb-3 line-clamp-2">{{ video.deskripsi }}</p>
            
            <!-- Meta Info -->
            <div class="space-y-2 mb-4">
              <div class="flex items-center gap-2 text-xs text-[#B0C4DE]">
                <Calendar class="w-3 h-3" />
                <span>{{ formatDate(video.tanggal_publish) }}</span>
              </div>
              <div v-if="video.sumber" class="flex items-center gap-2 text-xs text-[#B0C4DE]">
                <User class="w-3 h-3" />
                <span>{{ video.sumber }}</span>
              </div>
              <div class="flex items-center gap-2 text-xs text-[#B0C4DE]">
                <Hash class="w-3 h-3" />
                <span>Urutan: {{ video.urutan }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
              <Button
                @click="openVideoModal(video)"
                variant="outline"
                size="sm"
                class="flex-1"
              >
                <Eye class="h-3 w-3 mr-1" />
                Tonton
              </Button>
              
              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <Button variant="outline" size="sm">
                    <MoreVertical class="h-3 w-3" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                  <DropdownMenuItem @click="openVideoModal(video)">
                    <Eye class="h-3 w-3 mr-2" />
                    Tonton Video
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="editVideo(video)">
                    <Edit class="h-3 w-3 mr-2" />
                    Edit
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="toggleStatus(video)">
                    <ToggleLeft class="h-3 w-3 mr-2" />
                    {{ video.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                  </DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem @click="deleteVideo(video)" class="text-red-600">
                    <Trash2 class="h-3 w-3 mr-2" />
                    Hapus
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Pagination -->
      <div v-if="videoBeritas.links && videoBeritas.links.length > 3" class="mt-8 flex justify-center">
        <nav class="flex items-center gap-1">
          <Link
            v-for="link in videoBeritas.links"
            :key="link.label"
            :href="link.url"
            :class="[
              'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
              link.active
                ? 'bg-[#20B2AA] text-white'
                : 'text-[#2F4F4F] hover:bg-[#F0F8FF]'
            ]"
            v-html="link.label"
          />
        </nav>
      </div>

      <!-- Video Modal -->
      <transition name="fade">
        <div
          v-if="showVideoModal"
          class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
          @click.self="closeVideoModal"
        >
          <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 p-0 overflow-hidden relative">
            <button
              class="absolute top-4 right-4 z-10 bg-[#20B2AA] text-white rounded-full p-2 hover:bg-[#178d87] transition"
              @click="closeVideoModal"
            >
              <X class="h-6 w-6" />
            </button>
            <div class="w-full aspect-video bg-black flex items-center justify-center">
              <template v-if="selectedVideo">
                <iframe
                  v-if="selectedVideo.embed_url"
                  :src="selectedVideo.embed_url"
                  frameborder="0"
                  allowfullscreen
                  class="w-full h-full"
                ></iframe>
                <video
                  v-else
                  :src="selectedVideo.video_url"
                  controls
                  class="w-full h-full"
                ></video>
              </template>
            </div>
            <div class="p-6">
              <h3 class="text-2xl font-bold text-[#2F4F4F] mb-2">{{ selectedVideo?.judul }}</h3>
              <p class="text-base text-[#708090] mb-2">{{ selectedVideo?.deskripsi }}</p>
              <div class="flex items-center justify-between text-xs text-[#B0C4DE]">
                <span>{{ formatDate(selectedVideo?.tanggal_publish) }}</span>
                <span v-if="selectedVideo?.sumber">Sumber: {{ selectedVideo?.sumber }}</span>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { 
  DropdownMenu, 
  DropdownMenuContent, 
  DropdownMenuItem, 
  DropdownMenuSeparator, 
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu';
import { Plus, Play, Eye, Edit, Trash2, MoreVertical, Calendar, User, Hash, ToggleLeft, X } from 'lucide-vue-next';
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

interface PaginatedData {
  data: VideoBerita[];
  links: any[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const props = defineProps<{
  videoBeritas: PaginatedData;
}>();

// Modal logic
const showVideoModal = ref(false);
const selectedVideo = ref<VideoBerita | null>(null);

function openVideoModal(video: VideoBerita) {
  selectedVideo.value = video;
  showVideoModal.value = true;
}

function closeVideoModal() {
  showVideoModal.value = false;
  selectedVideo.value = null;
}

function editVideo(video: VideoBerita) {
  router.visit(route('video-berita.edit', video.id));
}

function toggleStatus(video: VideoBerita) {
  router.patch(route('video-berita.toggle-status', video.id), {}, {
    onSuccess: () => {
      // Refresh the page to update the status
      router.reload();
    },
  });
}

function deleteVideo(video: VideoBerita) {
  if (confirm('Apakah Anda yakin ingin menghapus video ini?')) {
    router.delete(route('video-berita.destroy', video.id), {
      onSuccess: () => {
        // Refresh the page to update the list
        router.reload();
      },
    });
  }
}

function formatDate(dateString: string) {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style> 