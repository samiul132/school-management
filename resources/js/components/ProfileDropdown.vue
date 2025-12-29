<template>
  <div class="relative">
    <button
      @click="open = !open"
      :class="['flex items-center transition-all duration-300 rounded-full', open ? 'ring-2 ring-blue-400' : '']"
    >
      <img
        src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&q=80"
        class="w-10 h-10 object-cover rounded-full shadow-sm hover:shadow-md hover:scale-105 transition-all duration-300"
        alt="User"
      />
    </button>

    <div v-if="open" class="absolute right-0 mt-3 w-56 bg-white text-black rounded-xl shadow-2xl overflow-hidden z-50">
      <div class="p-3 border-b border-black/10">
        <div class="font-medium">Denzel Washington</div>
        <div class="text-xs text-black/70">DevOps Engineer</div>
      </div>

      <RouterLink to="/profile" class="flex items-center px-4 py-2 hover:bg-black/10 transition-all">
        <UserIcon class="w-4 h-4 mr-2" /> Profile
      </RouterLink>
      <button class="w-full text-left flex items-center px-4 py-2 hover:bg-black/10 transition-all">
        <UserPlusIcon class="w-4 h-4 mr-2" /> Add Account
      </button>
      <button class="w-full text-left flex items-center px-4 py-2 hover:bg-black/10 transition-all">
        <LockIcon class="w-4 h-4 mr-2" /> Reset Password
      </button>
      <button class="w-full text-left flex items-center px-4 py-2 hover:bg-black/10 transition-all">
        <HelpCircleIcon class="w-4 h-4 mr-2" /> Help
      </button>
      <hr class="border-black/10 my-1" />
      <button class="w-full text-left flex items-center px-4 py-2 hover:bg-black/10 text-red-400 transition-all">
        <LogOutIcon class="w-4 h-4 mr-2" /> Logout
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { User as UserIcon, UserPlus as UserPlusIcon, Lock as LockIcon, HelpCircle as HelpCircleIcon, LogOut as LogOutIcon } from 'lucide-vue-next'

const open = ref(false)
</script>



No Need