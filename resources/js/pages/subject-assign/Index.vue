<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-1 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-2">
      <div>
        <h1 class="text-xl font-bold text-gray-800">
          Subject Assignment Management
        </h1>
      </div>
      
      <router-link 
        :to="{ name: 'subject-assign.create' }"
        class="inline-flex items-center gap-1 px-3 py-1.5 border bg-blue-600 hover:bg-blue-700 rounded-lg text-gray-100 text-sm font-medium transition-colors"
      >
        <i class="fas fa-plus text-xs"></i>
        New Assignment
      </router-link>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-2 mb-2">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-2">
        <!-- Session Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
            Session
          </label>
          <v-select
            v-model="filters.session_id"
            :options="sessions"
            label="session_name"
            :reduce="session => session.id"
            placeholder="Select Session"
            :clearable="true"
            class="v-select-custom text-sm"
          >
            <template #option="{ session_name, status }">
              <div class="flex items-center justify-between w-full text-sm">
                <div class="flex items-center gap-1">
                  <i class="fas fa-calendar text-blue-500 text-xs"></i>
                  {{ session_name }}
                </div>
              </div>
            </template>
          </v-select>
        </div>

        <!-- Version Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-book text-gray-400 text-xs"></i>
            Version
          </label>
          <v-select
            v-model="filters.version_id"
            :options="versions"
            label="version_name"
            :reduce="version => version.id"
            placeholder="Select Version"
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

        <!-- Shift Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-clock text-gray-400 text-xs"></i>
            Shift
          </label>
          <v-select
            v-model="filters.shift_id"
            :options="shifts"
            label="shift_name"
            :reduce="shift => shift.id"
            placeholder="Select Shift"
            :clearable="true"
            class="v-select-custom text-sm"
          >
            <template #option="{ shift_name }">
              <div class="flex items-center gap-1 text-sm">
                <i class="fas fa-clock text-orange-500 text-xs"></i>
                {{ shift_name }}
              </div>
            </template>
          </v-select>
        </div>

        <!-- Class Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-graduation-cap text-gray-400 text-xs"></i>
            Class
          </label>
          <v-select
            v-model="filters.class_id"
            :options="classes"
            label="class_name"
            :reduce="classItem => classItem.id"
            placeholder="Select Class"
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
          </label>
          <v-select
            v-model="filters.section_id"
            :options="sections"
            label="section_name"
            :reduce="section => section.id"
            placeholder="Select Section"
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
      </div>

      <!-- Buttons -->
      <div class="mt-2 flex gap-2 justify-end">
        <button 
          @click="applyFilters"
          class="px-3 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-1 shadow cursor-pointer"
        >
          <i class="fas fa-filter text-xs"></i>
          Apply Filter
        </button>
        
        <button 
          @click="clearFilters"
          class="px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition-colors flex items-center gap-1 cursor-pointer"
        >
          <i class="fas fa-times text-xs"></i>
          Clear
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="p-8 text-center">
      <div class="inline-block">
        <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-2"></i>
        <p class="text-gray-600 text-sm">Loading subject assignments...</p>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && assignments.length === 0" class="bg-white rounded-xl shadow border border-gray-100 p-8 text-center">
      <div class="inline-block p-3">
        <i class="fas fa-book-reader text-4xl text-gray-400 mb-3"></i>
        <p class="text-gray-600 text-base mb-1">No subject assignments found.</p>
        <p class="text-sm text-gray-500">Create a new assignment to get started.</p>
      </div>
    </div>

    <!-- Cards -->
    <div v-for="(versionAssignments, versionName) in groupedAssignments" :key="versionName" class="mb-6">
      <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
        <i class="fas fa-book" :class="{
          'text-blue-600': versionName === 'Bangla',
          'text-green-600': versionName === 'English',
          'text-purple-600': versionName !== 'Bangla' && versionName !== 'English'
        }"></i>
        {{ versionName }} Version
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div 
          v-for="(assignment, index) in versionAssignments" 
          :key="assignment.id"
          class="bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col"
        >
          <!-- Card Header -->
          <div class="px-4 py-3 bg-gradient-to-r from-gray-700 to-gray-900">
            <div>
              <div class="flex items-center gap-1 text-xs text-gray-100 mb-1">
                <i class="fas fa-book"></i>
                <p class="text-sm font-semibold text-gray-100">
                  {{ assignment.class?.class_name || 'N/A' }}, {{ assignment.section?.section_name || 'N/A' }} 
                </p>
              </div>
            </div>
          </div>

          <!-- Card Body -->
          <div class="p-4 flex-grow">
            <!-- Subjects Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <tbody>
                  <tr 
                    v-for="detail in assignment.details" 
                    :key="detail.id"
                    class="border-b border-gray-100 last:border-0 hover:bg-gray-50"
                  >
                    <td class="py-2 px-2 text-gray-700">
                      {{ detail.subject?.subject_name || 'N/A' }}
                    </td>
                  </tr>
                  <tr v-if="!assignment.details || assignment.details.length === 0">
                    <td class="py-3 px-2 text-xs text-gray-400 italic text-center">
                      No subjects assigned
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Card Footer -->
          <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-200 flex justify-between mt-auto">
            <p class="text-xs">{{ versionName }} Version</p>
            <div class="justify-end flex gap-2">
              <button
                @click="editAssignment(assignment.id)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer"
              >
                <i class="fas fa-edit"></i>
                Edit
              </button>
              
              <button 
                @click="handleDelete(assignment)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer"
              >
                <i class="fas fa-trash"></i>
                Delete
              </button>
            </div>
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
import { showSuccessAlert, showErrorAlert, showConfirmDialog } from '../../utils/sweetAlert'

const router = useRouter()

// Data
const assignments = ref([])
const loading = ref(false)
const currentPage = ref(1)
const perPage = ref(10)

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
  shift_id: '',
  class_id: '',
  section_id: ''
})

// Dropdown data
const sessions = ref([])
const versions = ref([])
const shifts = ref([])
const classes = ref([])
const sections = ref([])

// Computed - Group assignments by version
const groupedAssignments = computed(() => {
  const groups = {}
  
  assignments.value.forEach(assignment => {
    const versionName = assignment.version?.version_name || 'Unknown'
    
    if (!groups[versionName]) {
      groups[versionName] = []
    }
    
    groups[versionName].push(assignment)
  })
  
  const sortedGroups = {}
  const versionOrder = ['Bangla', 'English']
  
  // Add priority versions first
  versionOrder.forEach(version => {
    if (groups[version]) {
      sortedGroups[version] = groups[version]
    }
  })
  
  // Add remaining versions alphabetically
  Object.keys(groups)
    .filter(v => !versionOrder.includes(v))
    .sort()
    .forEach(version => {
      sortedGroups[version] = groups[version]
    })
  
  return sortedGroups
})

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
    // Fetch sessions
    const sessionsRes = await axios.get('/api/session-managements')
    if (Array.isArray(sessionsRes.data)) {
      sessions.value = sessionsRes.data
    } else if (sessionsRes.data.success && sessionsRes.data.data) {
      const sessionData = sessionsRes.data.data
      sessions.value = Array.isArray(sessionData) ? sessionData : (sessionData.data || [])
    }

    const activeSession = sessions.value.find(s => s.status === 'active')
    if (activeSession) {
      filters.value.session_id = activeSession.id
    }

    // Fetch versions
    const versionsRes = await axios.get('/api/version-managements')
    if (Array.isArray(versionsRes.data)) {
      versions.value = versionsRes.data
    } else if (versionsRes.data.success && versionsRes.data.data) {
      const versionData = versionsRes.data.data
      versions.value = Array.isArray(versionData) ? versionData : (versionData.data || [])
    }

    // Fetch shifts
    const shiftsRes = await axios.get('/api/shift-managements')
    if (Array.isArray(shiftsRes.data)) {
      shifts.value = shiftsRes.data
    } else if (shiftsRes.data.success && shiftsRes.data.data) {
      const shiftData = shiftsRes.data.data
      shifts.value = Array.isArray(shiftData) ? shiftData : (shiftData.data || [])
    }

    // Fetch classes
    const classesRes = await axios.get('/api/class-managements')
    if (Array.isArray(classesRes.data)) {
      classes.value = classesRes.data
    } else if (classesRes.data.success && classesRes.data.data) {
      const classData = classesRes.data.data
      classes.value = Array.isArray(classData) ? classData : (classData.data || [])
    }

    // Fetch sections
    const sectionsRes = await axios.get('/api/section-managements')
    if (Array.isArray(sectionsRes.data)) {
      sections.value = sectionsRes.data
    } else if (sectionsRes.data.success && sectionsRes.data.data) {
      const sectionData = sectionsRes.data.data
      sections.value = Array.isArray(sectionData) ? sectionData : (sectionData.data || [])
    }
  } catch (error) {
    console.error('Error fetching dropdown data:', error)
  }
}

const fetchAssignments = async (page = 1) => {
  loading.value = true
  
  try {
    const params = {
      page: page,
      per_page: perPage.value
    }
    
    if (filters.value.session_id) params.session_id = filters.value.session_id
    if (filters.value.version_id) params.version_id = filters.value.version_id
    if (filters.value.shift_id) params.shift_id = filters.value.shift_id
    if (filters.value.class_id) params.class_id = filters.value.class_id
    if (filters.value.section_id) params.section_id = filters.value.section_id
    
    const response = await axios.get('/api/subject-assigns', { params })
    
    if (response.data.success) {
      const data = response.data.data
      paginationInfo.value = {
        current_page: data.current_page || 1,
        last_page: data.last_page || 1,
        from: data.from || 0,
        to: data.to || 0,
        total: data.total || 0
      }
      
      assignments.value = data.data || []
    } else {
      assignments.value = []
      paginationInfo.value = {
        current_page: 1,
        last_page: 1,
        from: 0,
        to: 0,
        total: 0
      }
    }
  } catch (error) {
    console.error('Error fetching assignments:', error)
    showErrorAlert('Error', 'Failed to fetch subject assignments')
    assignments.value = []
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
  currentPage.value = 1
  fetchAssignments(1)
}

const clearFilters = () => {
  filters.value = {
    session_id: '',
    version_id: '',
    shift_id: '',
    class_id: '',
    section_id: ''
  }
  currentPage.value = 1
  fetchAssignments(1)
}

const goToPage = (page) => {
  if (page >= 1 && page <= paginationInfo.value.last_page) {
    currentPage.value = page
    fetchAssignments(page)
  }
}

const changePerPage = () => {
  currentPage.value = 1
  fetchAssignments(1)
}

const editAssignment = (id) => {
  router.push({ name: 'subject-assign.edit', params: { id } })
}

const handleDelete = async (assignment) => {
  const result = await showConfirmDialog(
    'Delete Subject Assignment?',
    'This will remove all assigned subjects and student assignments. This action cannot be undone.'
  )
  
  if (result.isConfirmed) {
    await deleteAssignment(assignment.id)
  }
}

const deleteAssignment = async (id) => {
  try {
    const response = await axios.delete(`/api/subject-assigns/${id}`)
    
    if (response.data.success) {
      showSuccessAlert('Deleted!', 'Subject assignment deleted successfully')
      fetchAssignments(currentPage.value)
    }
  } catch (error) {
    console.error('Error deleting assignment:', error)
    showErrorAlert('Error', error.response?.data?.message || 'Failed to delete assignment')
  }
}

const getSerialNumber = (index) => {
  return (paginationInfo.value.current_page - 1) * perPage.value + index + 1
}

// Lifecycle
onMounted(async () => {
  await fetchDropdownData()
  await fetchAssignments()
})
</script>

<style scoped>
.fa-spinner.fa-spin {
  animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>