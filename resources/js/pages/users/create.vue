<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, UserPlus, Shield, User, Mail, Lock } from 'lucide-vue-next';

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
</script>

<template>
  <Head title="Create User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 md:p-6 space-y-6 flex flex-col items-center min-h-screen">
      <!-- Header -->
      <div class="flex items-center gap-4 w-full max-w-2xl">
        <Link :href="route('users.index')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowLeft class="w-5 h-5 text-gray-600" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Create User</h1>
          <p class="text-gray-500 text-sm">Add a new user to the system</p>
        </div>
      </div>
      
      <!-- Form Card -->
      <div class="w-full max-w-2xl">
        <form @submit.prevent="submit" class="space-y-8 bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
          <!-- Name Field -->
          <div>
            <Label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
              <User class="w-4 h-4 inline mr-2" />
              Full Name
            </Label>
            <Input
              id="name"
              v-model="form.name"
              type="text"
              class="mt-1"
              required
              placeholder="Enter user's full name"
            />
            <div v-if="form.errors.name" class="mt-2 text-sm text-red-600 bg-red-50 p-2 rounded-md">
              {{ form.errors.name }}
            </div>
          </div>

          <!-- Email Field -->
          <div>
            <Label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
              <Mail class="w-4 h-4 inline mr-2" />
              Email Address
            </Label>
            <Input
              id="email"
              v-model="form.email"
              type="email"
              class="mt-1"
              required
              placeholder="Enter user's email address"
            />
            <div v-if="form.errors.email" class="mt-2 text-sm text-red-600 bg-red-50 p-2 rounded-md">
              {{ form.errors.email }}
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <Label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
              <Lock class="w-4 h-4 inline mr-2" />
              Password
            </Label>
            <Input
              id="password"
              v-model="form.password"
              type="password"
              class="mt-1"
              required
              placeholder="Enter secure password (min. 8 characters)"
            />
            <div v-if="form.errors.password" class="mt-2 text-sm text-red-600 bg-red-50 p-2 rounded-md">
              {{ form.errors.password }}
            </div>
          </div>

          <!-- Role Selection -->
          <div>
            <Label class="block text-sm font-semibold text-gray-700 mb-3">
              <Shield class="w-4 h-4 inline mr-2" />
              User Role
            </Label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div 
                @click="form.role = 'user'"
                :class="[
                  'relative p-4 border-2 rounded-lg cursor-pointer transition-all duration-200',
                  form.role === 'user' 
                    ? 'border-indigo-500 bg-indigo-50' 
                    : 'border-gray-200 hover:border-gray-300'
                ]"
              >
                <div class="flex items-center space-x-3">
                  <input
                    type="radio"
                    id="user"
                    value="user"
                    v-model="form.role"
                    class="h-4 w-4 border-primary text-primary focus:ring-primary"
                  />
                  <div>
                    <label for="user" class="font-medium text-gray-900 cursor-pointer">User</label>
                    <p class="text-sm text-gray-500">Standard user access</p>
                  </div>
                </div>
              </div>
              
              <div 
                @click="form.role = 'admin'"
                :class="[
                  'relative p-4 border-2 rounded-lg cursor-pointer transition-all duration-200',
                  form.role === 'admin' 
                    ? 'border-indigo-500 bg-indigo-50' 
                    : 'border-gray-200 hover:border-gray-300'
                ]"
              >
                <div class="flex items-center space-x-3">
                  <input
                    type="radio"
                    id="admin"
                    value="admin"
                    v-model="form.role"
                    class="h-4 w-4 border-primary text-primary focus:ring-primary"
                  />
                  <div>
                    <label for="admin" class="font-medium text-gray-900 cursor-pointer">Admin</label>
                    <p class="text-sm text-gray-500">Full system access</p>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="form.errors.role" class="mt-2 text-sm text-red-600 bg-red-50 p-2 rounded-md">
              {{ form.errors.role }}
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
            <Link 
              :href="route('users.index')"
              class="inline-flex items-center px-6 py-3 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium shadow-sm hover:bg-gray-50 transition"
            >
              Cancel
            </Link>
            <Button
              type="submit"
              class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-indigo-600 to-blue-500 py-3 px-6 text-sm font-semibold text-white shadow-md hover:from-indigo-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
              :disabled="form.processing"
            >
              <UserPlus v-if="!form.processing" class="w-4 h-4 mr-2" />
              <svg v-else class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Creating...' : 'Create User' }}
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
