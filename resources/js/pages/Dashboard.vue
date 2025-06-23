<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Package, Clock, Users, AlertCircle, Sparkles, ArrowUpRight, Star, Database, FileText, Settings, Activity } from 'lucide-vue-next';
import { computed } from 'vue';

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

.delay-100 { animation-delay: 100ms; }
.delay-300 { animation-delay: 300ms; }
.delay-500 { animation-delay: 500ms; }
</style>
