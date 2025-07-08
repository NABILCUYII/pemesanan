<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Info, Users, Shield, Database, Clock, Package, FileText, Settings, Activity, Star } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Tentang',
    href: '/tentang',
  },
];
</script>

<template>
  <Head title="Tentang Sistem">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <!-- Header Section -->
      <div class="text-center space-y-4">
        <div class="flex justify-center">
          <div class="p-4 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full">
            <Info class="w-12 h-12 text-white" />
          </div>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Tentang Sistem Pemesanan</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Sistem manajemen pemesanan dan inventaris yang dirancang untuk memudahkan pengelolaan barang, 
          peminjaman, dan permintaan dalam organisasi.
        </p>
      </div>

      <!-- Main Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Fitur Utama -->
        <Card class="hover:shadow-lg transition-shadow duration-300">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Package class="w-5 h-5 text-blue-500" />
              Manajemen Barang
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-600">
              Kelola inventaris barang dengan mudah. Tambah, edit, dan hapus data barang dengan 
              sistem kategorisasi yang terstruktur.
            </p>
          </CardContent>
        </Card>

        <Card class="hover:shadow-lg transition-shadow duration-300">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Clock class="w-5 h-5 text-green-500" />
              Sistem Peminjaman
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-600">
              Proses peminjaman barang yang efisien dengan tracking status real-time dan 
              notifikasi otomatis untuk pengembalian.
            </p>
          </CardContent>
        </Card>

        <Card class="hover:shadow-lg transition-shadow duration-300">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <FileText class="w-5 h-5 text-purple-500" />
              Permintaan Barang
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-600">
              Sistem permintaan barang dengan workflow approval yang terstruktur dan 
              tracking status permintaan yang transparan.
            </p>
          </CardContent>
        </Card>

        <Card class="hover:shadow-lg transition-shadow duration-300">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Database class="w-5 h-5 text-orange-500" />
              Inventaris Real-time
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-600">
              Monitoring stok barang secara real-time dengan laporan detail dan 
              peringatan otomatis untuk stok minimum.
            </p>
          </CardContent>
        </Card>

        <Card class="hover:shadow-lg transition-shadow duration-300">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Activity class="w-5 h-5 text-red-500" />
              Laporan & Analytics
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-600">
              Generate laporan komprehensif dengan grafik dan analisis data untuk 
              pengambilan keputusan yang lebih baik.
            </p>
          </CardContent>
        </Card>

        <Card class="hover:shadow-lg transition-shadow duration-300">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Users class="w-5 h-5 text-indigo-500" />
              Manajemen User
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-600">
              Sistem role-based access control dengan manajemen user yang aman dan 
              terstruktur sesuai hierarki organisasi.
            </p>
          </CardContent>
        </Card>
      </div>

      <!-- Informasi Teknis -->
      <Card class="mt-8">
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Settings class="w-5 h-5 text-gray-600" />
            Informasi Teknis
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <h3 class="font-semibold text-gray-800">Teknologi yang Digunakan</h3>
              <ul class="space-y-2 text-gray-600">
                <li class="flex items-center gap-2">
                  <Star class="w-4 h-4 text-yellow-500" />
                  Laravel 10 - Backend Framework
                </li>
                <li class="flex items-center gap-2">
                  <Star class="w-4 h-4 text-yellow-500" />
                  Vue.js 3 - Frontend Framework
                </li>
                <li class="flex items-center gap-2">
                  <Star class="w-4 h-4 text-yellow-500" />
                  Inertia.js - Full-stack Solution
                </li>
                <li class="flex items-center gap-2">
                  <Star class="w-4 h-4 text-yellow-500" />
                  Tailwind CSS - Styling
                </li>
                <li class="flex items-center gap-2">
                  <Star class="w-4 h-4 text-yellow-500" />
                  MySQL - Database
                </li>
              </ul>
            </div>
            <div class="space-y-4">
              <h3 class="font-semibold text-gray-800">Fitur Keamanan</h3>
              <ul class="space-y-2 text-gray-600">
                <li class="flex items-center gap-2">
                  <Shield class="w-4 h-4 text-green-500" />
                  Authentication & Authorization
                </li>
                <li class="flex items-center gap-2">
                  <Shield class="w-4 h-4 text-green-500" />
                  Role-based Access Control
                </li>
                <li class="flex items-center gap-2">
                  <Shield class="w-4 h-4 text-green-500" />
                  Data Encryption
                </li>
                <li class="flex items-center gap-2">
                  <Shield class="w-4 h-4 text-green-500" />
                  Audit Trail
                </li>
                <li class="flex items-center gap-2">
                  <Shield class="w-4 h-4 text-green-500" />
                  Backup & Recovery
                </li>
              </ul>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Versi dan Kontak -->
      <Card class="mt-6">
        <CardContent class="pt-6">
          <div class="text-center space-y-4">
            <p class="text-gray-600">
              <strong>Versi:</strong> 1.0.0 | <strong>Terakhir Update:</strong> {{ new Date().toLocaleDateString('id-ID') }}
            </p>
            <p class="text-gray-600">
              Untuk informasi lebih lanjut, silakan hubungi tim IT atau kunjungi halaman Bantuan.
            </p>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<style scoped>
.card-hover {
  transition: all 0.3s ease;
}

.card-hover:hover {
  transform: translateY(-2px);
}
</style> 