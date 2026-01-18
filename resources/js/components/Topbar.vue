<template>
  <div>
    <!-- Toast Notification -->
    <div 
      id="toast" 
      class="fixed top-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg opacity-0 transition-opacity duration-300 z-50"
    ></div>

    <!-- Topbar -->
    <div class="sticky top-5 flex flex-wrap md:flex-nowrap justify-between items-center gap-2 p-3 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-lg transition-all duration-500 z-40 w-auto">
      <!-- Mobile Menu Button - Always show on mobile -->
      <button
        @click="toggleSidebar"
        class="md:hidden w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 transition-all duration-300 shadow-sm hover:shadow-md shrink-0"
      >
        <svg class="w-6 h-6 text-gray-600 dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <!-- Desktop Menu Button - Show only when sidebar is closed -->
      <button
        v-if="!sidebarOpen && !isMobile"
        @click="toggleSidebar"
        class="hidden md:flex w-9 h-9 items-center justify-center rounded-lg bg-gray-100  hover:bg-gray-200 transition-all duration-300 shadow-sm hover:shadow-md shrink-0 cursor-pointer"
      >
        <svg class="w-6 h-6 text-gray-600 dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <!-- School Logo and Name -->
      <div class="flex items-center gap-2">
        <!-- School Name and Info -->
        <div class="hidden md:block">
          <h2 class="text-lg font-bold text-gray-900 leading-tight truncate max-w-xs">
            {{ schoolName }}
          </h2>
        </div>
        
        <!-- Mobile School Name Only -->
        <div class="md:hidden">
          <h2 class="text-sm font-semibold text-gray-900 truncate max-w-[150px]">
            {{ schoolName }}
          </h2>
        </div>
      </div>

      <!-- Search -->
      <div class="flex items-center justify-center md:justify-end flex-1 relative w-full md:w-auto">
        <div
          class="w-full md:w-36 lg:w-48 pl-4 pr-4 py-2 text-sm rounded-full 
                bg-gray-100 border border-gray-300 dark:border-gray-600
                text-gray-700 font-semibold text-center"
        >
          SMS Balance : <span class="text-gray-600">{{schoolSmsBalanace}} BDT</span>
        </div>
      </div>

      <!-- Right Section -->
      <div class="flex items-center gap-2 md:gap-3 shrink-0">
        <!-- Notifications -->
        <div class="relative">
          <button
            ref="notificationsButtonRef"
            @click="toggleNotifications"
            class="relative p-2 rounded-full bg-gray-100 hover:bg-blue-100 dark:hover:bg-blue-900 transition-all duration-300 shadow-sm hover:shadow-md cursor-pointer"
            title="Notifications"
          >
            <svg class="w-5 h-5 dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center shadow-md">2</span>
          </button>

          <!-- Notifications Dropdown -->
          <transition name="dropdown">
            <div 
              v-if="notificationsOpen"
              ref="notificationsDropdownRef"
              class="absolute right-0 mt-3 w-72 md:w-80 bg-white rounded-xl shadow-2xl overflow-hidden z-50"
            >
              <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 font-semibold dark:text-gray-100 bg-gray-50 dark:bg-gray-900">
                Notifications
              </div>
              <div class="max-h-64 overflow-y-auto">
                <div
                  v-for="notif in notifications"
                  :key="notif.id"
                  class="flex items-start gap-3 p-3 hover:bg-blue-50 cursor-pointer transition-all"
                >
                  <img 
                    :src="notif.avatar" 
                    class="w-10 h-10 rounded-full object-cover shrink-0" 
                    :alt="notif.name" 
                  />
                  <div class="min-w-0">
                    <p class="text-sm font-medium dark:text-gray-900">{{ notif.name }}</p>
                    <p class="text-xs dark:text-gray-900 truncate">{{ notif.message }}</p>
                    <span class="text-[11px] dark:text-gray-900">{{ notif.time }}</span>
                  </div>
                </div>
              </div>
              <div class="px-4 py-2 text-center text-sm text-gray-600 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 transition-all">
                View All Notifications
              </div>
            </div>
          </transition>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative">
          <button
            ref="profileButtonRef"
            @click="toggleProfile"
            :class="['flex items-center transition-all duration-300 rounded-full shrink-0', profileOpen ? 'ring-2 ring-blue-400' : '']"
            title="Profile"
          >
            <!-- School Logo (Highest Priority) -->
            <img
              v-if="schoolLogo"
              :src="schoolLogo"
              @error="handleImageError"
              class="w-10 h-10 rounded-full object-cover border border-gray-300 shadow-sm hover:scale-105 transition-all duration-300 cursor-pointer"
              alt="School Logo"
            />

            <!-- Fallback: User Initials -->
            <div
              v-else-if="user"
              class="w-10 h-10 rounded-full bg-linear-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-sm hover:scale-105 transition-all duration-300 cursor-pointer"
            >
              {{ getUserInitials(user.name) }}
            </div>

            <!-- Fallback: Default Icon -->
            <div
              v-else
              class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center shadow-sm"
            >
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
          </button>

          <!-- Profile Dropdown -->
          <transition name="dropdown">
            <div 
              v-if="profileOpen"
              ref="profileDropdownRef"
              class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl overflow-hidden z-50"
            >
              <div v-if="user" class="p-3 border-b border-gray-200 dark:border-gray-700">
                <div class="font-medium dark:text-gray-900">{{ user.name }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</div>
              </div>
              <div v-else class="p-3 border-b dark:border-gray-700">
                <div class="font-medium dark:text-gray-900">Guest User</div>
              </div>
              
              <router-link 
                :to="{ name: 'user.settings' }"
                class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all dark:text-gray-900"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Settings
              </router-link>
              <hr class="border-gray-200 dark:border-gray-700 my-1" />
              <a 
                @click.prevent="handleLogout" 
                href="#" 
                class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-400 transition-all cursor-pointer"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
              </a>
            </div>
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { authStore } from '../stores/auth';
import { useSchoolStore } from '@/stores/schoolStore'; 
import { useRouter } from 'vue-router';

export default {
  name: 'Topbar',
  props: {
    sidebarOpen: {
      type: Boolean,
      required: true
    },
    isMobile: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:sidebarOpen'],
  setup(props, { emit }) {
    const router = useRouter();
    const schoolStore = useSchoolStore();
    const notificationsOpen = ref(false);
    const profileOpen = ref(false);
    const searchActive = ref(false);

    const notificationsButtonRef = ref(null);
    const notificationsDropdownRef = ref(null);
    const profileButtonRef = ref(null);
    const profileDropdownRef = ref(null);

    const user = computed(() => authStore.state.user);

    // School data computed properties
    const schoolName = computed(() => schoolStore.getSchoolName());
    const schoolLogo = computed(() => schoolStore.getSchoolLogo());
    const schoolEmail = computed(() => schoolStore.getSchoolEmail());
    const schoolAddress = computed(() => schoolStore.getSchoolAddress());
    const schoolSmsBalanace = computed(() => schoolStore.getSchoolSmsBanalce());
    const schoolLoading = computed(() => schoolStore.loading);

    const notifications = [
      {
        id: 1,
        name: 'Denzel Washington',
        message: 'Sent you a message — "Hey, are you ...',
        time: '1h ago',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
      },
      {
        id: 2,
        name: 'Leonardo DiCaprio',
        message: 'Commented on your post.',
        time: '3h ago',
        avatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
      }
    ];

    const getUserInitials = (name) => {
      if (!name) return 'U';
      const names = name.split(' ');
      if (names.length >= 2) {
        return (names[0][0] + names[1][0]).toUpperCase();
      }
      return name.substring(0, 2).toUpperCase();
    };

    const toggleSidebar = () => {
      emit('update:sidebarOpen', !props.sidebarOpen);
    };

    const toggleNotifications = () => {
      notificationsOpen.value = !notificationsOpen.value;
      profileOpen.value = false;
    };

    const toggleProfile = () => {
      profileOpen.value = !profileOpen.value;
      notificationsOpen.value = false;
    };

    const handleLogout = async () => {
      try {
        schoolStore.clearSettings(); 
        
        await authStore.logout();
        
        router.push('/login');
        
        const toast = document.getElementById('toast');
        toast.textContent = 'Logged out successfully';
        toast.classList.add('opacity-100');
        setTimeout(() => toast.classList.remove('opacity-100'), 3000);
      } catch (error) {
        console.error('Logout failed:', error);
      }
    };

    const handleImageError = (event) => {
      event.target.src = '/assets/default-logo.png';
    };
    
    const handleClickOutside = (event) => {
      if (notificationsOpen.value) {
        if (notificationsButtonRef.value && !notificationsButtonRef.value.contains(event.target) &&
            notificationsDropdownRef.value && !notificationsDropdownRef.value.contains(event.target)) {
          notificationsOpen.value = false;
        }
      }

      if (profileOpen.value) {
        if (profileButtonRef.value && !profileButtonRef.value.contains(event.target) &&
            profileDropdownRef.value && !profileDropdownRef.value.contains(event.target)) {
          profileOpen.value = false;
        }
      }
    };

    onMounted(async () => {
      document.addEventListener('click', handleClickOutside);
      
      if (authStore.state.isAuthenticated && !authStore.state.user) {
        try {
          await authStore.fetchUser();
        } catch (error) {
          console.error('Failed to fetch user:', error);
        }
      }

      if (!schoolStore.settings) {
        try {
          await schoolStore.fetchSchoolSettings();
        } catch (error) {
          console.error('Failed to fetch school settings:', error);
        }
      }

      const smsBalanceInterval = setInterval(async () => {
        if (authStore.state.isAuthenticated) {
          try {
            await schoolStore.fetchSchoolSettings();
          } catch (error) {
            console.error('Failed to refresh SMS balance:', error);
          }
        }
      }, 30000); 

      onUnmounted(() => {
        clearInterval(smsBalanceInterval);
      });
    });

    // onMounted(async () => {
    //   document.addEventListener('click', handleClickOutside);
      
    //   if (authStore.state.isAuthenticated && !authStore.state.user) {
    //     try {
    //       await authStore.fetchUser();
    //     } catch (error) {
    //       console.error('Failed to fetch user:', error);
    //     }
    //   }

    //   if (!schoolStore.settings) {
    //     try {
    //       await schoolStore.fetchSchoolSettings();
    //     } catch (error) {
    //       console.error('Failed to fetch school settings:', error);
    //     }
    //   }

    // });

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside);
    });

    return {
      notificationsOpen,
      profileOpen,
      searchActive,
      notifications,
      notificationsButtonRef,
      notificationsDropdownRef,
      profileButtonRef,
      profileDropdownRef,
      user,
      schoolName,
      schoolLogo,
      schoolEmail,
      schoolAddress,
      schoolSmsBalanace,
      schoolLoading,
      toggleSidebar,
      toggleNotifications,
      toggleProfile,
      handleLogout,
      handleImageError,
      getUserInitials
    };
  }
};
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>