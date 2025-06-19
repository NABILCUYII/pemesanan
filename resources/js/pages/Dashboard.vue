<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Package, Clock, Users, AlertCircle, Sparkles, ArrowUpRight, Star } from 'lucide-vue-next';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

// Ambil data statistik dan aktivitas terbaru dari props (Inertia)
const page = usePage();

const stats = computed(() => page.props.stats ?? [
  {
    title: 'Total Barang',
    value: '0',
    icon: Package,
    change: '0%',
    trend: 'up',
    color: 'from-blue-500 to-blue-600',
  },
  {
    title: 'Barang Dipinjam',
    value: '0',
    icon: Clock,
    change: '0%',
    trend: 'up',
    color: 'from-purple-500 to-purple-600',
  },
  {
    title: 'Total Pengguna',
    value: '0',
    icon: Users,
    change: '0%',
    trend: 'up',
    color: 'from-green-500 to-green-600',
  },
  {
    title: 'Barang Rusak',
    value: '0',
    icon: AlertCircle,
    change: '0%',
    trend: 'down',
    color: 'from-red-500 to-red-600',
  },
]);

const recentActivities = computed(() => page.props.recentActivities ?? [
  {
    title: 'Tidak ada aktivitas',
    description: 'Belum ada aktivitas terbaru.',
    time: '',
  },
]);
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-[url('/images/grid.svg')] bg-gradient-to-br from-indigo-900/90 via-blue-900/90 to-purple-900/90 py-12 px-4 sm:px-6 lg:px-8 animate-fade-in">
      <!-- Welcome Section -->
      <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600/80 via-purple-600/80 to-pink-600/80 p-8 text-white shadow-2xl animate-gradient-x backdrop-blur-xl border border-white/10">
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-white/10 blur-3xl animate-pulse delay-300"></div>
        <div class="relative z-10">
          <h1 class="text-4xl font-extrabold mb-3 flex items-center gap-3">
            Selamat Datang!
            <Sparkles class="text-yellow-300 animate-pulse" />
            <Star class="text-yellow-300 animate-bounce delay-100" />
          </h1>
          <p class="text-lg text-white/90 animate-fade-in">Berikut adalah ringkasan aktivitas sistem peminjaman barang</p>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <Card
          v-for="(stat, index) in stats"
          :key="stat.title"
          class="group relative overflow-hidden rounded-2xl border border-white/10 bg-black/20 p-6 shadow-xl backdrop-blur-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl animate-slide-up"
          :style="{ animationDelay: `${index * 100}ms` }"
        >
          <!-- Gradient Background -->
          <div :class="`absolute inset-0 bg-gradient-to-br ${stat.color} opacity-10 group-hover:opacity-20 transition-opacity duration-300 animate-gradient-x`"></div>
          
          <!-- Icon background -->
          <div class="absolute right-4 top-4 opacity-10 text-white animate-float">
            <component :is="stat.icon" class="h-24 w-24" />
          </div>

          <div class="relative z-10">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-white/80">{{ stat.title }}</h3>
              <component :is="stat.icon" class="h-5 w-5 text-white animate-pulse" />
            </div>
            
            <div class="mt-4">
              <div class="text-3xl font-extrabold text-white animate-count-up">{{ stat.value }}</div>
              <div class="mt-2 flex items-center gap-2">
                <span :class="[
                  'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium animate-fade-in',
                  stat.trend === 'up' ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'
                ]">
                  <ArrowUpRight class="h-3 w-3" :class="stat.trend === 'down' ? 'rotate-180' : ''" />
                  {{ stat.change }}
                </span>
                <span class="text-xs text-white/60">dari bulan lalu</span>
              </div>
            </div>
          </div>
        </Card>
      </div>

      <!-- Recent Activity -->
      <Card class="mt-8 overflow-hidden rounded-2xl border border-white/10 bg-black/20 shadow-xl backdrop-blur-xl animate-slide-up delay-500">
        <CardHeader class="border-b border-white/10 bg-gradient-to-r from-black/20 to-black/10 p-6 animate-gradient-x">
          <CardTitle class="text-xl font-bold text-white">Aktivitas Terbaru</CardTitle>
        </CardHeader>
        <CardContent class="p-6">
          <div class="space-y-6">
            <div
              v-for="(activity, index) in recentActivities"
              :key="index"
              class="group flex items-start gap-4 rounded-xl p-4 transition-all duration-300 hover:bg-white/5 animate-fade-in"
              :style="{ animationDelay: `${index * 200}ms` }"
            >
              <div class="flex-shrink-0">
                <div class="relative">
                  <div class="h-2 w-2 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 animate-pulse"></div>
                  <div class="absolute -inset-1 rounded-full bg-gradient-to-r from-purple-400/20 to-pink-400/20 blur-sm group-hover:blur-md transition-all duration-300"></div>
                </div>
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium text-white">{{ activity.title }}</p>
                <p class="mt-1 text-xs text-white/60">{{ activity.description }}</p>
                <p v-if="activity.time" class="mt-2 text-xs text-white/40">{{ activity.time }}</p>
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
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes count-up {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes gradient-x {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

.animate-fade-in {
  animation: fade-in 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-slide-up {
  animation: slide-up 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.animate-count-up {
  animation: count-up 1s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-gradient-x {
  animation: gradient-x 15s ease infinite;
  background-size: 200% 200%;
}

.delay-100 {
  animation-delay: 100ms;
}

.delay-300 {
  animation-delay: 300ms;
}

.delay-500 {
  animation-delay: 500ms;
}
</style>
