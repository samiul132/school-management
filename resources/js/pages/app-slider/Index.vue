<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">App Sliders</h1>
        <p class="text-gray-600 mt-2">Manage mobile app slider images</p>
      </div>
      <button
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-semibold"
      >
        <i class="fas fa-plus mr-2"></i>
        Add Slider
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Sliders</p>
            <p class="text-3xl font-bold text-gray-800">{{ totalSliders }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
            <i class="fas fa-images text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Latest Slider</p>
            <p class="text-lg font-bold text-gray-800 truncate">
              {{ sliders[0]?.title || 'No sliders yet' }}
            </p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
            <i class="fas fa-image text-white text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <!-- Table Header with Search -->
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h3 class="text-lg font-semibold text-gray-800">All Sliders</h3>
        
        <!-- Search Box -->
        <div class="relative">
          <input 
            type="text" 
            v-model="search"
            placeholder="Search sliders..." 
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
          >
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">SL</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Image</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(slider, index) in filteredSliders" :key="slider.id" class="hover:bg-gray-50">
              <!-- SL -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-semibold">
                  {{ index + 1 }}
                </span>
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <button 
                    @click="toggleDropdown(slider.id)"
                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== slider.id,
                      'bg-gray-200 text-black': openDropdownId === slider.id
                    }"
                  >
                    Actions
                    <i 
                      class="fas fa-chevron-down text-xs transition-transform duration-200 ml-1"
                      :class="{
                        'rotate-180': openDropdownId === slider.id,
                        'text-white': openDropdownId !== slider.id,
                        'text-black': openDropdownId === slider.id
                      }"
                    ></i>
                  </button>
                  
                  <div 
                    v-if="openDropdownId === slider.id"
                    class="absolute left-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <button 
                        @click="openEditModal(slider)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Slider</span>
                      </button>
                      
                      <button 
                        @click="handleDeleteSlider(slider.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete Slider</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Image -->
              <td class="px-4 py-4 whitespace-nowrap">
                <img 
                  :src="slider.image_url" 
                  :alt="slider.title"
                  class="w-20 h-12 object-cover rounded-lg border border-gray-200"
                >
              </td>
              
              <!-- Title -->
              <td class="px-4 py-4 text-sm font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <i class="fas fa-image text-gray-400"></i>
                  <span>{{ slider.title || 'Untitled' }}</span>
                </div>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(slider.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(slider.created_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading sliders...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && sliders.length === 0" class="p-8 text-center">
        <i class="fas fa-images text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No sliders found.</p>
        <button 
          @click="openCreateModal"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Slider
        </button>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && sliders.length > 0 && filteredSliders.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No sliders match your search.</p>
        <button 
          @click="clearSearch"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Search
        </button>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div 
      v-if="showModal" 
      class="fixed inset-0 flex items-center justify-center z-50 backdrop-blur-sm bg-black/30 p-4 overflow-y-auto"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md my-auto">
        <div class="flex justify-center items-center p-6 border-b relative">
          <h3 class="text-lg font-semibold">{{ isEditing ? 'Edit Slider' : 'Add New Slider' }}</h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 absolute right-6" :disabled="submitting">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>

        <form @submit.prevent="isEditing ? updateSlider() : createSlider()" class="p-6 max-h-[70vh] overflow-y-auto">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
            <input 
              type="text" 
              v-model="form.title"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              placeholder="Slider title (optional)"
              :disabled="submitting"
            >
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Image * <span class="text-gray-500 text-xs">(Required)</span>
            </label>
            <input 
              type="file"
              @change="handleFileChange"
              accept="image/*"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              :required="!isEditing"
              :disabled="submitting"
            >
            <p class="text-xs text-gray-500 mt-1">Max 2MB. JPG, PNG, GIF, SVG, WebP</p>
            
            <div v-if="imagePreview" class="mt-4">
              <img :src="imagePreview" alt="Preview" class="w-full rounded-lg border">
            </div>
            <div v-else-if="isEditing && currentSlider?.image_url" class="mt-4">
              <img :src="currentSlider.image_url" alt="Current" class="w-full rounded-lg border">
            </div>
          </div>

          <div class="flex gap-3">
            <button 
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2 border rounded-lg hover:bg-gray-50"
              :disabled="submitting"
            >
              Cancel
            </button>
            <button 
              type="submit"
              :disabled="submitting"
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              <i v-if="submitting" class="fas fa-spinner fa-spin mr-2"></i>
              {{ isEditing ? 'Update' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { 
  showSuccessAlert, 
  showErrorAlert, 
  showConfirmDialog,
  showLoadingAlert,
  closeAlert 
} from '../../utils/sweetAlert'

const sliders = ref([])
const loading = ref(true)
const search = ref('')
const openDropdownId = ref(null)
const showModal = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const editingId = ref(null)
const currentSlider = ref(null)
const imagePreview = ref(null)

const form = ref({
  title: '',
  image: null
})

watch(showModal, (newValue) => {
  document.body.style.overflow = newValue ? 'hidden' : 'auto'
})

const fetchSliders = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/app-sliders')
    const data = response.data?.data
    sliders.value = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : [])
  } catch (error) {
    console.error('Fetch error:', error)
    showErrorAlert('Error', 'Failed to load sliders')
  } finally {
    loading.value = false
  }
}

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.value.image = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const createSlider = async () => {
  try {
    submitting.value = true
    showLoadingAlert('Creating slider...')
    
    const formData = new FormData()
    formData.append('title', form.value.title || '')
    if (form.value.image) {
      formData.append('image', form.value.image)
    }

    const response = await axios.post('/api/app-sliders', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    closeAlert()
    sliders.value.unshift(response.data.data)
    closeModal()
    await showSuccessAlert('Success!', 'Slider created successfully!')
    await fetchSliders()
  } catch (error) {
    closeAlert()
    showErrorAlert('Error', error.response?.data?.message || 'Failed to create slider')
  } finally {
    submitting.value = false
  }
}

const updateSlider = async () => {
  try {
    submitting.value = true
    showLoadingAlert('Updating slider...')
    
    const formData = new FormData()
    formData.append('title', form.value.title || '')
    if (form.value.image) {
      formData.append('image', form.value.image)
    }
    formData.append('_method', 'PUT')

    const response = await axios.post(`/api/app-sliders/${editingId.value}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    closeAlert()
    const index = sliders.value.findIndex(s => s.id === editingId.value)
    if (index !== -1) {
      sliders.value[index] = response.data.data
    }
    closeModal()
    await showSuccessAlert('Success!', 'Slider updated successfully!')
    await fetchSliders()
  } catch (error) {
    closeAlert()
    showErrorAlert('Error', error.response?.data?.message || 'Failed to update slider')
  } finally {
    submitting.value = false
  }
}

const deleteSlider = async (id) => {
  const result = await showConfirmDialog('Delete Slider?', 'This action cannot be undone!', 'warning')
  
  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting slider...')
      await axios.delete(`/api/app-sliders/${id}`)
      sliders.value = sliders.value.filter(s => s.id !== id)
      closeAlert()
      await showSuccessAlert('Deleted!', 'Slider deleted successfully')
      await fetchSliders()
    } catch (error) {
      closeAlert()
      showErrorAlert('Error', 'Failed to delete slider')
    }
  }
}

const openCreateModal = () => {
  isEditing.value = false
  editingId.value = null
  currentSlider.value = null
  form.value = { title: '', image: null }
  imagePreview.value = null
  showModal.value = true
}

const openEditModal = (slider) => {
  isEditing.value = true
  editingId.value = slider.id
  currentSlider.value = slider
  form.value = { title: slider.title || '', image: null }
  imagePreview.value = null
  closeDropdown()
  showModal.value = true
}

const closeModal = () => {
  if (submitting.value) return
  showModal.value = false
  form.value = { title: '', image: null }
  imagePreview.value = null
  isEditing.value = false
  editingId.value = null
  currentSlider.value = null
}

const handleDeleteSlider = async (id) => {
  closeDropdown()
  await deleteSlider(id)
}

const toggleDropdown = (id) => {
  openDropdownId.value = openDropdownId.value === id ? null : id
}

const closeDropdown = () => {
  openDropdownId.value = null
}

const clearSearch = () => {
  search.value = ''
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (timeString) => {
  if (!timeString) return 'N/A'
  const date = new Date(timeString)
  if (isNaN(date.getTime())) return 'N/A'
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

const totalSliders = computed(() => sliders.value.length)

const filteredSliders = computed(() => {
  if (!search.value) return sliders.value
  return sliders.value.filter(s => 
    s.title?.toLowerCase().includes(search.value.toLowerCase())
  )
})

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    openDropdownId.value = null
  }
}

const handleEscKey = (event) => {
  if (event.key === 'Escape' && showModal.value && !submitting.value) {
    closeModal()
  }
}

onMounted(async () => {
  await fetchSliders()
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscKey)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEscKey)
  document.body.style.overflow = 'auto'
})
</script>