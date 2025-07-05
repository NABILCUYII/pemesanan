<template>
  <Head title="Edit Video Berita" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#E6F3FF] to-[#F0FFF0] py-12 px-4 sm:px-6 lg:px-8">
      
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-[#2F4F4F] mb-2">Edit Video Berita</h1>
            <p class="text-[#708090]">Edit informasi video berita</p>
          </div>
          <Link
            :href="route('video-berita.index')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300"
          >
            <ArrowLeft class="h-4 w-4" />
            Kembali
          </Link>
        </div>
      </div>

      <!-- Form -->
      <Card class="max-w-2xl mx-auto overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl">
        <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] p-6">
          <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
            <Edit class="h-5 w-5" />
            Edit Video Berita
          </CardTitle>
        </CardHeader>
        <CardContent class="p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Judul -->
            <div>
              <Label for="judul" class="text-sm font-medium text-[#2F4F4F]">Judul Video *</Label>
              <Input
                id="judul"
                v-model="form.judul"
                type="text"
                placeholder="Masukkan judul video"
                class="mt-1"
                :class="{ 'border-red-500': errors.judul }"
              />
              <p v-if="errors.judul" class="mt-1 text-sm text-red-600">{{ errors.judul }}</p>
            </div>

            <!-- Deskripsi -->
            <div>
              <Label for="deskripsi" class="text-sm font-medium text-[#2F4F4F]">Deskripsi</Label>
              <Textarea
                id="deskripsi"
                v-model="form.deskripsi"
                placeholder="Masukkan deskripsi video (opsional)"
                class="mt-1"
                rows="3"
                :class="{ 'border-red-500': errors.deskripsi }"
              />
              <p v-if="errors.deskripsi" class="mt-1 text-sm text-red-600">{{ errors.deskripsi }}</p>
            </div>

            <!-- Video URL -->
            <div>
              <Label for="video_url" class="text-sm font-medium text-[#2F4F4F]">URL Video YouTube *</Label>
              <Input
                id="video_url"
                v-model="form.video_url"
                type="url"
                placeholder="https://www.youtube.com/watch?v=..."
                class="mt-1"
                :class="{ 'border-red-500': errors.video_url }"
              />
              <p class="mt-1 text-xs text-[#708090]">
                Format yang didukung: youtube.com/watch?v=..., youtu.be/..., youtube.com/embed/...
              </p>
              <p v-if="errors.video_url" class="mt-1 text-sm text-red-600">{{ errors.video_url }}</p>
            </div>

            <!-- Thumbnail URL -->
            <div>
              <Label for="thumbnail_url" class="text-sm font-medium text-[#2F4F4F]">URL Thumbnail (Opsional)</Label>
              <Input
                id="thumbnail_url"
                v-model="form.thumbnail_url"
                type="url"
                placeholder="https://example.com/thumbnail.jpg"
                class="mt-1"
                :class="{ 'border-red-500': errors.thumbnail_url }"
              />
              <p class="mt-1 text-xs text-[#708090]">
                Jika kosong, akan menggunakan thumbnail otomatis dari YouTube
              </p>
              <p v-if="errors.thumbnail_url" class="mt-1 text-sm text-red-600">{{ errors.thumbnail_url }}</p>
            </div>

            <!-- Sumber -->
            <div>
              <Label for="sumber" class="text-sm font-medium text-[#2F4F4F]">Sumber</Label>
              <Input
                id="sumber"
                v-model="form.sumber"
                type="text"
                placeholder="Contoh: TechNews Indonesia"
                class="mt-1"
                :class="{ 'border-red-500': errors.sumber }"
              />
              <p v-if="errors.sumber" class="mt-1 text-sm text-red-600">{{ errors.sumber }}</p>
            </div>

            <!-- Tanggal Publish -->
            <div>
              <Label for="tanggal_publish" class="text-sm font-medium text-[#2F4F4F]">Tanggal Publish *</Label>
              <Input
                id="tanggal_publish"
                v-model="form.tanggal_publish"
                type="date"
                class="mt-1"
                :class="{ 'border-red-500': errors.tanggal_publish }"
              />
              <p v-if="errors.tanggal_publish" class="mt-1 text-sm text-red-600">{{ errors.tanggal_publish }}</p>
            </div>

            <!-- Urutan -->
            <div>
              <Label for="urutan" class="text-sm font-medium text-[#2F4F4F]">Urutan Tampilan</Label>
              <Input
                id="urutan"
                v-model="form.urutan"
                type="number"
                min="0"
                placeholder="0"
                class="mt-1"
                :class="{ 'border-red-500': errors.urutan }"
              />
              <p class="mt-1 text-xs text-[#708090]">
                Angka kecil akan ditampilkan terlebih dahulu
              </p>
              <p v-if="errors.urutan" class="mt-1 text-sm text-red-600">{{ errors.urutan }}</p>
            </div>

            <!-- Status -->
            <div>
              <div class="flex items-center space-x-2">
                <Checkbox
                  id="is_active"
                  v-model:checked="form.is_active"
                />
                <Label for="is_active" class="text-sm font-medium text-[#2F4F4F]">
                  Video Aktif (ditampilkan di dashboard)
                </Label>
              </div>
              <p v-if="errors.is_active" class="mt-1 text-sm text-red-600">{{ errors.is_active }}</p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center gap-4 pt-4">
              <Button
                type="submit"
                :disabled="processing"
                class="flex-1 bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] hover:from-[#FF5252] hover:to-[#26A69A] text-white"
              >
                <Loader2 v-if="processing" class="h-4 w-4 mr-2 animate-spin" />
                <Save v-else class="h-4 w-4 mr-2" />
                {{ processing ? 'Menyimpan...' : 'Update Video' }}
              </Button>
              
              <Link
                :href="route('video-berita.index')"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-300"
              >
                Batal
              </Link>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Edit, ArrowLeft, Save, Loader2 } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

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
}

interface Props {
  videoBerita: VideoBerita;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Video Berita',
    href: '/video-berita',
  },
  {
    title: 'Edit Video',
    href: `/video-berita/${props.videoBerita.id}/edit`,
  },
];

const form = useForm({
  judul: props.videoBerita.judul,
  deskripsi: props.videoBerita.deskripsi || '',
  video_url: props.videoBerita.video_url,
  thumbnail_url: props.videoBerita.thumbnail_url || '',
  sumber: props.videoBerita.sumber || '',
  tanggal_publish: props.videoBerita.tanggal_publish,
  is_active: props.videoBerita.is_active,
  urutan: props.videoBerita.urutan,
});

const submit = () => {
  form.put(route('video-berita.update', props.videoBerita.id), {
    onSuccess: () => {
      // Redirect will be handled by the controller
    },
  });
};

const { errors, processing } = form;
</script> 