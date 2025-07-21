<template>
  <Head title="Help Center" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#E6F3FF] to-[#F0FFF0] py-12 px-4 sm:px-6 lg:px-8">
      
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-[#2F4F4F] mb-2">Help Center</h1>
            <p class="text-[#708090]">Pusat Bantuan Sistem Peminjaman Barang</p>
          </div>
          <Link
            href="/dashboard"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300"
          >
            <ArrowLeft class="h-4 w-4" />
            Kembali
          </Link>
        </div>
      </div>

      <!-- Search Section -->
      <Card class="max-w-4xl mx-auto mb-8 overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl">
        <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] p-6">
          <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
            <Search class="h-5 w-5" />
            Cari Bantuan
          </CardTitle>
        </CardHeader>
        <CardContent class="p-6">
          <div class="flex gap-4">
            <Input
              v-model="searchQuery"
              placeholder="Ketik pertanyaan Anda di sini..."
              class="flex-1"
            />
            <Button class="bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] hover:from-[#FF5252] hover:to-[#26A69A]">
              <Search class="h-4 w-4 mr-2" />
              Cari
            </Button>
          </div>
        </CardContent>
      </Card>

      <!-- FAQ Section -->
      <Card class="max-w-4xl mx-auto mb-8 overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl">
        <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] p-6">
          <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
            <HelpCircle class="h-5 w-5" />
            Pertanyaan yang Sering Diajukan (FAQ)
          </CardTitle>
        </CardHeader>
        <CardContent class="p-6">
          <div class="space-y-4">
            <div
              v-for="(faq, index) in filteredFaqs"
              :key="index"
              class="border border-gray-200 rounded-lg"
            >
              <button
                @click="toggleFaq(index)"
                class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors"
              >
                <span class="font-medium text-[#2F4F4F]">{{ faq.question }}</span>
                <ChevronDown
                  class="h-5 w-5 text-gray-500 transition-transform"
                  :class="{ 'rotate-180': openFaqs[index] }"
                />
              </button>
              <div
                v-show="openFaqs[index]"
                class="px-6 pb-4 text-[#708090] border-t border-gray-100"
              >
                <p class="mt-4">{{ faq.answer }}</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Quick Actions -->
      <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl hover:shadow-2xl transition-all duration-300">
          <CardContent class="p-6 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] rounded-full flex items-center justify-center mx-auto mb-4">
              <BookOpen class="h-6 w-6 text-white" />
            </div>
            <h3 class="text-lg font-semibold text-[#2F4F4F] mb-2">Panduan Pengguna</h3>
            <p class="text-sm text-[#708090] mb-4">
              Pelajari cara menggunakan sistem dengan panduan lengkap
            </p>
            <Button variant="outline" class="w-full">
              Lihat Panduan
            </Button>
          </CardContent>
        </Card>

        <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl hover:shadow-2xl transition-all duration-300">
          <CardContent class="p-6 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] rounded-full flex items-center justify-center mx-auto mb-4">
              <Video class="h-6 w-6 text-white" />
            </div>
            <h3 class="text-lg font-semibold text-[#2F4F4F] mb-2">Video Tutorial</h3>
            <p class="text-sm text-[#708090] mb-4">
              Tonton video tutorial untuk memahami fitur sistem
            </p>
            <Button variant="outline" class="w-full">
              Tonton Video
            </Button>
          </CardContent>
        </Card>

        <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl hover:shadow-2xl transition-all duration-300">
          <CardContent class="p-6 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-[#98FB98] to-[#20B2AA] rounded-full flex items-center justify-center mx-auto mb-4">
              <MessageCircle class="h-6 w-6 text-white" />
            </div>
            <h3 class="text-lg font-semibold text-[#2F4F4F] mb-2">Live Chat</h3>
            <p class="text-sm text-[#708090] mb-4">
              Chat langsung dengan tim support kami
            </p>
            <Button variant="outline" class="w-full">
              Mulai Chat
            </Button>
          </CardContent>
        </Card>
      </div>

      <!-- Contact Support -->
      <Card class="max-w-4xl mx-auto overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl">
        <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] p-6">
          <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
            <Headphones class="h-5 w-5" />
            Hubungi Support
          </CardTitle>
        </CardHeader>
        <CardContent class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-[#2F4F4F]">Informasi Kontak</h3>
              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <Mail class="h-4 w-4 text-[#20B2AA]" />
                  <span class="text-sm text-[#708090]">support@peminjaman.com</span>
                </div>
                <div class="flex items-center gap-3">
                  <Phone class="h-4 w-4 text-[#20B2AA]" />
                  <span class="text-sm text-[#708090]">+62 21 1234 5678</span>
                </div>
                <div class="flex items-center gap-3">
                  <Clock class="h-4 w-4 text-[#20B2AA]" />
                  <span class="text-sm text-[#708090]">Senin - Jumat, 08:00 - 17:00</span>
                </div>
              </div>
            </div>
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-[#2F4F4F]">Kirim Pesan</h3>
              <form class="space-y-3">
                <Input placeholder="Nama Anda" />
                <Input placeholder="Email" type="email" />
                <Textarea placeholder="Pesan Anda" rows="3" />
                <Button class="w-full bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] hover:from-[#FF5252] hover:to-[#26A69A]">
                  Kirim Pesan
                </Button>
              </form>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { 
  ArrowLeft, 
  Search, 
  HelpCircle, 
  ChevronDown, 
  BookOpen, 
  Video, 
  MessageCircle, 
  Headphones, 
  Mail, 
  Phone, 
  Clock 
} from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Help Center',
    href: '/help',
  },
];

const searchQuery = ref('');
const openFaqs = ref<{ [key: number]: boolean }>({});

const faqs = [
  {
    question: 'Bagaimana cara meminjam barang?',
    answer: 'Untuk meminjam barang, buka menu "Peminjaman" lalu klik "Tambah Peminjaman". Pilih barang yang ingin dipinjam, isi form dengan lengkap, dan submit permintaan. Admin akan menyetujui atau menolak permintaan Anda.'
  },
  {
    question: 'Berapa lama waktu peminjaman maksimal?',
    answer: 'Waktu peminjaman maksimal adalah 30 hari. Jika Anda membutuhkan waktu lebih lama, silakan hubungi admin untuk perpanjangan.'
  },
  {
    question: 'Bagaimana jika barang rusak saat dipinjam?',
    answer: 'Jika barang rusak saat dipinjam, segera laporkan ke admin melalui menu "Barang Rusak". Berikan detail kerusakan dan foto jika memungkinkan.'
  },
  {
    question: 'Apakah bisa meminjam lebih dari satu barang sekaligus?',
    answer: 'Ya, Anda bisa meminjam lebih dari satu barang sekaligus. Buat permintaan peminjaman terpisah untuk setiap barang atau gunakan fitur "Multiple Request".'
  },
  {
    question: 'Bagaimana cara melihat riwayat peminjaman?',
    answer: 'Untuk melihat riwayat peminjaman, buka menu "Riwayat" di sidebar. Anda akan melihat semua aktivitas peminjaman dan permintaan yang telah dilakukan.'
  },
  {
    question: 'Apakah ada notifikasi untuk pengembalian barang?',
    answer: 'Ya, sistem akan mengirim notifikasi email dan in-app notification 3 hari sebelum jatuh tempo pengembalian barang.'
  },
  {
    question: 'Bagaimana cara mengubah password?',
    answer: 'Untuk mengubah password, klik profil Anda di pojok kanan atas, lalu pilih "Settings" dan "Password". Masukkan password lama dan password baru.'
  },
  {
    question: 'Apakah sistem mendukung mobile?',
    answer: 'Ya, sistem ini responsif dan dapat diakses melalui smartphone, tablet, dan desktop. Semua fitur tersedia di perangkat mobile.'
  }
];

const filteredFaqs = computed(() => {
  if (!searchQuery.value) return faqs;
  return faqs.filter(faq => 
    faq.question.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    faq.answer.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const toggleFaq = (index: number) => {
  openFaqs.value[index] = !openFaqs.value[index];
};
</script> 