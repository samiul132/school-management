<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <!-- Title + Buttons -->
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <!-- Title -->
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Class Management
          </h1>
          <p class="text-gray-600 mt-2">
            Manage your academic classes
          </p>
        </div>

        <!-- Create Button -->
        <div class="flex gap-3">
          <button
            @click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
                    text-white px-4 py-2.5 rounded-lg font-semibold transition-colors"
          >
            <i class="fas fa-plus"></i>
            New Class
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Classes</p>
            <p class="text-3xl font-bold text-gray-800">{{ totalClasses }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
            <i class="fas fa-graduation-cap text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Active Classes</p>
            <p class="text-3xl font-bold text-green-600">{{ activeClasses }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
            <i class="fas fa-check-circle text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Inactive Classes</p>
            <p class="text-3xl font-bold text-gray-600">{{ inactiveClasses }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-gray-500 flex items-center justify-center">
            <i class="fas fa-times-circle text-white text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <!-- Table Header with Search and Filters -->
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <!-- Status Filter -->
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-700">Filter:</span>
          <select 
            v-model="statusFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
          >
            <option value="">All Status</option>
            <option value="active">Active Only</option>
            <option value="inactive">Inactive Only</option>
          </select>
        </div>
        
        <!-- Search Box -->
        <div class="relative">
          <input 
            type="text" 
            v-model="search"
            placeholder="Search classes..." 
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
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Class Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Updated At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="classItem in filteredClasses" :key="classItem.id" class="hover:bg-gray-50">
              <!-- ID -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ classItem.id }}
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <!-- Dropdown Button -->
                  <button 
                    @click="toggleDropdown(classItem.id)"
                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== classItem.id,
                      'bg-gray-200 text-black': openDropdownId === classItem.id
                    }"
                  >
                    Actions
                    <i 
                      class="fas fa-chevron-down text-xs transition-transform duration-200 ml-1"
                      :class="{
                        'rotate-180': openDropdownId === classItem.id,
                        'text-white': openDropdownId !== classItem.id,
                        'text-black': openDropdownId === classItem.id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === classItem.id"
                    class="absolute left-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- Edit Option -->
                      <button 
                        @click="openEditModal(classItem)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Class</span>
                      </button>
                      
                      <!-- Delete Option -->
                      <button 
                        @click="handleDeleteClass(classItem.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete Class</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Class Name -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <i class="fas fa-graduation-cap text-gray-400"></i>
                  <div class="truncate max-w-[200px]" :title="classItem.class_name">
                    {{ classItem.class_name }}
                  </div>
                </div>
              </td>
              
              <!-- Status -->
              <td class="px-4 py-4 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                  :class="{
                    'bg-green-100 text-green-800': classItem.status === 'active',
                    'bg-gray-100 text-gray-800': classItem.status === 'inactive'
                  }"
                >
                  <i 
                    class="fas fa-circle mr-1.5 text-[10px]"
                    :class="{
                      'text-green-500': classItem.status === 'active',
                      'text-gray-500': classItem.status === 'inactive'
                    }"
                  ></i>
                  {{ classItem.status === 'active' ? 'Active' : 'Inactive' }}
                </span>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(classItem.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(classItem.created_at) }}</span>
                </div>
              </td>
              
              <!-- Updated At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(classItem.updated_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(classItem.updated_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading classes...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && classes.length === 0" class="p-8 text-center">
        <i class="fas fa-graduation-cap text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No classes found.</p>
        <button 
          @click="openCreateModal"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Class
        </button>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && classes.length > 0 && filteredClasses.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No classes match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div 
      v-if="showModal" 
      class="fixed inset-0 flex items-center justify-center z-50 p-4 backdrop-blur-[2px]"
      @click.self="closeModal"
      :class="{ 'overflow-y-auto': showModal }"
    >
      <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-md my-auto"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 sticky top-0 bg-white rounded-t-2xl z-10">
          <h3 class="text-lg font-semibold text-gray-800">
            {{ isEditing ? 'Edit Class' : 'Create New Class' }}
          </h3>
          <button 
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
            :disabled="submitting"
          >
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 max-h-[70vh] overflow-y-auto">
          <form @submit.prevent="isEditing ? updateClass() : createClass()">
            <!-- Class Name -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Class Name *
              </label>
              <input 
                type="text" 
                v-model="form.class_name"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                placeholder="Enter class name (e.g., Class 1, Section A)"
                required
                :disabled="submitting"
              >
              <p v-if="formErrors.class_name" class="mt-2 text-sm text-red-600">
                {{ formErrors.class_name }}
              </p>
            </div>

            <!-- Status -->
            <div class="mb-8">
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Status *
              </label>
              <div class="flex gap-4">
                <label class="inline-flex items-center">
                  <input 
                    type="radio" 
                    v-model="form.status"
                    value="active"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                    required
                    :disabled="submitting"
                  >
                  <span class="ml-2 text-gray-700">Active</span>
                </label>
                <label class="inline-flex items-center">
                  <input 
                    type="radio" 
                    v-model="form.status"
                    value="inactive"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                    :disabled="submitting"
                  >
                  <span class="ml-2 text-gray-700">Inactive</span>
                </label>
              </div>
              <p v-if="formErrors.status" class="mt-2 text-sm text-red-600">
                {{ formErrors.status }}
              </p>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white rounded-b-2xl">
              <button 
                type="button"
                @click="closeModal"
                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                :disabled="submitting"
              >
                Cancel
              </button>
              <button 
                type="submit"
                :disabled="submitting"
                class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i v-if="submitting" class="fas fa-spinner fa-spin"></i>
                {{ isEditing ? 'Update Class' : 'Create Class' }}
              </button>
            </div>
          </form>
        </div>
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

const classes = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const openDropdownId = ref(null)
const showModal = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const editingClassId = ref(null)

// Form data
const form = ref({
  class_name: '',
  status: 'active'
})

const formErrors = ref({})

// Watch for modal state changes to handle body scroll
watch(showModal, (newValue) => {
  if (newValue) {
    // Disable body scroll when modal is open
    document.body.style.overflow = 'hidden'
  } else {
    // Enable body scroll when modal is closed
    document.body.style.overflow = 'auto'
  }
})

// Fetch classes
const fetchClasses = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/class-managements')
    
    if (Array.isArray(response.data)) {
      classes.value = response.data
    } else if (response.data?.data) {
      classes.value = response.data.data
    } else {
      classes.value = []
    }
    
  } catch (error) {
    console.error('Error fetching classes:', error)
    showErrorAlert('Error', 'Failed to load classes')
  } finally {
    loading.value = false
  }
}

// Create class
const createClass = async () => {
  try {
    submitting.value = true
    formErrors.value = {}
    
    showLoadingAlert('Creating class...')

    const response = await axios.post('/api/class-managements', form.value)
    
    closeAlert()
    
    if (response.status === 201) {
      const newClass = response.data.data
      classes.value.unshift(newClass)
      
      showModal.value = false
      formErrors.value = {}
      form.value = {
        class_name: '',
        status: 'active'
      }
      isEditing.value = false
      editingClassId.value = null
      
      await showSuccessAlert('Success!', 'Class created successfully.')
    }
    
  } catch (error) {
    closeAlert()
    
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', 'Failed to create class')
    }
  } finally {
    submitting.value = false
  }
}

// Update class
const updateClass = async () => {
  try {
    submitting.value = true
    formErrors.value = {}
    
    showLoadingAlert('Updating class...')

    const response = await axios.put(`/api/class-managements/${editingClassId.value}`, form.value)
    
    closeAlert()
    
    if (response.status === 200) {
      const index = classes.value.findIndex(c => c.id === editingClassId.value)
      if (index !== -1) {
        classes.value[index] = response.data.data
      }
      
      showModal.value = false
      formErrors.value = {}
      form.value = {
        class_name: '',
        status: 'active'
      }
      isEditing.value = false
      editingClassId.value = null
      
      await showSuccessAlert('Success!', 'Class updated successfully.')
    }
    
  } catch (error) {
    closeAlert()
    
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else if (error.response?.status === 404) {
      showErrorAlert('Error', 'Class not found')
      showModal.value = false
      form.value = {
        class_name: '',
        status: 'active'
      }
    } else {
      showErrorAlert('Error', 'Failed to update class')
    }
  } finally {
    submitting.value = false
  }
}

// Delete class
const deleteClass = async (id) => {
  const result = await showConfirmDialog(
    'Delete Class?',
    'Are you sure you want to delete this class? This action cannot be undone!',
    'warning'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting class...')
      
      await axios.delete(`/api/class-managements/${id}`)
      
      classes.value = classes.value.filter(classItem => classItem.id !== id)
      
      closeAlert()
      await showSuccessAlert('Deleted!', 'Class deleted successfully.')
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting class:', error)
      showErrorAlert('Error', 'Failed to delete class')
    }
  }
}

// Modal functions
const openCreateModal = () => {
  isEditing.value = false
  editingClassId.value = null
  
  form.value = {
    class_name: '',
    status: 'active'
  }
  
  formErrors.value = {}
  showModal.value = true
}

const openEditModal = (classItem) => {
  isEditing.value = true
  editingClassId.value = classItem.id
  form.value = {
    class_name: classItem.class_name,
    status: classItem.status
  }
  formErrors.value = {}
  closeDropdown()
  showModal.value = true
}

const closeModal = () => {
  if (submitting.value) {
    // Don't close if submitting
    return
  }
  showModal.value = false
  formErrors.value = {}
  // Reset form after closing
  form.value = {
    class_name: '',
    status: 'active'
  }
}

const handleDeleteClass = async (id) => {
  closeDropdown()
  await deleteClass(id)
}

// Utility functions
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
  
  // Handle both time-only strings and datetime strings
  const date = new Date(timeString)
  if (isNaN(date.getTime())) return 'N/A'
  
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

const toggleDropdown = (id) => {
  openDropdownId.value = openDropdownId.value === id ? null : id
}

const closeDropdown = () => {
  openDropdownId.value = null
}

const clearFilters = () => {
  search.value = ''
  statusFilter.value = ''
}

// Computed properties
const totalClasses = computed(() => classes.value.length)

const activeClasses = computed(() => 
  classes.value.filter(classItem => classItem.status === 'active').length
)

const inactiveClasses = computed(() => 
  classes.value.filter(classItem => classItem.status === 'inactive').length
)

const filteredClasses = computed(() => {
  let filtered = classes.value
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(classItem => 
      classItem.class_name?.toLowerCase().includes(searchTerm)
    )
  }
  
  if (statusFilter.value) {
    filtered = filtered.filter(classItem => classItem.status === statusFilter.value)
  }
  
  return filtered
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

// Lifecycle
onMounted(async () => {
  await fetchClasses()
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscKey)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEscKey)
  // Make sure to reset body overflow when component unmounts
  document.body.style.overflow = 'auto'
})
</script>

<style scoped>
/* Custom scrollbar for modal */
.modal-body-scroll {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f1f5f9;
}

.modal-body-scroll::-webkit-scrollbar {
  width: 6px;
}

.modal-body-scroll::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.modal-body-scroll::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.modal-body-scroll::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Modal backdrop */
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
}

/* Ensure modal is above everything */
.modal-container {
  z-index: 9999;
}
</style>