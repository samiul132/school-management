<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mt-6">
      
      <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-users text-white text-xl"></i>
          </div>
          <span :class="[
            'px-3 py-1 rounded-full text-xs font-semibold',
            studentAttendanceChange >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
          ]">
            <i :class="['mr-1', studentAttendanceChange >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down']"></i>
            {{ Math.abs(studentAttendanceChange) }}%
          </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Total Student's</h3>
        <p class="text-3xl font-bold text-gray-800 mb-2">{{ totalStudent }}</p>
        <div class="flex items-center text-xs text-gray-500">
          <i class="fas fa-clock mr-1"></i>
          <span>Today's Present ({{ todayStudentAttendance }})</span>
        </div>
        <div class="mt-4 bg-gray-200 rounded-full h-2 overflow-hidden">
          <div :class="[
            'h-full rounded-full',
            studentAttendanceChange >= 0 ? 'bg-gradient-to-r from-blue-500 to-blue-600' : 'bg-gradient-to-r from-red-500 to-red-600'
          ]" :style="`width: ${studentAttendancePercentage}%`"></div>
        </div>
      </div>

      <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-users text-white text-xl"></i>
          </div>
          <span :class="[
            'px-3 py-1 rounded-full text-xs font-semibold',
            teacherAttendanceChange >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
          ]">
            <i :class="['mr-1', teacherAttendanceChange >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down']"></i>
            {{ Math.abs(teacherAttendanceChange) }}%
          </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Total Teacher's</h3>
        <p class="text-3xl font-bold text-gray-800 mb-2">{{ totalTeacher }}</p>
        <div class="flex items-center text-xs text-gray-500">
          <i class="fas fa-clock mr-1"></i>
          <span>Today's Present ({{ todayTeacherAttendance }})</span>
        </div>
        <div class="mt-4 bg-gray-200 rounded-full h-2 overflow-hidden">
          <div :class="[
            'h-full rounded-full',
            teacherAttendanceChange >= 0 ? 'bg-gradient-to-r from-purple-500 to-purple-600' : 'bg-gradient-to-r from-red-500 to-red-600'
          ]" :style="`width: ${teacherAttendancePercentage}%`"></div>
        </div>
      </div>

      <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-users text-white text-xl"></i>
          </div>
          <span :class="[
            'px-3 py-1 rounded-full text-xs font-semibold',
            otherStaffAttendanceChange >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
          ]">
            <i :class="['mr-1', otherStaffAttendanceChange >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down']"></i>
            {{ Math.abs(otherStaffAttendanceChange) }}%
          </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Total Other's Staff</h3>
        <p class="text-3xl font-bold text-gray-800 mb-2">{{ totalOtherStaff }}</p>
        <div class="flex items-center text-xs text-gray-500">
          <i class="fas fa-clock mr-1"></i>
          <span>Today's Present ({{ todayOtherStaffAttendance }})</span>
        </div>
        <div class="mt-4 bg-gray-200 rounded-full h-2 overflow-hidden">
          <div :class="[
            'h-full rounded-full',
            otherStaffAttendanceChange >= 0 ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-red-500 to-red-600'
          ]" :style="`width: ${otherStaffAttendancePercentage}%`"></div>
        </div>
      </div>

      <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-dollar-sign text-white text-xl"></i>
          </div>
          <span :class="[
            'px-3 py-1 rounded-full text-xs font-semibold',
            profitChange >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
          ]">
            <i :class="['mr-1', profitChange >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down']"></i>
            {{ Math.abs(profitChange) }}%
          </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Current Session Profit</h3>
        <p class="text-3xl font-bold text-gray-800 mb-2">{{ currentSessionProfit.toLocaleString() }}/=</p>
        <div class="flex items-center text-xs">
          <span v-if="lastSessionProfit >= 0" class="text-green-600 font-medium">
            <i class="fas fa-arrow-up mr-1"></i>Last Year: {{ lastSessionProfit.toLocaleString() }}/= Profit
          </span>
          <span v-else class="text-red-600 font-medium">
            <i class="fas fa-arrow-down mr-1"></i>Last Year: {{ Math.abs(lastSessionProfit).toLocaleString() }}/= Loss
          </span>
        </div>
        <div class="mt-4 bg-gray-200 rounded-full h-2 overflow-hidden">
          <div :class="[
            'h-full rounded-full',
            profitChange >= 0 ? 'bg-gradient-to-r from-orange-500 to-orange-600' : 'bg-gradient-to-r from-red-500 to-red-600'
          ]" :style="`width: ${profitProgressPercentage}%`"></div>
        </div>
      </div>
    </div> 

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

      <!-- Total Received Fee -->
      <div class="stat-card bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl shadow-lg p-4 text-white">
        
        <!-- Heading -->
        <div class="flex items-center justify-between mb-4">
          <p class="text-sm font-semibold tracking-wide opacity-90">
            Total Received Fee
          </p>
          <i class="fas fa-money-bill-wave text-xl opacity-80"></i>
        </div>

        <!-- This Session -->
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm opacity-90">This Session</span>
          <span class="text-2xl font-bold">
            {{ thisSessionTotalReceivedFees.toLocaleString() }}/=
          </span>
        </div>

        <!-- Divider -->
        <div class="border-t border-white/20 my-2"></div>

        <!-- This Month -->
        <div class="flex items-center justify-between">
          <span class="text-sm opacity-90">This Month</span>
          <span class="text-lg font-semibold">
            {{ thisMonthTotalReceivedFees.toLocaleString() }}/=
          </span>
        </div>
      </div>

      <!-- Total Fee Assign -->
      <div class="stat-card bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl shadow-lg p-4 text-white">
        
        <div class="flex items-center justify-between mb-4">
          <p class="text-sm font-semibold tracking-wide opacity-90">
            Total Fee Assign
          </p>
          <i class="fas fa-clipboard-list text-xl opacity-80"></i>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-sm opacity-90">This Session</span>
          <span class="text-2xl font-bold">
            {{ thisSessionTotalAssignFees.toLocaleString() }}/=
          </span>
        </div>
      </div>

      <!-- Total Due Fee -->
      <div class="stat-card bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl shadow-lg p-4 text-white">
        
        <div class="flex items-center justify-between mb-4">
          <p class="text-sm font-semibold tracking-wide opacity-90">
            Total Due Fee
          </p>
          <i class="fas fa-exclamation-circle text-xl opacity-80"></i>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-sm opacity-90">This Session</span>
          <span class="text-2xl font-bold">
            {{ thisSessionTotalDueFees.toLocaleString() }}/=
          </span>
        </div>
      </div>

      <!-- Total Expense -->
      <div class="stat-card bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg p-4 text-white">
        
        <div class="flex items-center justify-between mb-4">
          <p class="text-sm font-semibold tracking-wide opacity-90">
            Total Expense
          </p>
          <i class="fas fa-wallet text-xl opacity-80"></i>
        </div>

        <div class="flex items-center justify-between mb-2">
          <span class="text-sm opacity-90">This Session</span>
          <span class="text-2xl font-bold">
            {{ thisSessionTotalExpense.toLocaleString() }}/=
          </span>
        </div>

        <div class="border-t border-white/20 my-2"></div>

        <div class="flex items-center justify-between">
          <span class="text-sm opacity-90">This Month</span>
          <span class="text-lg font-semibold">
            {{ thisMonthTotalExpense.toLocaleString() }}/=
          </span>
        </div>
      </div>

    </div>

    <!-- Today's Routine Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-8">
      <!-- Header with Toggle -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-calendar-day text-blue-600"></i>
            Today's Class Routine
          </h3>
          <p class="text-sm text-gray-500 mt-1">{{ todayDay }}'s Schedule</p>
        </div>
        
        <!-- Toggle Buttons -->
        <div class="flex gap-2 bg-gray-100 p-1 rounded-lg">
          <button 
            @click="viewMode = 'teacher'"
            :class="[
              'px-4 py-2 rounded-md text-sm font-medium transition-all cursor-pointer',
              viewMode === 'teacher' 
                ? 'bg-white text-blue-600 shadow-sm' 
                : 'text-gray-600 hover:text-gray-800'
            ]"
          >
            <i class="fas fa-chalkboard-teacher mr-2"></i>
            Teacher Wise
          </button>
          <button 
            @click="viewMode = 'class'"
            :class="[
              'px-4 py-2 rounded-md text-sm font-medium transition-all cursor-pointer',
              viewMode === 'class' 
                ? 'bg-white text-blue-600 shadow-sm' 
                : 'text-gray-600 hover:text-gray-800'
            ]"
          >
            <i class="fas fa-users mr-2"></i>
            Class Wise
          </button>
        </div>
      </div>

      <!-- Teacher-wise Table View -->
      <div v-if="viewMode === 'teacher'" class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-600">
          <thead>
            <!-- Shift Names Row -->
            <tr>
              <th class="border border-gray-600 px-3 py-2 text-center text-sm font-bold bg-white" rowspan="3">
                Teacher's
              </th>
              <th 
                v-for="(period, shiftId) in periods || {}" 
                :key="shiftId"
                :colspan="Object.keys(period).length"
                class="border border-gray-600 px-2 py-2 text-center text-sm font-bold bg-white"
              >
                {{ shifts[shiftId]?.shift_name }}
              </th>
            </tr>
            <!-- Period Numbers Row -->
            <tr>
              <template v-for="(period, shiftId) in periods || {}" :key="shiftId">
                <th 
                  v-for="(time, index) in period" 
                  :key="`period-${shiftId}-${index}`"
                  class="border border-gray-600 px-2 py-2 text-center text-sm font-bold bg-white"
                >
                  {{ index }}{{ index == 1 ? 'st' : index == 2 ? 'nd' : index == 3 ? 'rd' : 'th' }} Period
                </th>
              </template>
            </tr>
            <!-- Time Row -->
            <tr>
              <template v-for="(period, shiftId) in periods || {}" :key="shiftId">
                <th 
                  v-for="(time, index) in period"
                  :key="`time-${shiftId}-${index}`"
                  class="border border-gray-600 px-2 py-1 text-center text-xs bg-white"
                >
                  {{ time }}
                </th>
              </template>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(teacherRoutine, teacher_id) in teacherWiseData" :key="teacher_id">
              <td class="border border-gray-600 px-3 py-3 font-bold text-sm text-gray-800 bg-white">
                {{ teachers[teacher_id]?.first_name }} {{ teachers[teacher_id]?.last_name || '' }}
                <p class="font-normal text-xs text-gray-600">
                  {{ teachers[teacher_id]?.designation?.name }}
                </p>
              </td>
              <template v-for="(period, shiftId) in periods || {}" :key="shiftId">
                <td
                  v-for="(periodinfo, index) in period" 
                  :key="`cell-${teacher_id}-${shiftId}-${index}`"
                  class="border border-gray-600 px-1 py-2 bg-white"
                >
                  <div v-if="teacherRoutine?.[shiftId]?.[index]" class="space-y-1">
                    <div class="text-sm font-semibold text-gray-900 text-center">
                      {{ teacherRoutine[shiftId][index].subject_name }}
                    </div>
                    <div class="text-xs text-gray-600 text-center">
                      {{ teacherRoutine[shiftId][index].class_name }}, {{ shifts[shiftId]?.shift_name }}, {{ teacherRoutine[shiftId][index].version_name }}
                    </div>
                  </div>
                  <div v-else class="text-xs text-gray-400 italic text-center">
                    -
                  </div>
                </td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Class-wise Table View -->
      <div v-if="viewMode === 'class'" class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-600">
          <thead>
            <!-- Shift Names Row -->
            <tr>
              <th class="border border-gray-600 px-3 py-2 text-center text-sm font-bold bg-white" rowspan="3">
                Classes
              </th>
              <th 
                v-for="(period, shiftId) in periods || {}" 
                :key="shiftId"
                :colspan="Object.keys(period).length"
                class="border border-gray-600 px-2 py-2 text-center text-sm font-bold bg-white"
              >
                {{ shifts[shiftId]?.shift_name }}
              </th>
            </tr>
            <!-- Period Numbers Row -->
            <tr>
              <template v-for="(period, shiftId) in periods || {}" :key="shiftId">
                <th 
                  v-for="(time, index) in period" 
                  :key="`period-${shiftId}-${index}`"
                  class="border border-gray-600 px-2 py-2 text-center text-sm font-bold bg-white"
                >
                  {{ index }}{{ index == 1 ? 'st' : index == 2 ? 'nd' : index == 3 ? 'rd' : 'th' }} Period
                </th>
              </template>
            </tr>
            <!-- Time Row -->
            <tr>
              <template v-for="(period, shiftId) in periods || {}" :key="shiftId">
                <th 
                  v-for="(time, index) in period"
                  :key="`time-${shiftId}-${index}`"
                  class="border border-gray-600 px-2 py-1 text-center text-xs bg-white"
                >
                  {{ time }}
                </th>
              </template>
            </tr>
          </thead>
          <tbody>
            <template v-for="(classData, versionId) in classWiseData" :key="versionId">
              <template v-for="(sectionData, classId) in classData" :key="`${versionId}-${classId}`">
                <tr v-for="(shiftData, sectionId) in sectionData" :key="`${versionId}-${classId}-${sectionId}`">
                  <td class="border border-gray-600 px-3 py-3 font-bold text-sm text-gray-800 bg-white">
                    {{ classes[classId]?.class_name }}
                    <p class="font-normal text-xs text-gray-600">
                      {{ versions[versionId]?.version_name }}, Section {{ sectionId }}
                    </p>
                  </td>
                  <template v-for="(period, shiftId) in periods || {}" :key="shiftId">
                    <td
                      v-for="(periodinfo, index) in period" 
                      :key="`cell-${versionId}-${classId}-${sectionId}-${shiftId}-${index}`"
                      class="border border-gray-600 px-1 py-2 bg-white"
                    >
                      <div v-if="shiftData?.[shiftId]?.[index]" class="space-y-1">
                        <div class="text-sm font-semibold text-gray-900 text-center">
                          {{ shiftData[shiftId][index].subject_name }}
                        </div>
                        <div class="text-xs text-gray-600 text-center">
                          {{ teachers[shiftData[shiftId][index].teacher_id]?.first_name }} {{ teachers[shiftData[shiftId][index].teacher_id]?.last_name || '' }}, {{ teachers[shiftData[shiftId][index].teacher_id]?.designation?.name }}
                        </div>
                      </div>
                      <div v-else class="text-xs text-gray-400 italic text-center">
                        -
                      </div>
                    </td>
                  </template>
                </tr>
              </template>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 w-full">

      <!-- Revenue/Cost (Left Section) -->
      <div class="lg:col-span-2 bg-white rounded-2xl shadow-md p-6 border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
          <div>
            <h3 class="text-xl font-bold text-gray-800">Income Expense Overview</h3>
            <p class="text-sm text-gray-500">Yearly revenue/expense overview</p>
          </div>
        </div>

        <div class="w-full" style="height: 300px;">
          <Line :data="salesChartData" :options="salesChartOptions" />
        </div>
      </div>

      <!-- Recent Transactons (Right Section) -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Recent Transactions</h3>
        <div class="space-y-3">
          
          <div v-if="recentTransactions.length > 0" class="space-y-3">
            <div 
              v-for="transaction in recentTransactions" 
              :key="transaction.id"
              class="flex items-center justify-between p-3 border-b border-gray-100 hover:bg-gray-50 rounded-lg transition-colors"
            >
              <div class="flex items-center gap-3">
                <div 
                  :class="[
                    'w-10 h-10 rounded-full flex items-center justify-center',
                    transaction.voucher_type === 'CREDIT' ? 'bg-green-100' : 'bg-red-100'
                  ]"
                >
                  <i 
                    :class="[
                      'fas',
                      transaction.voucher_type === 'CREDIT' ? 'fa-arrow-down text-green-600' : 'fa-arrow-up text-red-600'
                    ]"
                  ></i>
                </div>
                <div>
                  <p class="font-semibold text-sm text-gray-800">{{ getTransactionDescription(transaction) }}</p>
                  <p v-if="transaction.from_to" class="text-xs text-gray-500 mt-1">{{ transaction.from_to }}</p>
                </div>
              </div>
              <div class="text-right">
                <p 
                  :class="[
                    'font-bold',
                    transaction.voucher_type === 'CREDIT' ? 'text-green-600' : 'text-red-600'
                  ]"
                >
                  {{ transaction.voucher_type === 'CREDIT' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
                </p>
                <p class="text-xs text-gray-500">{{ transaction.date }}</p>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8 text-gray-500">
            <i class="fas fa-receipt text-4xl mb-2"></i>
            <p>No recent transactions</p>
          </div>

        </div>
      </div>

    </div>

    <!-- Pie and Donut Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      
      <!-- Pie Chart: Student Attendance (Today) -->
      <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
        <div class="mb-6">
          <h3 class="text-xl font-bold text-gray-800">Student Attendance (Today)</h3>
          <p class="text-sm text-gray-500">Present, Absent, Late & Leave</p>
        </div>
        <div class="w-full flex justify-center" style="height: 300px;">
          <Pie :data="pieChartData" :options="pieChartOptions" />
        </div>
      </div>

      <!-- Donut Chart: Teacher Attendance (Today) -->
      <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
        <div class="mb-6">
          <h3 class="text-xl font-bold text-gray-800">Teacher Attendance (Today)</h3>
          <p class="text-sm text-gray-500">Present, Absent, Late & Leave</p>
        </div>
        <div class="w-full flex justify-center" style="height: 300px;">
          <Doughnut :data="donutChartData" :options="donutChartOptions" />
        </div>
      </div>

    </div>

  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  Chart as ChartJS, 
  CategoryScale, 
  LinearScale, 
  PointElement, 
  LineElement, 
  ArcElement,
  Title, 
  Tooltip, 
  Legend 
} from 'chart.js'
import { Line, Pie, Doughnut } from 'vue-chartjs'
import AppLayout from '../Layouts/AppLayout.vue'
import axios from 'axios'

ChartJS.register(
  CategoryScale, 
  LinearScale, 
  PointElement, 
  LineElement, 
  ArcElement,
  Title, 
  Tooltip, 
  Legend
)


const totalStudent = ref(0)
const todayStudentAttendance = ref(0)
const studentAttendanceChange = ref(0)
const studentAttendancePercentage = ref(0)

const totalTeacher = ref(0)
const todayTeacherAttendance = ref(0)
const teacherAttendanceChange = ref(0)
const teacherAttendancePercentage = ref(0)

const totalOtherStaff = ref(0)
const todayOtherStaffAttendance = ref(0)
const otherStaffAttendanceChange = ref(0)
const otherStaffAttendancePercentage = ref(0)

const currentSessionProfit = ref(0)
const lastSessionProfit = ref(0)
const profitChange = ref(0)
const profitProgressPercentage = ref(0)

const thisSessionTotalReceivedFees = ref(0)
const thisMonthTotalReceivedFees = ref(0)
const thisSessionTotalAssignFees = ref(0)
const thisSessionTotalDueFees = ref(0)
const thisSessionTotalExpense = ref(0)
const thisMonthTotalExpense = ref(0)

const recentTransactions = ref([])
const monthlyData = ref([])

const studentAttendanceBreakdown = ref({
  present: 0,
  absent: 0,
  late: 0,
  leave: 0
})

const teacherAttendanceBreakdown = ref({
  present: 0,
  absent: 0,
  late: 0,
  leave: 0
})

const viewMode = ref('teacher')
const todayDay = ref('')
const teacherWiseData = ref([])
const classWiseData = ref([])
const teachers = ref([])
const periods = ref([])
const shifts = ref([])
const subjects = ref([])
const versions = ref([])
const classes = ref([])

const fetchDashboardData = async () => {
  try {
    const response = await axios.get('/api/')
    if (response.data.success) {
      const data = response.data.data
      
      totalStudent.value = data.totalStudent
      todayStudentAttendance.value = data.todayStudentAttendance
      studentAttendanceChange.value = data.studentAttendanceChange
      studentAttendancePercentage.value = data.studentAttendancePercentage
      
      totalTeacher.value = data.totalTeacher
      todayTeacherAttendance.value = data.todayTeacherAttendance
      teacherAttendanceChange.value = data.teacherAttendanceChange
      teacherAttendancePercentage.value = data.teacherAttendancePercentage
      
      totalOtherStaff.value = data.totalOtherStaff
      todayOtherStaffAttendance.value = data.todayOtherStaffAttendance
      otherStaffAttendanceChange.value = data.otherStaffAttendanceChange
      otherStaffAttendancePercentage.value = data.otherStaffAttendancePercentage
      
      currentSessionProfit.value = data.currentSessionProfit
      lastSessionProfit.value = data.lastSessionProfit
      profitChange.value = data.profitChange
      profitProgressPercentage.value = data.profitProgressPercentage

      thisSessionTotalReceivedFees.value = data.thisSessionTotalReceivedFees
      thisMonthTotalReceivedFees.value = data.thisMonthTotalReceivedFees
      thisSessionTotalAssignFees.value = data.thisSessionTotalAssignFees
      thisSessionTotalDueFees.value = data.thisSessionTotalDueFees
      thisSessionTotalExpense.value = data.thisSessionTotalExpense
      thisMonthTotalExpense.value = data.thisMonthTotalExpense

      studentAttendanceBreakdown.value = data.studentAttendanceBreakdown || {
        present: 0,
        absent: 0,
        late: 0,
        leave: 0
      }
      
      teacherAttendanceBreakdown.value = data.teacherAttendanceBreakdown || {
        present: 0,
        absent: 0,
        late: 0,
        leave: 0
      }

      recentTransactions.value = data.recentTransactions || []
      monthlyData.value = response.data.data.monthlyData || []
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
  }
}

const fetchCustomRoutineData = async () => {
  try {
    const response = await axios.get('/api/custom-routines')
    if (response.data.success) {
      todayDay.value = response.data.data.day_name || ''
      teacherWiseData.value = response.data.data.routine.teacher_wise_data || []
      classWiseData.value = response.data.data.routine.class_wise_data || []
      teachers.value = response.data.data.teachers || []
      shifts.value = response.data.data.shifts || []
      subjects.value = response.data.data.subjects || []
      versions.value = response.data.data.versions || []
      classes.value = response.data.data.classes || []
      periods.value = response.data.data.routine.periods || []
      
    }
  } catch (error) {
    showErrorAlert('Error', 'Failed to load custom routine data')
  }
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount) + '/='
}

const getTransactionDescription = (transaction) => {
  if (transaction.transaction_type === 'Fee Payment') {
    return 'Fee Payment'
  } else if (transaction.transaction_type === 'SALARY_PAYMENT') {
    return 'Salary Payment'
  } else if (transaction.transaction_type === 'SALARY_ADVANCE') {
    return 'Salary Advance'
  } else {
    return transaction.transaction_type
  }
}

onMounted(() => {
  fetchDashboardData()
  fetchCustomRoutineData()
})

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend)

const salesChartData = computed(() => ({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  datasets: [
    {
      label: 'Income',
      data: monthlyData.value.map(m => m.income),
      borderColor: '#10B981',
      backgroundColor: 'rgba(16, 185, 129, 0.1)',
      borderWidth: 3,
      fill: true,
      tension: 0.4,
      pointRadius: 4,
      pointHoverRadius: 8,
      pointBackgroundColor: '#10B981',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointHoverBackgroundColor: '#10B981',
      pointHoverBorderColor: '#fff',
      pointHoverBorderWidth: 3
    },
    {
      label: 'Expense',
      data: monthlyData.value.map(m => m.expense),
      borderColor: '#EF4444',
      backgroundColor: 'rgba(239, 68, 68, 0.1)',
      borderWidth: 3,
      fill: true,
      tension: 0.4,
      pointRadius: 4,
      pointHoverRadius: 8,
      pointBackgroundColor: '#EF4444',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointHoverBackgroundColor: '#EF4444',
      pointHoverBorderColor: '#fff',
      pointHoverBorderWidth: 3
    }
  ]
}))

const salesChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    intersect: false,
    mode: 'index'
  },
  plugins: {
    legend: {
      display: true,
      position: 'top',
      labels: {
        usePointStyle: true,
        padding: 15,
        color: '#374151'
      }
    },
    tooltip: {
      enabled: true,
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      padding: 12,
      cornerRadius: 8,
      displayColors: true,
      callbacks: {
        label: (context) => {
          return context.dataset.label + ': ' + formatCurrency(context.parsed.y)
        }
      }
    }
  },
  scales: {
    x: {
      grid: { display: false },
      ticks: { color: '#9CA3AF' }
    },
    y: {
      beginAtZero: true,
      grid: { color: '#F3F4F6' },
      ticks: {
        color: '#9CA3AF',
        callback: (value) => (value / 1000) + 'k'
      }
    }
  }
}))


const pieChartData = computed(() => ({
  labels: ['Present', 'Absent', 'Late', 'Leave'],
  datasets: [
    {
      data: [
        studentAttendanceBreakdown.value.present,
        studentAttendanceBreakdown.value.absent,
        studentAttendanceBreakdown.value.late,
        studentAttendanceBreakdown.value.leave
      ],
      backgroundColor: [
        '#10B981', 
        '#EF4444', 
        '#F59E0B', 
        '#3B82F6'  
      ],
      borderColor: '#fff',
      borderWidth: 2,
      hoverOffset: 10
    }
  ]
}))

const pieChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'bottom',
      labels: {
        padding: 15,
        color: '#374151',
        usePointStyle: true
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      padding: 12,
      cornerRadius: 8,
      callbacks: {
        label: (context) => {
          const label = context.label || ''
          const value = context.parsed || 0
          const total = context.dataset.data.reduce((a, b) => a + b, 0)
          const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0
          return `${label}: ${value} students (${percentage}%)`
        }
      }
    }
  }
}))

const donutChartData = computed(() => ({
  labels: ['Present', 'Absent', 'Late', 'Leave'],
  datasets: [
    {
      data: [
        teacherAttendanceBreakdown.value.present,
        teacherAttendanceBreakdown.value.absent,
        teacherAttendanceBreakdown.value.late,
        teacherAttendanceBreakdown.value.leave
      ],
      backgroundColor: [
        '#10B981', 
        '#EF4444', 
        '#F59E0B', 
        '#3B82F6'  
      ],
      borderColor: '#fff',
      borderWidth: 3,
      hoverOffset: 15
    }
  ]
}))

const donutChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  cutout: '65%',
  plugins: {
    legend: {
      display: true,
      position: 'bottom',
      labels: {
        padding: 15,
        color: '#374151',
        usePointStyle: true,
        font: {
          size: 13
        }
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      padding: 12,
      cornerRadius: 8,
      callbacks: {
        label: (context) => {
          const label = context.label || ''
          const value = context.parsed || 0
          const total = context.dataset.data.reduce((a, b) => a + b, 0)
          const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0
          return `${label}: ${value} teachers (${percentage}%)`
        }
      }
    }
  }
}))
</script>

<style scoped>
.stat-card {
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
}
</style>