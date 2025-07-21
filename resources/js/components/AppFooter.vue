<template>
  <footer class="bg-gradient-to-r from-[#2F4F4F] via-[#20B2AA] to-[#87CEEB] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Main Footer Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        
        <!-- Company Info -->
        <div class="space-y-4">
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
              <Package class="w-6 h-6 text-white" />
            </div>
            <div>
              <h3 class="text-xl font-bold">Sistem Peminjaman</h3>
              <p class="text-sm text-white/80">Barang Digital</p>
            </div>
          </div>
          <p class="text-sm text-white/70 leading-relaxed">
            Platform manajemen peminjaman barang yang efisien dan terintegrasi untuk organisasi modern.
          </p>
          <div class="flex space-x-4">
            <a href="#" class="text-white/70 hover:text-white transition-colors">
              <Facebook class="w-5 h-5" />
            </a>
            <a href="#" class="text-white/70 hover:text-white transition-colors">
              <Twitter class="w-5 h-5" />
            </a>
            <a href="#" class="text-white/70 hover:text-white transition-colors">
              <Instagram class="w-5 h-5" />
            </a>
            <a href="#" class="text-white/70 hover:text-white transition-colors">
              <Linkedin class="w-5 h-5" />
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="space-y-4">
          <h4 class="text-lg font-semibold">Menu Utama</h4>
          <ul class="space-y-2">
            <li>
              <Link href="/dashboard" class="text-white/70 hover:text-white transition-colors text-sm">
                Dashboard
              </Link>
            </li>
            <li>
              <Link href="/barang" class="text-white/70 hover:text-white transition-colors text-sm">
                Barang
              </Link>
            </li>
            <li>
              <Link href="/peminjaman" class="text-white/70 hover:text-white transition-colors text-sm">
                Peminjaman
              </Link>
            </li>
            <li>
              <Link href="/permintaan" class="text-white/70 hover:text-white transition-colors text-sm">
                Permintaan
              </Link>
            </li>
            <li>
              <Link href="/inventaris" class="text-white/70 hover:text-white transition-colors text-sm">
                Inventaris
              </Link>
            </li>
            <li>
              <Link href="/riwayat" class="text-white/70 hover:text-white transition-colors text-sm">
                Riwayat
              </Link>
            </li>
          </ul>
        </div>

        <!-- Features -->
        <div class="space-y-4">
          <h4 class="text-lg font-semibold">Fitur Unggulan</h4>
          <ul class="space-y-2">
            <li class="flex items-center gap-2 text-white/70 text-sm">
              <CheckCircle class="w-4 h-4 text-green-400" />
              Manajemen Barang
            </li>
            <li class="flex items-center gap-2 text-white/70 text-sm">
              <CheckCircle class="w-4 h-4 text-green-400" />
              Tracking Peminjaman
            </li>
            <li class="flex items-center gap-2 text-white/70 text-sm">
              <CheckCircle class="w-4 h-4 text-green-400" />
              Laporan Real-time
            </li>
            <li class="flex items-center gap-2 text-white/70 text-sm">
              <CheckCircle class="w-4 h-4 text-green-400" />
              Notifikasi Otomatis
            </li>
            <li class="flex items-center gap-2 text-white/70 text-sm">
              <CheckCircle class="w-4 h-4 text-green-400" />
              Video Berita
            </li>
            <li class="flex items-center gap-2 text-white/70 text-sm">
              <CheckCircle class="w-4 h-4 text-green-400" />
              Dashboard Analytics
            </li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div class="space-y-4">
          <h4 class="text-lg font-semibold">Kontak</h4>
          <div class="space-y-3">
            <div class="flex items-center gap-3">
              <MapPin class="w-4 h-4 text-white/70" />
              <span class="text-sm text-white/70">
                Jl. Teknologi No. 123<br>
                Jakarta Selatan, 12345
              </span>
            </div>
            <div class="flex items-center gap-3">
              <Phone class="w-4 h-4 text-white/70" />
              <span class="text-sm text-white/70">+62 21 1234 5678</span>
            </div>
            <div class="flex items-center gap-3">
              <Mail class="w-4 h-4 text-white/70" />
              <span class="text-sm text-white/70">info@peminjaman.com</span>
            </div>
            <div class="flex items-center gap-3">
              <Clock class="w-4 h-4 text-white/70" />
              <span class="text-sm text-white/70">
                Senin - sabtu<br>
                08:00 - 17:00
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="border-t border-white/20 my-8"></div>

      <!-- Bottom Footer -->
      <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <div class="flex items-center space-x-4 text-sm text-white/70">
          <span>&copy; 2025 Sistem Peminjaman Barang. All rights reserved.</span>
          <span class="hidden md:inline">•</span>
          <span class="hidden md:inline">Made with ❤️ in Indonesia</span>
        </div>
        
        <div class="flex items-center space-x-6 text-sm">
          <Link href="/privacy" class="text-white/70 hover:text-white transition-colors">
            Privacy Policy
          </Link>
          <Link href="/terms" class="text-white/70 hover:text-white transition-colors">
            Terms of Service
          </Link>
          <Link href="/help" class="text-white/70 hover:text-white transition-colors">
            Help Center
          </Link>
          <Link href="/contact" class="text-white/70 hover:text-white transition-colors">
            Contact Us
          </Link>
        </div>
      </div>

      <!-- Back to Top Button -->
      <button
        @click="scrollToTop"
        class="fixed bottom-8 right-8 bg-gradient-to-r from-[#FF6B6B] to-[#4ECDC4] hover:from-[#FF5252] hover:to-[#26A69A] text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 z-50"
        :class="{ 'opacity-0 pointer-events-none': !showBackToTop }"
      >
        <ArrowUp class="w-5 h-5" />
      </button>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
  Package, 
  Facebook, 
  Twitter, 
  Instagram, 
  Linkedin, 
  CheckCircle, 
  MapPin, 
  Phone, 
  Mail, 
  Clock, 
  ArrowUp 
} from 'lucide-vue-next';

const showBackToTop = ref(false);

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
};

const handleScroll = () => {
  showBackToTop.value = window.scrollY > 300;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

footer {
  animation: fadeInUp 0.8s ease-out;
}

/* Hover effects for links */
a:hover {
  transform: translateY(-1px);
}

/* Social media icons hover effect */
a:hover svg {
  transform: scale(1.1);
}

/* Back to top button animation */
button {
  transition: all 0.3s ease;
}

button:hover {
  transform: translateY(-2px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .grid {
    gap: 2rem;
  }
}
</style> 