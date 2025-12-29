<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-1 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-2">
      <div>
        <h1 class="text-xl font-bold text-gray-800">
          Payment Management
        </h1>
      </div>
      
      <router-link 
        :to="{ name: 'payments.create' }"
        class="inline-flex items-center gap-1 px-3 py-1.5 border bg-blue-600 hover:bg-blue-700 rounded-lg text-gray-100 text-sm font-medium transition-colors"
      >
        <i class="fas fa-plus text-xs"></i>
        New Payment
      </router-link>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-2 mb-1">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
        <!-- Session Filter -->
        <div>
          <label class=" text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
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
          <label class=" text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
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
          <label class=" text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
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
          <label class=" text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
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

      <!-- Second Row -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mt-2 items-end">
        <!-- Roll Filter -->
        <div>
          <label class=" text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-hashtag text-gray-400 text-xs"></i>
            Class Roll
            <span v-if="isClassRollSearch" class="text-red-500">*</span>
          </label>
          <input 
            type="number" 
            v-model="filters.roll"
            @input="onRollChange"
            placeholder="Roll" 
            class="px-2 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-full"
            min="1"
          />
        </div>

        <!-- ID Card Number -->
        <div>
          <label class=" text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
            <i class="fas fa-id-card text-gray-400 text-xs"></i>
            ID Card No.
            <span v-if="!isClassRollSearch" class="text-red-500">*</span>
          </label>
          <input 
            type="number" 
            v-model="filters.id_card_number"
            @input="onIdCardChange"
            placeholder="ID Card No."
            class="px-2 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-full"
            min="1"
          />
        </div>

        <!-- Search Button -->
        <div>
          <button 
            @click="applyFilters"
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

      <!-- Validation Error -->
      <div v-if="validationError" class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center gap-1 text-red-700 text-sm">
          <i class="fas fa-exclamation-circle text-xs"></i>
          <span>{{ validationError }}</span>
        </div>
      </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
      <!-- Table Header -->
      <div class="px-3 py-2 border-b border-gray-200 bg-linear-to-r from-gray-50 to-white">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
          <div class="text-xs text-gray-700">
            <span>Total Payments: <span class="font-semibold">{{ paginationInfo.total }}</span></span>
            <span class="ml-3">Total Amount: <span class="font-semibold text-green-600">৳{{ formatAmount(totalAmount) }}</span></span>
          </div>

          <!-- Top Pagination Info -->
          <div class="flex items-center gap-2">
            <!-- Per Page Selector -->
            <div class="flex items-center gap-1 text-xs">
              <span class="text-gray-600">Show:</span>
              <select 
                v-model="perPage"
                @change="changePerPage"
                class="px-2 py-1 text-xs border border-gray-300 rounded-sm focus:outline-none bg-white text-gray-700"
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
          <p class="text-gray-600 text-sm">Loading payments...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && payments.length === 0" class="p-4 text-center">
        <div class="inline-block p-3">
          <i class="fas fa-receipt text-3xl text-gray-400 mb-2"></i>
          <p class="text-gray-600 text-sm mb-1">No payments found.</p>
          <p class="text-xs text-gray-500">Try adjusting your filters or create a new payment.</p>
        </div>
      </div>

      <!-- Table -->
      <div v-if="!loading && payments.length > 0" class="overflow-x-auto">
        <table class="w-full min-w-[1000px]">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-16">SL</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Actions</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Roll</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
              <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr 
              v-for="(payment, index) in payments" 
              :key="payment.id" 
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
                    @click="toggleDropdown(payment.id)"
                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== payment.id,
                      'bg-gray-200 text-black': openDropdownId === payment.id
                    }"
                  >
                    Actions
                    <i 
                      class="fas fa-chevron-down text-xs transition-transform duration-200 ml-1"
                      :class="{
                        'rotate-180': openDropdownId === payment.id,
                        'text-white': openDropdownId !== payment.id,
                        'text-black': openDropdownId === payment.id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === payment.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- View Option -->
                      <button
                        @click="viewPayment(payment.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-eye text-blue-600 text-xs w-4"></i>
                        <span>View</span>
                      </button>

                      <button
                        @click="editPayment(payment.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit</span>
                      </button>
                      
                      <!-- Delete Option -->
                      <button 
                        @click="handleDeletePayment(payment)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Date -->
              <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-800">
                <div class="flex items-center gap-1">
                  <i class="fas fa-calendar text-gray-400 text-xs"></i>
                  {{ formatDate(payment.payment_date) }}
                </div>
              </td>

              <!-- Student Info -->
              <td class="px-3 py-2">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                    <img 
                      v-if="payment.class_wise_student?.student?.student_image_url"
                      :src="payment.class_wise_student.student.student_image_url" 
                      :alt="payment.class_wise_student?.student?.student_name"
                      class="w-full h-full object-cover"
                    >
                    <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                      <i class="fas fa-user text-gray-400 text-xs"></i>
                    </div>
                  </div>
                  <div>
                    <div class="font-semibold text-gray-800 text-xs">
                      {{ payment.class_wise_student?.student?.student_name || 'N/A' }}
                    </div>
                    <div v-if="payment.class_wise_student?.student?.id_card_number" class="text-xs text-gray-500">
                      ID: {{ payment.class_wise_student.student.id_card_number }}
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Roll -->
              <td class="px-3 py-2 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                  {{ payment.class_wise_student?.class_roll || 'N/A' }}
                </span>
              </td>
              
              <!-- Class -->
              <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-800">
                <div class="flex items-center gap-1">
                  <i class="fas fa-graduation-cap text-gray-400 text-xs"></i>
                  {{ payment.class_wise_student?.class?.class_name || 'N/A' }}
                </div>
              </td>
              
              <!-- Month -->
              <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-800">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                  <i class="fas fa-calendar-day text-xs mr-1"></i>
                  {{ payment.month?.month_name || 'N/A' }}
                </span>
              </td>

              <!-- Account -->
              <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-800">
                <div class="flex items-center gap-1">
                  <i :class="payment.account?.account_type === 'bank' ? 'fas fa-university text-blue-500' : 'fas fa-wallet text-green-500'" class="text-xs"></i>
                  {{ payment.account?.account_name || 'N/A' }}
                </div>
              </td>
              
              <!-- Amount -->
              <td class="px-3 py-2 whitespace-nowrap text-right">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                  ৳{{ formatAmount(payment.total_paid_amount) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Bottom Pagination -->
      <div v-if="!loading && payments.length > 0" class="px-3 py-2 border-t border-gray-200 bg-gray-50">
        <div class="flex flex-col md:flex-row justify-between items-center gap-2">
          <!-- Pagination Info -->
          <div class="text-xs text-gray-600">
            Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of {{ paginationInfo.total }} payment{{ paginationInfo.total > 1 ? 's' : '' }}
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
                class="px-2 py-2 text-xs rounded-sm border transition-colors min-w-8"
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
import { showErrorAlert, showSuccessAlert, showLoadingAlert, closeAlert, showConfirmDialog } from '../../utils/sweetAlert'

const router = useRouter()

// Data
const payments = ref([])
const loading = ref(false)
const currentPage = ref(1)
const perPage = ref(10)
const openDropdownId = ref(null)
const totalAmount = ref(0)
const validationError = ref('')

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

// Computed
const isClassRollSearch = computed(() => {
  return filters.value.session_id || filters.value.version_id || filters.value.class_id || filters.value.section_id || filters.value.roll
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
    // Fetch sessions, classes, versions, sections
    const waverResponse = await axios.get('/api/wavers/dropdown-data')
    if (waverResponse.data.success) {
      sessions.value = waverResponse.data.data.sessions || []
      versions.value = waverResponse.data.data.versions || []
      classes.value = waverResponse.data.data.classes || []
      sections.value = waverResponse.data.data.sections || []
      
      // Auto-select active session
      const activeSession = waverResponse.data.data.active_session
      if (activeSession && activeSession.id) {
        filters.value.session_id = activeSession.id
      }
    }
  } catch (error) {
    console.error('Error fetching dropdown data:', error)
  }
}

const fetchPayments = async (page = 1) => {
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
    if (filters.value.id_card_number) params.id_card_number = filters.value.id_card_number
    
    const response = await axios.get('/api/payments', { params })
    
    if (response.data.success) {
      const data = response.data.data
      
      // Update pagination info
      paginationInfo.value = {
        current_page: data.current_page || 1,
        last_page: data.last_page || 1,
        from: data.from || 0,
        to: data.to || 0,
        total: data.total || 0
      }
      
      // Fix image paths
      const paymentsData = data.data || []
      payments.value = paymentsData.map(payment => {
        if (payment.class_wise_student?.student?.student_image) {
          payment.class_wise_student.student.student_image_url = 
            `/assets/images/student_images/${payment.class_wise_student.student.student_image}`
        } else {
          payment.class_wise_student.student.student_image_url = '/assets/images/default-avatar.png'
        }
        return payment
      })

      // Calculate total amount
      totalAmount.value = payments.value.reduce((sum, payment) => {
        return sum + parseFloat(payment.total_paid_amount || 0)
      }, 0)
    } else {
      payments.value = []
      totalAmount.value = 0
      paginationInfo.value = {
        current_page: 1,
        last_page: 1,
        from: 0,
        to: 0,
        total: 0
      }
    }
  } catch (error) {
    console.error('Error fetching payments:', error)
    payments.value = []
    totalAmount.value = 0
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

const validateSearch = () => {
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

const applyFilters = () => {
  if (!validateSearch()) {
    return
  }
  
  validationError.value = ''
  currentPage.value = 1
  fetchPayments(1)
}

const clearFilters = () => {
  // Get active session before clearing
  const activeSessionId = sessions.value.find(s => s.status === 'active')?.id || ''
  
  filters.value = {
    session_id: activeSessionId,
    version_id: '',
    class_id: '',
    section_id: '',
    roll: '',
    id_card_number: ''
  }
  validationError.value = ''
  currentPage.value = 1
  fetchPayments(1)
}

const onRollChange = () => {
  validationError.value = ''
}

const onIdCardChange = () => {
  validationError.value = ''
}

const goToPage = (page) => {
  if (page >= 1 && page <= paginationInfo.value.last_page) {
    currentPage.value = page
    fetchPayments(page)
  }
}

const changePerPage = () => {
  currentPage.value = 1
  fetchPayments(1)
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

const viewPayment = (paymentId) => {
  closeDropdown()
  router.push({ 
    name: 'payments.show', 
    params: { id: paymentId } 
  })
}

const editPayment = (paymentId) => {
  closeDropdown()  
  router.push({ 
    name: 'payments.edit',    
    params: { id: paymentId }  
  })
}

const handleDeletePayment = async (payment) => {
  closeDropdown()
  
  const result = await showConfirmDialog(
    'Delete Payment?',
    `Are you sure you want to delete this payment of ৳${formatAmount(payment.total_paid_amount)} for ${payment.class_wise_student?.student?.student_name}? This action cannot be undone.`
  )
  
  if (result.isConfirmed) {
    await deletePayment(payment.id)
  }
}

const deletePayment = async (paymentId) => {
  showLoadingAlert('Deleting payment...')
  
  try {
    const response = await axios.delete(`/api/payments/${paymentId}`)
    
    closeAlert()
    
    if (response.data.success) {
      await showSuccessAlert('Deleted!', 'Payment has been deleted successfully')
      fetchPayments(currentPage.value)
    }
  } catch (error) {
    console.error('Error deleting payment:', error)
    closeAlert()
    showErrorAlert(
      'Delete Failed',
      error.response?.data?.message || 'Failed to delete payment. Please try again.'
    )
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

const formatAmount = (amount) => {
  return parseFloat(amount || 0).toFixed(2)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
}

// Lifecycle
onMounted(async () => {
  await fetchDropdownData()
  await fetchPayments()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
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

.v-select-custom {
  font-size: 0.875rem;
}
</style>