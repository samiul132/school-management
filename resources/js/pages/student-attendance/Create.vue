<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            Mark Student Attendance
          </h1>
          <p class="text-gray-600 mt-1 font-medium">Record student attendance for the day</p>
        </div>

        <router-link 
          :to="{ name: 'student-attendance.index' }"
          class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-white bg-gray-600 hover:bg-gray-700 transition-all shadow-lg font-semibold"
        >
          <i class="fas fa-arrow-left"></i>
          Back to List
        </router-link>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-end">
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">
            <i class="fas fa-calendar-day text-indigo-600 mr-2"></i>
            Select Date <span class="text-red-500">*</span>
          </label>
          <input 
            type="date" 
            v-model="selectedDate"
            @change="loadStudentData"
            :max="today"
            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-gray-800 font-medium"
          >
        </div>

        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">
            <i class="fas fa-school text-indigo-600 mr-2"></i>
            Class <span class="text-red-500">*</span>
          </label>
          <v-select
            v-model="filters.class_id"
            :options="classList"
            :filterable="true"
            :searchable="true"
            label="class_name"
            :reduce="cls => cls.id"
            placeholder="Select Class"
            @update:modelValue="loadStudentData"
            class="v-select-custom"
          >
            <template #option="option">
              <div class="font-medium text-gray-800">
                {{ option.class_name }}
              </div>
            </template>
          </v-select>
        </div>

        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">
            <i class="fas fa-users text-indigo-600 mr-2"></i>
            Section
          </label>
          <v-select
            v-model="filters.section_id"
            :options="sectionList"
            :filterable="true"
            :searchable="true"
            label="section_name"
            :reduce="section => section.id"
            placeholder="Select Section"
            @update:modelValue="loadStudentData"
            class="v-select-custom"
          >
            <template #option="option">
              <div class="font-medium text-gray-800">
                {{ option.section_name }}
              </div>
            </template>
          </v-select>
        </div>

        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">
            <i class="fas fa-calendar-check text-indigo-600 mr-2"></i>
            Session
          </label>
          <v-select
            v-model="filters.session_id"
            :options="sessionList"
            :filterable="true"
            :searchable="true"
            label="session_name"
            :reduce="session => session.id"
            placeholder="Select Session"
            @update:modelValue="loadStudentData"
            class="v-select-custom"
          >
            <template #option="option">
              <div class="font-medium text-gray-800">
                {{ option.session_name }}
              </div>
            </template>
          </v-select>
        </div>

        <button 
          @click="loadStudentData"
          :disabled="!filters.class_id"
          class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white rounded-xl font-semibold transition-colors shadow-md disabled:cursor-not-allowed"
        >
          <i class="fas fa-search mr-2"></i>
          Load
        </button>
      </div>
    </div>

    <!-- Legend -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl border border-indigo-200 p-4 mb-8">
      <div class="flex flex-wrap items-center gap-4">
        <h3 class="text-sm font-bold text-gray-800">
          <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
          Status Legend:
        </h3>
        <div class="flex items-center gap-2 px-3 py-2 bg-green-50 rounded-lg">
          <span class="w-4 h-4 bg-green-500 rounded"></span>
          <span class="text-green-700 font-medium text-sm">Present (P)</span>
        </div>
        <div class="flex items-center gap-2 px-3 py-2 bg-red-50 rounded-lg">
          <span class="w-4 h-4 bg-red-500 rounded"></span>
          <span class="text-red-700 font-medium text-sm">Absent (A)</span>
        </div>
        <div class="flex items-center gap-2 px-3 py-2 bg-orange-50 rounded-lg">
          <span class="w-4 h-4 bg-orange-500 rounded"></span>
          <span class="text-orange-700 font-medium text-sm">Late (L)</span>
        </div>
        <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 rounded-lg">
          <span class="w-4 h-4 bg-blue-500 rounded"></span>
          <span class="text-blue-700 font-medium text-sm">Leave (LV)</span>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div v-if="studentList.length > 0" class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl border border-purple-200 p-6 mb-8">
      <div class="flex flex-wrap items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800">
          <i class="fas fa-bolt text-purple-600 mr-2"></i>
          Quick Actions:
        </h3>
        <button 
          @click="markAllPresent"
          class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold transition-colors shadow-md"
        >
          <i class="fas fa-check-double mr-2"></i>
          Mark All Present
        </button>
        <button 
          @click="markAllAbsent"
          class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors shadow-md"
        >
          <i class="fas fa-times-circle mr-2"></i>
          Mark All Absent
        </button>
        <button 
          @click="clearAll"
          class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors shadow-md"
        >
          <i class="fas fa-eraser mr-2"></i>
          Clear All
        </button>
      </div>
    </div>

    <!-- Student List -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-800">
          <i class="fas fa-user-graduate text-indigo-600 mr-2"></i>
          Student List ({{ studentList.length }} students)
        </h3>
        <div class="relative" v-if="studentList.length > 0">
          <input 
            type="text" 
            v-model="searchQuery"
            placeholder="Search students..." 
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-gray-800"
          >
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
          <i class="fas fa-spinner fa-spin text-3xl text-indigo-600"></i>
        </div>
        <p class="text-gray-600 font-medium">Loading student list...</p>
      </div>

      <!-- Initial State -->
      <div v-else-if="!filters.class_id" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-100 mb-4">
          <i class="fas fa-filter text-4xl text-indigo-400"></i>
        </div>
        <p class="text-gray-600 font-medium text-lg">Please select a class to load students</p>
        <p class="text-gray-500 text-sm mt-2">Choose date and class from the filters above</p>
      </div>

      <!-- Table -->
      <div v-else-if="studentList.length > 0" class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
            <tr>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-16">
                #
              </th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-24">
                Photo
              </th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Student Name
              </th>
              <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                Roll
              </th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Class & Section
              </th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                In Time
              </th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Out Time
              </th>
              <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider" colspan="4">
                Attendance Status
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr 
              v-for="(student, index) in filteredStudentList" 
              :key="student.id"
              class="hover:bg-indigo-50/50 transition-colors"
            >
              <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                {{ index + 1 }}
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="w-12 h-12 rounded-xl overflow-hidden border-2 border-indigo-200 shadow-md">
                  <img 
                    v-if="student.photo"
                    :src="getImageUrl(student.photo)"
                    :alt="student.student_name"
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ getInitials(student.student_name) }}</span>
                  </div>
                </div>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="font-semibold text-gray-800">
                  {{ student.student_name }}
                </div>
              </td>
              
              <td class="px-4 py-4 text-center">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 text-gray-700 font-bold">
                  {{ student.roll || '-' }}
                </span>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-600">
                  {{ student.class }} - {{ student.section }}
                </div>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap">
                <input 
                  type="time" 
                  v-model="attendance[student.id].in_time"
                  class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-gray-800 font-medium"
                >
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap">
                <input 
                  type="time" 
                  v-model="attendance[student.id].out_time"
                  class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-gray-800 font-medium"
                >
              </td>
              
              <td class="px-2 py-4 text-center">
                <label class="inline-flex items-center cursor-pointer group">
                  <input 
                    type="radio" 
                    :name="`attendance_${student.id}`"
                    v-model="attendance[student.id].status"
                    value="Present"
                    class="sr-only"
                  >
                  <span 
                    :class="[
                      'w-12 h-12 rounded-xl flex items-center justify-center font-bold text-sm transition-all shadow-md',
                      attendance[student.id].status === 'Present' 
                        ? 'bg-green-500 text-white scale-110 ring-4 ring-green-200' 
                        : 'bg-gray-100 text-gray-400 hover:bg-green-100 hover:text-green-600'
                    ]"
                  >
                    P
                  </span>
                </label>
              </td>
              
              <td class="px-2 py-4 text-center">
                <label class="inline-flex items-center cursor-pointer group">
                  <input 
                    type="radio" 
                    :name="`attendance_${student.id}`"
                    v-model="attendance[student.id].status"
                    value="Late"
                    class="sr-only"
                  >
                  <span 
                    :class="[
                      'w-12 h-12 rounded-xl flex items-center justify-center font-bold text-sm transition-all shadow-md',
                      attendance[student.id].status === 'Late' 
                        ? 'bg-orange-500 text-white scale-110 ring-4 ring-orange-200' 
                        : 'bg-gray-100 text-gray-400 hover:bg-orange-100 hover:text-orange-600'
                    ]"
                  >
                    L
                  </span>
                </label>
              </td>
              
              <td class="px-2 py-4 text-center">
                <label class="inline-flex items-center cursor-pointer group">
                  <input 
                    type="radio" 
                    :name="`attendance_${student.id}`"
                    v-model="attendance[student.id].status"
                    value="Absent"
                    class="sr-only"
                  >
                  <span 
                    :class="[
                      'w-12 h-12 rounded-xl flex items-center justify-center font-bold text-sm transition-all shadow-md',
                      attendance[student.id].status === 'Absent' 
                        ? 'bg-red-500 text-white scale-110 ring-4 ring-red-200' 
                        : 'bg-gray-100 text-gray-400 hover:bg-red-100 hover:text-red-600'
                    ]"
                  >
                    A
                  </span>
                </label>
              </td>
              
              <td class="px-2 py-4 text-center">
                <label class="inline-flex items-center cursor-pointer group">
                  <input 
                    type="radio" 
                    :name="`attendance_${student.id}`"
                    v-model="attendance[student.id].status"
                    value="Leave"
                    class="sr-only"
                  >
                  <span 
                    :class="[
                      'w-12 h-12 rounded-xl flex items-center justify-center font-bold text-xs transition-all shadow-md',
                      attendance[student.id].status === 'Leave' 
                        ? 'bg-blue-500 text-white scale-110 ring-4 ring-blue-200' 
                        : 'bg-gray-100 text-gray-400 hover:bg-blue-100 hover:text-blue-600'
                    ]"
                  >
                    LV
                  </span>
                </label>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-else-if="!loading && studentList.length === 0 && filters.class_id" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
          <i class="fas fa-user-slash text-4xl text-gray-400"></i>
        </div>
        <p class="text-gray-600 font-medium text-lg">No students found</p>
        <p class="text-gray-500 text-sm mt-2">Try adjusting your filters</p>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && studentList.length > 0 && filteredStudentList.length === 0" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
          <i class="fas fa-search text-4xl text-gray-400"></i>
        </div>
        <p class="text-gray-600 font-medium text-lg">No students match your search</p>
      </div>

      <!-- Submit Button -->
      <div v-if="!loading && filteredStudentList.length > 0" class="px-6 py-6 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-600">
            <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
            <span class="font-medium">{{ selectedCount }}</span> students marked
          </p>
          <button 
            @click="submitAttendance"
            :disabled="submitting || selectedCount === 0"
            class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-400 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:transform-none disabled:cursor-not-allowed flex items-center gap-2"
          >
            <i :class="submitting ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
            {{ submitting ? 'Saving...' : 'Save Attendance' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert } from '../../utils/sweetAlert'

const router = useRouter()
const loading = ref(false)
const submitting = ref(false)
const studentList = ref([])
const classList = ref([])
const sectionList = ref([])
const sessionList = ref([])
const attendance = ref({})
const searchQuery = ref('')

const today = new Date().toISOString().split('T')[0]
const selectedDate = ref(today)

const filters = ref({
  class_id: null,
  section_id: null,
  session_id: null
})

const filteredStudentList = computed(() => {
  if (!searchQuery.value) return studentList.value
  
  const query = searchQuery.value.toLowerCase()
  return studentList.value.filter(student => 
    student.student_name.toLowerCase().includes(query) ||
    (student.roll && student.roll.toString().includes(query)) ||
    student.id_card_number.toLowerCase().includes(query)
  )
})

const selectedCount = computed(() => {
  return Object.values(attendance.value).filter(a => a.status).length
})

const fetchDropdownData = async () => {
  try {
    const response = await axios.get('/api/student-attendance/dropdown-data')
    if (response.data.success) {
      classList.value = response.data.data.classes
      sectionList.value = response.data.data.sections
      sessionList.value = response.data.data.sessions
    }
  } catch (error) {
    console.error('Error fetching dropdown data:', error)
    showErrorAlert('Error', 'Failed to load dropdown data')
  }
}

const loadStudentData = async () => {
  if (!filters.value.class_id) {
    showErrorAlert('Error', 'Please select a class first')
    return
  }

  try {
    loading.value = true
    const params = {
      date: selectedDate.value,
      class_id: filters.value.class_id
    }

    if (filters.value.section_id) params.section_id = filters.value.section_id
    if (filters.value.session_id) params.session_id = filters.value.session_id

    const response = await axios.get('/api/student-attendance/student-list', { params })

    if (response.data.success) {
      studentList.value = response.data.data
      
      // Initialize attendance object
      attendance.value = {}
      studentList.value.forEach(student => {
        attendance.value[student.id] = {
          class_wise_student_id: student.id,
          status: student.attendance?.status || null,
          in_time: student.attendance?.in_time || '',
          out_time: student.attendance?.out_time || ''
        }
      })
    }
  } catch (error) {
    console.error('Error loading student list:', error)
    showErrorAlert('Error', 'Failed to load student list')
  } finally {
    loading.value = false
  }
}

const markAllPresent = () => {
  Object.keys(attendance.value).forEach(studentId => {
    attendance.value[studentId].status = 'Present'
  })
  showSuccessAlert('Success', 'All students marked as present')
}

const markAllAbsent = () => {
  Object.keys(attendance.value).forEach(studentId => {
    attendance.value[studentId].status = 'Absent'
  })
  showSuccessAlert('Success', 'All students marked as absent')
}

const clearAll = () => {
  Object.keys(attendance.value).forEach(studentId => {
    attendance.value[studentId].status = null
    attendance.value[studentId].in_time = ''
    attendance.value[studentId].out_time = ''
  })
  showSuccessAlert('Success', 'All attendance cleared')
}

const submitAttendance = async () => {
  if (selectedCount.value === 0) {
    showErrorAlert('Error', 'Please mark attendance for at least one student')
    return
  }

  try {
    submitting.value = true

    const attendanceData = Object.values(attendance.value)
      .filter(a => a.status)
      .map(a => ({
        class_wise_student_id: a.class_wise_student_id,
        status: a.status,
        in_time: a.in_time || null,
        out_time: a.out_time || null
      }))

    const response = await axios.post('/api/student-attendance', {
      date: selectedDate.value,
      attendance: attendanceData
    })

    if (response.data.success) {
      showSuccessAlert('Success', 'Student attendance saved successfully')
      router.push({ name: 'student-attendance.index' })
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to save attendance')
    }

  } catch (error) {
    console.error('Error submitting attendance:', error)
    
    if (error.response?.status === 422) {
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to save attendance')
    }
  } finally {
    submitting.value = false
  }
}

const getImageUrl = (imagePath) => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http')) return imagePath
  return `/assets/images/student_images/${imagePath}`
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .substring(0, 2)
}

onMounted(() => {
  fetchDropdownData()
})
</script>

<style scoped>
input[type="time"]::-webkit-calendar-picker-indicator {
  filter: invert(40%) sepia(10%) saturate(500%) hue-rotate(180deg);
}

.v-select-custom {
  --vs-controls-color: #6366f1;
  --vs-border-color: #d1d5db;
  --vs-dropdown-bg: #ffffff;
  --vs-selected-bg: #eef2ff;
  --vs-selected-color: #4f46e5;
  --vs-search-input-color: #1f2937;
  --vs-dropdown-option--active-bg: #eef2ff;
  --vs-dropdown-option--active-color: #4f46e5;
}
</style>