<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Edit Subsidiary
          </h1>
          <p class="text-gray-600">
            Update subsidiary information
          </p>
        </div>
        <div class="flex items-center gap-3 mb-2">
          <router-link 
            :to="{ name: 'subsidiaries.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-semibold"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Subsidiaries
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pageLoading" class="flex items-center justify-center py-12">
      <div class="text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
        <p class="text-gray-600">Loading subsidiary...</p>
      </div>
    </div>

    <!-- Form Section -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Form -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <!-- Form Header -->
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
              Subsidiary Information
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              Update the information about the subsidiary
            </p>
          </div>

          <!-- Form Content -->
          <div class="p-6">
            <div class="space-y-6">
              <!-- Subsidiary Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                  Subsidiary Name <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                  :class="{ 'border-red-500': errors.name }"
                  placeholder="Enter subsidiary name"
                />
                <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                  {{ errors.name[0] }}
                </p>
              </div>
            </div>
          </div>
          
          <!-- Form Actions -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-between gap-3">
              <!-- Delete Button -->
              <button
                @click="handleDelete"
                :disabled="deleteLoading"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                      bg-red-600 hover:bg-red-700 text-white
                      disabled:opacity-50 disabled:cursor-not-allowed
                      w-full sm:w-auto text-center"
              >
                <i v-if="deleteLoading" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-trash"></i>
                {{ deleteLoading ? 'Deleting...' : 'Delete Subsidiary' }}
              </button>

              <div class="flex flex-col sm:flex-row gap-3">
                <!-- Cancel Button -->
                <router-link
                  :to="{ name: 'subsidiaries.index' }"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                        bg-gray-600 hover:bg-gray-700 text-white
                        w-full sm:w-auto text-center"
                >
                  <i class="fas fa-times"></i>
                  Cancel
                </router-link>

                <!-- Update Button -->
                <button
                  @click="submitForm"
                  :disabled="loading"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                        bg-blue-600 hover:bg-blue-700 text-white
                        disabled:opacity-50 disabled:cursor-not-allowed
                        w-full sm:w-auto text-center"
                >
                  <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                  <i v-else class="fas fa-save"></i>
                  {{ loading ? 'Updating...' : 'Update Subsidiary' }}
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <!-- Status Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-flag text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Subsidiary Status</h3>
              <p class="text-gray-600 text-sm">Update subsidiary status</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Status -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Status <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.status }"
              >
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <p v-if="errors.status" class="text-red-500 text-sm">{{ errors.status[0] }}</p>
            </div>

            <!-- Status Badge Preview -->
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-600 mb-2">Status Preview:</p>
              <span 
                :class="[
                  'px-3 py-1 rounded-full text-xs font-medium',
                  form.status === 'active' 
                    ? 'bg-green-100 text-green-800' 
                    : form.status === 'inactive'
                    ? 'bg-red-100 text-red-800'
                    : 'bg-gray-100 text-gray-800'
                ]"
              >
                {{ form.status || 'Not selected' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6 mb-6">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
              <i class="fas fa-info-circle text-blue-600"></i>
            </div>
            <div>
              <h4 class="font-semibold text-blue-800 mb-2">
                Information
              </h4>
              <ul class="text-sm text-blue-700 space-y-1">
                <li>• Created: {{ formatDate(originalData.created_at) }}</li>
                <li>• Last Updated: {{ formatDate(originalData.updated_at) }}</li>
                <li>• Subsidiary ID: <span class="font-mono">{{ originalData.id }}</span></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Tips -->
        <div class="bg-green-50 rounded-2xl border border-green-200 p-6">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
              <i class="fas fa-lightbulb text-green-600"></i>
            </div>
            <div>
              <h4 class="font-semibold text-green-800 mb-2">
                Update Tips
              </h4>
              <ul class="text-sm text-green-700 space-y-1">
                <li>• Ensure subsidiary name is unique</li>
                <li>• Set appropriate status</li>
                <li>• Review changes before saving</li>
                <li>• Inactive subsidiaries won't appear in dropdowns</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, showConfirmDialog, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const subsidiaryId = route.params.id

const form = reactive({
  name: '',
  status: 'active'
})

const originalData = ref({})
const loading = ref(false)
const deleteLoading = ref(false)
const pageLoading = ref(true)
const errors = ref({})

const fetchSubsidiary = async () => {
  pageLoading.value = true
  try {
    const response = await axios.get(`/api/subsidiaries/${subsidiaryId}`)
    const data = response.data.data
    
    originalData.value = { ...data }
    Object.assign(form, {
      name: data.name,
      status: data.status
    })
  } catch (error) {
    console.error('Error fetching subsidiary:', error)
    showErrorAlert('Error', 'Failed to fetch subsidiary data')
    router.push({ name: 'subsidiaries.index' })
  } finally {
    pageLoading.value = false
  }
}

const validateForm = () => {
  const newErrors = {}
  if (!form.name.trim()) newErrors.name = ['Subsidiary name is required']
  if (!form.status) newErrors.status = ['Status is required']
  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

const submitForm = async () => {
  if (!validateForm()) {
    showErrorAlert('Validation Error', 'Please fix the errors in the form')
    return
  }

  loading.value = true
  errors.value = {}

  try {
    showLoadingAlert('Updating subsidiary...')
    await axios.put(`/api/subsidiaries/${subsidiaryId}`, form)
    closeAlert()
    
    showSuccessAlert('Success!', 'Subsidiary updated successfully.')
    router.push({ 
      name: 'subsidiaries.index', 
      query: { updated: 'true' }  
    })

  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors')
    } else {
      console.error('Error updating subsidiary:', error)
      showErrorAlert('Error', error.response?.data?.message || 'Failed to update subsidiary. Please try again.')
    }
  }
}

const handleDelete = async () => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You are about to delete this subsidiary. This action cannot be undone!',
    'warning',
    'Yes, delete it!'
  )

  if (result.isConfirmed) {
    try {
      deleteLoading.value = true
      showLoadingAlert('Deleting subsidiary...')

      await axios.delete(`/api/subsidiaries/${subsidiaryId}`)
      closeAlert()
      
      showSuccessAlert('Deleted!', 'Subsidiary has been deleted successfully.')
      router.push({ name: 'subsidiaries.index' })

    } catch (error) {
      closeAlert()
      deleteLoading.value = false
      console.error('Error deleting subsidiary:', error)
      showErrorAlert('Error', 'Failed to delete subsidiary. Please try again.')
    }
  }
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  fetchSubsidiary()
})
</script>