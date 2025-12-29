<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-1 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-2">
      <div>
        <h1 class="text-xl font-bold text-gray-800">
          Create Payment
        </h1>
      </div>
      <router-link 
        to="/payments"
        class="inline-flex items-center gap-1 px-3 py-1.5 border bg-blue-600 hover:bg-blue-700 rounded-lg text-gray-100 text-sm font-medium transition-colors"
      >
        <i class="fas fa-arrow-left text-xs"></i>
        Back to List
      </router-link>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-2 mb-1">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
        <!-- Session Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
            Session
            <span v-if="isClassRollSearch" class="text-red-500">*</span>
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
            <span v-if="isClassRollSearch" class="text-red-500">*</span>
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

        <!-- Class Filter -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-graduation-cap text-gray-400 text-xs"></i>
            Class
            <span v-if="isClassRollSearch" class="text-red-500">*</span>
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
            <span v-if="isClassRollSearch" class="text-red-500">*</span>
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

      <!-- Roll, ID Card, Month, Search, Clear -->
      <div class="col-span-2 mt-1">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-2 items-end">
          <!-- Roll Filter -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-hashtag text-gray-400 text-xs"></i>
              Class Roll
              <span v-if="isClassRollSearch" class="text-red-500">*</span>
            </label>
            <input 
              type="number" 
              v-model="filters.roll"
              @input="onRollChange"
              placeholder="Roll" 
              class="px-2 py-2.5 text-sm border border-gray-300 rounded-lg w-full"
              min="1"
            />
          </div>

          <!-- ID Card Number -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-id-card text-gray-400 text-xs"></i>
              ID Card No.
              <span v-if="!isClassRollSearch" class="text-red-500">*</span>
            </label>
            <input 
              type="number" 
              v-model="filters.id_card_number"
              @input="onIdCardChange"
              placeholder="ID Card No."
              class="px-2 py-2.5 text-sm border border-gray-300 rounded-lg w-full"
              min="1"
            />
          </div>

          <!-- Month Selection -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-calendar-day text-gray-400 text-xs"></i>
              Month
              <span class="text-red-500">*</span>
            </label>
            <v-select
              v-model="filters.month_id"
              :options="months"
              label="month_name"
              :reduce="month => month.id"
              placeholder="Select Month"
              :clearable="true"
              class="v-select-custom text-sm"
            >
              <template #option="{ month_name }">
                <div class="flex items-center gap-1 text-sm">
                  <i class="fas fa-calendar-alt text-indigo-500 text-xs"></i>
                  {{ month_name }}
                </div>
              </template>
            </v-select>
          </div>

          <!-- Search Button -->
          <div>
            <button 
              @click="searchStudent"
              class="w-full px-3 py-2.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold flex items-center justify-center gap-1 shadow cursor-pointer"
            >
              <i class="fas fa-search text-xs"></i>
              Search 
            </button>
          </div>

          <!-- Clear Button -->
          <div>
            <button 
              @click="clearFilters"
              class="w-full px-3 py-2.5 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold flex items-center justify-center gap-1 cursor-pointer"
            >
              <i class="fas fa-times text-xs"></i>
              Clear
            </button>
          </div>
        </div>
      </div>

      <!-- Validation Error -->
      <div v-if="validationError" class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center gap-1 text-red-700 text-sm">
          <i class="fas fa-exclamation-circle text-xs"></i>
          <span>{{ validationError }}</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-xl shadow border border-gray-100 p-4 text-center">
      <div class="inline-block">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
        <p class="text-gray-600 text-sm">Searching student...</p>
      </div>
    </div>

    <!-- Student Not Found -->
    <div v-if="!loading && studentNotFound" class="bg-white rounded-xl shadow border border-gray-100 p-4 text-center">
      <div class="inline-block p-4">
        <i class="fas fa-user-slash text-3xl text-red-400 mb-2"></i>
        <p class="text-gray-600 mb-1">Student not found with the provided information.</p>
        <p class="text-xs text-gray-500">Please check your filters and try again.</p>
      </div>
    </div>

    <!-- Student Found - Show Student Info and Payment Form -->
    <div v-if="!loading && selectedStudent && !studentNotFound && showPaymentForm" class="space-y-3">
      <!-- Student Information Card -->
      <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <div class="p-2">
          <div class="flex flex-col md:flex-row gap-3 items-start md:items-center">
            <!-- Student Image -->
            <div class="shrink-0">
              <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white shadow">
                <img 
                  v-if="selectedStudent.student_image"
                  :src="getStudentImageUrl(selectedStudent.student_image)" 
                  :alt="selectedStudent.student_name"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                >
                <div v-else class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                  <i class="fas fa-user-graduate text-blue-400 text-2xl"></i>
                </div>
              </div>
            </div>

            <!-- Student Info -->
            <div class="flex-1">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                  <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                    <i class="fas fa-user text-xs"></i>
                    Student Name
                  </div>
                  <div class="font-semibold text-gray-800 text-sm">{{ selectedStudent.student_name }}</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                  <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                    <i class="fas fa-id-card text-xs"></i>
                    ID Card No.
                  </div>
                  <div class="font-semibold text-gray-800 text-sm">{{ selectedStudent.id_card_number || 'N/A' }}</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                  <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                    <i class="fas fa-sort-numeric-up text-xs"></i>
                    Class Roll
                  </div>
                  <div class="font-semibold text-gray-800">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ selectedStudent.roll }}
                    </span>
                  </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                  <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                    <i class="fas fa-phone text-xs"></i>
                    Mobile
                  </div>
                  <div class="font-semibold text-gray-800 text-sm">{{ selectedStudent.mobile_number || 'N/A' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Academic Info -->
        <div class="px-3 py-2 border-t border-gray-200 bg-gray-50">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Session</div>
              <div class="font-semibold text-gray-800 text-sm">{{ getSessionName(selectedStudent.session_id) }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Class</div>
              <div class="font-semibold text-gray-800 text-sm">{{ getClassName(selectedStudent.class_id) }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Version</div>
              <div class="font-semibold text-gray-800 text-sm">{{ getVersionName(selectedStudent.version_id) }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Section</div>
              <div class="font-semibold text-gray-800 text-sm">{{ getSectionName(selectedStudent.section_id) }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Form -->
      <div class="bg-white rounded-xl shadow border border-gray-100 p-2">
        <!-- Payment Details -->
        <div class="mb-3">
          <div class="grid grid-cols-2 gap-4 mb-2">
            <!-- Title Section -->
            <div class="flex items-center gap-2">
              <div class="p-1.5 bg-green-100 rounded-lg">
                <i class="fas fa-receipt text-green-600 text-sm"></i>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-gray-800">Payment Details</h3>
                <p class="text-xs text-gray-500">Showing all outstanding dues</p>
              </div>
            </div>

            <!-- Payment Date and Account -->
            <div class="grid grid-cols-2 gap-2">
              <!-- Payment Date -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
                  <i class="fas fa-calendar-day text-gray-400 text-xs"></i>
                  Payment Date
                  <span class="text-red-500">*</span>
                </label>
                <input 
                  type="date" 
                  v-model="paymentData.payment_date"
                  class="px-2 py-2 text-sm border border-gray-300 rounded-lg w-full"
                  required
                />
              </div>

              <!-- Account Selection -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
                  <i class="fas fa-university text-gray-400 text-xs"></i>
                  Account
                  <span class="text-red-500">*</span>
                </label>
                <v-select
                  v-model="paymentData.account_id"
                  :options="accounts"
                  label="account_name"
                  :reduce="account => account.id"
                  placeholder="Select Account"
                  class="v-select-custom text-sm"
                >
                  <template #option="option">
                    <div class="flex items-center gap-1 text-sm">
                      <i class="fas fa-university text-blue-500 text-xs"></i>
                      {{ option.account_name }}
                    </div>
                  </template>
                  <template #selected-option="option">
                    <div class="flex items-center gap-1">
                      <i class="fas fa-university text-blue-500 text-xs"></i>
                      {{ option.account_name }}
                    </div>
                  </template>
                </v-select>
              </div>
            </div>
          </div>
        </div>

        <!-- Fee Heads Table -->
        <div>
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <div class="p-1.5 bg-blue-100 rounded-lg">
                <i class="fas fa-money-bill-wave text-blue-600 text-sm"></i>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-gray-800">Fee Heads and Payment Amounts</h3>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase w-12">
                    SL
                  </th>
                  <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase">
                    Fee Head
                  </th>
                  <template v-for="(month, month_id) in dueMonths" :key="month_id">
                    <th
                      v-if="studentFees && studentFees[month_id]"
                      class="px-2 py-1.5 text-center text-xs font-medium text-gray-500 uppercase w-20"
                    >
                      {{ month }}
                    </th>
                  </template>
                  <th class="px-2 py-1.5 text-right text-xs font-medium text-gray-500 uppercase w-32">
                    Total Due
                  </th>
                  <th class="px-2 py-1.5 text-right text-xs font-medium text-gray-500 uppercase w-48">
                    Pay Amount
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr 
                  v-for="(fee, index) in feeHeadsArray" 
                  :key="fee.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-2 py-2">
                    <div class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100">
                      <span class="text-xs font-medium text-gray-700">{{ index + 1 }}</span>
                    </div>
                  </td>
                  
                  <td class="px-2 py-2">
                    <div class="flex items-center gap-2">
                      <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-green-600 text-sm"></i>
                      </div>
                      <div>
                        <div class="text-sm font-semibold text-gray-900">{{ fee.head_name }}</div>
                      </div>
                    </div>
                  </td>

                  <template v-for="(month, month_id) in dueMonths" :key="month_id">
                    <td
                      v-if="studentFees && studentFees[month_id]"
                      class="px-2 py-2 text-center"
                    >
                      <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                      >
                        {{ studentFees?.[month_id]?.[fee.id]?.due_amount ?? 0 }}
                      </span>
                    </td>
                  </template>

                  <td class="px-2 py-2 text-right">
                    <span class="text-sm font-bold text-orange-600">{{ formatAmount(headWiseTotal[fee.id]) }}</span>
                  </td>
                  
                  
                  <td class="px-2 py-2">
                    <div class="relative">
                      <input 
                        type="number" 
                        v-model="paymentAmounts[fee.id]"
                        :max="getTotalDueForHead(fee.id)"
                        :min="0"
                        step="0.01"
                        placeholder="Enter amount"
                        class="pl-2 pr-4 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                        @input="validatePaymentAmount(fee.id, getTotalDueForHead(fee.id))"
                        @blur="calculateTotal"
                      />
                      <div class="absolute right-2 top-1/2 transform -translate-y-1/2">
                        <span class="text-gray-500 text-sm font-medium"></span>
                      </div>
                    </div>
                  </td>
                </tr>
                
                <!-- Total Row -->
                <tr class="bg-blue-50 border-t-2 border-blue-300">
                  <td :colspan="dueMonthsCount + 2" class="px-2 py-2.5 text-right text-sm text-gray-800">
                    <span class="font-bold text-base">Total Payment Amount:</span>
                  </td>
                  <td class="px-2 py-2.5">
                    <div class="text-lg font-bold text-orange-600 text-right">
                      {{ formatAmount(getTotalDueAllFees()) }} /=
                    </div>
                  </td>
                  
                  <td class="px-2 py-2.5">
                    <div class="text-lg font-bold text-blue-600 text-right">
                      {{ formatAmount(totalPayment) }} /=
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Action Buttons -->
          <div class="mt-3 pt-3 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-end items-start sm:items-center gap-2">
              <button 
                @click="savePayment"
                :disabled="isSaving || !canSave"
                class="px-3 py-1.5 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-1 shadow disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i v-if="isSaving" class="fas fa-spinner fa-spin text-xs"></i>
                <i v-else class="fas fa-save text-xs"></i>
                {{ isSaving ? 'Saving...' : 'Save Payment' }}
              </button>
            </div>
          </div>
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
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { showErrorAlert, showSuccessAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()

// Data
const loading = ref(false)
const validationError = ref('')
const studentNotFound = ref(false)
const selectedStudent = ref(null)
const studentFees = ref({})
const headWiseTotal = ref([])
const feeHeads = ref({})
const dueMonths = ref({})
const isSaving = ref(false)
const showPaymentForm = ref(false)

// Filters
const filters = ref({
  session_id: '',
  version_id: '',
  class_id: '',
  section_id: '',
  roll: '',
  id_card_number: '',
  month_id: ''
})

// Dropdown data
const sessions = ref([])
const versions = ref([])
const classes = ref([])
const sections = ref([])
const months = ref([])
const accounts = ref([])

// Payment data
const paymentData = ref({
  payment_date: new Date().toISOString().split('T')[0],
  account_id: null
})

const paymentAmounts = ref({})
const totalPayment = ref(0)

// Computed
const isClassRollSearch = computed(() => {
  return filters.value.session_id || filters.value.version_id || filters.value.class_id || filters.value.section_id || filters.value.roll
})

const canSave = computed(() => {
  if (!selectedStudent.value || !paymentData.value.payment_date || !paymentData.value.account_id) {
    return false
  }
  return totalPayment.value > 0
})

const feeHeadsArray = computed(() => {
  return Object.values(feeHeads.value)
})

const dueMonthsCount = computed(() => {
  return Object.keys(studentFees.value).length
})

// Helper Methods
const getMonthDueAmount = (monthId, headId) => {
  return studentFees.value?.[monthId]?.[headId]?.due_amount ?? 0
}

const getTotalDueForHead = (headId) => {
  let total = 0
  Object.keys(dueMonths.value).forEach(monthId => {
    total += parseFloat(getMonthDueAmount(monthId, headId) || 0)
  })
  return total
}

const getTotalDueAllFees = () => {
  let total = 0
  Object.values(feeHeads.value).forEach(fee => {
    total += getTotalDueForHead(fee.id)
  })
  return total
}

// Methods
const fetchDropdownData = async () => {
  try {
    const response = await axios.get('/api/wavers/dropdown-data')
    if (response.data.success) {
      sessions.value = response.data.data.sessions || []
      versions.value = response.data.data.versions || []
      classes.value = response.data.data.classes || []
      sections.value = response.data.data.sections || []
      months.value = response.data.data.months || []
      
      const activeSession = response.data.data.active_session
      if (activeSession && activeSession.id) {
        filters.value.session_id = activeSession.id
      }
    }

    // Fetch accounts
    const accountsResponse = await axios.get('/api/cash-banks')
    
    if (Array.isArray(accountsResponse.data)) {
      accounts.value = accountsResponse.data
    } else if (accountsResponse.data.success && accountsResponse.data.data) {
      accounts.value = accountsResponse.data.data
    } else {
      accounts.value = []
    }
  } catch (error) {
    showErrorAlert('Error', 'Failed to load dropdown data')
  }
}

const clearPaymentAmounts = () => {
  paymentAmounts.value = {}
  totalPayment.value = 0
}

const validateSearch = () => {
  // Check if month is selected
  if (!filters.value.month_id) {
    validationError.value = 'Please select a month'
    return false
  }

  // ID Card search
  if (filters.value.id_card_number) {
    return true
  }
  
  // Class Roll search - all required including session
  if (filters.value.session_id && filters.value.version_id && filters.value.class_id && filters.value.section_id && filters.value.roll) {
    return true
  }
  
  // Check which fields are missing
  if (isClassRollSearch.value) {
    const missing = []
    if (!filters.value.session_id) missing.push('Session')
    if (!filters.value.version_id) missing.push('Version')
    if (!filters.value.class_id) missing.push('Class')
    if (!filters.value.section_id) missing.push('Section')
    if (!filters.value.roll) missing.push('Class Roll')
    
    if (missing.length > 0) {
      validationError.value = `Please select: ${missing.join(', ')}`
      return false
    }
  } else {
    validationError.value = 'Please enter ID Card Number OR select Session, Version, Class, Section and Roll'
    return false
  }
  
  return true
}

const searchStudent = async () => {
  if (!validateSearch()) {
    return
  }

  validationError.value = ''
  loading.value = true
  studentNotFound.value = false
  selectedStudent.value = null
  studentFees.value = []
  paymentAmounts.value = {}
  totalPayment.value = 0
  showPaymentForm.value = false

  try {
    const params = {
      month_id: filters.value.month_id
    }
    
    // ID Card search
    if (filters.value.id_card_number) {
      params.id_card_number = filters.value.id_card_number
    } else {
      // Class Roll search
      params.session_id = filters.value.session_id
      params.version_id = filters.value.version_id
      params.class_id = filters.value.class_id
      params.section_id = filters.value.section_id
      params.roll = filters.value.roll
    }

    const response = await axios.get('/api/payments/student-fees', { params })
    
    if (response.data.success) {
      // Store student data directly from API
      selectedStudent.value = response.data.data.student
      
      // Store fees data directly from API
      studentFees.value = response.data.data.dueFees
      
      // Fee HEads
      feeHeads.value = response.data.data.feeHeads
      
      // Due Months
      dueMonths.value = response.data.data.dueMonths
      headWiseTotal.value = response.data.data.headWiseTotal
      
      if (Object.keys(feeHeads.value).length === 0) {
        studentNotFound.value = true
      } else {
        showPaymentForm.value = true
        clearPaymentAmounts()
      }
    }
  } catch (error) {
    if (error.response?.status === 404) {
      studentNotFound.value = true
    } else {
      //showErrorAlert('Error', error.response?.data?.message || 'Failed to search student')
    }
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  filters.value = {
    session_id: '',
    version_id: '',
    class_id: '',
    section_id: '',
    roll: '',
    id_card_number: '',
    month_id: ''
  }
  
  const activeSession = sessions.value.find(s => s.status === 'active')
  if (activeSession) {
    filters.value.session_id = activeSession.id
  }
  
  validationError.value = ''
  studentNotFound.value = false
  selectedStudent.value = null
  studentFees.value = {}
  feeHeads.value = {}
  dueMonths.value = {}
  paymentAmounts.value = {}
  totalPayment.value = 0
  showPaymentForm.value = false
}

const onRollChange = () => {
  if (filters.value.roll) {
    filters.value.id_card_number = ''
  }
}

const onIdCardChange = () => {
  if (filters.value.id_card_number) {
    filters.value.session_id = ''
    filters.value.version_id = ''
    filters.value.class_id = ''
    filters.value.section_id = ''
    filters.value.roll = ''
  }
}

const calculateTotal = () => {
  let total = 0
  Object.keys(paymentAmounts.value).forEach(headId => {
    const amount = parseFloat(paymentAmounts.value[headId] || 0)
    total += amount
  })
  totalPayment.value = total
}

const validatePaymentAmount = (headId, maxAmount) => {
  let value = parseFloat(paymentAmounts.value[headId] || 0)
  
  if (value > maxAmount) {
    paymentAmounts.value[headId] = maxAmount
  } else if (value < 0) {
    paymentAmounts.value[headId] = 0
  }
  
  calculateTotal()
}

const savePayment = async () => {
  if (!canSave.value) {
    showErrorAlert('Error', 'Please fill all required fields and enter payment amounts')
    return
  }

  const payment_details = []
  
  Object.values(feeHeads.value).forEach(fee => {
    const amount = parseFloat(paymentAmounts.value[fee.id] || 0)
    if (amount > 0) {
      let remainingAmount = amount
      Object.keys(dueMonths.value).forEach(monthId => {
        const monthDue = parseFloat(getMonthDueAmount(monthId, fee.id) || 0)
        if (monthDue > 0 && remainingAmount > 0) {
          const payAmount = Math.min(remainingAmount, monthDue)
          payment_details.push({
            head_id: fee.id,
            month_id: monthId,
            paid_amount: payAmount
          })
          remainingAmount -= payAmount
        }
      })
    }
  })

  if (payment_details.length === 0) {
    showErrorAlert('Error', 'Please enter at least one payment amount')
    return
  }

  const paymentPayload = {
    payment_date: paymentData.value.payment_date,
    account_id: paymentData.value.account_id,
    class_wise_student_id: selectedStudent.value.class_wise_student_id,
    month_id: filters.value.month_id,
    total_paid_amount: totalPayment.value,
    payment_details: payment_details
  }

  isSaving.value = true
  showLoadingAlert('Saving Payment', 'Please wait...')

  try {
    const response = await axios.post('/api/payments', paymentPayload)
    
    if (response.data.success) {
      closeAlert()
      await showSuccessAlert('Success', 'Payment saved successfully')
      router.push('/payments')
    }
  } catch (error) {
    closeAlert()
    const errorMessage = error.response?.data?.message || 'Failed to save payment'
    const errors = error.response?.data?.errors
    
    if (errors) {
      const errorList = Object.values(errors).flat().join('\n')
      showErrorAlert('Validation Error', errorList)
    } else {
      showErrorAlert('Error', errorMessage)
    }
  } finally {
    isSaving.value = false
  }
}

const formatAmount = (amount) => {
  return parseFloat(amount || 0).toFixed(2)
}

const getSessionName = (sessionId) => {
  const session = sessions.value.find(s => s.id === sessionId)
  return session?.session_name || 'N/A'
}

const getClassName = (classId) => {
  const classItem = classes.value.find(c => c.id === classId)
  return classItem?.class_name || 'N/A'
}

const getVersionName = (versionId) => {
  const version = versions.value.find(v => v.id === versionId)
  return version?.version_name || 'N/A'
}

const getSectionName = (sectionId) => {
  const section = sections.value.find(s => s.id === sectionId)
  return section?.section_name || 'N/A'
}

const getStudentImageUrl = (imagePath) => {
  if (!imagePath) return '/assets/images/default-avatar.png'
  if (imagePath.startsWith('http') || imagePath.startsWith('/assets')) {
    return imagePath
  }
  return `/assets/images/student_images/${imagePath}`
}

const handleImageError = (event) => {
  event.target.style.display = 'none'
  event.target.parentElement.innerHTML = `
    <div class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
      <i class="fas fa-user-graduate text-blue-400 text-2xl"></i>
    </div>
  `
}

onMounted(() => {
  fetchDropdownData()
})
</script>