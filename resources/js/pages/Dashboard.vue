<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Package, Clock, Users, AlertCircle, Sparkles, ArrowUpRight, Star, Database, FileText, Settings, Activity, TrendingUp, BarChart3, PieChart } from 'lucide-vue-next';
import { computed } from 'vue';
import ChartComponent from '@/components/ChartComponent.vue';
import VideoBeritaCard from '@/components/VideoBeritaCard.vue';

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
  const pageStats = page.props.stats as StatItem[] || [];
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
  return page.props.recentActivities as ActivityItem[] || [
    {
      title: 'Tidak ada aktivitas',
      description: 'Belum ada aktivitas terbaru.',
      time: '',
    },
  ];
});

const videoBeritas = computed<VideoBerita[]>(() => {
  const videos = page.props.videoBeritas as VideoBerita[] || [];
  console.log('Dashboard - videoBeritas:', videos);
  return videos;
});

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
  if (chartData.length === 0) {
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
  if (chartData.length === 0) {
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
            label: function(tooltipItem: any, data: any) {
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
            const percentage = ((dataset.data[tooltipItem.index] / total) * 100).toFixed(1);
            return data.labels[tooltipItem.index] + ': ' + dataset.data[tooltipItem.index] + ' pinjaman (' + percentage + '%)';
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
        <VideoBeritaCard :videoBeritas="videoBeritas" />
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
</style>
