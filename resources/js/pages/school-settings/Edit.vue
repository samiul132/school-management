<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <!-- Title + Buttons -->
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <!-- Title -->
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Edit School
          </h1>
          <p class="text-gray-600 mt-2">
            Update your school information
          </p>
        </div>

        <!-- Back Button -->
        <div class="mb-6">
          <router-link
            :to="{ name: 'school-settings.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Settings
          </router-link>
        </div>
      </div>
    </div>

    <!-- Main Form Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column - School Information -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Basic Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-school text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">School Information</h3>
              <p class="text-gray-600 text-sm">Update school details</p>
            </div>
          </div>

          <div class="space-y-6">
            <!-- School Name -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                School Name <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                v-model="form.school_name"
                placeholder="Enter school name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                :class="{ 'border-red-500': errors.school_name }"
              >
              <p v-if="errors.school_name" class="text-red-500 text-sm">{{ errors.school_name[0] }}</p>
            </div>

            <!-- Address -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Address
              </label>
              <textarea
                v-model="form.address"
                rows="3"
                placeholder="Enter school address"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500 resize-none"
                :class="{ 'border-red-500': errors.address }"
              ></textarea>
              <p v-if="errors.address" class="text-red-500 text-sm">{{ errors.address[0] }}</p>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Mobile Number -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                  Mobile Number
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-phone text-gray-400"></i>
                  </div>
                  <input 
                    type="tel" 
                    v-model="form.mobile_number"
                    placeholder="01XXXXXXXXX"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                    :class="{ 'border-red-500': errors.mobile_number }"
                  >
                </div>
                <p v-if="errors.mobile_number" class="text-red-500 text-sm">{{ errors.mobile_number[0] }}</p>
              </div>

              <!-- Email -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                  Email Address
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                  </div>
                  <input 
                    type="email" 
                    v-model="form.email"
                    placeholder="school@example.com"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                    :class="{ 'border-red-500': errors.email }"
                  >
                </div>
                <p v-if="errors.email" class="text-red-500 text-sm">{{ errors.email[0] }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Logo Upload Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-image text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">School Logo</h3>
              <p class="text-gray-600 text-sm">Update school logo</p>
            </div>
          </div>

          <div class="space-y-6">
            <!-- Dropify Logo Upload -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                School Logo
              </label>
              <input 
                type="file" 
                ref="logoInput"
                class="dropify" 
                data-height="200"
                data-max-file-size="2M"
                data-allowed-file-extensions="jpg jpeg png svg"
                @change="handleLogoUpload"
                accept="image/*"
              />
              <p class="text-xs text-gray-500 mt-1">
                Recommended size: 300x300px. Max file size: 2MB. Formats: JPG, PNG, SVG
              </p>
              <p v-if="errors.logo" class="text-red-500 text-sm">{{ errors.logo[0] }}</p>
            </div>

            <!-- Current Logo & Remove Option -->
            <div v-if="existingLogo || form.logoPreview" class="mt-4">
              <p class="text-sm font-medium text-gray-700 mb-2">Logo Preview:</p>
              <div class="flex items-center gap-4">
                <div class="w-32 h-32 rounded-lg overflow-hidden border-4 border-white shadow-lg">
                  <img :src="logoPreviewUrl" alt="Logo Preview" class="w-full h-full object-cover">
                </div>
                <div class="text-sm text-gray-600">
                  <p class="font-medium mb-1">Logo Details:</p>
                  <p v-if="logoFileSize">• Size: {{ logoFileSize }}</p>
                  <p v-if="logoDimensions">• Dimensions: {{ logoDimensions }}</p>
                  <p v-if="logoFormat">• Format: {{ logoFormat }}</p>
                  
                  <!-- Remove Logo Button -->
                  <button 
                    v-if="existingLogo || form.logo"
                    @click="removeLogo"
                    class="mt-2 text-red-600 hover:text-red-800 text-xs font-medium flex items-center gap-1"
                  >
                    <i class="fas fa-trash"></i>
                    Remove Logo
                  </button>
                </div>
              </div>
            </div>

            <!-- No Logo Message -->
            <div v-if="!existingLogo && !form.logoPreview && !form.logo" class="mt-4 p-4 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-600 text-center">
                <i class="fas fa-image text-gray-400 mr-2"></i>
                No logo uploaded yet
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Actions & Tips -->
      <div class="space-y-8">
        <!-- Form Actions Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-green-500 flex items-center justify-center">
              <i class="fas fa-save text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Update Settings</h3>
              <p class="text-gray-600 text-sm">Save your changes</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Required Fields Info -->
            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
              <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-circle text-yellow-500 mt-0.5"></i>
                <div>
                  <p class="text-sm font-medium text-yellow-800 mb-1">Required Information</p>
                  <ul class="text-xs text-yellow-700 space-y-1">
                    <li>✓ School Name is required</li>
                    <li>✓ Other fields are optional</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="space-y-3">
              <router-link
                :to="{ name: 'school-settings.index' }"
                class="inline-flex items-center justify-center gap-2 w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg font-semibold transition-colors"
              >
                <i class="fas fa-times"></i>
                Cancel
              </router-link>

              <button 
                @click="submitForm"
                :disabled="loading"
                class="inline-flex items-center justify-center gap-2 w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-save"></i>
                {{ loading ? 'Updating...' : 'Update Settings' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Preview Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center">
              <i class="fas fa-eye text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Settings Preview</h3>
              <p class="text-gray-600 text-sm">Preview your changes</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- School Logo Preview -->
            <div class="flex justify-center">
              <div 
                v-if="logoPreviewUrl" 
                class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-lg"
              >
                <img :src="logoPreviewUrl" alt="School Logo" class="w-full h-full object-cover">
              </div>
              <div 
                v-else 
                class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center border-4 border-white shadow-lg"
              >
                <i class="fas fa-school text-blue-500 text-3xl"></i>
              </div>
            </div>

            <!-- School Info Preview -->
            <div class="space-y-3">
              <div>
                <p class="text-xs text-gray-500">School Name</p>
                <p class="font-medium text-gray-800 truncate">{{ form.school_name || 'Not set' }}</p>
              </div>
              
              <div>
                <p class="text-xs text-gray-500">Address</p>
                <p class="text-sm text-gray-700 truncate">{{ form.address || 'Not set' }}</p>
              </div>
              
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <p class="text-xs text-gray-500">Mobile</p>
                  <p class="text-sm text-gray-700">{{ form.mobile_number || 'Not set' }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Email</p>
                  <p class="text-sm text-gray-700 truncate">{{ form.email || 'Not set' }}</p>
                </div>
              </div>

              <!-- Last Updated -->
              <div v-if="schoolData.updated_at" class="pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500">Last Updated</p>
                <p class="text-sm text-gray-700">{{ formatDate(schoolData.updated_at) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tips Card -->
        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-lightbulb text-blue-500 text-xl"></i>
            <h4 class="text-lg font-semibold text-blue-800">Update Tips</h4>
          </div>
          <ul class="space-y-2 text-blue-700 text-sm">
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Update school information as needed</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Upload a new logo to replace the existing one</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Keep contact information up to date</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>All changes are saved immediately</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-exclamation-triangle text-yellow-500 mt-1"></i>
              <span class="text-yellow-700">Deleting school cannot be undone</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="initialLoading" class="fixed inset-0 bg-white bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 flex flex-col items-center">
        <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
        <p class="text-gray-700">Loading school data...</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import Dropify from 'dropify'
import 'dropify/dist/css/dropify.css'
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
const initialLoading = ref(true)
const logoInput = ref(null)
const dropify = ref(null)

// Logo details
const logoFileSize = ref('')
const logoDimensions = ref('')
const logoFormat = ref('')
const existingLogo = ref(null)
const removeLogoFlag = ref(false)

// School data
const schoolData = ref({})

// Form data
const form = reactive({
  school_name: '',
  address: '',
  mobile_number: '',
  email: '',
  logo: null,
  logoPreview: null
})

// Errors object
const errors = ref({})

// Logo preview URL
const logoPreviewUrl = computed(() => {
  if (form.logoPreview) {
    return form.logoPreview
  } else if (existingLogo.value && !removeLogoFlag.value) {
    return `/assets/images/school_logo/${existingLogo.value}`
  }
  return null
})

// Initialize Dropify
const initDropify = () => {
  if (logoInput.value) {
    dropify.value = new Dropify(logoInput.value, {
      messages: {
        'default': 'Drag and drop a logo or click to browse',
        'replace': 'Drag and drop or click to replace',
        'remove': 'Remove',
        'error': 'Ooops, something wrong happened.'
      },
      error: {
        'fileSize': 'The file size is too big ({{ value }} max).',
        'fileExtension': 'The file format is not allowed ({{ value }} only).'
      }
    })

    // Handle file change
    logoInput.value.addEventListener('change', handleLogoUpload)
  }
}

// Fetch school data
const fetchSchoolData = async () => {
  try {
    initialLoading.value = true
    const schoolId = route.params.id
    
    const response = await axios.get(`/api/school-settings/${schoolId}`)
    
    if (response.data && response.data.success && response.data.data) {
      const school = response.data.data
      schoolData.value = school
      
      // Populate form
      form.school_name = school.school_name || ''
      form.address = school.address || ''
      form.mobile_number = school.mobile_number || ''
      form.email = school.email || ''
      
      // Store existing logo
      if (school.logo) {
        existingLogo.value = school.logo
      }
    } else {
      showErrorAlert('Error', 'Failed to load school data')
      router.push({ name: 'school-settings.index' })
    }
    
  } catch (error) {
    console.error('Error fetching school:', error)
    
    if (error.response?.status === 404) {
      showErrorAlert('Error', 'School not found')
      router.push({ name: 'school-settings.index' })
    } else {
      showErrorAlert('Error', 'Failed to load school data')
      router.push({ name: 'school-settings.index' })
    }
  } finally {
    initialLoading.value = false
  }
}

// Handle logo upload
const handleLogoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
      showErrorAlert('Error', 'File size must be less than 2MB')
      // Clear the file input
      if (dropify.value) {
        dropify.value.destroy()
        initDropify()
      }
      return
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml']
    if (!allowedTypes.includes(file.type)) {
      showErrorAlert('Error', 'Only JPG, PNG, and SVG images are allowed')
      // Clear the file input
      if (dropify.value) {
        dropify.value.destroy()
        initDropify()
      }
      return
    }

    form.logo = file
    
    // Get logo details
    logoFileSize.value = formatFileSize(file.size)
    logoFormat.value = file.type.split('/')[1].toUpperCase()
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      form.logoPreview = e.target.result
      
      // Get image dimensions
      const img = new Image()
      img.onload = () => {
        logoDimensions.value = `${img.width} x ${img.height}px`
      }
      img.src = e.target.result
    }
    reader.readAsDataURL(file)
    
    // Reset remove flag if new logo uploaded
    removeLogoFlag.value = false
  } else {
    form.logo = null
    form.logoPreview = null
    logoFileSize.value = ''
    logoDimensions.value = ''
    logoFormat.value = ''
  }
}

// Remove logo
const removeLogo = () => {
  form.logo = null
  form.logoPreview = null
  existingLogo.value = null
  removeLogoFlag.value = true
  
  // Clear dropify input
  if (dropify.value) {
    dropify.value.destroy()
    initDropify()
  }
  
  showSuccessAlert('Logo Removed', 'Logo will be removed when you save changes')
}

// Format file size
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
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

// Submit form
const submitForm = async () => {
  loading.value = true
  errors.value = {}

  try {
    // Validate required fields on frontend
    if (!form.school_name.trim()) {
      errors.value.school_name = ['The school name field is required.']
      loading.value = false
      return
    }

    showLoadingAlert('Updating school settings...')

    const formData = new FormData()
    formData.append('school_name', form.school_name.trim())
    formData.append('address', form.address || '')
    formData.append('mobile_number', form.mobile_number || '')
    formData.append('email', form.email || '')
    
    if (form.logo) {
      formData.append('logo', form.logo)
    }
    
    if (removeLogoFlag.value) {
      formData.append('remove_logo', '1')
    }

    formData.append('_method', 'PUT')

    const schoolId = route.params.id
    const response = await axios.post(`/api/school-settings/${schoolId}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    })
    
    closeAlert()
    
    if (response.data && response.data.success) {
      showSuccessAlert('Success!', 'School settings updated successfully.')
      router.push({ 
        name: 'school-settings.index',
        query: { updated: 'true' }
      })
    } else {
      showErrorAlert('Error', 'Failed to update school settings')
    }
    
  } catch (error) {
    closeAlert()
    loading.value = false

    console.error('API Error:', error)
    console.error('Error Response:', error.response)
    
    if (error.response && error.response.status === 422) {
      // Validation errors
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else if (error.response && error.response.data) {
      // Other API errors
      const errorMessage = error.response.data.message || 
                          error.response.data.error || 
                          'Failed to update school settings. Please try again.'
      showErrorAlert('Error', errorMessage)
    } else if (error.request) {
      // Network error
      showErrorAlert('Network Error', 'No response from server. Please check your connection.')
    } else {
      showErrorAlert('Error', 'Failed to update school settings. Please try again.')
    }
  }
}

// Lifecycle
onMounted(() => {
  initDropify()
  fetchSchoolData()
})

onUnmounted(() => {
  // Cleanup
  if (dropify.value) {
    dropify.value.destroy()
  }
  
  // Clean up object URLs
  if (form.logoPreview) {
    URL.revokeObjectURL(form.logoPreview)
  }
})
</script>

<style scoped>
/* Scoped CSS fix for Dropify */
:deep(.dropify-wrapper .dropify-message p) {
  font-size: 0.85rem;
  line-height: 1.3;
}

:deep(.dropify-wrapper .dropify-message span.file-icon) {
  font-size: 1.5rem;
}

/* Form styling */
input, textarea, select {
  transition: all 0.3s ease;
}

input:focus, textarea:focus, select:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Preview styling */
.preview-logo {
  transition: transform 0.3s ease;
}

.preview-logo:hover {
  transform: scale(1.05);
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