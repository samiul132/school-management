<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            Mark Attendance
          </h1>
          <p class="text-gray-600 mt-2 font-medium">Record staff attendance for the day</p>
        </div>

        <router-link 
          :to="{ name: 'attendance.index' }"
          class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white bg-gray-600 hover:bg-gray-700 transition-all shadow-lg font-semibold"
        >
          <i class="fas fa-arrow-left"></i>
          Back to List
        </router-link>
      </div>
    </div>

    <!-- Date Selector -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">
            <i class="fas fa-calendar-day text-indigo-600 mr-2"></i>
            Select Date <span class="text-red-500">*</span>
          </label>
          <input 
            type="date" 
            v-model="selectedDate"
            @change="loadAttendanceData"
            :max="today"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-gray-800 font-medium"
          >
        </div>

        <div class="flex items-center gap-2 text-sm">
          <div class="flex items-center gap-2 px-3 py-2 bg-green-50 rounded-lg">
            <span class="w-4 h-4 bg-green-500 rounded"></span>
            <span class="text-green-700 font-medium">Present (P)</span>
          </div>
          <div class="flex items-center gap-2 px-3 py-2 bg-red-50 rounded-lg">
            <span class="w-4 h-4 bg-red-500 rounded"></span>
            <span class="text-red-700 font-medium">Absent (A)</span>
          </div>
        </div>

        <div class="flex items-center gap-2 text-sm">
          <div class="flex items-center gap-2 px-3 py-2 bg-orange-50 rounded-lg">
            <span class="w-4 h-4 bg-orange-500 rounded"></span>
            <span class="text-orange-700 font-medium">Late (L)</span>
          </div>
          <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 rounded-lg">
            <span class="w-4 h-4 bg-blue-500 rounded"></span>
            <span class="text-blue-700 font-medium">Leave (LV)</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl border border-indigo-200 p-6 mb-8">
      <div class="flex flex-wrap items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800">
          <i class="fas fa-bolt text-indigo-600 mr-2"></i>
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

    <!-- Staff List -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-800">
          <i class="fas fa-users text-indigo-600 mr-2"></i>
          Staff List ({{ staffList.length }} members)
        </h3>
        <div class="relative">
          <input 
            type="text" 
            v-model="searchQuery"
            placeholder="Search staff..." 
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
        <p class="text-gray-600 font-medium">Loading staff list...</p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
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
                Staff Name
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
              v-for="(staff, index) in filteredStaffList" 
              :key="staff.id"
              class="hover:bg-indigo-50/50 transition-colors"
            >
              <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                {{ index + 1 }}
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="w-12 h-12 rounded-xl overflow-hidden border-2 border-indigo-200 shadow-md">
                  <img 
                    v-if="staff.photo"
                    :src="getImageUrl(staff.photo)"
                    :alt="staff.first_name"
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                    <span class="text-white font-bold">{{ getInitials(staff.first_name, staff.last_name) }}</span>
                  </div>
                </div>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="font-semibold text-gray-800">
                  {{ staff.first_name }} {{ staff.last_name }}
                </div>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap">
                <input 
                  type="time" 
                  v-model="attendance[staff.id].in_time"
                  class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-gray-800 font-medium"
                >
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap">
                <input 
                  type="time" 
                  v-model="attendance[staff.id].out_time"
                  class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-gray-800 font-medium"
                >
              </td>
              
              <td class="px-2 py-4 text-center">
                <label class="inline-flex items-center cursor-pointer group">
                  <input 
                    type="radio" 
                    :name="`attendance_${staff.id}`"
                    v-model="attendance[staff.id].status"
                    value="Present"
                    class="sr-only"
                  >
                  <span 
                    :class="[
                      'w-12 h-12 rounded-xl flex items-center justify-center font-bold text-sm transition-all shadow-md',
                      attendance[staff.id].status === 'Present' 
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
                    :name="`attendance_${staff.id}`"
                    v-model="attendance[staff.id].status"
                    value="Late"
                    class="sr-only"
                  >
                  <span 
                    :class="[
                      'w-12 h-12 rounded-xl flex items-center justify-center font-bold text-sm transition-all shadow-md',
                      attendance[staff.id].status === 'Late' 
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
                    :name="`attendance_${staff.id}`"
                    v-model="attendance[staff.id].status"
                    value="Absent"
                    class="sr-only"
                  >
                  <span 
                    :class="[
                      'w-12 h-12 rounded-xl flex items-center justify-center font-bold text-sm transition-all shadow-md',
                      attendance[staff.id].status === 'Absent' 
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
                    :name="`attendance_${staff.id}`"
                    v-model="attendance[staff.id].status"
                    value="Leave"
                    class="sr-only"
                  >
                  <span 
                    :class="[
                      'w-12 h-12 rounded-xl flex items-center justify-center font-bold text-xs transition-all shadow-md',
                      attendance[staff.id].status === 'Leave' 
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
      <div v-if="!loading && staffList.length === 0" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
          <i class="fas fa-users-slash text-4xl text-gray-400"></i>
        </div>
        <p class="text-gray-600 font-medium text-lg">No staff found</p>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && staffList.length > 0 && filteredStaffList.length === 0" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
          <i class="fas fa-search text-4xl text-gray-400"></i>
        </div>
        <p class="text-gray-600 font-medium text-lg">No staff match your search</p>
      </div>

      <!-- Submit Button -->
      <div v-if="!loading && filteredStaffList.length > 0" class="px-6 py-6 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-600">
            <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
            <span class="font-medium">{{ selectedCount }}</span> staff members marked
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
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert, showConfirmDialog } from '../../utils/sweetAlert'

const router = useRouter()
const loading = ref(true)
const submitting = ref(false)
const staffList = ref([])
const attendance = ref({})
const searchQuery = ref('')

const today = new Date().toISOString().split('T')[0]
const selectedDate = ref(today)

const filteredStaffList = computed(() => {
  if (!searchQuery.value) return staffList.value
  
  const query = searchQuery.value.toLowerCase()
  return staffList.value.filter(staff => 
    staff.first_name.toLowerCase().includes(query) ||
    staff.last_name.toLowerCase().includes(query)
  )
})

const selectedCount = computed(() => {
  return Object.values(attendance.value).filter(a => a.status).length
})

const fetchStaffList = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/attendance/staff-list', {
      params: { date: selectedDate.value }
    })

    if (response.data.success) {
      staffList.value = response.data.data
      
      // Initialize attendance object
      attendance.value = {}
      staffList.value.forEach(staff => {
        attendance.value[staff.id] = {
          staff_id: staff.id,
          status: staff.attendance?.status || null,
          in_time: staff.attendance?.in_time || '',
          out_time: staff.attendance?.out_time || ''
        }
      })
    }
  } catch (error) {
    console.error('Error fetching staff list:', error)
    showErrorAlert('Error', 'Failed to load staff list')
  } finally {
    loading.value = false
  }
}

const loadAttendanceData = () => {
  fetchStaffList()
}

const markAllPresent = () => {
  Object.keys(attendance.value).forEach(staffId => {
    attendance.value[staffId].status = 'Present'
  })
  showSuccessAlert('Success', 'All staff marked as present')
}

const markAllAbsent = () => {
  Object.keys(attendance.value).forEach(staffId => {
    attendance.value[staffId].status = 'Absent'
  })
  showSuccessAlert('Success', 'All staff marked as absent')
}

const clearAll = () => {
  Object.keys(attendance.value).forEach(staffId => {
    attendance.value[staffId].status = null
    attendance.value[staffId].in_time = ''
    attendance.value[staffId].out_time = ''
  })
  showSuccessAlert('Success', 'All attendance cleared')
}

const submitAttendance = async () => {
  if (selectedCount.value === 0) {
    showErrorAlert('Error', 'Please mark attendance for at least one staff member')
    return
  }

  try {
    submitting.value = true

    const attendanceData = Object.values(attendance.value)
      .filter(a => a.status)
      .map(a => ({
        staff_id: a.staff_id,
        status: a.status,
        in_time: a.in_time || null,
        out_time: a.out_time || null
      }))

    const response = await axios.post('/api/attendance', {
      date: selectedDate.value,
      attendance: attendanceData
    })

    if (response.data.success) {
      
      router.push({ name: 'attendance.index' })
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
  return `/assets/images/staffs/${imagePath}`
}

const getInitials = (firstName, lastName) => {
  return `${firstName[0]}${lastName[0]}`.toUpperCase()
}

onMounted(() => {
  fetchStaffList()
})
</script>

<style scoped>
input[type="time"]::-webkit-calendar-picker-indicator {
  filter: invert(40%) sepia(10%) saturate(500%) hue-rotate(180deg);
}
</style>