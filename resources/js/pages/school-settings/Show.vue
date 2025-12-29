<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <!-- Title + Buttons -->
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <!-- Title -->
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            School Details
          </h1>
          <p class="text-gray-600 mt-2">
            View complete school information
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3">
          <!-- Back Button -->
          <router-link
            :to="{ name: 'school-settings.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors"
          >
            <i class="fas fa-arrow-left"></i>
            Back to List
          </router-link>

          <!-- Edit Button -->
          <router-link
            :to="{ name: 'school-settings.edit', params: { id: schoolId } }"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors"
          >
            <i class="fas fa-edit"></i>
            Edit School
          </router-link>
        </div>
      </div>
    </div>

    <!-- Main Content Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column - School Information -->
      <div class="lg:col-span-2 space-y-8">
        <!-- School Profile Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-4 mb-6">
            <!-- Logo Display -->
            <div class="relative">
              <div 
                v-if="school.logo_url || school.logo"
                class="w-20 h-20 rounded-full overflow-hidden border-4 border-white shadow-lg"
              >
                <img 
                  :src="school.logo_url || `/assets/images/school_logo/${school.logo}`" 
                  :alt="school.school_name"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                />
              </div>
              <div 
                v-else
                class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center border-4 border-white shadow-lg"
              >
                <i class="fas fa-school text-blue-500 text-2xl"></i>
              </div>
            </div>

            <!-- School Name -->
            <div>
              <h2 class="text-2xl font-bold text-gray-800">{{ school.school_name }}</h2>
              <p class="text-gray-600">School ID: #{{ school.id }}</p>
            </div>
          </div>

          <!-- Details Grid -->
          <div class="space-y-6">
            <!-- School Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">School Name</label>
                <p class="text-gray-800 font-medium">{{ school.school_name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">School ID</label>
                <p class="text-gray-800 font-mono">{{ school.id }}</p>
              </div>
            </div>

            <!-- Address Section -->
            <div v-if="school.address">
              <label class="block text-sm font-medium text-gray-500 mb-2">Address</label>
              <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-gray-700 whitespace-pre-line">{{ school.address }}</p>
              </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Mobile Number -->
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Mobile Number</label>
                <div class="flex items-center gap-2">
                  <i class="fas fa-phone text-gray-400"></i>
                  <p class="text-gray-800">{{ school.mobile_number || 'Not provided' }}</p>
                </div>
              </div>

              <!-- Email Address -->
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                <div class="flex items-center gap-2">
                  <i class="fas fa-envelope text-gray-400"></i>
                  <p class="text-gray-800">{{ school.email || 'Not provided' }}</p>
                </div>
              </div>
            </div>

            <!-- Timestamps -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                <div class="flex items-center gap-2">
                  <i class="fas fa-calendar-plus text-gray-400"></i>
                  <p class="text-gray-800">{{ formatDate(school.created_at) }}</p>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                <div class="flex items-center gap-2">
                  <i class="fas fa-calendar-check text-gray-400"></i>
                  <p class="text-gray-800">{{ formatDate(school.updated_at) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center">
              <i class="fas fa-bolt text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Quick Actions</h3>
              <p class="text-gray-600 text-sm">Manage this school</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Edit Button -->
            <router-link
              :to="{ name: 'school-settings.edit', params: { id: schoolId } }"
              class="flex items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors group"
            >
              <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200">
                <i class="fas fa-edit text-blue-600"></i>
              </div>
              <div>
                <p class="font-medium text-gray-800">Edit School</p>
                <p class="text-sm text-gray-600">Update school information</p>
              </div>
            </router-link>

            <!-- Delete Button -->
            <button 
              @click="confirmDelete"
              :disabled="loading"
              class="flex items-center gap-3 p-4 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors group disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center group-hover:bg-red-200">
                <i class="fas fa-trash text-red-600"></i>
              </div>
              <div>
                <p class="font-medium text-gray-800">Delete School</p>
                <p class="text-sm text-gray-600">Remove this school permanently</p>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Right Column - Additional Info & Stats -->
      <div class="space-y-8">
        <!-- School Stats Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-chart-bar text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">School Information</h3>
              <p class="text-gray-600 text-sm">Complete details</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Logo Status -->
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div class="flex items-center gap-2">
                <i class="fas fa-image text-gray-400"></i>
                <span class="text-sm text-gray-600">Logo Status</span>
              </div>
              <span 
                :class="school.logo || school.logo_url ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                class="px-2 py-1 rounded-full text-xs font-medium"
              >
                {{ school.logo || school.logo_url ? 'Uploaded' : 'Not Uploaded' }}
              </span>
            </div>

            <!-- Contact Info Status -->
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div class="flex items-center gap-2">
                <i class="fas fa-phone text-gray-400"></i>
                <span class="text-sm text-gray-600">Contact Info</span>
              </div>
              <span 
                :class="school.mobile_number || school.email ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                class="px-2 py-1 rounded-full text-xs font-medium"
              >
                {{ school.mobile_number || school.email ? 'Complete' : 'Incomplete' }}
              </span>
            </div>

            <!-- Address Status -->
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div class="flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-gray-400"></i>
                <span class="text-sm text-gray-600">Address</span>
              </div>
              <span 
                :class="school.address ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                class="px-2 py-1 rounded-full text-xs font-medium"
              >
                {{ school.address ? 'Provided' : 'Missing' }}
              </span>
            </div>

            <!-- Registration Date -->
            <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
              <div class="flex items-center gap-2 mb-1">
                <i class="fas fa-calendar text-blue-500"></i>
                <span class="text-sm font-medium text-blue-700">Registration Date</span>
              </div>
              <p class="text-sm text-blue-600">{{ formatDate(school.created_at) }}</p>
            </div>

            <!-- Last Updated -->
            <div class="p-3 bg-green-50 rounded-lg border border-green-100">
              <div class="flex items-center gap-2 mb-1">
                <i class="fas fa-history text-green-500"></i>
                <span class="text-sm font-medium text-green-700">Last Updated</span>
              </div>
              <p class="text-sm text-green-600">{{ formatDate(school.updated_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Tips Card -->
        <div class="bg-yellow-50 rounded-2xl border border-yellow-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-lightbulb text-yellow-500 text-xl"></i>
            <h4 class="text-lg font-semibold text-yellow-800">Quick Tips</h4>
          </div>
          <ul class="space-y-2 text-yellow-700 text-sm">
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-yellow-500 mt-0.5"></i>
              <span>Keep school information updated</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-yellow-500 mt-0.5"></i>
              <span>Upload logo for better identification</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-yellow-500 mt-0.5"></i>
              <span>Maintain accurate contact information</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
              <span class="text-red-700">Deleting school cannot be undone</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="loading" class="fixed inset-0 bg-white bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 flex flex-col items-center">
        <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
        <p class="text-gray-700">Loading school details...</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { 
  showSuccessAlert, 
  showErrorAlert, 
  showLoadingAlert, 
  closeAlert,
  showConfirmDialog 
} from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const school = reactive({})

const schoolId = computed(() => route.params.id)

// Fetch school details
const fetchSchoolDetails = async () => {
  try {
    loading.value = true
    
    // Fetch school data
    const response = await axios.get(`/api/school-settings/${schoolId.value}`)
    
    if (response.data && response.data.success) {
      Object.assign(school, response.data.data)
    } else {
      showErrorAlert('Error', 'Failed to load school details')
      router.push({ name: 'school-settings.index' })
    }
    
  } catch (error) {
    console.error('Error fetching school details:', error)
    
    if (error.response?.status === 404) {
      showErrorAlert('Error', 'School not found')
      router.push({ name: 'school-settings.index' })
    } else {
      showErrorAlert('Error', 'Failed to load school details')
      router.push({ name: 'school-settings.index' })
    }
  } finally {
    loading.value = false
  }
}

// Confirm delete
const confirmDelete = async () => {
  const result = await showConfirmDialog(
    'Delete School?',
    'Are you sure you want to delete this school? This action cannot be undone!',
    'warning'
  )

  if (result.isConfirmed) {
    deleteSchool()
  }
}

// Delete school
const deleteSchool = async () => {
  try {
    loading.value = true
    showLoadingAlert('Deleting school...')
    
    const response = await axios.delete(`/api/school-settings/${schoolId.value}`)
    
    closeAlert()
    
    if (response.data && response.data.success) {
      await showSuccessAlert('Deleted!', 'School deleted successfully.')
      router.push({ name: 'school-settings.index' })
    } else {
      showErrorAlert('Error', 'Failed to delete school')
    }
    
  } catch (error) {
    closeAlert()
    console.error('Error deleting school:', error)
    
    if (error.response?.status === 404) {
      showErrorAlert('Error', 'School not found')
    } else if (error.response?.status === 500) {
      showErrorAlert('Error', 'Server error. Please try again later.')
    } else {
      showErrorAlert('Error', 'Failed to delete school')
    }
  } finally {
    loading.value = false
  }
}

// Format date
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Handle image error
const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNFMEU2RkYiLz48cGF0aCBkPSJNNTAuNSA0MS4xNjY3QzU0LjQ5NzIgNDEuMTY2NyA1Ny43NSAzNy45MTM5IDU3Ljc1IDMzLjkxNjdDNTcuNzUgMjkuOTE5NSA1NC40OTcyIDI2LjY2NjcgNTAuNSAyNi42NjY3QzQ2LjUwMjggMjYuNjY2NyA0My4yNSAyOS45MTk1IDQzLjI1IDMzLjkxNjdDNDMuMjUgMzcuOTEzOSA0Ni41MDI4IDQxLjE2NjcgNTAuNSA0MS4xNjY3Wk01MC41IDQ5LjMzMzNDMzcuMDQ0NSA0OS4zMzMzIDI2LjUgNTcuNzc4MiAyNi41IDY2LjY2NjdDNjYuNSA2Ni42NjY3IDc0LjUgNjYuNjY2NyA3NC41IDY2LjY2NjdDNzQuNSA1Ny43NzgyIDYzLjk1NTUgNDkuMzMzMyA1MC41IDQ5LjMzMzNaIiBmaWxsPSIjMzM2NkZGIi8+PC9zdmc+'
}

// Lifecycle
onMounted(() => {
  fetchSchoolDetails()
})
</script>

<style scoped>
/* Custom styles */
.profile-logo {
  transition: transform 0.3s ease;
}

.profile-logo:hover {
  transform: scale(1.05);
}

/* Status badges */
.status-badge {
  transition: all 0.3s ease;
}

/* Card hover effects */
.action-card {
  transition: all 0.3s ease;
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media (max-width: 1024px) {
  .grid-cols-1 {
    grid-template-columns: 1fr;
  }
  
  .lg\:col-span-2 {
    grid-column: span 1;
  }
}
</style>