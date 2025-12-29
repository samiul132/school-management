<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Edit Designation
          </h1>
          <p class="text-gray-600">
            Update Designation information
          </p>
        </div>
        <div class="flex items-center gap-3 mb-2">
          <router-link 
            :to="{ name: 'designations.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-semibold"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Designation
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pageLoading" class="flex items-center justify-center py-12">
      <div class="text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
        <p class="text-gray-600">Loading Designation...</p>
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
              Designation Information
            </h3>
          </div>

          <!-- Form Content -->
          <div class="p-6">
            <div class="space-y-6">
              <!-- Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                  Name <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                  :class="{ 'border-red-500': errors.name }"
                  placeholder="Enter Designation Name"
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
                {{ deleteLoading ? 'Deleting...' : 'Delete Designation' }}
              </button>

              <div class="flex flex-col sm:flex-row gap-3">
                <!-- Cancel Button -->
                <router-link
                  :to="{ name: 'designations.index' }"
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
                  {{ loading ? 'Updating...' : 'Update Designation' }}
                </button>
              </div>
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
const designationId = route.params.id

const form = reactive({
  name: ''
})

const originalData = ref({})
const loading = ref(false)
const deleteLoading = ref(false)
const pageLoading = ref(true)
const errors = ref({})

const fetchDesignation = async () => {
  pageLoading.value = true
  try {
    const response = await axios.get(`/api/designations/${designationId}`)
    const data = response.data.data
    
    originalData.value = { ...data }
    Object.assign(form, {
      name: data.name,
      status: data.status
    })
  } catch (error) {
    console.error('Error fetching Designation:', error)
    showErrorAlert('Error', 'Failed to fetch Designation data')
    router.push({ name: 'designations.index' })
  } finally {
    pageLoading.value = false
  }
}

const validateForm = () => {
  const newErrors = {}
  if (!form.name.trim()) newErrors.name = ['Designation name is required']
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
    showLoadingAlert('Updating Designation...')
    await axios.put(`/api/designations/${designationId}`, form)
    closeAlert()
    
    showSuccessAlert('Success!', 'Designation updated successfully.')
    router.push({ 
      name: 'designations.index', 
      query: { updated: 'true' }  
    })

  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors')
    } else {
      console.error('Error updating Designation:', error)
      showErrorAlert('Error', error.response?.data?.message || 'Failed to update Designation. Please try again.')
    }
  }
}

const handleDelete = async () => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You are about to delete this Designation. This action cannot be undone!',
    'warning',
    'Yes, delete it!'
  )

  if (result.isConfirmed) {
    try {
      deleteLoading.value = true
      showLoadingAlert('Deleting Designation...')

      await axios.delete(`/api/designations/${designationId}`)
      closeAlert()
      
      showSuccessAlert('Deleted!', 'Designation has been deleted successfully.')
      router.push({ name: 'designations.index' })

    } catch (error) {
      closeAlert()
      deleteLoading.value = false
      console.error('Error deleting Designation:', error)
      showErrorAlert('Error', 'Failed to delete Designation. Please try again.')
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
  fetchDesignation()
})
</script>