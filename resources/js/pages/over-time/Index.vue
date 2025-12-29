<template>
  <AppLayout>
    <!-- Header with Create Button -->
    <div class="mb-3 flex flex-col md:flex-row justify-between md:items-center gap-4">
      <!-- Title Section -->
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Overtime Records</h1>
        <p class="text-gray-600">Manage all overtime records</p>
      </div>

      <!-- Button -->
      <router-link 
        :to="{ name: 'over-time.create' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
            text-white px-3 py-2 rounded-lg font-semibold transition-colors w-full md:w-auto"
      >
        <i class="fas fa-plus"></i>
        Add Overtime Record
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
              placeholder="Search overtime records..." 
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designation</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime Hours</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="overtime in paginatedOvertimes" :key="overtime.id" class="hover:bg-gray-50">
              <!-- ID -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ overtime.id }}
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <button 
                    @click="toggleDropdown(overtime.id)"
                    class="inline-flex items-center justify-center px-2 h-8 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== overtime.id,
                      'bg-gray-200 text-black': openDropdownId === overtime.id
                    }"
                  >
                    Action
                    <i 
                      class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                      :class="{
                        'rotate-180': openDropdownId === overtime.id,
                        'text-white': openDropdownId !== overtime.id,
                        'text-black': openDropdownId === overtime.id
                      }"
                    ></i>
                  </button>
                  
                  <div 
                    v-if="openDropdownId === overtime.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <router-link 
                        :to="{ name: 'over-time.edit', params: { id: overtime.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Record</span>
                      </router-link>
                      
                      <button 
                        @click="handleDeleteOvertime(overtime.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete Record</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Date -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ formatDate(overtime.date) }}
              </td>

              <!-- Staff Name -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                {{ overtime.staff.first_name }} {{ overtime.staff.last_name }}
              </td>
              
              <!-- Designation -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs">
                  {{ overtime.staff.designation.name }}
                </span>
              </td>

              <!-- Overtime Hours -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full">
                  {{ overtime.over_time_hour }} hr
                </span>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(overtime.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(overtime.created_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading overtime records...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && overtimes.length === 0" class="p-8 text-center">
        <i class="fas fa-clock text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No overtime records found.</p>
        <router-link 
          :to="{ name: 'over-time.create' }"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Overtime
        </router-link>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && overtimes.length > 0 && filteredOvertimes.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No overtime records match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredOvertimes.length > 0" class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredOvertimes.length }} results
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
const overtimes = ref([])
const loading = ref(true)
const search = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const openDropdownId = ref(null)

const fetchOvertimes = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/over-time')
    
    if (response.data.success) {
      overtimes.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      overtimes.value = response.data
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to load overtime records')
    }
  } catch (error) {
    console.error('Error fetching overtimes:', error)
    showErrorAlert('Error', 'Failed to load overtime records. Please try again.')
  } finally {
    loading.value = false
  }
}

const filteredOvertimes = computed(() => {
  let filtered = overtimes.value
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(overtime => 
      overtime.staff.first_name?.toLowerCase().includes(searchTerm) ||
      overtime.staff.last_name?.toLowerCase().includes(searchTerm) ||
      overtime.staff.designation.name?.toLowerCase().includes(searchTerm) ||
      overtime.over_time_hour?.toString().includes(searchTerm)
    )
  }
  
  return filtered
})

const totalPages = computed(() => Math.ceil(filteredOvertimes.value.length / itemsPerPage.value))

const paginatedOvertimes = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredOvertimes.value.slice(start, end)
})

const showingStart = computed(() => {
  return filteredOvertimes.value.length === 0 ? 0 : (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredOvertimes.value.length ? filteredOvertimes.value.length : end
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

const deleteOvertime = async (id) => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You won\'t be able to revert this!'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting overtime record...')
      
      const response = await axios.delete(`/api/over-time/${id}`)
      
      if (response.data.success) {
        overtimes.value = overtimes.value.filter(overtime => overtime.id !== id)
        closeAlert()
        await showSuccessAlert('Deleted!', response.data.message || 'Overtime record has been deleted.')
      } else {
        closeAlert()
        showErrorAlert('Error', response.data.message || 'Failed to delete overtime record')
      }
    } catch (error) {
      closeAlert()
      console.error('Error deleting overtime:', error)
      showErrorAlert('Error', 'Failed to delete overtime record')
    }
  }
}

const handleDeleteOvertime = async (id) => {
  closeDropdown()
  await deleteOvertime(id)
}

const toggleDropdown = (id) => {
  openDropdownId.value = openDropdownId.value === id ? null : id
}

const closeDropdown = () => {
  openDropdownId.value = null
}

const clearFilters = () => {
  search.value = ''
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

watch([search], () => {
  currentPage.value = 1
})

watch(itemsPerPage, () => {
  currentPage.value = 1
})

onMounted(async () => {
  await fetchOvertimes()
  document.addEventListener('click', handleClickOutside)
  
  if (route.query.created === 'true') {
    showSuccessAlert('Success!', 'Overtime record created successfully.')
    router.replace({ name: 'over-time.index' })
  }
  
  if (route.query.updated === 'true') {
    showSuccessAlert('Success!', 'Overtime record updated successfully.')
    router.replace({ name: 'over-time.index' })
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>