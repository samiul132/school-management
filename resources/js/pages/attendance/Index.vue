<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-3">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            Attendance Management
          </h1>
          <p class="text-gray-600 mt-2 font-medium">Track and manage staff attendance records</p>
        </div>

        <router-link 
          :to="{ name: 'attendance.create' }"
          class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
        >
          <i class="fas fa-plus-circle"></i>
          Add Attendance
        </router-link>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
      <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-3 text-white transform hover:scale-105 transition-transform">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-sm font-medium mb-1">Total Present</p>
            <h3 class="text-4xl font-bold">{{ statistics.total_present || 0 }}</h3>
          </div>
          <div class="bg-white/20 p-4 rounded-xl">
            <i class="fas fa-check-circle text-3xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl shadow-xl p-3 text-white transform hover:scale-105 transition-transform">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-red-100 text-sm font-medium mb-1">Total Absent</p>
            <h3 class="text-4xl font-bold">{{ statistics.total_absent || 0 }}</h3>
          </div>
          <div class="bg-white/20 p-4 rounded-xl">
            <i class="fas fa-times-circle text-3xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl shadow-xl p-3 text-white transform hover:scale-105 transition-transform">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-orange-100 text-sm font-medium mb-1">Total Late</p>
            <h3 class="text-4xl font-bold">{{ statistics.total_late || 0 }}</h3>
          </div>
          <div class="bg-white/20 p-4 rounded-xl">
            <i class="fas fa-clock text-3xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl shadow-xl p-3 text-white transform hover:scale-105 transition-transform">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-blue-100 text-sm font-medium mb-1">Total Leave</p>
            <h3 class="text-4xl font-bold">{{ statistics.total_leave || 0 }}</h3>
          </div>
          <div class="bg-white/20 p-4 rounded-xl">
            <i class="fas fa-umbrella-beach text-3xl"></i>
          </div>
        </div>
      </div>
    </div>


    <!-- Filters Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 mb-3">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">
            <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>Month
          </label>
          <select 
            v-model="filters.month"
            @change="fetchAttendanceSummary"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-gray-800 font-medium"
          >
            <option v-for="(month, index) in months" :key="index" :value="index + 1">
              {{ month }}
            </option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">
            <i class="fas fa-calendar text-indigo-600 mr-2"></i>Year
          </label>
          <select 
            v-model="filters.year"
            @change="fetchAttendanceSummary"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-gray-800 font-medium"
          >
            <option v-for="year in years" :key="year" :value="year">
              {{ year }}
            </option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">
            <i class="fas fa-user text-indigo-600 mr-2"></i>Staff
          </label>
          <v-select
            v-model="filters.staff_id"
            :options="staffList"
            :filterable="true"
            :searchable="true"
            label="name"
            :reduce="staff => staff.id"
            placeholder="All Staff"
            @update:modelValue="fetchAttendanceSummary"
          >
            <template #option="option">
              <div class="font-medium text-gray-800">
                {{ option.name }}
                <span class="text-xs text-gray-500 block">{{ option.designation }}</span>
              </div>
            </template>
          </v-select>
        </div>

        <div class="flex items-end">
          <button 
            @click="clearFilters"
            class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center gap-2"
          >
            <i class="fas fa-redo"></i>
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-xl font-bold text-gray-800">
          <i class="fas fa-table text-indigo-600 mr-2"></i>
          Monthly Attendance Sheet - {{ months[filters.month - 1] }} {{ filters.year }}
        </h3>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
          <i class="fas fa-spinner fa-spin text-3xl text-indigo-600"></i>
        </div>
        <p class="text-gray-600 font-medium">Loading attendance data...</p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full min-w-max">
          <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
            <tr>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider sticky left-0 bg-gradient-to-r from-indigo-50 to-purple-50 z-10">
                Staff Name
              </th>
              <th class="px-4 py-4 text-center text-xs font-bold text-green-700 uppercase tracking-wider">
                <i class="fas fa-check-circle mr-1"></i>P
              </th>
              <th class="px-4 py-4 text-center text-xs font-bold text-red-700 uppercase tracking-wider">
                <i class="fas fa-times-circle mr-1"></i>A
              </th>
              <th class="px-4 py-4 text-center text-xs font-bold text-orange-700 uppercase tracking-wider">
                <i class="fas fa-clock mr-1"></i>L
              </th>
              <th class="px-4 py-4 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">
                <i class="fas fa-umbrella-beach mr-1"></i>LV
              </th>
              <th 
                v-for="(date, index) in monthDays" 
                :key="index"
                class="px-3 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[50px]"
              >
                <div class="flex flex-col items-center">
                  <span>{{ index + 1 }}</span>
                  <span class="text-[10px] text-gray-500 font-normal">
                    {{ getDayName(date) }}
                  </span>
                </div>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr 
              v-for="staff in attendanceData" 
              :key="staff.id"
              class="hover:bg-indigo-50/50 transition-colors"
            >
              <td class="px-4 py-4 whitespace-nowrap sticky left-0 bg-white z-10 border-r border-gray-200">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-indigo-200">
                    <img 
                      v-if="staff.photo"
                      :src="getImageUrl(staff.photo)"
                      :alt="staff.name"
                      class="w-full h-full object-cover"
                    >
                    <div v-else class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                      <span class="text-white font-bold text-sm">{{ getInitials(staff.name) }}</span>
                    </div>
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ staff.name }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4 text-center">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 text-green-700 font-bold">
                  {{ staff.total_present }}
                </span>
              </td>
              <td class="px-4 py-4 text-center">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-100 text-red-700 font-bold">
                  {{ staff.total_absent }}
                </span>
              </td>
              <td class="px-4 py-4 text-center">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-orange-100 text-orange-700 font-bold">
                  {{ staff.total_late }}
                </span>
              </td>
              <td class="px-4 py-4 text-center">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 text-blue-700 font-bold">
                  {{ staff.total_leave }}
                </span>
              </td>
              <td 
                v-for="(date, index) in monthDays" 
                :key="index"
                class="px-3 py-4 text-center"
              >
                <span 
                  v-if="staff.attendance[date]?.status"
                  :class="getStatusClass(staff.attendance[date].status)"
                  class="inline-flex items-center justify-center w-9 h-9 rounded-lg font-bold text-sm cursor-pointer hover:scale-110 transition-transform"
                  :title="`${staff.attendance[date].status}\nIn: ${staff.attendance[date].in_time || 'N/A'}\nOut: ${staff.attendance[date].out_time || 'N/A'}`"
                >
                  {{ getStatusShort(staff.attendance[date].status) }}
                </span>
                <span v-else class="text-gray-300 font-medium">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && attendanceData.length === 0" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
          <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
        </div>
        <p class="text-gray-600 font-medium text-lg">No attendance records found</p>
        <p class="text-gray-500 text-sm mt-2">Try adjusting your filters or mark attendance first</p>
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
const loading = ref(true)
const attendanceData = ref([])
const monthDays = ref([])
const staffList = ref([])

// Initialize statistics with default values
const statistics = ref({
  total_present: 0,
  total_absent: 0,
  total_late: 0,
  total_leave: 0
})

const months = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
]

const filters = ref({
  month: new Date().getMonth() + 1,
  year: new Date().getFullYear(),
  staff_id: null
})

const years = computed(() => {
  const currentYear = new Date().getFullYear()
  const startYear = 2020
  const yearList = []
  for (let year = currentYear; year >= startYear; year--) {
    yearList.push(year)
  }
  return yearList
})

const fetchStaffList = async () => {
  try {
    const response = await axios.get('/api/staffs')
    const staffData = response.data.success ? response.data.data : response.data
    staffList.value = staffData.map(staff => ({
      id: staff.id,
      name: `${staff.first_name} ${staff.last_name}`,
      designation: staff.designation?.name || 'N/A'
    }))
  } catch (error) {
    console.error('Error fetching staff list:', error)
  }
}

const fetchAttendanceSummary = async () => {
  try {
    loading.value = true
    const params = {
      month: filters.value.month,
      year: filters.value.year
    }

    if (filters.value.staff_id) {
      params.staff_id = filters.value.staff_id
    }

    const response = await axios.get('/api/attendance/summary', { params })

    if (response.data.success) {
      attendanceData.value = response.data.data.staff_list
      monthDays.value = response.data.data.month_days
      
      // Calculate total statistics from staff data
      statistics.value = {
        total_present: attendanceData.value.reduce((sum, staff) => sum + (staff.total_present || 0), 0),
        total_absent: attendanceData.value.reduce((sum, staff) => sum + (staff.total_absent || 0), 0),
        total_late: attendanceData.value.reduce((sum, staff) => sum + (staff.total_late || 0), 0),
        total_leave: attendanceData.value.reduce((sum, staff) => sum + (staff.total_leave || 0), 0)
      }
    }

  } catch (error) {
    console.error('Error fetching attendance:', error)
    showErrorAlert('Error', 'Failed to load attendance data')
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  filters.value = {
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear(),
    staff_id: null
  }
  fetchAttendanceSummary()
}

const getStatusClass = (status) => {
  const classes = {
    'Present': 'bg-green-500 text-white shadow-lg shadow-green-200',
    'Absent': 'bg-red-500 text-white shadow-lg shadow-red-200',
    'Late': 'bg-orange-500 text-white shadow-lg shadow-orange-200',
    'Leave': 'bg-blue-500 text-white shadow-lg shadow-blue-200'
  }
  return classes[status] || 'bg-gray-200 text-gray-600'
}

const getStatusShort = (status) => {
  const shorts = {
    'Present': 'P',
    'Absent': 'A',
    'Late': 'L',
    'Leave': 'LV'
  }
  return shorts[status] || '-'
}

const getDayName = (date) => {
  const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
  return days[new Date(date).getDay()]
}

const getImageUrl = (imagePath) => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http')) return imagePath
  return `/assets/images/staffs/${imagePath}`
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .substring(0, 2)
}

onMounted(async () => {
  await fetchStaffList()
  await fetchAttendanceSummary()
})
</script>

<style scoped>
/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
  height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: linear-gradient(to right, #6366f1, #a855f7);
  border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to right, #4f46e5, #9333ea);
}
</style>