<template>
  <Head title="Contact Us" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#E6F3FF] to-[#F0FFF0] py-12 px-4 sm:px-6 lg:px-8">
      
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-[#2F4F4F] mb-2">Contact Us</h1>
            <p class="text-[#708090]">Hubungi Tim Support Kami</p>
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

      <!-- Contact Information -->
      <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Contact Form -->
        <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl">
          <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] p-6">
            <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
              <MessageSquare class="h-5 w-5" />
              Kirim Pesan
            </CardTitle>
          </CardHeader>
          <CardContent class="p-6">
            <form @submit.prevent="submitForm" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="firstName">Nama Depan *</Label>
                  <Input
                    id="firstName"
                    v-model="form.firstName"
                    placeholder="Masukkan nama depan"
                    required
                  />
                </div>
                <div>
                  <Label for="lastName">Nama Belakang *</Label>
                  <Input
                    id="lastName"
                    v-model="form.lastName"
                    placeholder="Masukkan nama belakang"
                    required
                  />
                </div>
              </div>
              
              <div>
                <Label for="email">Email *</Label>
                <Input
                  id="email"
                  v-model="form.email"
                  type="email"
                  placeholder="contoh@email.com"
                  required
                />
              </div>
              
              <div>
                <Label for="phone">Nomor Telepon</Label>
                <Input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  placeholder="+62 812-3456-7890"
                />
              </div>
              
              <div>
                <Label for="subject">Subjek *</Label>
                <Select v-model="form.subject">
                  <SelectTrigger>
                    <SelectValue placeholder="Pilih subjek" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="general">Pertanyaan Umum</SelectItem>
                    <SelectItem value="technical">Masalah Teknis</SelectItem>
                    <SelectItem value="billing">Billing & Pembayaran</SelectItem>
                    <SelectItem value="feature">Saran Fitur</SelectItem>
                    <SelectItem value="bug">Laporan Bug</SelectItem>
                    <SelectItem value="other">Lainnya</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              
              <div>
                <Label for="message">Pesan *</Label>
                <Textarea
                  id="message"
                  v-model="form.message"
                  placeholder="Tulis pesan Anda di sini..."
                  rows="5"
                  required
                />
              </div>
              
              <Button
                type="submit"
                :disabled="isSubmitting"
                class="w-full bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] hover:from-[#FF5252] hover:to-[#26A69A]"
              >
                <Loader2 v-if="isSubmitting" class="h-4 w-4 mr-2 animate-spin" />
                <Send v-else class="h-4 w-4 mr-2" />
                {{ isSubmitting ? 'Mengirim...' : 'Kirim Pesan' }}
              </Button>
            </form>
          </CardContent>
        </Card>

        <!-- Contact Information -->
        <div class="space-y-6">
          <!-- Office Location -->
          <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl">
            <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] p-6">
              <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
                <MapPin class="h-5 w-5" />
                Lokasi Kantor
              </CardTitle>
            </CardHeader>
            <CardContent class="p-6">
              <div class="space-y-4">
                <div class="flex items-start gap-3">
                  <MapPin class="h-5 w-5 text-[#20B2AA] mt-1" />
                  <div>
                    <h3 class="font-semibold text-[#2F4F4F]">Alamat</h3>
                    <p class="text-sm text-[#708090]">
                      Jl. Teknologi No. 123<br>
                      Jakarta Selatan, 12345<br>
                      Indonesia
                    </p>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <Clock class="h-5 w-5 text-[#20B2AA]" />
                  <div>
                    <h3 class="font-semibold text-[#2F4F4F]">Jam Operasional</h3>
                    <p class="text-sm text-[#708090]">
                      Senin - Jumat: 08:00 - 17:00<br>
                      Sabtu: 09:00 - 15:00<br>
                      Minggu: Tutup
                    </p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Contact Methods -->
          <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl">
            <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#98FB98] to-[#20B2AA] p-6">
              <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
                <Phone class="h-5 w-5" />
                Kontak Langsung
              </CardTitle>
            </CardHeader>
            <CardContent class="p-6">
              <div class="space-y-4">
                <div class="flex items-center gap-3">
                  <Mail class="h-5 w-5 text-[#20B2AA]" />
                  <div>
                    <h3 class="font-semibold text-[#2F4F4F]">Email</h3>
                    <p class="text-sm text-[#708090]">info@peminjaman.com</p>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <Phone class="h-5 w-5 text-[#20B2AA]" />
                  <div>
                    <h3 class="font-semibold text-[#2F4F4F]">Telepon</h3>
                    <p class="text-sm text-[#708090]">+62 21 1234 5678</p>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <MessageCircle class="h-5 w-5 text-[#20B2AA]" />
                  <div>
                    <h3 class="font-semibold text-[#2F4F4F]">WhatsApp</h3>
                    <p class="text-sm text-[#708090]">+62 812-3456-7890</p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Social Media -->
          <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl">
            <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#87CEEB] to-[#98FB98] p-6">
              <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
                <Share2 class="h-5 w-5" />
                Media Sosial
              </CardTitle>
            </CardHeader>
            <CardContent class="p-6">
              <div class="grid grid-cols-2 gap-4">
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                  <Facebook class="h-5 w-5 text-blue-600" />
                  <span class="text-sm font-medium text-[#2F4F4F]">Facebook</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                  <Twitter class="h-5 w-5 text-blue-400" />
                  <span class="text-sm font-medium text-[#2F4F4F]">Twitter</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                  <Instagram class="h-5 w-5 text-pink-600" />
                  <span class="text-sm font-medium text-[#2F4F4F]">Instagram</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                  <Linkedin class="h-5 w-5 text-blue-700" />
                  <span class="text-sm font-medium text-[#2F4F4F]">LinkedIn</span>
                </a>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { 
  ArrowLeft, 
  MessageSquare, 
  Send, 
  Loader2, 
  MapPin, 
  Clock, 
  Phone, 
  Mail, 
  MessageCircle, 
  Share2, 
  Facebook, 
  Twitter, 
  Instagram, 
  Linkedin 
} from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Contact Us',
    href: '/contact',
  },
];

const isSubmitting = ref(false);

const form = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  subject: '',
  message: '',
});

const submitForm = async () => {
  isSubmitting.value = true;
  
  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 2000));
  
  // Reset form
  form.value = {
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
  };
  
  isSubmitting.value = false;
  
  // Show success message (you can implement toast notification here)
  alert('Pesan berhasil dikirim! Kami akan segera menghubungi Anda.');
};
</script> 