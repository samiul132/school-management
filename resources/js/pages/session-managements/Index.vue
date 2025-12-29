<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <!-- Title + Buttons -->
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <!-- Title -->
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Session Management
          </h1>
          <p class="text-gray-600 mt-2">
            Manage your academic sessions
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
            New Session
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Sessions</p>
            <p class="text-3xl font-bold text-gray-800">{{ totalSessions }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
            <i class="fas fa-calendar-alt text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Active Sessions</p>
            <p class="text-3xl font-bold text-green-600">{{ activeSessions }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
            <i class="fas fa-check-circle text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Inactive Sessions</p>
            <p class="text-3xl font-bold text-gray-600">{{ inactiveSessions }}</p>
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
            placeholder="Search sessions..." 
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
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Order</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Session Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Updated At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="session in filteredSessions" :key="session.id" class="hover:bg-gray-50">
              <!-- ID -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ session.id }}
              </td>
              
              <!-- Order Number -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 rounded-full font-semibold">
                  {{ session.order_number }}
                </span>
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <!-- Dropdown Button -->
                  <button 
                    @click="toggleDropdown(session.id)"
                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== session.id,
                      'bg-gray-200 text-black': openDropdownId === session.id
                    }"
                  >
                    Actions
                    <i 
                      class="fas fa-chevron-down text-xs transition-transform duration-200 ml-1"
                      :class="{
                        'rotate-180': openDropdownId === session.id,
                        'text-white': openDropdownId !== session.id,
                        'text-black': openDropdownId === session.id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === session.id"
                    class="absolute left-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- Edit Option -->
                      <button 
                        @click="openEditModal(session)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Session</span>
                      </button>
                      
                      <!-- Delete Option -->
                      <button 
                        @click="handleDeleteSession(session.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete Session</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Session Name -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <div class="truncate max-w-[200px]" :title="session.session_name">
                    {{ session.session_name }}
                  </div>
                </div>
              </td>
              
              <!-- Status -->
              <td class="px-4 py-4 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                  :class="{
                    'bg-green-100 text-green-800': session.status === 'active',
                    'bg-gray-100 text-gray-800': session.status === 'inactive'
                  }"
                >
                  <i 
                    class="fas fa-circle mr-1.5 text-[10px]"
                    :class="{
                      'text-green-500': session.status === 'active',
                      'text-gray-500': session.status === 'inactive'
                    }"
                  ></i>
                  {{ session.status === 'active' ? 'Active' : 'Inactive' }}
                </span>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(session.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(session.created_at) }}</span>
                </div>
              </td>
              
              <!-- Updated At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(session.updated_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(session.updated_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading sessions...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && sessions.length === 0" class="p-8 text-center">
        <i class="fas fa-calendar-alt text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No sessions found.</p>
        <button 
          @click="openCreateModal"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Session
        </button>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && sessions.length > 0 && filteredSessions.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No sessions match your search criteria.</p>
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
    >
      <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-md"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800">
            {{ isEditing ? 'Edit Session' : 'Create New Session' }}
          </h3>
          <button 
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
          <form @submit.prevent="isEditing ? updateSession() : createSession()">
            <!-- Session Name -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Session Name *
              </label>
              <input 
                type="text" 
                v-model="form.session_name"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                placeholder="Enter session name"
                required
                :disabled="submitting"
              >
              <p v-if="formErrors.session_name" class="mt-2 text-sm text-red-600">
                {{ formErrors.session_name }}
              </p>
            </div>

            <!-- Order Number -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Order Number
              </label>
              <input 
                type="number" 
                v-model="form.order_number"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                placeholder="Enter order number"
                min="1"
                :disabled="submitting"
              >
              <p v-if="formErrors.order_number" class="mt-2 text-sm text-red-600">
                {{ formErrors.order_number }}
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
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
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
                {{ isEditing ? 'Update Session' : 'Create Session' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { 
  showSuccessAlert, 
  showErrorAlert, 
  showConfirmDialog, 
  showLoadingAlert, 
  closeAlert 
} from '../../utils/sweetAlert'

const sessions = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const openDropdownId = ref(null)
const showModal = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const editingSessionId = ref(null)

// Form data
const form = ref({
  session_name: '',
  order_number: '',
  status: 'active'
})

const formErrors = ref({})

// Fetch sessions
const fetchSessions = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/session-managements')
    
    // Debugging জন্য
    console.log('Sessions API Response:', response.data)
    
    if (response.data.success && Array.isArray(response.data.data)) {
      sessions.value = response.data.data
    } else if (Array.isArray(response.data)) {
      sessions.value = response.data
    } else if (response.data?.data) {
      sessions.value = response.data.data
    } else {
      sessions.value = []
      console.warn('Unexpected API response structure:', response.data)
    }
    
  } catch (error) {
    console.error('Error fetching sessions:', error)
    showErrorAlert('Error', 'Failed to load sessions: ' + (error.response?.data?.message || error.message))
  } finally {
    loading.value = false
  }
}

// Create session
const createSession = async () => {
  try {
    submitting.value = true
    formErrors.value = {}
    
    showLoadingAlert('Creating session...')

    const response = await axios.post('/api/session-managements', {
      session_name: form.value.session_name,
      order_number: form.value.order_number || null,
      status: form.value.status
    })
    
    closeAlert()
    
    // Response structure check করুন
    if (response.data.message || response.data.success) {
      const newSession = response.data.data || response.data
      sessions.value.unshift(newSession)
      
      showModal.value = false
      formErrors.value = {}
      form.value = {
        session_name: '',
        order_number: sessions.value.length > 0 
          ? (Math.max(...sessions.value.map(s => s.order_number || 0)) + 1).toString()
          : '1',
        status: 'active'
      }
      isEditing.value = false
      editingSessionId.value = null
      
      await showSuccessAlert('Success!', response.data.message || 'Session created successfully.')
    } else {
      throw new Error('Failed to create session')
    }
    
  } catch (error) {
    closeAlert()
    
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else {
      console.error('Create error:', error)
      showErrorAlert('Error', error.response?.data?.message || error.message || 'Failed to create session')
    }
  } finally {
    submitting.value = false
  }
}

// Update session
const updateSession = async () => {
  try {
    submitting.value = true
    formErrors.value = {}
    
    showLoadingAlert('Updating session...')

    const response = await axios.put(`/api/session-managements/${editingSessionId.value}`, {
      session_name: form.value.session_name,
      order_number: form.value.order_number || null,
      status: form.value.status
    })
    
    closeAlert()
    
    if (response.data.message || response.data.success) {
      const index = sessions.value.findIndex(s => s.id === editingSessionId.value)
      if (index !== -1) {
        sessions.value[index] = response.data.data || response.data
      }
      
      showModal.value = false
      formErrors.value = {}
      form.value = {
        session_name: '',
        order_number: sessions.value.length > 0 
          ? (Math.max(...sessions.value.map(s => s.order_number || 0)) + 1).toString()
          : '1',
        status: 'active'
      }
      isEditing.value = false
      editingSessionId.value = null
      
      await showSuccessAlert('Success!', response.data.message || 'Session updated successfully.')
    } else {
      throw new Error('Failed to update session')
    }
    
  } catch (error) {
    closeAlert()
    
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', error.response.data.message || 'Please check the form for errors.')
    } else if (error.response?.status === 404) {
      showErrorAlert('Error', error.response.data.message || 'Session not found')
      showModal.value = false
      form.value = {
        session_name: '',
        order_number: '',
        status: 'active'
      }
    } else {
      console.error('Update error:', error)
      showErrorAlert('Error', error.response?.data?.message || error.message || 'Failed to update session')
    }
  } finally {
    submitting.value = false
  }
}

// Delete session
const deleteSession = async (id) => {
  const result = await showConfirmDialog(
    'Delete Session?',
    'Are you sure you want to delete this session? This action cannot be undone!',
    'warning'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting session...')
      
      await axios.delete(`/api/session-managements/${id}`)
      
      sessions.value = sessions.value.filter(session => session.id !== id)
      
      closeAlert()
      await showSuccessAlert('Deleted!', 'Session deleted successfully.')
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting session:', error)
      showErrorAlert('Error', error.response?.data?.message || error.message || 'Failed to delete session')
    }
  }
}

// Modal functions
const openCreateModal = () => {
  isEditing.value = false
  editingSessionId.value = null
  
  const nextOrderNumber = sessions.value.length > 0 
    ? Math.max(...sessions.value.map(s => s.order_number || 0)) + 1
    : 1
  
  form.value = {
    session_name: '',
    order_number: nextOrderNumber.toString(),
    status: 'active'
  }
  
  formErrors.value = {}
  showModal.value = true
}

const openEditModal = (session) => {
  isEditing.value = true
  editingSessionId.value = session.id
  form.value = {
    session_name: session.session_name,
    order_number: session.order_number?.toString() || '',
    status: session.status
  }
  formErrors.value = {}
  closeDropdown()
  showModal.value = true
}

const closeModal = () => {
  if (submitting.value) return
  showModal.value = false
  formErrors.value = {}
}

const handleDeleteSession = async (id) => {
  closeDropdown()
  await deleteSession(id)
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
}

// Computed properties
const totalSessions = computed(() => sessions.value.length)

const activeSessions = computed(() => 
  sessions.value.filter(session => session.status === 'active').length
)

const inactiveSessions = computed(() => 
  sessions.value.filter(session => session.status === 'inactive').length
)

const filteredSessions = computed(() => {
  let filtered = sessions.value
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(session => 
      session.session_name?.toLowerCase().includes(searchTerm)
    )
  }
  
  if (statusFilter.value) {
    filtered = filtered.filter(session => session.status === statusFilter.value)
  }
  
  return filtered
})

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    openDropdownId.value = null
  }
}

const handleEscKey = (event) => {
  if (event.key === 'Escape' && showModal.value) {
    closeModal()
  }
}

// Lifecycle
onMounted(async () => {
  await fetchSessions()
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscKey)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEscKey)
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
</style>