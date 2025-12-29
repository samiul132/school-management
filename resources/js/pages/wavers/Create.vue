<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-1 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-2">
      <div>
        <h1 class="text-xl font-bold text-gray-800">
          Create Waver
        </h1>
      </div>
      <router-link 
        to="/wavers"
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
            <span class="text-red-500">*</span>
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
            <span class="text-red-500">*</span>
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
            <span class="text-red-500">*</span>
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
            <span class="text-red-500">*</span>
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

      <!-- Roll, ID Card, Apply, Clear -->
      <div class="col-span-2 mt-1">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 items-end">
          <!-- Roll Filter -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-hashtag text-gray-400 text-xs"></i>
              Class Roll 
              <span v-if="!filters.id_card_number" class="text-red-500">*</span>
            </label>
            <input 
              type="number" 
              v-model="filters.roll"
              @input="onRollChange"
              :placeholder="filters.id_card_number ? 'Class Roll' : 'Class Roll'" 
              class="px-2 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-full"
              min="1"
            />
          </div>

          <!-- ID Card Number -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-id-card text-gray-400 text-xs"></i>
              ID Card No.
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

          <!-- Apply Button -->
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

    <!-- Create/Edit Waver Interface -->
    <div v-if="!loading && selectedStudent && !studentNotFound" class="space-y-3">
      <!-- Student Information -->
      <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <div class="p-2">
          <div class="flex flex-col md:flex-row gap-3 items-start md:items-center">
            <!-- Student Image -->
            <div class="shrink-0">
              <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white shadow">
                <img 
                  v-if="selectedStudent.student_image_url"
                  :src="selectedStudent.student_image_url" 
                  :alt="selectedStudent.name"
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
                  <div class="font-semibold text-gray-800 text-sm">{{ selectedStudent.name }}</div>
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
              <div class="font-semibold text-gray-800 text-sm">{{ getSessionName() }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Class</div>
              <div class="font-semibold text-gray-800 text-sm">{{ getClassName() }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Version</div>
              <div class="font-semibold text-gray-800 text-sm">{{ getVersionName() }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Section</div>
              <div class="font-semibold text-gray-800 text-sm">{{ getSectionName() }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Waver Form -->
      <div class="bg-white rounded-xl shadow border border-gray-100 p-2">
        <!-- Months Selection -->
        <div class="mb-3">
          <div class="flex justify-between items-center mb-2">
            <div class="flex items-center gap-1">
              <div class="p-1.5 bg-blue-100 rounded-lg">
                <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
              </div>
              <h3 class="text-sm font-semibold text-gray-800">Select Months</h3>
            </div>

            <label class="flex items-center cursor-pointer">
              <input 
                type="checkbox" 
                v-model="selectAllMonths"
                @change="toggleAllMonths"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              >
              <span class="ml-2 text-gray-700 text-sm font-medium">All Months</span>
            </label>
          </div>
          
          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
            <div 
              v-for="month in months" 
              :key="month.id"
              class="flex items-center p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer"
              @click="toggleMonthSelection(month.id)"
            >
              <input 
                type="checkbox" 
                :id="'month-' + month.id"
                v-model="selectedMonths"
                :value="month.id"
                class="h-3.5 w-3.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                @click.stop
              >
              <label 
                :for="'month-' + month.id"
                class="ml-1.5 text-gray-700 text-sm cursor-pointer flex-1"
                @click.stop
              >
                {{ month.month_name }}
              </label>

            </div>
          </div>
        </div>

        <!-- Fee Heads Table -->
        <div>
          <div class="flex items-center gap-2 mb-2">
            <div class="p-1.5 bg-blue-100 rounded-lg">
              <i class="fas fa-money-bill-wave text-blue-600 text-sm"></i>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-800">Fee Heads and Waiver Amounts</h3>
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
                  <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase w-40">
                    Waiver Type
                  </th>
                  <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase w-48">
                    Amount / Percentage
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr 
                  v-for="(head, index) in feeHeads" 
                  :key="head.id"
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
                        <div class="text-sm font-semibold text-gray-900">{{ head.head_name }}</div>
                      </div>
                    </div>
                  </td>
                  
                  <td class="px-2 py-2">
                    <div class="flex flex-col space-y-1">
                      <label class="inline-flex items-center cursor-pointer">
                        <input 
                          type="radio" 
                          :name="'waver-type-' + head.id"
                          value="fixed"
                          v-model="selectedWaiverTypes[head.id]"
                          class="h-3.5 w-3.5 text-blue-600 focus:ring-blue-500 border-gray-300"
                          @change="clearWaiverAmount(head.id)"
                        >
                        <span class="ml-1.5 text-xs text-gray-700">Fixed Amount (৳)</span>
                      </label>
                      
                      <label class="inline-flex items-center cursor-pointer">
                        <input 
                          type="radio" 
                          :name="'waver-type-' + head.id"
                          value="percentage"
                          v-model="selectedWaiverTypes[head.id]"
                          class="h-3.5 w-3.5 text-blue-600 focus:ring-blue-500 border-gray-300"
                          @change="clearWaiverAmount(head.id)"
                        >
                        <span class="ml-1.5 text-xs text-gray-700">Percentage (%)</span>
                      </label>
                    </div>
                  </td>
                  
                  <td class="px-2 py-2">
                    <div class="relative">
                      <input 
                        type="number" 
                        v-model="waiverAmounts[head.id]"
                        :placeholder="selectedWaiverTypes[head.id] === 'percentage' ? '0-100%' : 'Amount in ৳'"
                        :min="0"
                        :max="selectedWaiverTypes[head.id] === 'percentage' ? 100 : undefined"
                        step="0.01"
                        :disabled="!selectedWaiverTypes[head.id]"
                        class="pl-2 pr-8 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full disabled:bg-gray-50"
                      />
                      <div class="absolute right-2 top-1/2 transform -translate-y-1/2">
                        <span class="text-gray-500 text-sm font-medium">
                          {{ selectedWaiverTypes[head.id] === 'percentage' ? '%' : '৳' }}
                        </span>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Action Buttons -->
          <div class="mt-3 pt-3 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
              <div class="text-xs text-gray-600">
                <i class="fas fa-info-circle text-blue-500"></i>
                Total fee heads with waiver: <span class="font-semibold">{{ getSelectedFeeHeadsCount() }}</span>
              </div>
              
              <div class="flex items-center gap-2">
                <button 
                  @click="resetWaverForm"
                  class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-colors flex items-center gap-1"
                >
                  <i class="fas fa-redo text-xs"></i>
                  Reset
                </button>
                
                <button 
                  @click="saveWaivers"
                  :disabled="isSaving || !canSave"
                  class="px-3 py-1.5 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-1 shadow disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i v-if="isSaving" class="fas fa-spinner fa-spin text-xs"></i>
                  <i v-else class="fas fa-save text-xs"></i>
                  {{ isSaving ? 'Saving...' : (isEditMode ? 'Save Waivers' : 'Save Waivers') }}
                </button>
              </div>
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
const existingWavers = ref({})
const isEditMode = ref(false)
const isSaving = ref(false)

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
const months = ref([])
const feeHeads = ref([])

// Waver data
const selectedMonths = ref([])
const selectedWaiverTypes = ref({})
const waiverAmounts = ref({})
const selectAllMonths = ref(false)

// Computed
const canSave = computed(() => {
  if (!selectedStudent.value) return false
  if (selectedMonths.value.length === 0) return false
  return getSelectedFeeHeadsCount() > 0
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
      months.value = response.data.data.months || []
      feeHeads.value = response.data.data.fee_heads || []
      
      const activeSession = response.data.data.active_session
      if (activeSession && activeSession.id) {
        filters.value.session_id = activeSession.id
      }

      // Initialize fee heads with 'fixed' as default type
      feeHeads.value.forEach(head => {
        selectedWaiverTypes.value[head.id] = 'fixed'
        waiverAmounts.value[head.id] = ''
      })
    }
  } catch (error) {
    showErrorAlert('Error', 'Failed to load dropdown data')
  }
}

const searchStudent = async () => {
  // Validation
  if (filters.value.id_card_number) {
    // ID card search - only ID card required
    if (!filters.value.id_card_number) {
      validationError.value = 'ID Card Number is required'
      return
    }
  } else {
    // Roll search - all fields required
    if (!filters.value.roll || !filters.value.session_id || !filters.value.version_id || 
        !filters.value.class_id || !filters.value.section_id) {
      validationError.value = 'All Red Star fields are required'
      return
    }
  }

  validationError.value = ''
  loading.value = true
  studentNotFound.value = false
  selectedStudent.value = null
  isEditMode.value = false

  try {
    const params = {}
    
    if (filters.value.id_card_number) params.id_card_number = filters.value.id_card_number
    if (filters.value.roll) params.roll = filters.value.roll
    if (filters.value.session_id) params.session_id = filters.value.session_id
    if (filters.value.version_id) params.version_id = filters.value.version_id
    if (filters.value.class_id) params.class_id = filters.value.class_id
    if (filters.value.section_id) params.section_id = filters.value.section_id
    
    const response = await axios.get('/api/wavers/get-students-by-class-section', { params })
    
    if (response.data.success && response.data.data.length > 0) {
      const student = response.data.data[0]
      
      // Fetch student image if needed
      if (student.student_id && (!student.student?.student_image_url || student.student?.student_image_url === null)) {
        try {
          const profileResponse = await axios.get(`/api/student-profiles/${student.student_id}`)
          if (profileResponse.data.success) {
            const profileData = profileResponse.data.data
            student.student = {
              ...student.student,
              student_image_url: profileData.student_image 
                ? `/assets/images/student_images/${profileData.student_image}`
                : '/assets/images/default-avatar.png'
            }
          }
        } catch (error) {
          console.log('Failed to fetch profile image')
        }
      }

      selectedStudent.value = {
        id: student.id,
        student_id: student.student_id,
        name: student.student?.name || student.student?.student_name || 'No Name',
        roll: student.class_roll,
        id_card_number: student.student?.id_card_number || student.id_card_number,
        student_image_url: student.student?.student_image_url,
        mobile_number: student.student?.mobile_number,
        session_id: student.session_id,
        version_id: student.version_id,
        class_id: student.class_id,
        section_id: student.section_id
      }

      // Check for existing waivers
      await checkExistingWaivers(student.id)
      
    } else {
      studentNotFound.value = true
    }
  } catch (error) {
    showErrorAlert('Error', error.response?.data?.message || 'Failed to search student')
  } finally {
    loading.value = false
  }
}

const checkExistingWaivers = async (classWiseStudentDataId) => {
  try {
    const response = await axios.get(`/api/wavers/student/${classWiseStudentDataId}/edit-form`)
    
    if (response.data.success) {
      isEditMode.value = true
      const wavers = response.data.data.wavers
      
      // Reset selections
      selectedMonths.value = []
      existingWavers.value = {}
      
      // First, reset all fee heads to default 'fixed' type
      feeHeads.value.forEach(head => {
        selectedWaiverTypes.value[head.id] = 'fixed'
        waiverAmounts.value[head.id] = ''
      })
      
      // Load existing waiver data
      for (const [monthId, monthData] of Object.entries(wavers)) {
        if (!selectedMonths.value.includes(parseInt(monthId))) {
          selectedMonths.value.push(parseInt(monthId))
        }
        
        for (const [headId, waverData] of Object.entries(monthData.fee_heads)) {
          const headIdInt = parseInt(headId)
          
          if (!existingWavers.value[monthId]) {
            existingWavers.value[monthId] = {}
          }
          existingWavers.value[monthId][headId] = waverData.id
          
          // Override with existing waiver type and amount
          selectedWaiverTypes.value[headIdInt] = waverData.waver_type
          waiverAmounts.value[headIdInt] = parseFloat(waverData.waver_amount)
        }
      }
    }
  } catch (error) {
    // No existing waivers - create mode with default 'fixed' type
    isEditMode.value = false
    
    // Reset to default 'fixed' type for all fee heads
    feeHeads.value.forEach(head => {
      selectedWaiverTypes.value[head.id] = 'fixed'
      waiverAmounts.value[head.id] = ''
    })
  }
}

const saveWaivers = async () => {
  if (!canSave.value) {
    validationError.value = 'Please select at least one fee head with valid waiver amount'
    return
  }

  isSaving.value = true
  showLoadingAlert('Please wait', 'Saving waivers...')

  try {
    const validFeeHeads = feeHeads.value.filter(head => isFeeHeadSelected(head.id))
    const allFeeHeads = feeHeads.value
    
    let successCount = 0
    let failCount = 0
    
    // Delete deselected month waivers
    if (isEditMode.value) {
      const existingMonthIds = Object.keys(existingWavers.value).map(id => parseInt(id))
      const deselectedMonthIds = existingMonthIds.filter(
        monthId => !selectedMonths.value.includes(monthId)
      )
      
      for (const monthId of deselectedMonthIds) {
        const monthWaivers = existingWavers.value[monthId]
        for (const headId in monthWaivers) {
          const waverId = monthWaivers[headId]
          try {
            await axios.delete(`/api/wavers/${waverId}`)
          } catch (error) {
            console.error(`Failed to delete waiver ${waverId}`)
          }
        }
        delete existingWavers.value[monthId]
      }
      
      // Delete waivers for fee heads with empty/zero amount (but still in selected months)
      for (const monthId of selectedMonths.value) {
        for (const head of allFeeHeads) {
          const amount = parseFloat(waiverAmounts.value[head.id] || 0)
          const existingWaverId = existingWavers.value[monthId]?.[head.id]
          
          // If amount is 0 or empty but waiver exists, delete it
          if ((!amount || amount <= 0) && existingWaverId) {
            try {
              await axios.delete(`/api/wavers/${existingWaverId}`)
              console.log(`Deleted waiver ${existingWaverId} for empty amount`)
              
              // Remove from tracking
              if (existingWavers.value[monthId]) {
                delete existingWavers.value[monthId][head.id]
              }
            } catch (error) {
              console.error(`Failed to delete waiver ${existingWaverId}`)
            }
          }
        }
      }
    }
    
    // Save waivers
    for (const head of validFeeHeads) {
      const type = selectedWaiverTypes.value[head.id]
      const amount = parseFloat(waiverAmounts.value[head.id])
      
      if (!type || !amount || amount <= 0) continue

      for (const monthId of selectedMonths.value) {
        try {
          const waiverData = {
            class_wise_student_id: selectedStudent.value.id,
            session_id: selectedStudent.value.session_id,
            version_id: selectedStudent.value.version_id,
            class_id: selectedStudent.value.class_id,
            section_id: selectedStudent.value.section_id,
            month_id: monthId,
            head_id: head.id,
            roll: selectedStudent.value.roll,
            waver_type: type,
            waver_amount: amount,
          }

          const existingWaverId = existingWavers.value[monthId]?.[head.id]

          if (existingWaverId) {
            await axios.put(`/api/wavers/${existingWaverId}`, waiverData)
          } else {
            await axios.post('/api/wavers', waiverData)
          }
          
          successCount++
          await new Promise(resolve => setTimeout(resolve, 50))
          
        } catch (error) {
          failCount++
          console.error(`Error for month ${monthId}, head ${head.id}:`, error)
        }
      }
    }

    closeAlert()
    
    if (failCount === 0) {
      showSuccessAlert(
        'Success!',
        `${isEditMode.value ? 'Updated' : 'Created'} ${successCount} waiver(s) successfully`
      )
      
      // Reset form after success
      setTimeout(() => {
        resetAll()
      }, 1500)
    } else {
      showSuccessAlert(
        'Partial Success',
        `${successCount} waiver(s) saved, ${failCount} failed`
      )
    }

    router.push({ name: 'wavers.index', query: { created: 'true' } })

  } catch (error) {
    closeAlert()
    showErrorAlert('Error', error.response?.data?.message || 'Failed to save waivers')
  } finally {
    isSaving.value = false
  }
}

const toggleAllMonths = () => {
  if (selectAllMonths.value) {
    selectedMonths.value = months.value.map(month => month.id)
  } else {
    selectedMonths.value = []
  }
}

const toggleMonthSelection = (monthId) => {
  const index = selectedMonths.value.indexOf(monthId)
  if (index === -1) {
    selectedMonths.value.push(monthId)
  } else {
    selectedMonths.value.splice(index, 1)
  }
}

const getSelectedFeeHeadsCount = () => {
  return feeHeads.value.filter(head => isFeeHeadSelected(head.id)).length
}

const isFeeHeadSelected = (headId) => {
  const type = selectedWaiverTypes.value[headId]
  const amount = waiverAmounts.value[headId]
  
  // Changed: type can be 'fixed' or 'percentage', not null
  return type && (type === 'fixed' || type === 'percentage') && 
         amount && parseFloat(amount) > 0 &&
         (type !== 'percentage' || parseFloat(amount) <= 100)
}

const clearWaiverAmount = (headId) => {
  waiverAmounts.value[headId] = ''
}

const resetWaverForm = () => {
  selectedMonths.value = []
  // Reset to default 'fixed' type instead of null
  feeHeads.value.forEach(head => {
    selectedWaiverTypes.value[head.id] = 'fixed'
    waiverAmounts.value[head.id] = ''
  })
  selectAllMonths.value = false
}

const resetAll = () => {
  filters.value = {
    session_id: filters.value.session_id, // Keep session
    version_id: '',
    class_id: '',
    section_id: '',
    roll: '',
    id_card_number: ''
  }
  selectedStudent.value = null
  studentNotFound.value = false
  existingWavers.value = {}
  isEditMode.value = false
  resetWaverForm()
}

const clearFilters = () => {
  resetAll()
  validationError.value = ''
}

const onRollChange = () => {
  validationError.value = ''
}

const onIdCardChange = () => {
  validationError.value = ''
}

const handleImageError = (event) => {
  event.target.style.display = 'none'
}

const getSessionName = () => {
  if (!selectedStudent.value?.session_id) return 'N/A'
  const session = sessions.value.find(s => s.id == selectedStudent.value.session_id)
  return session ? session.session_name : 'N/A'
}

const getClassName = () => {
  if (!selectedStudent.value?.class_id) return 'N/A'
  const classItem = classes.value.find(c => c.id == selectedStudent.value.class_id)
  return classItem ? classItem.class_name : 'N/A'
}

const getVersionName = () => {
  if (!selectedStudent.value?.version_id) return 'N/A'
  const version = versions.value.find(v => v.id == selectedStudent.value.version_id)
  return version ? version.version_name : 'N/A'
}

const getSectionName = () => {
  if (!selectedStudent.value?.section_id) return 'N/A'
  const section = sections.value.find(s => s.id == selectedStudent.value.section_id)
  return section ? section.section_name : 'N/A'
}

// Lifecycle
onMounted(() => {
  fetchDropdownData()
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

input[type="radio"]:checked {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
  background-size: 1em 1em;
}

input[type="checkbox"]:checked {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
  background-size: 1em 1em;
}
</style>