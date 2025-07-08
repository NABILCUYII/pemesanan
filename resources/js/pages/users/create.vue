<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Heading from '@/components/Heading.vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: route('users.index') },
  { title: 'Create User', href: route('users.create') },
];

const form = useForm({
  name: '',
  email: '',
  password: '',
  role: 'user',
});

const submit = () => {
  form.post(route('users.store'));
};

const goBack = () => {
  router.visit(route('users.index'));
};
</script>

<template>
  <Head title="Create User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-lg px-4 py-10">
      <div class="mb-8 flex flex-col items-center">
        <div class="rounded-full bg-primary/10 p-4 mb-3">
          <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
        </div>
        <Heading title="Create User" description="Tambah user baru ke sistem dengan mudah dan cepat." />
      </div>
      <form @submit.prevent="submit" class="rounded-xl bg-white shadow-lg p-8 space-y-7 border border-gray-100">
        <div>
          <Label for="name" class="text-base font-semibold text-gray-700">Nama Lengkap</Label>
          <Input
            id="name"
            v-model="form.name"
            type="text"
            placeholder="Masukkan nama lengkap"
            class="mt-2"
            required
            autocomplete="off"
          />
          <transition name="fade">
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
          </transition>
        </div>

        <div>
          <Label for="email" class="text-base font-semibold text-gray-700">Email</Label>
          <Input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="Masukkan email"
            class="mt-2"
            required
            autocomplete="off"
          />
          <transition name="fade">
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
          </transition>
        </div>

        <div>
          <Label for="password" class="text-base font-semibold text-gray-700">Password</Label>
          <Input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="Buat password"
            class="mt-2"
            required
            autocomplete="new-password"
          />
          <transition name="fade">
            <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
          </transition>
        </div>

        <div>
          <Label class="text-base font-semibold text-gray-700">Role</Label>
          <div class="flex gap-6 mt-2">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                type="radio"
                id="admin"
                value="admin"
                v-model="form.role"
                class="accent-primary focus:ring-primary"
              />
              <span class="text-sm font-medium text-gray-700">Admin</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                type="radio"
                id="user"
                value="user"
                v-model="form.role"
                class="accent-primary focus:ring-primary"
              />
              <span class="text-sm font-medium text-gray-700">User</span>
            </label>
          </div>
          <transition name="fade">
            <p v-if="form.errors.role" class="mt-1 text-sm text-red-500">{{ form.errors.role }}</p>
          </transition>
        </div>

        <div class="flex flex-col sm:flex-row justify-end sm:justify-between items-center gap-4 pt-2">
          <Button
            type="button"
            @click="goBack"
            class="px-6 py-2 rounded-lg font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
          </Button>
          <Button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2 rounded-lg font-semibold text-white bg-primary hover:bg-primary/90 transition"
          >
            <span v-if="form.processing" class="animate-spin mr-2 inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
            Buat User
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
