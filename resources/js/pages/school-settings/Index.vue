<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <!-- Title + Buttons -->
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <!-- Title -->
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            School Settings
          </h1>
          <p class="text-gray-600 mt-2">
            Configure your school information
          </p>
        </div>

        <!-- Create Button -->
        <div class="flex gap-3">
          <router-link
            :to="{ name: 'school-settings.create' }"
            class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
                    text-white px-4 py-2.5 rounded-lg font-semibold transition-colors"
          >
            <i class="fas fa-plus"></i>
            Create School Settings
          </router-link>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Schools</p>
            <p class="text-3xl font-bold text-gray-800">{{ totalSchools }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
            <i class="fas fa-school text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">With Logo</p>
            <p class="text-3xl font-bold text-green-600">{{ withLogo }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
            <i class="fas fa-image text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Complete Info</p>
            <p class="text-3xl font-bold text-purple-600">{{ completeInfo }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-500 flex items-center justify-center">
            <i class="fas fa-check-circle text-white text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <!-- Table Header with Search and Filters -->
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
              placeholder="Search schools..." 
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>

          <!-- Contact Filter -->
          <select 
            v-model="contactFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
          >
            <option value="">All Contact</option>
            <option value="email">Email Only</option>
            <option value="mobile">Mobile Only</option>
            <option value="both">Both Email & Mobile</option>
            <option value="none">No Contact Info</option>
          </select>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1200px]">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Logo</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">School Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Address</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Contact</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Updated At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="school in paginatedSchools" :key="school.id" class="hover:bg-gray-50">
              <!-- ID -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ school.id }}
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <!-- Dropdown Button -->
                  <button 
                    @click="toggleDropdown(school.id)"
                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== school.id,
                      'bg-gray-200 text-black': openDropdownId === school.id
                    }"
                  >
                    Actions
                    <i 
                      class="fas fa-chevron-down text-xs transition-transform duration-200 ml-1"
                      :class="{
                        'rotate-180': openDropdownId === school.id,
                        'text-white': openDropdownId !== school.id,
                        'text-black': openDropdownId === school.id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === school.id"
                    class="absolute left-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- View Option -->
                      <router-link 
                        :to="{ name: 'school-settings.show', params: { id: school.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-eye text-green-600 text-xs w-4"></i>
                        <span>View Details</span>
                      </router-link>
                      
                      <!-- Edit Option -->
                      <router-link 
                        :to="{ name: 'school-settings.edit', params: { id: school.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit School</span>
                      </router-link>
                      
                      <!-- Delete Option -->
                      <button 
                        @click="handleDeleteSchool(school.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete School</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Logo -->
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200">
                  <img 
                    v-if="school.logo_url"
                    :src="school.logo_url"
                    :alt="school.school_name"
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-school text-gray-400 text-xl"></i>
                  </div>
                </div>
              </td>
              
              <!-- School Name -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <div class="truncate max-w-[200px]" :title="school.school_name">
                    {{ school.school_name }}
                  </div>
                </div>
              </td>
              
              <!-- Address -->
              <td class="px-4 py-4 text-sm text-gray-600">
                <div class="max-w-[200px]">
                  <div v-if="school.address && school.address.trim() !== ''" 
                       class="line-clamp-2" 
                       :title="school.address">
                    {{ truncateText(school.address, 50) }}
                  </div>
                  <div v-else class="text-gray-400 italic text-xs">
                    No address
                  </div>
                </div>
              </td>
              
              <!-- Contact -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <div class="space-y-1">
                  <div v-if="school.email" class="flex items-center gap-1">
                    <i class="fas fa-envelope text-gray-400 text-xs"></i>
                    <span class="text-gray-700 truncate max-w-[150px]" :title="school.email">
                      {{ school.email }}
                    </span>
                  </div>
                  <div v-if="school.mobile_number" class="flex items-center gap-1">
                    <i class="fas fa-phone text-gray-400 text-xs"></i>
                    <span class="text-gray-700">{{ school.mobile_number }}</span>
                  </div>
                  <div v-if="!school.email && !school.mobile_number" class="text-gray-400 italic text-xs">
                    No contact info
                  </div>
                </div>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(school.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(school.created_at) }}</span>
                </div>
              </td>
              
              <!-- Updated At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(school.updated_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(school.updated_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading school settings...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && schools.length === 0" class="p-8 text-center">
        <i class="fas fa-school text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No school settings found.</p>
        <router-link 
          :to="{ name: 'school-settings.create' }"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First School
        </router-link>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && schools.length > 0 && filteredSchools.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No schools match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredSchools.length > 0" class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredSchools.length }} results
        </div>
        
        <div class="flex items-center gap-2">
          <!-- Previous Button -->
          <button 
            @click="previousPage"
            :disabled="currentPage === 1"
            class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <i class="fas fa-chevron-left"></i>
          </button>
          
          <!-- Page Numbers -->
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
          
          <!-- Next Button -->
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
import { 
  showSuccessAlert, 
  showErrorAlert, 
  showConfirmDialog, 
  showLoadingAlert, 
  closeAlert 
} from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const schools = ref([])
const loading = ref(true)
const search = ref('')
const logoFilter = ref('')
const contactFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const openDropdownId = ref(null)

const fetchSchoolSettings = async () => {
  try {
    loading.value = true
    
    const response = await axios.get('/api/school-settings')
    
    if (response.data.success && response.data.data) {
      const data = response.data.data
      
      if (data.id && !Array.isArray(data)) {
        schools.value = [data] 
      } 
      else if (Array.isArray(data)) {
        schools.value = data
      }
    }
    
  } catch (error) {
    console.error('Error:', error)
    schools.value = []
  } finally {
    loading.value = false
  }
}
// Delete school
const deleteSchool = async (id) => {
  const result = await showConfirmDialog(
    'Delete School?',
    'Are you sure you want to delete this school? This action cannot be undone!',
    'warning'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting school...')
      
      const response = await axios.delete(`/api/school-settings/${id}`)
      
      if (response.status === 200 || response.data?.message) {
        // Remove from list
        schools.value = schools.value.filter(school => school.id !== id)
        
        closeAlert()
        await showSuccessAlert('Deleted!', 'School deleted successfully.')
      } else {
        closeAlert()
        showErrorAlert('Error', 'Failed to delete school')
      }
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting school:', error)
      
      if (error.response?.status === 404) {
        showErrorAlert('Error', 'School not found')
      } else if (error.response?.status === 500) {
        showErrorAlert('Error', 'Server error. Please try again later.')
      } else {
        showErrorAlert('Error', 'Failed to delete school')
      }
    }
  }
}

const handleDeleteSchool = async (id) => {
  closeDropdown()
  await deleteSchool(id)
}

// Utility functions
const truncateText = (text, maxLength = 50) => {
  if (!text) return ''
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
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

const toggleDropdown = (id) => {
  if (openDropdownId.value === id) {
    openDropdownId.value = null
  } else {
    openDropdownId.value = id
  }
}

const closeDropdown = () => {
  openDropdownId.value = null
}

const clearFilters = () => {
  search.value = ''
  logoFilter.value = ''
  contactFilter.value = ''
  currentPage.value = 1
}

// Computed properties
const totalSchools = computed(() => schools.value.length)

const withLogo = computed(() => 
  schools.value.filter(school => school.logo).length
)

const completeInfo = computed(() => 
  schools.value.filter(school => 
    school.school_name && 
    school.address && 
    school.email && 
    school.mobile_number
  ).length
)

const filteredSchools = computed(() => {
  let filtered = schools.value
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(school => 
      school.school_name?.toLowerCase().includes(searchTerm) ||
      school.address?.toLowerCase().includes(searchTerm) ||
      school.email?.toLowerCase().includes(searchTerm) ||
      school.mobile_number?.includes(searchTerm)
    )
  }
  
  if (logoFilter.value === 'yes') {
    filtered = filtered.filter(school => school.logo)
  } else if (logoFilter.value === 'no') {
    filtered = filtered.filter(school => !school.logo)
  }
  
  if (contactFilter.value) {
    switch (contactFilter.value) {
      case 'email':
        filtered = filtered.filter(school => school.email && !school.mobile_number)
        break
      case 'mobile':
        filtered = filtered.filter(school => school.mobile_number && !school.email)
        break
      case 'both':
        filtered = filtered.filter(school => school.email && school.mobile_number)
        break
      case 'none':
        filtered = filtered.filter(school => !school.email && !school.mobile_number)
        break
    }
  }
  
  return filtered
})

const totalPages = computed(() => Math.ceil(filteredSchools.value.length / itemsPerPage.value))

const paginatedSchools = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredSchools.value.slice(start, end)
})

const showingCount = computed(() => paginatedSchools.value.length)

const showingStart = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredSchools.value.length ? filteredSchools.value.length : end
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

// Watchers
watch([search, logoFilter, contactFilter], () => {
  currentPage.value = 1
})

watch(itemsPerPage, () => {
  currentPage.value = 1
})

// Lifecycle
onMounted(async () => {
  await fetchSchoolSettings()
  
  document.addEventListener('click', handleClickOutside)
  
  if (route.query.created === 'true') {
    await showSuccessAlert('Success!', 'School settings created successfully.')
    router.replace({ name: 'school-settings.index' })
  }
  
  if (route.query.updated === 'true') {
    await showSuccessAlert('Success!', 'School settings updated successfully.')
    router.replace({ name: 'school-settings.index' })
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>