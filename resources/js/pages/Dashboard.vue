<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Package, Clock, Users, AlertCircle, Sparkles, ArrowUpRight, Star, Database, FileText, Settings, Activity, TrendingUp, BarChart3, PieChart } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ChartComponent from '@/components/ChartComponent.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

interface StatItem {
  title: string;
  value: number;
  icon: string;
  change: string;
  trend: 'up' | 'down';
  color: string;
}

interface ActivityItem {
  title: string;
  description: string;
  time: string;
}

interface ChartDataItem {
  month?: number;
  year?: number;
  total: number;
  barang_id?: number;
  barang?: {
    id: number;
    nama_barang: string;
    kode_barang: string;
  };
}

interface ChartData {
  barangMasuk: ChartDataItem[];
  seringDiminta: ChartDataItem[];
  seringDipinjam: ChartDataItem[];
}

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

const page = usePage();

const iconMap = {
  Package,
  Clock,
  Users,
  AlertCircle,
  Sparkles,
  ArrowUpRight,
  Star,
  Database,
  FileText,
  Settings,
  Activity,
  TrendingUp,
  BarChart3,
  PieChart,
};

const stats = computed<StatItem[]>(() => {
  const pageStats = (page.props.stats as StatItem[]) || [];
  return pageStats.map(stat => {
    if (stat.title === 'Total Kategori') {
      return {
        ...stat,
        title: 'Pinjaman Pending',
        icon: stat.icon || 'Clock',
      };
    }
    return {
      ...stat,
      icon: stat.icon || 'Package',
    };
  });
});

const recentActivities = computed<ActivityItem[]>(() => {
  const activities = page.props.recentActivities as ActivityItem[] | undefined;
  if (activities && activities.length > 0) {
    return activities;
  }
  return [
    {
      title: 'Tidak ada aktivitas',
      description: 'Belum ada aktivitas terbaru.',
      time: '',
    },
  ];
});

const videoBeritas = computed<VideoBerita[]>(() => {
  const videos = page.props.videoBeritas as VideoBerita[] || [];
  return videos;
});

// Modal logic for video
const showVideoModal = ref(false);
const selectedVideo = ref<VideoBerita | null>(null);

function openVideoModal(video: VideoBerita) {
  let embedUrl = video.embed_url;
  if (embedUrl) {
    embedUrl += embedUrl.includes('?') ? '&autoplay=1' : '?autoplay=1';
  }
  selectedVideo.value = { ...video, embed_url: embedUrl };
  showVideoModal.value = true;
}

function closeVideoModal() {
  showVideoModal.value = false;
  selectedVideo.value = null;
}

// Chart data for barang masuk (items in)
const barangMasukChart = computed(() => {
  const chartData = (page.props.chartData as ChartData)?.barangMasuk || [];

  // Create month labels for the last 12 months
  const monthLabels: string[] = [];
  const monthData: number[] = [];

  for (let i = 11; i >= 0; i--) {
    const date = new Date();
    date.setMonth(date.getMonth() - i);
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    monthLabels.push(`${month}/${year}`);

    // Find data for this month
    const monthDataItem = chartData.find((item: ChartDataItem) =>
      item.month === month && item.year === year
    );
    monthData.push(monthDataItem ? monthDataItem.total : 0);
  }

  // If no data at all, show message
  if (monthData.every(value => value === 0)) {
    return {
      type: 'line' as const,
      data: {
        labels: ['Belum ada data'],
        datasets: [{
          label: 'Barang Masuk',
          lineTension: 0.3,
          backgroundColor: "rgba(32, 178, 170, 0.1)",
          borderColor: "rgba(32, 178, 170, 1)",
          pointRadius: 3,
          pointBackgroundColor: "rgba(32, 178, 170, 1)",
          pointBorderColor: "rgba(32, 178, 170, 1)",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "rgba(32, 178, 170, 1)",
          pointHoverBorderColor: "rgba(32, 178, 170, 1)",
          pointHitRadius: 10,
          pointBorderWidth: 2,
          fill: true,
          data: [0],
        }],
      },
      options: {
        scales: {
          yAxes: [{
            ticks: {
              callback: function(value: any) {
                return value + ' item';
              }
            }
          }]
        }
      }
    };
  }

  return {
    type: 'line' as const,
    data: {
      labels: monthLabels,
      datasets: [{
        label: 'Barang Masuk',
        lineTension: 0.3,
        backgroundColor: "rgba(32, 178, 170, 0.1)",
        borderColor: "rgba(32, 178, 170, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(32, 178, 170, 1)",
        pointBorderColor: "rgba(32, 178, 170, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(32, 178, 170, 1)",
        pointHoverBorderColor: "rgba(32, 178, 170, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        fill: true,
        data: monthData,
      }],
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            callback: function(value: any) {
              return value + ' item';
            }
          }
        }]
      }
    }
  };
});

// Chart data for frequently requested items
const frequentlyRequestedChart = computed(() => {
  const chartData = (page.props.chartData as ChartData)?.seringDiminta || [];

  // If no data, show empty state
  if (!chartData || chartData.length === 0) {
    return {
      type: 'bar' as const,
      data: {
        labels: ['Belum ada data'],
        datasets: [{
          label: 'Permintaan',
          backgroundColor: ['rgba(135, 206, 235, 0.8)'],
          hoverBackgroundColor: ['rgba(135, 206, 235, 1)'],
          borderColor: ['rgba(135, 206, 235, 1)'],
          data: [0],
        }],
      },
      options: {
        scales: {
          yAxes: [{
            ticks: {
              callback: function(value: any) {
                return value + ' kali';
              }
            }
          }]
        }
      }
    };
  }

  return {
    type: 'bar' as const,
    data: {
      labels: chartData.map((item: ChartDataItem) => item.barang?.nama_barang || 'Barang'),
      datasets: [{
        label: 'Permintaan',
        backgroundColor: [
          'rgba(135, 206, 235, 0.8)',
          'rgba(152, 251, 152, 0.8)',
          'rgba(255, 182, 193, 0.8)',
          'rgba(255, 218, 185, 0.8)',
          'rgba(221, 160, 221, 0.8)',
          'rgba(176, 196, 222, 0.8)'
        ],
        hoverBackgroundColor: [
          'rgba(135, 206, 235, 1)',
          'rgba(152, 251, 152, 1)',
          'rgba(255, 182, 193, 1)',
          'rgba(255, 218, 185, 1)',
          'rgba(221, 160, 221, 1)',
          'rgba(176, 196, 222, 1)'
        ],
        borderColor: [
          'rgba(135, 206, 235, 1)',
          'rgba(152, 251, 152, 1)',
          'rgba(255, 182, 193, 1)',
          'rgba(255, 218, 185, 1)',
          'rgba(221, 160, 221, 1)',
          'rgba(176, 196, 222, 1)'
        ],
        data: chartData.map((item: ChartDataItem) => item.total),
      }],
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            callback: function(value: any) {
              return value + ' kali';
            }
          }
        }]
      }
    }
  };
});

// Chart data for frequently borrowed items
const frequentlyBorrowedChart = computed(() => {
  const chartData = (page.props.chartData as ChartData)?.seringDipinjam || [];

  // If no data, show empty state
  if (!chartData || chartData.length === 0) {
    return {
      type: 'doughnut' as const,
      data: {
        labels: ['Belum ada data'],
        datasets: [{
          data: [1],
          backgroundColor: ['#E5E7EB'],
          hoverBackgroundColor: ['#D1D5DB'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        tooltips: {
          callbacks: {
            label: function() {
              return 'Belum ada data pinjaman';
            }
          }
        }
      }
    };
  }

  return {
    type: 'doughnut' as const,
    data: {
      labels: chartData.map((item: ChartDataItem) => item.barang?.nama_barang || 'Barang'),
      datasets: [{
        data: chartData.map((item: ChartDataItem) => item.total),
        backgroundColor: [
          '#20B2AA',
          '#87CEEB',
          '#98FB98',
          '#FFB6C1',
          '#FFDAB9',
          '#DDA0DD'
        ],
        hoverBackgroundColor: [
          '#1A9A94',
          '#6BB6E0',
          '#7AE87A',
          '#FF9BB0',
          '#FFC8A7',
          '#C890C8'
        ],
        hoverBorderColor: "rgba(234, 236, 244, 1)",
      }],
    },
    options: {
      tooltips: {
        callbacks: {
          label: function(tooltipItem: any, data: any) {
            const dataset = data.datasets[tooltipItem.datasetIndex];
            const total = dataset.data.reduce((a: number, b: number) => a + b, 0);
            const value = dataset.data[tooltipItem.index];
            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
            return data.labels[tooltipItem.index] + ': ' + value + ' pinjaman (' + percentage + '%)';
          }
        }
      }
    }
  };
});
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#E6F3FF] to-[#F0FFF0] py-12 px-4 sm:px-6 lg:px-8 animate-fade-in">


      <!-- Welcome -->
      <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#20B2AA] via-[#87CEEB] to-[#98FB98] p-8 text-white shadow-2xl backdrop-blur-xl border border-[#B0C4DE] animate-gradient-x">
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/20 blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-white/20 blur-3xl animate-pulse delay-300"></div>
        <div class="relative z-10">
          <h1 class="text-4xl font-extrabold mb-3 flex items-center gap-3 text-white">
            Selamat Datang!
            <Sparkles class="text-[#F0FFF0] animate-pulse" />
            <Star class="text-[#F0FFF0] animate-bounce delay-100" />
          </h1>
          <p class="text-lg text-white/90">Berikut adalah ringkasan aktivitas sistem peminjaman barang</p>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <Card
          v-for="(stat, index) in stats"
          :key="stat.title"
          class="group relative overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 p-6 shadow-lg backdrop-blur-xl transition-all duration-300 hover:scale-105 hover:shadow-xl animate-slide-up"
          :style="{ animationDelay: `${index * 100}ms` }"
        >
          <div :class="`absolute inset-0 bg-gradient-to-br ${stat.color} opacity-10 group-hover:opacity-20 transition-opacity duration-300 animate-gradient-x`"></div>
          <div class="absolute right-4 top-4 opacity-10 text-[#2F4F4F] animate-float">
            <component :is="iconMap[stat.icon as keyof typeof iconMap]" class="h-24 w-24" />
          </div>

          <div class="relative z-10">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-[#2F4F4F]">{{ stat.title }}</h3>
              <component :is="iconMap[stat.icon as keyof typeof iconMap]" class="h-5 w-5 text-[#20B2AA] animate-pulse" />
            </div>
            <div class="mt-4">
              <div class="text-3xl font-extrabold text-[#2F4F4F] animate-count-up">{{ stat.value }}</div>
              <div class="mt-2 flex items-center gap-2">
                <span :class="[ 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium animate-fade-in', stat.trend === 'up' ? 'bg-[#98FB98] text-[#2F4F4F]' : 'bg-red-100 text-red-700' ]">
                  <ArrowUpRight class="h-3 w-3" :class="stat.trend === 'down' ? 'rotate-180' : ''" />
                  {{ stat.change }}
                </span>
                <span class="text-xs text-[#708090]">dari bulan lalu</span>
              </div>
            </div>
          </div>
        </Card>
      </div>

      <!-- Charts Section -->
      <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <!-- Barang Masuk Chart -->
        <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl animate-slide-up">
          <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] p-6 animate-gradient-x">
            <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
              <TrendingUp class="h-5 w-5" />
              Barang Masuk
            </CardTitle>
          </CardHeader>
          <CardContent class="p-6">
            <ChartComponent
              chartId="barangMasukChart"
              :chartData="barangMasukChart"
            />
          </CardContent>
        </Card>

        <!-- Frequently Requested Items Chart -->
        <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl animate-slide-up delay-200">
          <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#87CEEB] to-[#98FB98] p-6 animate-gradient-x">
            <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
              <BarChart3 class="h-5 w-5" />
              Barang Sering Diminta
            </CardTitle>
          </CardHeader>
          <CardContent class="p-6">
            <ChartComponent
              chartId="frequentlyRequestedChart"
              :chartData="frequentlyRequestedChart"
            />
          </CardContent>
        </Card>

        <!-- Frequently Borrowed Items Chart -->
        <Card class="overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl animate-slide-up delay-400">
          <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#98FB98] to-[#20B2AA] p-6 animate-gradient-x">
            <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
              <PieChart class="h-5 w-5" />
              Barang Sering Dipinjam
            </CardTitle>
          </CardHeader>
          <CardContent class="p-6">
            <ChartComponent
              chartId="frequentlyBorrowedChart"
              :chartData="frequentlyBorrowedChart"
            />
          </CardContent>
        </Card>
      </div>

      <!-- Video Berita Section -->
      <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-[#2F4F4F]">Video Berita</h2>
          <Link
            href="/video-berita"
            class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium bg-[#20B2AA] text-white hover:bg-[#178d87] transition"
          >
            Lihat Semua Video
          </Link>
        </div>
        <!-- Custom grid for bigger video cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
          <template v-for="video in videoBeritas" :key="video.id">
            <div
              class="cursor-pointer rounded-2xl overflow-hidden shadow-lg border border-[#B0C4DE] bg-white/90 hover:scale-105 transition-all duration-300"
              @click="openVideoModal(video)"
            >
              <div class="relative aspect-video bg-black overflow-hidden">
                <img
                  v-if="video.thumbnail_url || video.youtube_thumbnail"
                  :src="video.thumbnail_url || video.youtube_thumbnail"
                  alt="Thumbnail"
                  class="w-full h-full object-cover"
                />
                <div class="absolute inset-0 flex items-center justify-center">
                  <svg class="w-20 h-20 text-white/80" fill="currentColor" viewBox="0 0 84 84">
                    <circle cx="42" cy="42" r="42" fill="black" fill-opacity="0.4"/>
                    <polygon points="34,28 62,42 34,56" fill="white"/>
                  </svg>
                </div>
              </div>
              <div class="p-6">
                <h3 class="text-xl font-bold text-[#2F4F4F] mb-2 truncate">{{ video.judul }}</h3>
                <p class="text-sm text-[#708090] line-clamp-2 mb-2">{{ video.deskripsi }}</p>
                <div class="flex items-center justify-between text-xs text-[#B0C4DE]">
                  <span>{{ video.tanggal_publish }}</span>
                  <span v-if="video.sumber">Sumber: {{ video.sumber }}</span>
                </div>
              </div>
            </div>
          </template>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
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
                  <span>{{ selectedVideo?.tanggal_publish }}</span>
                  <span v-if="selectedVideo?.sumber">Sumber: {{ selectedVideo?.sumber }}</span>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </div>

      <!-- Recent Activity -->
      <Card class="mt-8 overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl animate-slide-up delay-500">
        <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#87CEEB] to-[#98FB98] p-6 animate-gradient-x">
          <CardTitle class="text-xl font-bold text-[#2F4F4F]">Aktivitas Terbaru</CardTitle>
        </CardHeader>
        <CardContent class="p-6">
          <div class="space-y-6">
            <div
              v-for="(activity, index) in recentActivities"
              :key="index"
              class="group flex items-start gap-4 rounded-xl p-4 transition-all duration-300 hover:bg-[#F0F8FF]/60 animate-fade-in"
              :style="{ animationDelay: `${index * 200}ms` }"
            >
              <div class="flex-shrink-0">
                <div class="relative">
                  <div class="h-2 w-2 rounded-full bg-gradient-to-r from-[#20B2AA] to-[#98FB98] animate-pulse"></div>
                  <div class="absolute -inset-1 rounded-full bg-gradient-to-r from-[#87CEEB]/40 to-[#98FB98]/40 blur-sm group-hover:blur-md transition-all duration-300"></div>
                </div>
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium text-[#2F4F4F]">{{ activity.title }}</p>
                <p class="mt-1 text-xs text-[#708090]">{{ activity.description }}</p>
                <p v-if="activity.time" class="mt-2 text-xs text-[#B0C4DE]">{{ activity.time }}</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

@keyframes count-up {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes gradient-x {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

@keyframes chart-glow {
  0%, 100% { box-shadow: 0 0 20px rgba(32, 178, 170, 0.3); }
  50% { box-shadow: 0 0 30px rgba(32, 178, 170, 0.5); }
}

.animate-fade-in {
  animation: fade-in 0.8s ease-in-out;
}

.animate-slide-up {
  animation: slide-up 0.8s ease-in-out;
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.animate-count-up {
  animation: count-up 1s ease-in-out;
}

.animate-gradient-x {
  animation: gradient-x 15s ease infinite;
  background-size: 200% 200%;
}

.animate-chart-glow {
  animation: chart-glow 2s ease-in-out infinite;
}

.delay-100 { animation-delay: 100ms; }
.delay-200 { animation-delay: 200ms; }
.delay-300 { animation-delay: 300ms; }
.delay-400 { animation-delay: 400ms; }
.delay-500 { animation-delay: 500ms; }

/* Chart container hover effects */
.chart-container {
  transition: all 0.3s ease;
}

.chart-container:hover {
  transform: scale(1.02);
}

/* Responsive chart grid */
@media (max-width: 1024px) {
  .lg\\:grid-cols-3 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .lg\\:grid-cols-3 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
}

/* Modal fade transition */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
