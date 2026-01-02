<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-2 mt-2">
      <h1 class="text-3xl font-bold text-gray-800">Student ID Cards</h1>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-3 mb-4">
      <div class="flex items-center gap-2 mb-3">
        <i class="fas fa-filter text-blue-600 text-sm"></i>
        <h3 class="text-base font-semibold text-gray-800">Filters</h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <!-- Session Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Session</label>
          <v-select
            v-model="filters.session_id"
            :options="availableSessions"
            label="session_name"
            :reduce="s => s.id"
            placeholder="Select Session"
            class="v-select-custom"
          />
        </div>

        <!-- Class Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Class</label>
          <v-select
            v-model="filters.class_id"
            :options="availableClasses"
            label="class_name"
            :reduce="c => c.id"
            placeholder="Select Class"
            class="v-select-custom"
          />
        </div>

        <!-- Section Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Section</label>
          <v-select
            v-model="filters.section_id"
            :options="availableSections"
            label="section_name"
            :reduce="s => s.id"
            placeholder="Select Section"
            class="v-select-custom"
          />
        </div>

        <!-- Search -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
          <input 
            type="text" 
            v-model="search"
            placeholder="Name, ID or Roll..." 
            class="px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full text-sm"
          >
        </div>
      </div>

      <div class="mt-3 flex gap-2">
        <button 
          @click="applyFilters"
          class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors"
        >
          <i class="fas fa-search mr-1 text-xs"></i>Apply
        </button>
        <button 
          @click="clearFilters"
          class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors"
        >
          <i class="fas fa-times mr-1 text-xs"></i>Clear
        </button>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
      <!-- Table Header -->
      <div class="px-4 py-2 border-b border-gray-200 flex justify-between items-center">
        <div class="text-xs text-gray-700">
          <span class="font-medium">{{ filteredStudents.length }}</span> students
        </div>
        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-700">Show:</span>
          <select 
            v-model="itemsPerPage"
            class="px-2 py-1 border border-gray-300 rounded text-xs bg-white text-gray-800"
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">SL</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Photo</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID Number</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Academic Info</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(student, index) in paginatedStudents" :key="student.id" class="hover:bg-gray-50">
              <!-- SL -->
              <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                {{ (currentPage - 1) * itemsPerPage + index + 1 }}
              </td>

              <!-- Photo -->
              <td class="px-3 py-2 whitespace-nowrap">
                <div class="w-12 h-12 rounded overflow-hidden border border-gray-200">
                  <img 
                    v-if="student.student_image_url"
                    :src="student.student_image_url" 
                    :alt="student.student_name"
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-user text-gray-400 text-lg"></i>
                  </div>
                </div>
              </td>

              <!-- Student Name -->
              <td class="px-3 py-2 whitespace-nowrap">
                <div class="font-medium text-sm text-gray-900">{{ student.student_name }}</div>
                <div v-if="getClassRoll(student)" class="text-xs text-gray-500">Roll: {{ getClassRoll(student) }}</div>
              </td>

              <!-- ID Card Number -->
              <td class="px-3 py-2 whitespace-nowrap">
                <span v-if="student.id_card_number" class="font-mono bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                  {{ student.id_card_number }}
                </span>
                <span v-else class="text-gray-400 text-xs">No ID</span>
              </td>

              <!-- Academic Info -->
              <td class="px-3 py-2">
                <div v-if="student.class_wise_data && student.class_wise_data.length > 0" class="space-y-0.5">
                  <div class="text-xs text-gray-900">
                    <i class="fas fa-graduation-cap text-blue-600 mr-1 text-[10px]"></i>
                    {{ getClassName(student) }}
                  </div>
                  <div class="text-xs text-gray-600">
                    <i class="fas fa-book-open text-green-600 mr-1 text-[10px]"></i>
                    {{ getVersionName(student) }}
                  </div>
                  <div class="text-xs text-gray-600">
                    <i class="fas fa-users text-purple-600 mr-1 text-[10px]"></i>
                    {{ getSectionName(student) }}
                  </div>
                </div>
                <span v-else class="text-xs text-red-500">Not Assigned</span>
              </td>

              <!-- Action -->
              <td class="px-3 py-2 whitespace-nowrap">
                <router-link 
                  :to="{ name: 'student-idcards.show', params: { id: student.id } }"
                  class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium transition-colors"
                >
                  <i class="fas fa-eye text-[10px]"></i>
                  View
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-sm text-gray-600">Loading students...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && filteredStudents.length === 0" class="p-8 text-center">
        <i class="fas fa-id-card text-4xl text-gray-400 mb-3"></i>
        <p class="text-gray-600">No students found</p>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredStudents.length > 0" class="px-4 py-2 border-t border-gray-200 flex justify-between items-center">
        <div class="text-xs text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredStudents.length }}
        </div>
        
        <div class="flex gap-1">
          <button 
            @click="previousPage"
            :disabled="currentPage === 1"
            class="px-2 py-1 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-xs"
          >
            <i class="fas fa-chevron-left"></i>
          </button>
          
          <button 
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-2 py-1 rounded text-xs font-medium',
              currentPage === page
                ? 'bg-blue-600 text-white'
                : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>
          
          <button 
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-2 py-1 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-xs"
          >
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { showErrorAlert } from '../../utils/sweetAlert'

const students = ref([])
const loading = ref(true)
const search = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)

const filters = ref({
  session_id: null,
  class_id: null,
  section_id: null
})

const availableSessions = ref([])
const availableClasses = ref([])
const availableSections = ref([])

// Fetch students
const fetchStudents = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/student-profiles')
    
    if (response.data.success) {
      students.value = response.data.data.data || []
      extractFilters()
      setActiveSession()
    }
  } catch (error) {
    console.error('Error fetching students:', error)
    showErrorAlert('Error', 'Failed to load students')
  } finally {
    loading.value = false
  }
}

// Extract available filters
const extractFilters = () => {
  const sessionsMap = new Map()
  const classesMap = new Map()
  const sectionsMap = new Map()
  
  students.value.forEach(student => {
    if (student.class_wise_data && student.class_wise_data.length > 0) {
      student.class_wise_data.forEach(data => {
        if (data.session) sessionsMap.set(data.session.id, data.session)
        if (data.class) classesMap.set(data.class.id, data.class)
        if (data.section) sectionsMap.set(data.section.id, data.section)
      })
    }
  })
  
  availableSessions.value = Array.from(sessionsMap.values())
  availableClasses.value = Array.from(classesMap.values())
  availableSections.value = Array.from(sectionsMap.values())
}

// Set active session as default
const setActiveSession = () => {
  const activeSession = availableSessions.value.find(session => session.status === 'active')
  if (activeSession) {
    filters.value.session_id = activeSession.id
  }
}

// Filtered students
const filteredStudents = computed(() => {
  let filtered = students.value

  // Search filter (includes name, ID, and roll)
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(student => {
      const nameMatch = student.student_name?.toLowerCase().includes(searchTerm)
      const idMatch = student.id_card_number?.toLowerCase().includes(searchTerm)
      
      // Check roll number in class wise data
      let rollMatch = false
      if (student.class_wise_data && student.class_wise_data.length > 0) {
        rollMatch = student.class_wise_data.some(data => 
          data.class_roll && data.class_roll.toString().includes(searchTerm)
        )
      }
      
      return nameMatch || idMatch || rollMatch
    })
  }

  // Session filter
  if (filters.value.session_id) {
    filtered = filtered.filter(student => {
      if (student.class_wise_data && student.class_wise_data.length > 0) {
        return student.class_wise_data.some(data => data.session_id == filters.value.session_id)
      }
      return false
    })
  }

  // Class filter
  if (filters.value.class_id) {
    filtered = filtered.filter(student => {
      if (student.class_wise_data && student.class_wise_data.length > 0) {
        return student.class_wise_data.some(data => data.class_id == filters.value.class_id)
      }
      return false
    })
  }

  // Section filter
  if (filters.value.section_id) {
    filtered = filtered.filter(student => {
      if (student.class_wise_data && student.class_wise_data.length > 0) {
        return student.class_wise_data.some(data => data.section_id == filters.value.section_id)
      }
      return false
    })
  }

  return filtered
})

// Pagination
const totalPages = computed(() => Math.ceil(filteredStudents.value.length / itemsPerPage.value))

const paginatedStudents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredStudents.value.slice(start, end)
})

const showingStart = computed(() => (currentPage.value - 1) * itemsPerPage.value + 1)
const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredStudents.value.length ? filteredStudents.value.length : end
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

// Helper methods
const getClassName = (student) => {
  if (student.class_wise_data && student.class_wise_data.length > 0) {
    return student.class_wise_data[0].class?.class_name || 'N/A'
  }
  return 'N/A'
}

const getVersionName = (student) => {
  if (student.class_wise_data && student.class_wise_data.length > 0) {
    return student.class_wise_data[0].version?.version_name || 'N/A'
  }
  return 'N/A'
}

const getSectionName = (student) => {
  if (student.class_wise_data && student.class_wise_data.length > 0) {
    return student.class_wise_data[0].section?.section_name || 'N/A'
  }
  return 'N/A'
}

const getClassRoll = (student) => {
  if (student.class_wise_data && student.class_wise_data.length > 0) {
    return student.class_wise_data[0].class_roll || null
  }
  return null
}

const applyFilters = () => {
  currentPage.value = 1
}

const clearFilters = () => {
  filters.value = {
    session_id: null,
    class_id: null,
    section_id: null
  }
  search.value = ''
  currentPage.value = 1
  setActiveSession()
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

onMounted(() => {
  fetchStudents()
})
</script>

<style scoped>
.v-select-custom {
  --vs-border-color: #d1d5db;
  --vs-border-radius: 0.5rem;
}
</style>