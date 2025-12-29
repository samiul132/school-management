<template>
  <AppLayout>
    <!-- Header with Create Button -->
    <div class="mb-3 flex flex-col md:flex-row justify-between md:items-center gap-4">
      <!-- Title Section -->
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Staffs</h1>
        <p class="text-gray-600">Manage all staffs</p>
      </div>

      <!-- Button -->
      <router-link 
        :to="{ name: 'staffs.create' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
            text-white px-3 py-2 rounded-lg font-semibold transition-colors w-full md:w-auto"
      >
        <i class="fas fa-plus"></i>
        Create New Staff
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
              placeholder="Search staffs..." 
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
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1400px]">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Photo</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Father's Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Mother's Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Designation</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Type</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Subsidiary</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Marital Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Date of Birth</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Joining Date</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Phone</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Email</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Qualification</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Address</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">NID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Basic Salary</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Working Hour</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="staff in paginatedStaffs" :key="staff.id" class="hover:bg-gray-50">
              <!-- ID -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ staff.id }}
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <button 
                    @click="toggleDropdown(staff.id)"
                    class="inline-flex items-center justify-center px-2 h-8 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== staff.id,
                      'bg-gray-200 text-black': openDropdownId === staff.id
                    }"
                  >
                    Action
                    <i 
                      class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                      :class="{
                        'rotate-180': openDropdownId === staff.id,
                        'text-white': openDropdownId !== staff.id,
                        'text-black': openDropdownId === staff.id
                      }"
                    ></i>
                  </button>
                  
                  <div 
                    v-if="openDropdownId === staff.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <router-link 
                        :to="{ name: 'staffs.edit', params: { id: staff.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Staff</span>
                      </router-link>
                      
                      <button 
                        @click="handleDeleteStaff(staff.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete Staff</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Photo -->
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200">
                  <img 
                    v-if="staff.photo"
                    :src="getImageUrl(staff.photo)"
                    :alt="staff.first_name"
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-user text-gray-400 text-xl"></i>
                  </div>
                </div>
              </td>
              
              <!-- Name -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                <div class="truncate max-w-[180px]" :title="staff.first_name + ' ' + staff.last_name">
                  {{ staff.first_name }} {{ staff.last_name }}
                </div>
              </td>
              
              <!-- Father's Name -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="truncate max-w-[150px]" :title="staff.fathers_name">
                  {{ staff.fathers_name || 'N/A' }}
                </div>
              </td>

              <!-- Mother's Name -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="truncate max-w-[150px]" :title="staff.mothers_name">
                  {{ staff.mothers_name || 'N/A' }}
                </div>
              </td>

              <!-- Designation -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs">
                  {{ staff.designation.name }}
                </span>
              </td>

              <!-- Type (Teacher/Staff) -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <span 
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-medium inline-flex items-center gap-1',
                    staff.is_teachers === 1 
                      ? 'bg-purple-100 text-purple-800' 
                      : 'bg-gray-100 text-gray-800'
                  ]"
                >
                  <i 
                    :class="[
                      'fas text-xs',
                      staff.is_teachers === 1 ? 'fa-chalkboard-teacher' : 'fa-user-tie'
                    ]"
                  ></i>
                  {{ staff.is_teachers === 1 ? 'Teacher' : 'Staff' }}
                </span>
              </td>

              <!-- Subsidiary -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <span class="px-2 py-1 rounded-full text-xs">
                  {{ staff.subsidiary.name }}
                </span>
              </td>

              <!-- Marital Status -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ staff.maritial_status || 'N/A' }}
              </td>

              <!-- Date of Birth -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ formatDate(staff.date_of_birth) }}
              </td>

              <!-- Joining Date -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ formatDate(staff.joining_date) }}
              </td>

              <!-- Phone -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ staff.phone || 'N/A' }}
              </td>

              <!-- Email -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="truncate max-w-[150px]" :title="staff.email">
                  {{ staff.email || 'N/A' }}
                </div>
              </td>

              <!-- Qualification -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ staff.qualification || 'N/A' }}
              </td>

              <!-- Address -->
              <td class="px-4 py-4 text-sm text-gray-800">
                <div class="max-w-[200px] truncate" :title="staff.address">
                  {{ staff.address || 'N/A' }}
                </div>
              </td>

              <!-- NID -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ staff.nid || 'N/A' }}
              </td>

              <!-- Basic Salary -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                ৳{{ formatPrice(staff.basic_salary) }}
              </td>

              <!-- Working Hour -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ staff.working_hour }} hrs
              </td>
              
              <!-- Status -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <span 
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-medium',
                    staff.status === 'active' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ staff.status }}
                </span>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(staff.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(staff.created_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading staffs...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && staffs.length === 0" class="p-8 text-center">
        <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No staffs found.</p>
        <router-link 
          :to="{ name: 'staffs.create' }"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Staff
        </router-link>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && staffs.length > 0 && filteredStaffs.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No staffs match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredStaffs.length > 0" class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredStaffs.length }} results
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
const staffs = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const openDropdownId = ref(null)

const fetchStaffs = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/staffs')
    
    if (response.data.success) {
      staffs.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      staffs.value = response.data
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to load staffs')
    }
  } catch (error) {
    console.error('Error fetching staffs:', error)
    showErrorAlert('Error', 'Failed to load staffs. Please try again.')
  } finally {
    loading.value = false
  }
}

const totalStaffs = computed(() => staffs.value.length)

const activeStaffs = computed(() => 
  staffs.value.filter(staff => staff.status === 'active').length
)

const showingCount = computed(() => paginatedStaffs.value.length)

const filteredStaffs = computed(() => {
  let filtered = staffs.value
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(staff => 
      staff.first_name?.toLowerCase().includes(searchTerm) ||
      staff.last_name?.toLowerCase().includes(searchTerm)
    )
  }
  
  if (statusFilter.value) {
    filtered = filtered.filter(staff => 
      staff.status === statusFilter.value
    )
  }
  
  return filtered
})

const totalPages = computed(() => Math.ceil(filteredStaffs.value.length / itemsPerPage.value))

const paginatedStaffs = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredStaffs.value.slice(start, end)
})

const showingStart = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredStaffs.value.length ? filteredStaffs.value.length : end
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

const deleteStaff = async (id) => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You won\'t be able to revert this!'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting staff...')
      
      const response = await axios.delete(`/api/staffs/${id}`)
      
      if (response.data.success) {
        staffs.value = staffs.value.filter(staff => staff.id !== id)
        closeAlert()
        await showSuccessAlert('Deleted!', response.data.message || 'Staff has been deleted.')
      } else {
        closeAlert()
        showErrorAlert('Error', response.data.message || 'Failed to delete staff')
      }
    } catch (error) {
      closeAlert()
      console.error('Error deleting staff:', error)
      showErrorAlert('Error', 'Failed to delete staff')
    }
  }
}

const handleDeleteStaff = async (id) => {
  closeDropdown()
  await deleteStaff(id)
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

const formatPrice = (price) => {
  if (!price) return '0.00'
  return parseFloat(price).toFixed(2)
}

const getImageUrl = (imagePath) => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http')) return imagePath
  if (!imagePath.includes('/')) return `/assets/images/staffs/${imagePath}`
  return imagePath
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

watch([search, statusFilter], () => {
  currentPage.value = 1
})

watch(itemsPerPage, () => {
  currentPage.value = 1
})

onMounted(async () => {
  await fetchStaffs()
  document.addEventListener('click', handleClickOutside)
  
  if (route.query.created === 'true') {
    showSuccessAlert('Success!', 'Staff created successfully.')
    router.replace({ name: 'staffs.index' })
  }
  
  if (route.query.updated === 'true') {
    showSuccessAlert('Success!', 'Staff updated successfully.')
    router.replace({ name: 'staffs.index' })
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>