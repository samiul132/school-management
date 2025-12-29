<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Fee Head Management
          </h1>
          <p class="text-gray-600 mt-2">
            Manage fee heads for billing system
          </p>
        </div>

        <div class="flex gap-3">
          <button
            @click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
                    text-white px-4 py-2.5 rounded-lg font-semibold transition-colors"
          >
            <i class="fas fa-plus"></i>
            New Fee Head
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Fee Heads</p>
            <p class="text-3xl font-bold text-gray-800">{{ totalFeeHeads }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
            <i class="fas fa-tags text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Active Fee Heads</p>
            <p class="text-3xl font-bold text-green-600">{{ activeFeeHeads }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
            <i class="fas fa-check-circle text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">General Fee Heads</p>
            <p class="text-3xl font-bold text-purple-600">{{ generalFeeHeads }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-500 flex items-center justify-center">
            <i class="fas fa-file-invoice text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Transport Heads</p>
            <p class="text-3xl font-bold text-orange-600">{{ transportHeads }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-orange-500 flex items-center justify-center">
            <i class="fas fa-bus text-white text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <!-- Table Header with Search and Filters -->
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-4">
          <!-- Status Filter -->
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-700">Status:</span>
            <select 
              v-model="statusFilter"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          
          <!-- Type Filter -->
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-700">Type:</span>
            <select 
              v-model="typeFilter"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
            >
              <option value="">All Types</option>
              <option value="general">General</option>
              <option value="transport">Transport</option>
            </select>
          </div>
        </div>
        
        <!-- Search Box -->
        <div class="relative">
          <input 
            type="text" 
            v-model="search"
            placeholder="Search fee heads..." 
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
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">SL</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Fee Head Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Head Type</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Updated At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr 
              v-for="feeHead in filteredFeeHeads" 
              :key="feeHead.id" 
              class="hover:bg-gray-50 transition-colors"
            >
              <!-- SL Order Number -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-semibold"
                  :class="{
                    'bg-blue-100 text-blue-800': feeHead.head_type === 'general',
                    'bg-orange-100 text-orange-800': feeHead.head_type === 'transport'
                  }"
                >
                  {{ feeHead.order_number }}
                </span>
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <!-- Dropdown Button -->
                  <button 
                    @click="toggleDropdown(feeHead.id)"
                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== feeHead.id,
                      'bg-gray-200 text-black': openDropdownId === feeHead.id
                    }"
                  >
                    Actions
                    <i 
                      class="fas fa-chevron-down text-xs transition-transform duration-200 ml-1"
                      :class="{
                        'rotate-180': openDropdownId === feeHead.id,
                        'text-white': openDropdownId !== feeHead.id,
                        'text-black': openDropdownId === feeHead.id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === feeHead.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- Edit Option -->
                      <button 
                        @click="openEditModal(feeHead)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Fee Head</span>
                      </button>
                      
                      <!-- Delete Option -->
                      <div class="border-t border-gray-100 mt-1 pt-1">
                        <button 
                          @click="handleDeleteFeeHead(feeHead.id)"
                          class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                        >
                          <i class="fas fa-trash text-xs w-4"></i>
                          <span>Delete Fee Head</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Fee Head Name -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <i class="fas fa-tag" :class="{
                    'text-purple-500': feeHead.head_type === 'general',
                    'text-orange-500': feeHead.head_type === 'transport'
                  }"></i>
                  <div class="truncate max-w-[180px]" :title="feeHead.head_name">
                    {{ feeHead.head_name }}
                  </div>
                </div>
              </td>
              
              <!-- Head Type -->
              <td class="px-4 py-4 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                  :class="{
                    'bg-purple-100 text-purple-800': feeHead.head_type === 'general',
                    'bg-orange-100 text-orange-800': feeHead.head_type === 'transport'
                  }"
                >
                  <i 
                    class="fas mr-1.5"
                    :class="{
                      'fa-file-invoice text-purple-500': feeHead.head_type === 'general',
                      'fa-bus text-orange-500': feeHead.head_type === 'transport'
                    }"
                  ></i>
                  {{ feeHead.head_type === 'general' ? 'General' : 'Transport' }}
                </span>
              </td>
              
              <!-- Status -->
              <td class="px-4 py-4 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                  :class="{
                    'bg-green-100 text-green-800': feeHead.status === 'active',
                    'bg-gray-100 text-gray-800': feeHead.status === 'inactive'
                  }"
                >
                  <i 
                    class="fas fa-circle mr-1.5 text-[10px]"
                    :class="{
                      'text-green-500': feeHead.status === 'active',
                      'text-gray-500': feeHead.status === 'inactive'
                    }"
                  ></i>
                  {{ feeHead.status === 'active' ? 'Active' : 'Inactive' }}
                </span>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(feeHead.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(feeHead.created_at) }}</span>
                </div>
              </td>
              
              <!-- Updated At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(feeHead.updated_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(feeHead.updated_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading fee heads...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && feeHeads.length === 0" class="p-8 text-center">
        <i class="fas fa-tags text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No fee heads found.</p>
        <button 
          @click="openCreateModal"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Fee Head
        </button>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && feeHeads.length > 0 && filteredFeeHeads.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No fee heads match your search criteria.</p>
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
            {{ isEditing ? 'Edit Fee Head' : 'Create New Fee Head' }}
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
          <form @submit.prevent="isEditing ? updateFeeHead() : createFeeHead()">
            <!-- Fee Head Name -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Fee Head Name *
              </label>
              <input 
                type="text" 
                v-model="form.head_name"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                placeholder="Enter fee head name (e.g., Tuition Fee, Transport Fee)"
                required
                :disabled="submitting"
              >
              <p v-if="formErrors.head_name" class="mt-2 text-sm text-red-600">
                {{ formErrors.head_name }}
              </p>
            </div>

            <!-- Head Type -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Head Type *
              </label>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative">
                  <input 
                    type="radio" 
                    v-model="form.head_type"
                    value="general"
                    class="sr-only peer"
                    required
                    :disabled="submitting"
                  >
                  <div class="border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer transition-all hover:bg-gray-50 peer-checked:border-purple-500 peer-checked:bg-purple-50">
                    <i class="fas fa-file-invoice text-2xl mb-2 text-gray-600 peer-checked:text-purple-600"></i>
                    <div class="font-medium text-gray-800 peer-checked:text-purple-700">General</div>
                    <p class="text-xs text-gray-500 mt-1">For regular fee heads</p>
                  </div>
                </label>
                
                <label class="relative">
                  <input 
                    type="radio" 
                    v-model="form.head_type"
                    value="transport"
                    class="sr-only peer"
                    :disabled="submitting"
                  >
                  <div class="border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer transition-all hover:bg-gray-50 peer-checked:border-orange-500 peer-checked:bg-orange-50">
                    <i class="fas fa-bus text-2xl mb-2 text-gray-600 peer-checked:text-orange-600"></i>
                    <div class="font-medium text-gray-800 peer-checked:text-orange-700">Transport</div>
                    <p class="text-xs text-gray-500 mt-1">For transport related fees</p>
                  </div>
                </label>
              </div>
              <p v-if="formErrors.head_type" class="mt-2 text-sm text-red-600">
                {{ formErrors.head_type }}
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

            <!-- Order Number -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Order Number
                <span class="text-xs text-gray-500 ml-1">(Optional)</span>
              </label>
              <input 
                type="number" 
                v-model="form.order_number"
                min="1"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                placeholder="Enter order number (e.g., 1, 2, 3)"
                :disabled="submitting"
              >
              <div class="mt-2 space-y-2">
                <div class="flex items-center gap-2 text-sm">
                  <i class="fas fa-info-circle text-blue-500"></i>
                  <span class="text-gray-600">Leave blank to add at the end</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                  <i class="fas fa-info-circle text-blue-500"></i>
                  <span class="text-gray-600">Lower numbers appear first</span>
                </div>
              </div>
              <p v-if="formErrors.order_number" class="mt-2 text-sm text-red-600">
                {{ formErrors.order_number }}
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
                {{ isEditing ? 'Update Fee Head' : 'Create Fee Head' }}
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

const feeHeads = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const typeFilter = ref('')
const openDropdownId = ref(null)
const showModal = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const editingFeeHeadId = ref(null)

// Form data
const form = ref({
  head_name: '',
  order_number: '',
  head_type: 'general',
  status: 'active'
})

const formErrors = ref({})

// Watch for modal state changes to handle body scroll
watch(showModal, (newValue) => {
  if (newValue) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = 'auto'
  }
})

// Fetch fee heads
const fetchFeeHeads = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/fee-heads')
    
    if (Array.isArray(response.data)) {
      feeHeads.value = response.data
    } else if (response.data?.data) {
      feeHeads.value = response.data.data
    } else {
      feeHeads.value = []
    }
    
  } catch (error) {
    console.error('Error fetching fee heads:', error)
    showErrorAlert('Error', 'Failed to load fee heads')
  } finally {
    loading.value = false
  }
}

// Helper function to get next order number
const getNextOrderNumber = () => {
  if (feeHeads.value.length === 0) {
    return 1
  }
  
  // Find the maximum order number
  const maxOrder = Math.max(...feeHeads.value.map(feeHead => feeHead.order_number || 0))
  return maxOrder + 1
}

// Create fee head
const createFeeHead = async () => {
  try {
    submitting.value = true
    formErrors.value = {}
    
    showLoadingAlert('Creating fee head...')

    const response = await axios.post('/api/fee-heads', {
      ...form.value,
      order_number: form.value.order_number.trim() === '' ? null : parseInt(form.value.order_number)
    })
    
    closeAlert()
    
    if (response.status === 201) {
      const newFeeHead = response.data.data
      feeHeads.value.unshift(newFeeHead)
      
      showModal.value = false
      formErrors.value = {}
      form.value = {
        head_name: '',
        order_number: '',
        head_type: 'general',
        status: 'active'
      }
      isEditing.value = false
      editingFeeHeadId.value = null
      
      await showSuccessAlert('Success!', 'Fee head created successfully.')
      await fetchFeeHeads()
    }
    
  } catch (error) {
    closeAlert()
    
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to create fee head')
    }
  } finally {
    submitting.value = false
  }
}

// Update fee head
const updateFeeHead = async () => {
  try {
    submitting.value = true
    formErrors.value = {}
    
    showLoadingAlert('Updating fee head...')

    const response = await axios.put(`/api/fee-heads/${editingFeeHeadId.value}`, {
      ...form.value,
      order_number: form.value.order_number.trim() === '' ? null : parseInt(form.value.order_number)
    })
    
    closeAlert()
    
    if (response.status === 200) {
      const index = feeHeads.value.findIndex(fh => fh.id === editingFeeHeadId.value)
      if (index !== -1) {
        feeHeads.value[index] = response.data.data
      }
      
      showModal.value = false
      formErrors.value = {}
      form.value = {
        head_name: '',
        order_number: '',
        head_type: 'general',
        status: 'active'
      }
      isEditing.value = false
      editingFeeHeadId.value = null
      
      await showSuccessAlert('Success!', 'Fee head updated successfully.')
      await fetchFeeHeads()
    }
    
  } catch (error) {
    closeAlert()
    
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else if (error.response?.status === 404) {
      showErrorAlert('Error', 'Fee head not found')
      showModal.value = false
      form.value = {
        head_name: '',
        order_number: '',
        head_type: 'general',
        status: 'active'
      }
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to update fee head')
    }
  } finally {
    submitting.value = false
  }
}

// Delete fee head
const deleteFeeHead = async (id) => {
  const feeHead = feeHeads.value.find(fh => fh.id === id)
  
  const result = await showConfirmDialog(
    'Delete Fee Head?',
    `Are you sure you want to delete "${feeHead?.head_name}"? This action cannot be undone!`,
    'warning'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting fee head...')
      
      await axios.delete(`/api/fee-heads/${id}`)
      
      feeHeads.value = feeHeads.value.filter(fh => fh.id !== id)
      
      closeAlert()
      await showSuccessAlert('Deleted!', 'Fee head deleted successfully.')
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting fee head:', error)
      showErrorAlert('Error', error.response?.data?.message || 'Failed to delete fee head')
    }
  }
}

// Modal functions
const openCreateModal = () => {
  isEditing.value = false
  editingFeeHeadId.value = null
  
  // Calculate next order number automatically
  const nextOrderNumber = getNextOrderNumber()
  
  form.value = {
    head_name: '',
    order_number: nextOrderNumber.toString(),
    head_type: 'general',
    status: 'active'
  }
  
  formErrors.value = {}
  showModal.value = true
  
  setTimeout(() => {
    const input = document.querySelector('input[name="head_name"]')
    if (input) input.focus()
  }, 100)
}

const openEditModal = (feeHead) => {
  isEditing.value = true
  editingFeeHeadId.value = feeHead.id
  form.value = {
    head_name: feeHead.head_name,
    order_number: feeHead.order_number?.toString() || '',
    head_type: feeHead.head_type,
    status: feeHead.status
  }
  formErrors.value = {}
  closeDropdown()
  showModal.value = true
  
  setTimeout(() => {
    const input = document.querySelector('input[name="head_name"]')
    if (input) input.focus()
  }, 100)
}

const closeModal = () => {
  if (submitting.value) return
  showModal.value = false
  formErrors.value = {}
  form.value = {
    head_name: '',
    order_number: '',
    head_type: 'general',
    status: 'active'
  }
}

const handleDeleteFeeHead = async (id) => {
  closeDropdown()
  await deleteFeeHead(id)
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

const formatTime = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
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
  typeFilter.value = ''
}

// Computed properties
const totalFeeHeads = computed(() => feeHeads.value.length)

const activeFeeHeads = computed(() => 
  feeHeads.value.filter(feeHead => feeHead.status === 'active').length
)

const generalFeeHeads = computed(() => 
  feeHeads.value.filter(feeHead => feeHead.head_type === 'general').length
)

const transportHeads = computed(() => 
  feeHeads.value.filter(feeHead => feeHead.head_type === 'transport').length
)

const filteredFeeHeads = computed(() => {
  let filtered = [...feeHeads.value]
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(feeHead => 
      feeHead.head_name?.toLowerCase().includes(searchTerm)
    )
  }
  
  if (statusFilter.value) {
    filtered = filtered.filter(feeHead => feeHead.status === statusFilter.value)
  }
  
  if (typeFilter.value) {
    filtered = filtered.filter(feeHead => feeHead.head_type === typeFilter.value)
  }
  
  // Sort by order number (if available), then by ID
  return filtered.sort((a, b) => {
    const orderA = a.order_number || 999999
    const orderB = b.order_number || 999999
    return orderA - orderB
  })
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
  await fetchFeeHeads()
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscKey)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEscKey)
  document.body.style.overflow = 'auto'
})
</script>

<style scoped>
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

.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-container {
  z-index: 9999;
}
</style>