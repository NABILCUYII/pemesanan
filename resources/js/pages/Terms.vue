<template>
  <Head title="Terms of Service" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#E6F3FF] to-[#F0FFF0] py-12 px-4 sm:px-6 lg:px-8">
      
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-[#2F4F4F] mb-2">Terms of Service</h1>
            <p class="text-[#708090]">Ketentuan Layanan Sistem Peminjaman Barang</p>
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

      <!-- Content -->
      <Card class="max-w-4xl mx-auto overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl">
        <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] p-6">
          <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
            <FileText class="h-5 w-5" />
            Ketentuan Layanan
          </CardTitle>
        </CardHeader>
        <CardContent class="p-8">
          <div class="prose prose-lg max-w-none">
            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">1. Penerimaan Ketentuan</h2>
            <p class="text-[#708090] mb-6">
              Dengan menggunakan Sistem Peminjaman Barang, Anda menyetujui untuk terikat dengan ketentuan layanan ini. Jika Anda tidak setuju dengan ketentuan ini, harap jangan menggunakan layanan kami.
            </p>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">2. Deskripsi Layanan</h2>
            <p class="text-[#708090] mb-6">
              Sistem Peminjaman Barang adalah platform digital yang menyediakan layanan manajemen peminjaman barang, termasuk:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Pendaftaran dan pengelolaan barang</li>
              <li>Proses peminjaman dan pengembalian</li>
              <li>Tracking status peminjaman</li>
              <li>Laporan dan analitik</li>
              <li>Notifikasi otomatis</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">3. Penggunaan Layanan</h2>
            <p class="text-[#708090] mb-6">
              Anda setuju untuk menggunakan layanan ini sesuai dengan:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Hukum yang berlaku di Indonesia</li>
              <li>Kebijakan organisasi tempat Anda bekerja</li>
              <li>Etika dan norma yang berlaku</li>
              <li>Ketentuan teknis penggunaan sistem</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">4. Akun Pengguna</h2>
            <p class="text-[#708090] mb-6">
              Untuk menggunakan layanan ini, Anda harus:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Memiliki akun yang valid dan terdaftar</li>
              <li>Menjaga kerahasiaan kredensial login</li>
              <li>Memberikan informasi yang akurat dan terkini</li>
              <li>Bertanggung jawab atas aktivitas dalam akun Anda</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">5. Kewajiban Pengguna</h2>
            <p class="text-[#708090] mb-6">
              Sebagai pengguna, Anda berkewajiban untuk:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Menggunakan barang yang dipinjam dengan hati-hati</li>
              <li>Mengembalikan barang tepat waktu</li>
              <li>Melaporkan kerusakan atau kehilangan</li>
              <li>Mematuhi prosedur peminjaman yang berlaku</li>
              <li>Tidak menyalahgunakan akses sistem</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">6. Pembatasan Penggunaan</h2>
            <p class="text-[#708090] mb-6">
              Anda dilarang untuk:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Menggunakan sistem untuk tujuan ilegal</li>
              <li>Mencoba mengakses sistem tanpa otorisasi</li>
              <li>Mengganggu kinerja sistem</li>
              <li>Menyebarkan virus atau malware</li>
              <li>Melakukan reverse engineering pada sistem</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">7. Ketersediaan Layanan</h2>
            <p class="text-[#708090] mb-6">
              Kami berusaha untuk menyediakan layanan yang tersedia 24/7, namun kami tidak dapat menjamin:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Layanan tidak akan pernah mengalami gangguan</li>
              <li>Data akan selalu tersedia</li>
              <li>Sistem akan selalu berfungsi sempurna</li>
              <li>Tidak ada downtime untuk maintenance</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">8. Pembatasan Tanggung Jawab</h2>
            <p class="text-[#708090] mb-6">
              Dalam batas maksimum yang diizinkan oleh hukum, kami tidak bertanggung jawab atas:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Kerugian langsung atau tidak langsung</li>
              <li>Kehilangan data atau informasi</li>
              <li>Gangguan layanan</li>
              <li>Kerusakan pada perangkat pengguna</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">9. Perubahan Ketentuan</h2>
            <p class="text-[#708090] mb-6">
              Kami berhak untuk mengubah ketentuan layanan ini dari waktu ke waktu. Perubahan akan diumumkan melalui:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Notifikasi di dalam aplikasi</li>
              <li>Email kepada pengguna</li>
              <li>Pengumuman di website</li>
              <li>Pemberitahuan resmi dari organisasi</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">10. Hukum yang Berlaku</h2>
            <p class="text-[#708090] mb-6">
              Ketentuan layanan ini tunduk pada hukum Republik Indonesia. Setiap sengketa akan diselesaikan melalui:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Musyawarah dan mufakat</li>
              <li>Mediasi jika diperlukan</li>
              <li>Pengadilan yang berwenang</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">11. Kontak</h2>
            <p class="text-[#708090] mb-6">
              Untuk pertanyaan tentang ketentuan layanan ini, silakan hubungi:
            </p>
            <div class="bg-[#F0F8FF] p-6 rounded-lg">
              <p class="text-[#708090] mb-2"><strong>Email:</strong> legal@peminjaman.com</p>
              <p class="text-[#708090] mb-2"><strong>Telepon:</strong> +62 21 1234 5678</p>
              <p class="text-[#708090]"><strong>Alamat:</strong> Jl. Teknologi No. 123, Jakarta Selatan</p>
            </div>

            <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
              <p class="text-yellow-800 text-sm">
                <strong>Terakhir diperbarui:</strong> 1 Januari 2024
              </p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, FileText } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Terms of Service',
    href: '/terms',
  },
];
</script> 