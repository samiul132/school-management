<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-1 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-2">
      <div>
        <h1 class="text-xl font-bold text-gray-800">
          Waver Management
        </h1>
      </div>
      
      <router-link 
        :to="{ name: 'wavers.create' }"
        class="inline-flex items-center gap-1 px-3 py-1.5 border bg-blue-600 hover:bg-blue-700 rounded-lg text-gray-100 text-sm font-medium transition-colors"
      >
        <i class="fas fa-plus text-xs"></i>
        New Waver
      </router-link>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-2 mb-2">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
        <!-- Session Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
            Session
            <span v-if="!filters.id_card_number" class="text-red-500">*</span>
          </label>
          <v-select
            v-model="filters.session_id"
            :options="sessions"
            label="session_name"
            :reduce="session => session.id"
            :placeholder="filters.id_card_number ? 'Select Session' : 'Select Session'"
            :clearable="true"
            class="v-select-custom text-sm"
          >
            <template #option="{ session_name, status }">
              <div class="flex items-center justify-between w-full text-sm">
                <div class="flex items-center gap-1">
                  <i class="fas fa-calendar text-blue-500 text-xs"></i>
                  {{ session_name }}
                </div>
                <span v-if="status === 'active'" 
                      class="text-xs bg-green-100 text-green-800 px-1.5 py-0.5 rounded">
                  Active
                </span>
              </div>
            </template>
          </v-select>
        </div>

        <!-- Version Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-book text-gray-400 text-xs"></i>
            Version
            <span v-if="!filters.id_card_number" class="text-red-500">*</span>
          </label>
          <v-select
            v-model="filters.version_id"
            :options="versions"
            label="version_name"
            :reduce="version => version.id"
            :placeholder="filters.id_card_number ? 'Select Version' : 'Select Version'"
            :clearable="true"
            class="v-select-custom text-sm"
          >
            <template #option="{ version_name }">
              <div class="flex items-center gap-1 text-sm">
                <i class="fas fa-book-open text-green-500 text-xs"></i>
                {{ version_name }}
              </div>
            </template>
          </v-select>
        </div>

        <!-- Class Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-graduation-cap text-gray-400 text-xs"></i>
            Class
            <span v-if="!filters.id_card_number" class="text-red-500">*</span>
          </label>
          <v-select
            v-model="filters.class_id"
            :options="classes"
            label="class_name"
            :reduce="classItem => classItem.id"
            :placeholder="filters.id_card_number ? 'Select Class' : 'Select Class'"
            :clearable="true"
            class="v-select-custom text-sm"
          >
            <template #option="{ class_name }">
              <div class="flex items-center gap-1 text-sm">
                <i class="fas fa-school text-purple-500 text-xs"></i>
                {{ class_name }}
              </div>
            </template>
          </v-select>
        </div>

        <!-- Section Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-layer-group text-gray-400 text-xs"></i>
            Section
            <span v-if="!filters.id_card_number" class="text-red-500">*</span>
          </label>
          <v-select
            v-model="filters.section_id"
            :options="sections"
            label="section_name"
            :reduce="section => section.id"
            :placeholder="filters.id_card_number ? 'Select Section' : 'Select Section'"
            :clearable="true"
            class="v-select-custom text-sm"
          >
            <template #option="{ section_name }">
              <div class="flex items-center gap-1 text-sm">
                <i class="fas fa-users text-yellow-500 text-xs"></i>
                {{ section_name }}
              </div>
            </template>
          </v-select>
        </div>

        <!-- Class Roll Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-hashtag text-gray-400 text-xs"></i>
            Class Roll
            <span v-if="!filters.id_card_number" class="text-red-500">*</span>
          </label>
          <input 
            type="number" 
            v-model="filters.roll"
            :placeholder="filters.id_card_number ? 'Search by Class Roll' : 'Search by Class Roll'" 
            class="px-2 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full"
            min="1"
          />
        </div>

        <!-- ID Card Number Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-id-card text-gray-400 text-xs"></i>
            ID Card No.
          </label>
          <input 
            type="number" 
            v-model="filters.id_card_number"
            placeholder="Search by ID Card"
            class="px-2 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full"
            min="1"
          />
        </div>

        <!-- Button Container -->
        <div class="lg:col-span-2">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-2 h-full items-end">
            <button 
              @click="applyFilters"
              class="px-3 py-2.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors flex items-center justify-center gap-1 shadow cursor-pointer"
            >
              <i class="fas fa-filter text-xs"></i>
              Apply Filter
            </button>
            
            <button 
              @click="clearFilters"
              class="px-3 py-2.5 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition-colors flex items-center justify-center gap-1 cursor-pointer"
            >
              <i class="fas fa-times text-xs"></i>
              Clear
            </button>
          </div>
        </div>
      </div>

      <!-- Validation Message -->
      <div v-if="validationError" class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center gap-1 text-red-700 text-xs">
          <i class="fas fa-exclamation-circle text-xs"></i>
          <span>{{ validationError }}</span>
        </div>
      </div>
    </div>

    <!-- Students with Wavers Table -->
    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
      <!-- Table Header -->
      <div class="px-3 py-2 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
          <div class="text-xs text-gray-700">
            <span>Total Students: <span class="font-semibold">{{ paginationInfo.total }}</span></span>
          </div>

          <!-- Top Pagination Info -->
          <div class="flex items-center gap-2">
            <!-- Per Page Selector -->
            <div class="flex items-center gap-1 text-xs">
              <span class="text-gray-600">Show:</span>
              <select 
                v-model="perPage"
                @change="changePerPage"
                class="px-2 py-1 text-xs border border-gray-300 rounded-sm focus:outline-none  bg-white text-gray-700"
              >
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-4 text-center">
        <div class="inline-block">
          <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
          <p class="text-gray-600 text-sm">Loading students with wavers...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && groupedStudents.length === 0" class="p-4 text-center">
        <div class="inline-block p-3">
          <i class="fas fa-file-invoice-dollar text-3xl text-gray-400 mb-2"></i>
          <p class="text-gray-600 text-sm mb-1">No students with wavers found.</p>
          <p class="text-xs text-gray-500">Try adjusting your filters or create a new waver.</p>
        </div>
      </div>

      <!-- Table -->
      <div v-if="!loading && groupedStudents.length > 0" class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-16">SL</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Actions</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Roll</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Wavers</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr 
              v-for="(studentGroup, index) in groupedStudents" 
              :key="studentGroup.class_wise_student_id" 
              class="hover:bg-gray-50 transition-colors"
            >
              <!-- SL -->
              <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ getSerialNumber(index) }}
              </td>

              <!-- Actions -->
              <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <!-- Dropdown Button -->
                  <button 
                    @click="toggleDropdown(studentGroup.class_wise_student_id)"
                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== studentGroup.class_wise_student_id,
                      'bg-gray-200 text-black': openDropdownId === studentGroup.class_wise_student_id
                    }"
                  >
                    Actions
                    <i 
                      class="fas fa-chevron-down text-xs transition-transform duration-200 ml-1"
                      :class="{
                        'rotate-180': openDropdownId === studentGroup.class_wise_student_id,
                        'text-white': openDropdownId !== studentGroup.class_wise_student_id,
                        'text-black': openDropdownId === studentGroup.class_wise_student_id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === studentGroup.class_wise_student_id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- View/Edit Option -->
                      <button
                        @click="viewEditWavers(studentGroup.class_wise_student_id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>View/Edit</span>
                      </button>
                      
                      <!-- Delete All Wavers Option -->
                      <button 
                        @click="handleDeleteStudent(studentGroup)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete All</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Student Info -->
              <td class="px-3 py-2">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                    <img 
                      v-if="studentGroup.student_image_url"
                      :src="studentGroup.student_image_url" 
                      :alt="studentGroup.student_name"
                      class="w-full h-full object-cover"
                    >
                    <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                      <i class="fas fa-user text-gray-400 text-xs"></i>
                    </div>
                  </div>
                  <div>
                    <div class="font-semibold text-gray-800 text-xs">
                      {{ studentGroup.student_name }}
                    </div>
                    <div v-if="studentGroup.id_card_number" class="text-xs text-gray-500">
                      ID: {{ studentGroup.id_card_number }}
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Roll -->
              <td class="px-3 py-2 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                  {{ studentGroup.roll }}
                </span>
              </td>
              
              <!-- Class -->
              <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-800">
                <div class="flex items-center gap-1">
                  <i class="fas fa-graduation-cap text-gray-400 text-xs"></i>
                  {{ studentGroup.class_name }}
                </div>
              </td>
              
              <!-- Section -->
              <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-800">
                <div class="flex items-center gap-1">
                  <i class="fas fa-layer-group text-gray-400 text-xs"></i>
                  {{ studentGroup.section_name }}
                </div>
              </td>
              
              <!-- Total Wavers -->
              <td class="px-3 py-2 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                  <i class="fas fa-file-invoice-dollar text-xs mr-1"></i>
                  {{ studentGroup.total_wavers }} Waver{{ studentGroup.total_wavers > 1 ? 's' : '' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Bottom Pagination -->
      <div v-if="!loading && groupedStudents.length > 0" class="px-3 py-2 border-t border-gray-200 bg-gray-50">
        <div class="flex flex-col md:flex-row justify-between items-center gap-2">
          <!-- Pagination Info -->
          <div class="text-xs text-gray-600">
            Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of {{ paginationInfo.total }} student{{ paginationInfo.total > 1 ? 's' : '' }}
          </div>

          <!-- Pagination Controls -->
          <div class="flex items-center gap-1">
            <!-- Previous Button -->
            <button 
              @click="goToPage(paginationInfo.current_page - 1)"
              :disabled="paginationInfo.current_page === 1"
              class="px-3 py-1.5 border border-gray-300 rounded text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Previous
            </button>

            <!-- Page Numbers -->
            <div class="flex items-center gap-1">
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="goToPage(page)"
                class="px-2 py-2 text-xs rounded-sm border transition-colors min-w-[32px]"
                :class="page === paginationInfo.current_page 
                  ? 'bg-blue-600 text-white border-blue-600' 
                  : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'"
              >
                {{ page }}
              </button>
            </div>

            <!-- Next Button -->
            <button 
              @click="goToPage(paginationInfo.current_page + 1)"
              :disabled="paginationInfo.current_page === paginationInfo.last_page"
              class="px-3 py-1.5 border border-gray-300 rounded text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

const router = useRouter()

// Data
const wavers = ref([])
const loading = ref(false)
const validationError = ref('')
const currentPage = ref(1)
const perPage = ref(10)
const openDropdownId = ref(null)

// Pagination Info
const paginationInfo = ref({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0
})

// Filters
const filters = ref({
  session_id: '',
  version_id: '',
  class_id: '',
  section_id: '',
  roll: '',
  id_card_number: ''
})

// Dropdown data
const sessions = ref([])
const versions = ref([])
const classes = ref([])
const sections = ref([])

// Computed - Group wavers by student
const groupedStudents = computed(() => {
  const grouped = {}
  
  wavers.value.forEach(waver => {
    const studentId = waver.class_wise_student_id
    
    if (!grouped[studentId]) {
      grouped[studentId] = {
        class_wise_student_id: studentId,
        student_name: waver.student_data?.student?.student_name || 'N/A',
        student_image_url: waver.student_data?.student?.student_image_url || null,
        id_card_number: waver.student_data?.student?.id_card_number || null,
        roll: waver.roll || waver.student_data?.class_roll || 'N/A',
        class_name: waver.class?.class_name || 'N/A',
        section_name: waver.section?.section_name || 'N/A',
        session_name: waver.session?.session_name || 'N/A',
        wavers: [],
        total_wavers: 0
      }
    }
    
    grouped[studentId].wavers.push(waver)
    grouped[studentId].total_wavers++
  })
  
  return Object.values(grouped)
})

// Computed - Visible page numbers
const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  const lastPage = paginationInfo.value.last_page
  const current = paginationInfo.value.current_page
  
  let start = Math.max(1, current - Math.floor(maxVisible / 2))
  let end = Math.min(lastPage, start + maxVisible - 1)
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const fetchDropdownData = async () => {
  try {
    const response = await axios.get('/api/wavers/dropdown-data')
    if (response.data.success) {
      sessions.value = response.data.data.sessions || []
      versions.value = response.data.data.versions || []
      classes.value = response.data.data.classes || []
      sections.value = response.data.data.sections || []
      
      // Auto-select active session
      const activeSession = response.data.data.active_session
      if (activeSession && activeSession.id) {
        filters.value.session_id = activeSession.id
      }
    }
  } catch (error) {
    console.error('Error fetching dropdown data:', error)
  }
}

const fetchWavers = async (page = 1) => {
  loading.value = true
  
  try {
    const params = {
      page: page,
      per_page: perPage.value
    }
    
    // Add filter parameters if they exist
    if (filters.value.session_id) params.session_id = filters.value.session_id
    if (filters.value.version_id) params.version_id = filters.value.version_id
    if (filters.value.class_id) params.class_id = filters.value.class_id
    if (filters.value.section_id) params.section_id = filters.value.section_id
    if (filters.value.roll) params.roll = filters.value.roll
    
    // Handle ID Card Number search differently
    if (filters.value.id_card_number) {
      params.student_id_card = filters.value.id_card_number
    }
    
    const response = await axios.get('/api/wavers', { params })
    
    if (response.data.success) {
      // Update pagination info
      const data = response.data.data
      paginationInfo.value = {
        current_page: data.current_page || 1,
        last_page: data.last_page || 1,
        from: data.from || 0,
        to: data.to || 0,
        total: data.total || 0
      }
      
      // Fix image paths
      const waversData = data.data || []
      
      // Apply client-side filtering for ID card if backend doesn't support it
      let filteredWavers = waversData
      
      if (filters.value.id_card_number) {
        filteredWavers = waversData.filter(waver => {
          const studentIdCard = waver.student_data?.student?.id_card_number
          return studentIdCard && studentIdCard.toString() === filters.value.id_card_number.toString()
        })
      }
      
      wavers.value = filteredWavers.map(waver => {
        if (waver.student_data?.student?.student_image) {
          waver.student_data.student.student_image_url = `/assets/images/student_images/${waver.student_data.student.student_image}`
        } else {
          waver.student_data.student.student_image_url = '/assets/images/default-avatar.png'
        }
        return waver
      })
    } else {
      wavers.value = []
      paginationInfo.value = {
        current_page: 1,
        last_page: 1,
        from: 0,
        to: 0,
        total: 0
      }
    }
  } catch (error) {
    console.error('Error fetching wavers:', error)
    wavers.value = []
    paginationInfo.value = {
      current_page: 1,
      last_page: 1,
      from: 0,
      to: 0,
      total: 0
    }
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  // Validate filters
  if (!filters.value.id_card_number) {
    // If no ID card, all other fields are required
    if (!filters.value.session_id || !filters.value.version_id || 
        !filters.value.class_id || !filters.value.section_id || !filters.value.roll) {
      validationError.value = 'Please fill all required fields (Session, Version, Class, Section, Roll) or search by ID Card Number'
      return
    }
  }
  
  validationError.value = ''
  currentPage.value = 1
  fetchWavers(1)
}

const clearFilters = () => {
  filters.value = {
    session_id: '',
    version_id: '',
    class_id: '',
    section_id: '',
    roll: '',
    id_card_number: ''
  }
  validationError.value = ''
  currentPage.value = 1
  fetchWavers(1)
}

const goToPage = (page) => {
  if (page >= 1 && page <= paginationInfo.value.last_page) {
    currentPage.value = page
    fetchWavers(page)
  }
}

const changePerPage = () => {
  currentPage.value = 1
  fetchWavers(1)
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

const viewEditWavers = (classWiseStudentDataId) => {
  closeDropdown()
  router.push({ 
    name: 'wavers.student.edit', 
    params: { class_wise_student_id: classWiseStudentDataId } 
  })
}

const handleDeleteStudent = async (studentGroup) => {
  closeDropdown()
  
  // For now, using browser confirm
  const confirmed = confirm(
    `Are you sure you want to delete all ${studentGroup.total_wavers} waver(s) for ${studentGroup.student_name}? This action cannot be undone.`
  )
  
  if (confirmed) {
    await deleteStudentWavers(studentGroup)
  }
}

const deleteStudentWavers = async (studentGroup) => {
  try {
    // Delete all wavers for this student
    const deletePromises = studentGroup.wavers.map(waver => 
      axios.delete(`/api/wavers/${waver.id}`)
    )
    
    await Promise.all(deletePromises)
    
    alert(`Successfully deleted ${studentGroup.total_wavers} waver(s) for ${studentGroup.student_name}`)
    
    // Refresh the list
    fetchWavers(currentPage.value)
  } catch (error) {
    console.error('Error deleting wavers:', error)
    alert('Failed to delete wavers. Please try again.')
  }
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    openDropdownId.value = null
  }
}

const getSerialNumber = (index) => {
  return (paginationInfo.value.current_page - 1) * perPage.value + index + 1
}

// Lifecycle
onMounted(async () => {
  await fetchDropdownData()
  await fetchWavers()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Animation for loading spinner */
.fa-spinner.fa-spin {
  animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>