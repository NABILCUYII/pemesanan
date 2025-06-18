<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
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
  role: 'user', // Set default role
});

const submit = () => {
  form.post(route('users.store'));
};
</script>

<template>
  <Head title="Create User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-md px-4 py-6 sm:max-w-lg sm:px-6 lg:max-w-xl lg:px-8">
      <Heading title="Create User" description="Add a new user to the system" />
      
      <form @submit.prevent="submit" class="space-y-6">
        <div class="space-y-2">
          <Label for="name">Name</Label>
          <Input
            id="name"
            v-model="form.name"
            type="text"
            required
          />
          <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
        </div>

        <div class="space-y-2">
          <Label for="email">Email</Label>
          <Input
            id="email"
            v-model="form.email"
            type="email"
            required
          />
          <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
        </div>

        <div class="space-y-2">
          <Label for="password">Password</Label>
          <Input
            id="password"
            v-model="form.password"
            type="password"
            required
          />
          <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
        </div>

        <div class="space-y-2">
          <Label>Role</Label>
          <div class="grid gap-2">
            <div class="flex items-center space-x-2">
              <input
                type="radio"
                id="admin"
                value="admin"
                v-model="form.role"
                class="h-4 w-4 border-primary text-primary focus:ring-primary"
              />
              <Label for="admin">Admin</Label>
            </div>
            <div class="flex items-center space-x-2">
              <input
                type="radio"
                id="user"
                value="user"
                v-model="form.role"
                class="h-4 w-4 border-primary text-primary focus:ring-primary"
              />
              <Label for="user">User</Label>
            </div>
          </div>
          <p v-if="form.errors.role" class="text-sm text-destructive">{{ form.errors.role }}</p>
        </div>

        <div class="flex justify-end">
          <Button
            type="submit"
            :disabled="form.processing"
          >
            Create User
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

