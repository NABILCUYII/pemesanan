import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    role: 'admin', // Change as needed: 'admin' or 'user'
  }),
}); 