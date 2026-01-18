<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            User Settings
          </h1>
          <p class="text-gray-600">
            Update your profile information and password
          </p>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loadingUser" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
      <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
      <p class="mt-4 text-gray-600">Loading user data...</p>
    </div>

    <!-- Form Section -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Form -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <!-- Form Header -->
          <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-user text-white"></i>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">
                Profile Information
              </h3>
              <p class="text-sm text-gray-600">
                Update your personal details
              </p>
            </div>
          </div>

          <!-- Form Content -->
          <div class="p-6">
            <div class="space-y-6">
              <!-- Name and Email -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="name"
                    v-model="form.name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                    :class="{ 'border-red-500': errors.name }"
                    placeholder="Enter full name"
                  />
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                    {{ errors.name[0] }}
                  </p>
                </div>

                <!-- Email -->
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="email"
                    id="email"
                    v-model="form.email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                    :class="{ 'border-red-500': errors.email }"
                    placeholder="user@example.com"
                  />
                  <p v-if="errors.email" class="mt-1 text-sm text-red-600">
                    {{ errors.email[0] }}
                  </p>
                </div>
              </div>

              <!-- Password Section -->
              <div class="border-t border-gray-200 pt-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <i class="fas fa-lock text-blue-600"></i>
                  Change Password
                </h4>

                <div class="space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Current Password -->
                    <div>
                        <label for="old_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Current Password
                        </label>
                        <div class="relative">
                        <input
                            :type="showOldPassword ? 'text' : 'password'"
                            id="old_password"
                            v-model="form.old_password"
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                            :class="{ 'border-red-500': errors.old_password }"
                            placeholder="Enter current password"
                        />
                        <button
                            type="button"
                            @click="showOldPassword = !showOldPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <i :class="showOldPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                        </div>
                        <p v-if="errors.old_password" class="mt-1 text-sm text-red-600">
                        {{ errors.old_password }}
                        </p>
                    </div>
                  </div>  

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- New Password -->
                    <div>
                      <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        New Password
                      </label>
                      <div class="relative">
                        <input
                          :type="showPassword ? 'text' : 'password'"
                          id="password"
                          v-model="form.password"
                          class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                          :class="{ 'border-red-500': errors.password }"
                          placeholder="Enter new password"
                        />
                        <button
                          type="button"
                          @click="showPassword = !showPassword"
                          class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                          <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                      </div>
                      <p v-if="errors.password" class="mt-1 text-sm text-red-600">
                        {{ errors.password }}
                      </p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                      <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password
                      </label>
                      <div class="relative">
                        <input
                          :type="showConfirmPassword ? 'text' : 'password'"
                          id="password_confirmation"
                          v-model="form.password_confirmation"
                          class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                          :class="{ 'border-red-500': errors.password_confirmation }"
                          placeholder="Confirm new password"
                        />
                        <button
                          type="button"
                          @click="showConfirmPassword = !showConfirmPassword"
                          class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                          <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                      </div>
                      <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">
                        {{ errors.password_confirmation }}
                      </p>
                    </div>
                  </div>

                  <p class="text-xs text-gray-500">
                    Leave password fields empty if you don't want to change your password.
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Form Actions -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-end">
              <button
                @click="submitForm"
                :disabled="loading"
                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg font-semibold transition-colors
                      bg-blue-600 hover:bg-blue-700 text-white
                      disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-save"></i>
                {{ loading ? 'Updating...' : 'Update Profile' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <!-- User Summary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-user-circle text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Profile Summary</h3>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Avatar / School Logo -->
            <div class="flex justify-center">
              <img
                v-if="schoolLogo"
                :src="schoolLogo"
                alt="School Logo"
                class="w-24 h-24 rounded-full object-contain border shadow"
              />

              <div
                v-else
                class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600
                      flex items-center justify-center text-white font-bold text-3xl shadow-lg"
              >
                {{ getUserInitials(form.name) }}
              </div>
            </div>

            <!-- Name Preview -->
            <div class="text-center">
              <p class="text-lg font-semibold text-gray-800">
                {{ form.name || 'Not entered' }}
              </p>
              <p class="text-sm text-gray-600">
                {{ form.email || 'Not entered' }}
              </p>
            </div>

            <!-- Role Badge -->
            <div class="text-center">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                <i class="fas fa-user-shield mr-1"></i>
                {{ currentUser?.role?.role_name || 'No Role' }}
              </span>
            </div>

            <!-- Status Badge -->
            <div class="text-center">
              <span 
                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                :class="{
                  'bg-green-100 text-green-800': currentUser?.status === 'active',
                  'bg-red-100 text-red-800': currentUser?.status === 'inactive'
                }"
              >
                <span 
                  class="w-1.5 h-1.5 rounded-full mr-1.5"
                  :class="{
                    'bg-green-600': currentUser?.status === 'active',
                    'bg-red-600': currentUser?.status === 'inactive'
                  }"
                ></span>
                {{ currentUser?.status === 'active' ? 'Active' : 'Inactive' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Quick Tips -->
        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
              <i class="fas fa-info-circle text-blue-600"></i>
            </div>
            <div>
              <h4 class="font-semibold text-blue-800 mb-2">
                Password Tips
              </h4>
              <ul class="text-sm text-blue-700 space-y-1">
                <li>• Enter current password to change</li>
                <li>• New password must be 8+ characters</li>
                <li>• Confirm password must match</li>
                <li>• Leave empty to keep current password</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'
import { authStore } from '../../stores/auth'
import { useSchoolStore } from '../../stores/schoolStore'

const schoolStore = useSchoolStore()

const schoolName = computed(() => schoolStore.getSchoolName())
const schoolLogo = computed(() => schoolStore.getSchoolLogo())
const schoolEmail = computed(() => schoolStore.getSchoolEmail())

const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  role_id: null,
  status: 'active',
  old_password: '',
  password: '',
  password_confirmation: ''
})

const loading = ref(false)
const loadingUser = ref(true)
const errors = ref({})
const showOldPassword = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const currentUser = computed(() => authStore.state.user)

const getUserInitials = (name) => {
  if (!name) return 'U'
  const names = name.split(' ')
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
}

const fetchUser = async () => {
  try {
    loadingUser.value = true
    const response = await axios.get('/api/user')
    
    form.name = response.data.name
    form.email = response.data.email
    form.role_id = response.data.role_id
    form.status = response.data.status
    
  } catch (error) {
    console.error('Error fetching user:', error)
    showErrorAlert('Error', 'Failed to load user data')
  } finally {
    loadingUser.value = false
  }
}

const validateForm = () => {
  const newErrors = {}

  if (!form.name.trim()) {
    newErrors.name = ['Name is required']
  }

  if (!form.email.trim()) {
    newErrors.email = ['Email is required']
  }

  // Password validation শুধু যখন কোনো password field filled থাকবে
  const hasPasswordFields = form.old_password || form.password || form.password_confirmation

  if (hasPasswordFields) {
    if (!form.old_password) {
      newErrors.old_password = 'Current password is required'
    }
    
    if (!form.password) {
      newErrors.password = 'New password is required'
    } else if (form.password.length < 8) {
      newErrors.password = 'Password must be at least 8 characters'
    }
    
    if (!form.password_confirmation) {
      newErrors.password_confirmation = 'Please confirm your password'
    } else if (form.password !== form.password_confirmation) {
      newErrors.password_confirmation = 'Passwords do not match'
    }
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

const submitForm = async () => {
  if (!validateForm()) {
    showErrorAlert('Validation Error', 'Please fix the errors')
    return
  }

  loading.value = true

  try {
    showLoadingAlert('Updating profile...')
    
    const formData = {
      name: form.name,
      email: form.email,
      role_id: form.role_id,
      status: form.status
    }

    // শুধু password fields filled থাকলে পাঠাবে
    if (form.old_password || form.password || form.password_confirmation) {
      formData.old_password = form.old_password
      formData.password = form.password
      formData.password_confirmation = form.password_confirmation
    }

    await axios.put(`/api/users/${currentUser.value.id}/settings`, formData)

    // Update auth store
    await authStore.fetchUser()
    
    closeAlert()
    
    // Password fields clear করা
    form.old_password = ''
    form.password = ''
    form.password_confirmation = ''
    
    showSuccessAlert('Success', 'Profile updated successfully')

  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response?.status === 422) {
      const errorMsg = error.response.data.error || 'Validation failed'
      showErrorAlert('Error', errorMsg)
      errors.value = error.response.data.errors || {}
    } else {
      showErrorAlert('Error', 'Failed to update profile')
    }
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await fetchUser()
})
</script>