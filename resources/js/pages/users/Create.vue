<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Create New User
          </h1>
          <p class="text-gray-600">
            Add a new system user with role and permissions
          </p>
        </div>
        <div class="flex items-center gap-3 mb-2">
          <router-link 
            :to="{ name: 'users.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-semibold"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Users
          </router-link>
        </div>
      </div>
    </div>

    <!-- Form Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
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
                User Information
              </h3>
              <p class="text-sm text-gray-600">
                Fill in the user details and credentials
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

              <!-- User Role with v-select -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700">
                    User Role <span class="text-red-500">*</span>
                  </label>
                  <v-select
                    v-model="form.role_id"
                    :options="roles"
                    :filterable="true"
                    :searchable="true"
                    label="role_name"
                    :reduce="role => role.id"
                    placeholder="Search or select role..."
                    :class="{ 'border-red-500': errors.role_id }"
                  >
                    <template #option="option">
                      <div>
                        <div class="font-medium text-gray-800">
                          <span class="text-blue-600 font-semibold mr-2 text-xs">#{{ option.id }}</span>
                          {{ option.role_name }}
                        </div>
                        <div v-if="option.role_details && option.role_details.length > 0" class="text-xs text-gray-500 mt-1">
                          {{ option.role_details.length }} permission(s)
                        </div>
                      </div>
                    </template>
                    <template #no-options="{ search }">
                      <div class="p-3 text-center text-gray-500 text-sm">
                        <template v-if="search">
                          <i class="fas fa-search mb-2 text-lg"></i>
                          <p>No roles found for "{{ search }}"</p>
                        </template>
                        <template v-else>
                          <i class="fas fa-user-shield mb-2 text-lg"></i>
                          <p>Type to search roles</p>
                        </template>
                      </div>
                    </template>
                  </v-select>
                  <p v-if="errors.role_id" class="mt-1 text-sm text-red-600">
                    {{ errors.role_id[0] }}
                  </p>
                </div>

                <!-- Status -->
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                    :class="{ 'border-red-500': errors.status }"
                  >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                  <p v-if="errors.status" class="mt-1 text-sm text-red-600">
                    {{ errors.status[0] }}
                  </p>
                </div>
              </div>

              <!-- Password Section -->
              <div class="border-t border-gray-200 pt-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <i class="fas fa-lock text-blue-600"></i>
                  Password Setup
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                      Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <input
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        v-model="form.password"
                        class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                        :class="{ 'border-red-500': errors.password }"
                        placeholder="Enter password"
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
                      {{ errors.password[0] }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                      Minimum 8 characters required
                    </p>
                  </div>
                </div>

                <!-- Password Strength Indicator -->
                <div v-if="form.password" class="mt-4">
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm font-medium text-gray-700">Password Strength:</span>
                    <span 
                      class="text-sm font-semibold"
                      :class="{
                        'text-red-600': passwordStrength === 'Weak',
                        'text-yellow-600': passwordStrength === 'Medium',
                        'text-green-600': passwordStrength === 'Strong'
                      }"
                    >
                      {{ passwordStrength }}
                    </span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="h-2 rounded-full transition-all duration-300"
                      :class="{
                        'bg-red-500 w-1/3': passwordStrength === 'Weak',
                        'bg-yellow-500 w-2/3': passwordStrength === 'Medium',
                        'bg-green-500 w-full': passwordStrength === 'Strong'
                      }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Form Actions -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-end gap-3">
              <!-- Cancel Button -->
              <router-link
                :to="{ name: 'users.index' }"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                      bg-gray-600 hover:bg-gray-700 text-white
                      w-full sm:w-auto text-center"
              >
                <i class="fas fa-times"></i>
                Cancel
              </router-link>

              <!-- Create Button -->
              <button
                @click="submitForm"
                :disabled="loading || passwordMismatch"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                      bg-blue-600 hover:bg-blue-700 text-white
                      disabled:opacity-50 disabled:cursor-not-allowed
                      w-full sm:w-auto text-center"
              >
                <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-save"></i>
                {{ loading ? 'Creating...' : 'Create User' }}
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
              <h3 class="text-xl font-semibold text-gray-800">User Summary</h3>
              <p class="text-gray-600 text-sm">Account overview</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Name Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Full Name
              </label>
              <div class="p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-800 font-medium">
                  {{ form.name || 'Not entered' }}
                </p>
              </div>
            </div>

            <!-- Email Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Email
              </label>
              <div class="p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-800">
                  {{ form.email || 'Not entered' }}
                </p>
              </div>
            </div>

            <!-- Role Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Assigned Role
              </label>
              <div class="p-3 bg-gray-50 rounded-lg">
                <span 
                  v-if="selectedRole"
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                >
                  <i class="fas fa-user-shield mr-1"></i>
                  {{ selectedRole.role_name }}
                </span>
                <span v-else class="text-sm text-gray-500">
                  No role selected
                </span>
              </div>
            </div>

            <!-- Status Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Account Status
              </label>
              <div class="p-3 bg-gray-50 rounded-lg">
                <span 
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="{
                    'bg-green-100 text-green-800': form.status === 'active',
                    'bg-red-100 text-red-800': form.status === 'inactive'
                  }"
                >
                  <span 
                    class="w-1.5 h-1.5 rounded-full mr-1.5"
                    :class="{
                      'bg-green-600': form.status === 'active',
                      'bg-red-600': form.status === 'inactive'
                    }"
                  ></span>
                  {{ form.status === 'active' ? 'Active' : 'Inactive' }}
                </span>
              </div>
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
                Quick Tips
              </h4>
              <ul class="text-sm text-blue-700 space-y-1">
                <li>• All fields marked with * are required</li>
                <li>• Use a strong password with minimum 8 characters</li>
                <li>• Email must be unique in the system</li>
                <li>• Select appropriate role for permissions</li>
                <li>• Passwords must match to create account</li>
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
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()

// Form data
const form = reactive({
  name: '',
  email: '',
  role_id: null,
  password: '',
  status: 'active'
})

// Dropdown data
const roles = ref([])

// UI state
const loading = ref(false)
const errors = ref({})
const showPassword = ref(false)
const showConfirmPassword = ref(false)

// Computed properties
const selectedRole = computed(() => {
  return roles.value.find(role => role.id === form.role_id) || null
})


const passwordStrength = computed(() => {
  const password = form.password
  if (!password) return ''
  
  let strength = 0
  
  // Length check
  if (password.length >= 8) strength++
  if (password.length >= 12) strength++
  
  // Character variety checks
  if (/[a-z]/.test(password)) strength++
  if (/[A-Z]/.test(password)) strength++
  if (/[0-9]/.test(password)) strength++
  if (/[^a-zA-Z0-9]/.test(password)) strength++
  
  if (strength <= 2) return 'Weak'
  if (strength <= 4) return 'Medium'
  return 'Strong'
})

// Fetch roles
const fetchRoles = async () => {
  try {
    const response = await axios.get('/api/users/roles')
    roles.value = response.data
    console.log('Roles loaded:', roles.value)
  } catch (error) {
    console.error('Error fetching roles:', error)
    showErrorAlert('Error', 'Failed to load user roles')
  }
}

// Form validation
const validateForm = () => {
  const newErrors = {}

  if (!form.name.trim()) {
    newErrors.name = ['Name is required']
  }

  if (!form.email.trim()) {
    newErrors.email = ['Email is required']
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    newErrors.email = ['Please enter a valid email address']
  }

  if (!form.role_id) {
    newErrors.role_id = ['User role is required']
  }

  if (!form.password) {
    newErrors.password = ['Password is required']
  } else if (form.password.length < 8) {
    newErrors.password = ['Password must be at least 8 characters']
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

// Submit form
const submitForm = async () => {
  if (!validateForm()) {
    showErrorAlert('Validation Error', 'Please fix the errors in the form')
    return
  }

  loading.value = true

  try {
    showLoadingAlert('Creating user...')
    
    const formData = {
      name: form.name.trim(),
      email: form.email.trim(),
      role_id: form.role_id,
      password: form.password,
      status: form.status
    }

    await axios.post('/api/users', formData)

    closeAlert()
    router.push({ 
      name: 'users.index',
      query: { created: 'true' }
    })

  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors')
    } else {
      console.error('Error creating user:', error)
      showErrorAlert('Error', error.response?.data?.message || 'Failed to create user.')
    }
  }
}

// Initialize
onMounted(() => {
  fetchRoles()
})
</script>
