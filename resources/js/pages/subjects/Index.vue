<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Subject Management</h1>
          <p class="text-gray-600 mt-2">Manage academic subjects and their details</p>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
                  text-white px-4 py-2.5 rounded-lg font-semibold transition-colors"
        >
          <i class="fas fa-plus"></i>
          New Subject
        </button>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <!-- Filters -->
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-2 text-sm text-gray-700">
          <span>Show</span>
          <select
            v-model="itemsPerPage"
            @change="currentPage = 1"
            class="px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
          <span>per page</span>
        </div>
        
        <div class="relative">
          <input 
            type="text" 
            v-model="search"
            placeholder="Search subjects..." 
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
          >
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px]">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">SL</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Code</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr 
              v-for="(subject, index) in paginatedSubjects" 
              :key="subject.id" 
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-semibold">
                  {{ startIndex + index + 1 }}
                </span>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <button 
                    @click="toggleDropdown(subject.id)"
                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== subject.id,
                      'bg-gray-200 text-black': openDropdownId === subject.id
                    }"
                  >
                    Actions
                    <i 
                      class="fas fa-chevron-down text-xs transition-transform duration-200 ml-1"
                      :class="{
                        'rotate-180': openDropdownId === subject.id,
                        'text-white': openDropdownId !== subject.id,
                        'text-black': openDropdownId === subject.id
                      }"
                    ></i>
                  </button>
                  
                  <div 
                    v-if="openDropdownId === subject.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <button 
                        @click="openEditModal(subject)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Subject</span>
                      </button>
                      
                      <div class="border-t border-gray-100 mt-1 pt-1">
                        <button 
                          @click="handleDeleteSubject(subject.id)"
                          class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                        >
                          <i class="fas fa-trash text-xs w-4"></i>
                          <span>Delete Subject</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <i class="fas fa-book text-blue-500"></i>
                  <span>{{ subject.subject_name }}</span>
                </div>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full font-mono font-medium">
                  {{ subject.subject_code }}
                </span>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                  :class="{
                    'bg-green-100 text-green-800': subject.status === 'active',
                    'bg-gray-100 text-gray-800': subject.status === 'inactive'
                  }"
                >
                  <i 
                    class="fas fa-circle mr-1.5 text-[10px]"
                    :class="{
                      'text-green-500': subject.status === 'active',
                      'text-gray-500': subject.status === 'inactive'
                    }"
                  ></i>
                  {{ subject.status === 'active' ? 'Active' : 'Inactive' }}
                </span>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(subject.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(subject.created_at) }}</span>
                </div>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(subject.updated_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(subject.updated_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading subjects...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && subjects.length === 0" class="p-8 text-center">
        <i class="fas fa-book text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No subjects found.</p>
        <button 
          @click="openCreateModal"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Subject
        </button>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && subjects.length > 0 && filteredSubjects.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No subjects match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredSubjects.length > 0" class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-white">
        <div class="text-sm text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredSubjects.length }} subjects
        </div>
        <div class="flex items-center gap-2">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Previous
          </button>

          <template v-if="startPage > 1">
            <button
              @click="currentPage = 1"
              class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              1
            </button>
            <span v-if="startPage > 2" class="px-2 text-gray-500">...</span>
          </template>

          <button
            v-for="page in visiblePages"
            :key="page"
            @click="currentPage = page"
            :class="[
              'px-3 py-2 text-sm font-medium rounded-lg transition-colors',
              currentPage === page
                ? 'bg-blue-600 text-white'
                : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>

          <template v-if="endPage < totalPages">
            <span v-if="endPage < totalPages - 1" class="px-2 text-gray-500">...</span>
            <button
              @click="currentPage = totalPages"
              class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              {{ totalPages }}
            </button>
          </template>

          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div 
      v-if="showModal" 
      class="fixed overflow-auto inset-0 flex items-center justify-center z-50 p-4 backdrop-blur-[2px]"
      @click.self="closeModal"
    >
      <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-md my-auto"
        @click.stop
      >
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800">
            {{ isEditing ? 'Edit Subject' : 'Create New Subject' }}
          </h3>
          <button 
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
            :disabled="submitting"
          >
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>

        <div class="p-6">
          <div @submit.prevent="isEditing ? updateSubject() : createSubject()">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Subject Name *
              </label>
              <input 
                type="text" 
                v-model="form.subject_name"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                placeholder="Enter subject name"
                required
                :disabled="submitting"
              >
              <p v-if="formErrors.subject_name" class="mt-2 text-sm text-red-600">
                {{ formErrors.subject_name }}
              </p>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Subject Code *
              </label>
              <input 
                type="number" 
                v-model="form.subject_code"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                placeholder="Enter subject code"
                required
                :disabled="submitting"
              >
              <p v-if="formErrors.subject_code" class="mt-2 text-sm text-red-600">
                {{ formErrors.subject_code }}
              </p>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Order Number
                <span class="text-xs text-gray-500 ml-1">(Optional)</span>
              </label>
              <input 
                type="number" 
                v-model="form.order_number"
                min="1"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                placeholder="Enter order number"
                :disabled="submitting"
              >
              <div class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                <i class="fas fa-info-circle text-blue-500"></i>
                <span>Lower numbers appear first</span>
              </div>
              <p v-if="formErrors.order_number" class="mt-2 text-sm text-red-600">
                {{ formErrors.order_number }}
              </p>
            </div>

            <div class="mb-6">
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
                type="button"
                @click="isEditing ? updateSubject() : createSubject()"
                :disabled="submitting"
                class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i v-if="submitting" class="fas fa-spinner fa-spin"></i>
                {{ isEditing ? 'Update Subject' : 'Create Subject' }}
              </button>
            </div>
          </div>
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

const subjects = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const openDropdownId = ref(null)
const showModal = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const editingSubjectId = ref(null)

// Pagination state
const currentPage = ref(1)
const itemsPerPage = ref(10)

const form = ref({
  subject_name: '',
  subject_code: '',
  order_number: '',
  status: 'active'
})

const formErrors = ref({})

// Watch modal state
watch(showModal, (newValue) => {
  if (newValue) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = 'auto'
  }
})

// Fetch subjects
const fetchSubjects = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/subjects')
    
    if (Array.isArray(response.data)) {
      subjects.value = response.data
    } else if (response.data?.data) {
      subjects.value = response.data.data
    } else {
      subjects.value = []
    }
  } catch (error) {
    console.error('Error fetching subjects:', error)
    showErrorAlert('Error', 'Failed to load subjects')
  } finally {
    loading.value = false
  }
}

const getNextOrderNumber = () => {
  if (subjects.value.length === 0) return 1
  const maxOrder = Math.max(...subjects.value.map(s => s.order_number || 0))
  return maxOrder + 1
}

// Create subject
const createSubject = async () => {
  try {
    submitting.value = true
    formErrors.value = {}
    
    showLoadingAlert('Creating subject...')

    const response = await axios.post('/api/subjects', {
      ...form.value,
      order_number: form.value.order_number.trim() === '' ? null : parseInt(form.value.order_number),
      subject_code: parseInt(form.value.subject_code)
    })
    
    closeAlert()
    
    if (response.status === 201) {
      const newSubject = response.data.data
      subjects.value.unshift(newSubject)
      
      showModal.value = false
      formErrors.value = {}
      form.value = {
        subject_name: '',
        subject_code: '',
        order_number: '',
        status: 'active'
      }
      
      await showSuccessAlert('Success!', 'Subject created successfully.')
      await fetchSubjects()
    }
  } catch (error) {
    closeAlert()
    
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to create subject')
    }
  } finally {
    submitting.value = false
  }
}

const showingStart = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredSubjects.value.length ? filteredSubjects.value.length : end
})

// Update subject
const updateSubject = async () => {
  try {
    submitting.value = true
    formErrors.value = {}
    
    showLoadingAlert('Updating subject...')

    const response = await axios.put(`/api/subjects/${editingSubjectId.value}`, {
      ...form.value,
      order_number: form.value.order_number.trim() === '' ? null : parseInt(form.value.order_number),
      subject_code: parseInt(form.value.subject_code)
    })
    
    closeAlert()
    
    if (response.status === 200) {
      const index = subjects.value.findIndex(s => s.id === editingSubjectId.value)
      if (index !== -1) {
        subjects.value[index] = response.data.data
      }
      
      showModal.value = false
      formErrors.value = {}
      form.value = {
        subject_name: '',
        subject_code: '',
        order_number: '',
        status: 'active'
      }
      isEditing.value = false
      editingSubjectId.value = null
      
      await showSuccessAlert('Success!', 'Subject updated successfully.')
      await fetchSubjects()
    }
  } catch (error) {
    closeAlert()
    
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else if (error.response?.status === 404) {
      showErrorAlert('Error', 'Subject not found')
      showModal.value = false
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to update subject')
    }
  } finally {
    submitting.value = false
  }
}

// Delete subject
const deleteSubject = async (id) => {
  const subject = subjects.value.find(s => s.id === id)
  
  const result = await showConfirmDialog(
    'Delete Subject?',
    `Are you sure you want to delete "${subject?.subject_name}"? This action cannot be undone!`,
    'warning'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting subject...')
      
      await axios.delete(`/api/subjects/${id}`)
      
      subjects.value = subjects.value.filter(s => s.id !== id)
      
      closeAlert()
      await showSuccessAlert('Deleted!', 'Subject deleted successfully.')
    } catch (error) {
      closeAlert()
      console.error('Error deleting subject:', error)
      showErrorAlert('Error', error.response?.data?.message || 'Failed to delete subject')
    }
  }
}

// Modal functions
const openCreateModal = () => {
  isEditing.value = false
  editingSubjectId.value = null
  
  const nextOrderNumber = getNextOrderNumber()
  
  form.value = {
    subject_name: '',
    subject_code: '',
    order_number: nextOrderNumber.toString(),
    status: 'active'
  }
  
  formErrors.value = {}
  showModal.value = true
}

const openEditModal = (subject) => {
  isEditing.value = true
  editingSubjectId.value = subject.id
  form.value = {
    subject_name: subject.subject_name,
    subject_code: subject.subject_code?.toString() || '',
    order_number: subject.order_number?.toString() || '',
    status: subject.status
  }
  formErrors.value = {}
  closeDropdown()
  showModal.value = true
}

const closeModal = () => {
  if (submitting.value) return
  showModal.value = false
  formErrors.value = {}
  form.value = {
    subject_name: '',
    subject_code: '',
    order_number: '',
    status: 'active'
  }
}

const handleDeleteSubject = async (id) => {
  closeDropdown()
  await deleteSubject(id)
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
const totalSubjects = computed(() => subjects.value.length)

const activeSubjects = computed(() => 
  subjects.value.filter(subject => subject.status === 'active').length
)

const inactiveSubjects = computed(() => 
  subjects.value.filter(subject => subject.status === 'inactive').length
)

const filteredSubjects = computed(() => {
  let filtered = [...subjects.value]
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(subject => 
      subject.subject_name?.toLowerCase().includes(searchTerm) ||
      subject.subject_code?.toString().includes(searchTerm)
    )
  }
  
  if (statusFilter.value) {
    filtered = filtered.filter(subject => subject.status === statusFilter.value)
  }
  
  return filtered.sort((a, b) => {
    const orderA = a.order_number || 999999
    const orderB = b.order_number || 999999
    return orderA - orderB
  })
})

// Pagination computed properties
const totalPages = computed(() => Math.ceil(filteredSubjects.value.length / itemsPerPage.value))

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value)

const endIndex = computed(() => startIndex.value + itemsPerPage.value)

const paginatedSubjects = computed(() => 
  filteredSubjects.value.slice(startIndex.value, endIndex.value)
)

const maxVisiblePages = 5

const startPage = computed(() => {
  let start = Math.max(1, currentPage.value - Math.floor(maxVisiblePages / 2))
  let end = Math.min(totalPages.value, start + maxVisiblePages - 1)
  
  if (end - start < maxVisiblePages - 1) {
    start = Math.max(1, end - maxVisiblePages + 1)
  }
  
  return start
})

const endPage = computed(() => {
  let start = startPage.value
  return Math.min(totalPages.value, start + maxVisiblePages - 1)
})

const visiblePages = computed(() => {
  const pages = []
  for (let i = startPage.value; i <= endPage.value; i++) {
    pages.push(i)
  }
  return pages
})

// Watch filters to reset pagination
watch([search, statusFilter], () => {
  currentPage.value = 1
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
  await fetchSubjects()
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
.rotate-180 {
  transform: rotate(180deg);
}
</style>