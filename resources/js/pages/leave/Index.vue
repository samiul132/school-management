<template>
  <AppLayout>
    <!-- Header with Create Button -->
    <div class="mb-3 flex flex-col md:flex-row justify-between md:items-center gap-4">
      <!-- Title Section -->
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Leave Applications</h1>
        <p class="text-gray-600">Manage all leave applications</p>
      </div>

      <!-- Button -->
      <router-link 
        :to="{ name: 'leave.create' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
            text-white px-3 py-2 rounded-lg font-semibold transition-colors w-full md:w-auto"
      >
        <i class="fas fa-plus"></i>
        Create Leave Application
      </router-link>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <!-- Table Header with Search -->
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <!-- Items Per Page -->
        <div class="flex items-center gap-2 text-sm text-gray-700">
          <span>Show:</span>
          <select 
            v-model="itemsPerPage"
            class="px-2 py-1 border border-gray-300 rounded bg-white text-gray-800"
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span>per page</span>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
          <!-- Search Box -->
          <div class="relative">
            <input 
              type="text" 
              v-model="search"
              placeholder="Search leaves..." 
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>

          <!-- Status Filter -->
          <select 
            v-model="statusFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
          >
            <option value="">All Status</option>
            <option value="PENDING">Pending</option>
            <option value="APPROVED">Approved</option>
            <option value="DECLINED">Declined</option>
          </select>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">SL</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designation</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application Date</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From Date</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To Date</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Days</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="leave in paginatedLeaves" :key="leave.id" class="hover:bg-gray-50">
              <!-- ID -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ getRowNumber(leave) }}
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <button 
                    @click="toggleDropdown(leave.id)"
                    class="inline-flex items-center justify-center px-2 h-8 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== leave.id,
                      'bg-gray-200 text-black': openDropdownId === leave.id
                    }"
                  >
                    Action
                    <i 
                      class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                      :class="{
                        'rotate-180': openDropdownId === leave.id,
                        'text-white': openDropdownId !== leave.id,
                        'text-black': openDropdownId === leave.id
                      }"
                    ></i>
                  </button>
                  
                  <div 
                    v-if="openDropdownId === leave.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <router-link 
                        :to="{ name: 'leave.edit', params: { id: leave.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Leave</span>
                      </router-link>
                      
                      <button 
                        @click="handleDeleteLeave(leave.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete Leave</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Staff Name -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                {{ leave.staff.first_name }} {{ leave.staff.last_name }}
              </td>
              
              <!-- Designation -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs">
                  {{ leave.staff.designation.name }}
                </span>
              </td>

              <!-- Application Date -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ formatDate(leave.application_date) }}
              </td>

              <!-- From Date -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ formatDate(leave.leave_from_date) }}
              </td>

              <!-- To Date -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ formatDate(leave.leave_to_date) }}
              </td>

              <!-- Total Days -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                {{ leave.total_leave }} days
              </td>
              
              <!-- Status -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <span 
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-medium',
                    leave.status === 'APPROVED' 
                      ? 'bg-green-100 text-green-800' 
                      : leave.status === 'PENDING'
                      ? 'bg-yellow-100 text-yellow-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ leave.status }}
                </span>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(leave.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(leave.created_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading leaves...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && leaves.length === 0" class="p-8 text-center">
        <i class="fas fa-calendar-alt text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No leave applications found.</p>
        <router-link 
          :to="{ name: 'leave.create' }"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Create First Leave
        </router-link>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && leaves.length > 0 && filteredLeaves.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No leaves match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredLeaves.length > 0" class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredLeaves.length }} results
        </div>
        
        <div class="flex items-center gap-2">
          <button 
            @click="previousPage"
            :disabled="currentPage === 1"
            class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <i class="fas fa-chevron-left"></i>
          </button>
          
          <div class="flex items-center gap-1">
            <button 
              v-for="page in visiblePages"
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                currentPage === page
                  ? 'bg-blue-600 text-white'
                  : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            
            <span v-if="hasMorePages" class="px-2 text-gray-500">...</span>
          </div>
          
          <button 
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showConfirmDialog, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const leaves = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const openDropdownId = ref(null)

const fetchLeaves = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/leave')
    
    if (response.data.success) {
      leaves.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      leaves.value = response.data
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to load leaves')
    }
  } catch (error) {
    console.error('Error fetching leaves:', error)
    showErrorAlert('Error', 'Failed to load leaves. Please try again.')
  } finally {
    loading.value = false
  }
}

const filteredLeaves = computed(() => {
  let filtered = leaves.value
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(leave => 
      leave.staff.first_name?.toLowerCase().includes(searchTerm) ||
      leave.staff.last_name?.toLowerCase().includes(searchTerm) ||
      leave.staff.designation.name?.toLowerCase().includes(searchTerm)
    )
  }
  
  if (statusFilter.value) {
    filtered = filtered.filter(leave => 
      leave.status === statusFilter.value
    )
  }
  
  return filtered
})

const totalPages = computed(() => Math.ceil(filteredLeaves.value.length / itemsPerPage.value))

const paginatedLeaves = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredLeaves.value.slice(start, end)
})

const showingStart = computed(() => {
  return filteredLeaves.value.length === 0 ? 0 : (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredLeaves.value.length ? filteredLeaves.value.length : end
})

const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  let start = Math.max(1, currentPage.value - 2)
  let end = Math.min(total, start + 4)
  
  if (end - start < 4) {
    start = Math.max(1, end - 4)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

const hasMorePages = computed(() => {
  return visiblePages.value[visiblePages.value.length - 1] < totalPages.value
})

const deleteLeave = async (id) => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You won\'t be able to revert this!'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting leave...')
      
      const response = await axios.delete(`/api/leave/${id}`)
      
      if (response.data.success) {
        leaves.value = leaves.value.filter(leave => leave.id !== id)
        closeAlert()
        await showSuccessAlert('Deleted!', response.data.message || 'Leave has been deleted.')
      } else {
        closeAlert()
        showErrorAlert('Error', response.data.message || 'Failed to delete leave')
      }
    } catch (error) {
      closeAlert()
      console.error('Error deleting leave:', error)
      showErrorAlert('Error', 'Failed to delete leave')
    }
  }
}

const handleDeleteLeave = async (id) => {
  closeDropdown()
  await deleteLeave(id)
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
  currentPage.value = 1
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const goToPage = (page) => {
  currentPage.value = page
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    openDropdownId.value = null
  }
}

const getRowNumber = (leave) => {
  const index = filteredLeaves.value.findIndex(s => s.id === leave.id)
  return index + 1
}

watch([search, statusFilter], () => {
  currentPage.value = 1
})

watch(itemsPerPage, () => {
  currentPage.value = 1
})

onMounted(async () => {
  await fetchLeaves()
  document.addEventListener('click', handleClickOutside)
  
  if (route.query.created === 'true') {
    showSuccessAlert('Success!', 'Leave created successfully.')
    router.replace({ name: 'leave.index' })
  }
  
  if (route.query.updated === 'true') {
    showSuccessAlert('Success!', 'Leave updated successfully.')
    router.replace({ name: 'leave.index' })
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>