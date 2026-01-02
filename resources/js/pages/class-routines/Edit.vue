<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-1 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-2">
      <div>
        <h1 class="text-xl font-bold text-gray-800">Edit Class Routine</h1>
      </div>
      <router-link 
        to="/class-routines"
        class="inline-flex items-center gap-1 px-3 py-1.5 border bg-blue-600 hover:bg-blue-700 rounded-lg text-gray-100 text-sm font-medium transition-colors"
      >
        <i class="fas fa-arrow-left text-xs"></i>
        Back to List
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="bg-white rounded-xl shadow border border-gray-100 p-8 text-center">
      <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
      <p class="text-gray-600">Loading routine data...</p>
    </div>

    <!-- Form -->
    <div v-else class="bg-white rounded-xl shadow border border-gray-100 p-3">
      <!-- Class Selection -->
      <div class="mb-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
          <div class="p-1.5 bg-blue-100 rounded-lg">
            <i class="fas fa-graduation-cap text-blue-600 text-sm"></i>
          </div>
          Select Class Information
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-2">
          <!-- Session -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
              Session <span class="text-red-500">*</span>
            </label>
            <v-select
              v-model="form.session_id"
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

          <!-- Version -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-book text-gray-400 text-xs"></i>
              Version <span class="text-red-500">*</span>
            </label>
            <v-select
              v-model="form.version_id"
              :options="versions"
              label="version_name"
              :reduce="version => version.id"
              placeholder="Select Version"
              :clearable="true"
              class="v-select-custom text-sm"
            />
          </div>

          <!-- Shift -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-clock text-gray-400 text-xs"></i>
              Shift <span class="text-red-500">*</span>
            </label>
            <v-select
              v-model="form.shift_id"
              :options="shifts"
              label="shift_name"
              :reduce="shift => shift.id"
              placeholder="Select Shift"
              :clearable="true"
              class="v-select-custom text-sm"
            />
          </div>

          <!-- Class -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-graduation-cap text-gray-400 text-xs"></i>
              Class <span class="text-red-500">*</span>
            </label>
            <v-select
              v-model="form.class_id"
              :options="classes"
              label="class_name"
              :reduce="classItem => classItem.id"
              placeholder="Select Class"
              :clearable="true"
              class="v-select-custom text-sm"
            />
          </div>

          <!-- Section -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-layer-group text-gray-400 text-xs"></i>
              Section <span class="text-red-500">*</span>
            </label>
            <v-select
              v-model="form.section_id"
              :options="sections"
              label="section_name"
              :reduce="section => section.id"
              placeholder="Select Section"
              :clearable="true"
              class="v-select-custom text-sm"
            />
          </div>

          <!-- Number of Periods -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-hashtag text-gray-400 text-xs"></i>
              Periods <span class="text-red-500">*</span>
            </label>
            <input
              v-model.number="form.number_of_periods"
              type="number"
              min="1"
              max="10"
              placeholder="1"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

            <!-- Off Days -->
          <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
                <i class="fas fa-calendar-times text-gray-400 text-xs"></i>
                Off Days
            </label>
            <v-select
                v-model="form.off_days"
                :options="allDaysOptions"
                label="label"
                :reduce="day => day.value"
                placeholder="Select Off Days"
                :clearable="true"
                multiple
                class="v-select-custom text-sm"
            >
                <template #option="{ label }">
                <div class="flex items-center gap-1 text-sm">
                    <i class="fas fa-calendar-day text-red-500 text-xs"></i>
                    {{ label }}
                </div>
                </template>
                <template #selected-option="{ label }">
                <div class="flex items-center gap-1 text-xs">
                    <i class="fas fa-calendar-day text-red-500"></i>
                    {{ label }}
                </div>
                </template>
            </v-select>
          </div>
        </div>
      </div>

      <!-- Routine Table -->
      <div class="mb-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <div class="p-1.5 bg-green-100 rounded-lg">
                <i class="fas fa-calendar-week text-green-600 text-sm"></i>
            </div>
            Class Routine Schedule
            <span v-if="form.off_days && form.off_days.length > 0" class="text-xs text-gray-500">
                (Excluding: {{ form.off_days.join(', ') }})
            </span>
        </h3>

        <div class="table-container overflow-x-auto border border-gray-200 rounded-lg">
          <table class="routine-table border-collapse">
            <thead>
              <tr class="bg-gray-50">
                <th class="border border-gray-200 px-3 py-2 text-left text-xs font-semibold text-gray-700 day-column">
                  Day
                </th>
                <th 
                  v-for="period in displayPeriods" 
                  :key="period"
                  class="border border-gray-200 px-3 py-2 text-center text-xs font-semibold text-gray-700 period-column"
                >
                  <div class="flex flex-col items-center gap-1">
                    <span class="text-sm font-bold">{{ period }}</span>
                    <input
                      v-model="periodTimes[period]"
                      type="text"
                      placeholder="10:30 am - 11:30 am"
                      class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center focus:ring-1 focus:ring-blue-500"
                    />
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="day in activeDays" :key="day" class="hover:bg-gray-50">
                <td class="border border-gray-200 px-3 py-2 font-medium text-sm text-gray-700 bg-gray-50 day-column">
                  {{ day }}
                </td>
                <td 
                  v-for="period in displayPeriods" 
                  :key="`${day}-${period}`"
                  class="border border-gray-200 p-2 period-column"
                >
                  <div class="space-y-1.5">
                    <!-- Subject -->
                    <v-select
                      v-model="routineData[day][period].subject_id"
                      :options="subjects"
                      label="subject_name"
                      :reduce="subject => subject.id"
                      placeholder="Select Subject"
                      :clearable="true"
                      class="v-select-small"
                    >
                      <template #option="{ subject_name, subject_code }">
                        <div class="flex items-center gap-1 text-xs">
                          <i class="fas fa-book text-blue-500"></i>
                          <span>{{ subject_name }}</span>
                          <span v-if="subject_code" class="text-gray-500">({{ subject_code }})</span>
                        </div>
                      </template>
                      <template #selected-option="{ subject_name }">
                        <div class="flex items-center gap-1 text-xs">
                          <i class="fas fa-book text-blue-500"></i>
                          <span>{{ subject_name }}</span>
                        </div>
                      </template>
                    </v-select>

                    <!-- Teacher -->
                    <v-select
                      v-model="routineData[day][period].teacher_id"
                      :options="teachers"
                      :reduce="teacher => teacher.id"
                      placeholder="Select Teacher"
                      :clearable="true"
                      class="v-select-small"
                    >
                      <template #option="teacher">
                        <div class="flex items-center gap-1 text-xs">
                          <i class="fas fa-user text-green-500"></i>
                          <span>{{ teacher.first_name }} {{ teacher.last_name }}</span>
                        </div>
                      </template>
                      <template #selected-option="teacher">
                        <div class="flex items-center gap-1 text-xs">
                          <i class="fas fa-user text-green-500"></i>
                          <span>{{ teacher.first_name }} {{ teacher.last_name }}</span>
                        </div>
                      </template>
                      <template #no-options>
                        <div class="text-xs text-gray-500 p-2">No teachers available</div>
                      </template>
                    </v-select>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Validation Error -->
      <div v-if="validationError" class="mb-4 p-2 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center gap-1 text-red-700 text-xs">
          <i class="fas fa-exclamation-circle text-xs"></i>
          <span>{{ validationError }}</span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pt-3 border-t border-gray-200">
        <div class="text-xs text-gray-600">
          <i class="fas fa-info-circle text-blue-500"></i>
          Fill in all periods for each day to update the routine
        </div>
        
        <div class="flex items-center gap-2">
          <button 
            @click="updateRoutine"
            :disabled="!canSave"
            class="px-4 py-2 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-1 shadow disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i v-if="isSaving" class="fas fa-spinner fa-spin text-xs"></i>
            <i v-else class="fas fa-save text-xs"></i>
            {{ isSaving ? 'Updating...' : 'Update Routine' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()

// Data
const validationError = ref('')
const isSaving = ref(false)
const isLoading = ref(true)

const days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']

const allDaysOptions = [
  { label: 'Saturday', value: 'Saturday' },
  { label: 'Sunday', value: 'Sunday' },
  { label: 'Monday', value: 'Monday' },
  { label: 'Tuesday', value: 'Tuesday' },
  { label: 'Wednesday', value: 'Wednesday' },
  { label: 'Thursday', value: 'Thursday' },
  { label: 'Friday', value: 'Friday' }
]

// Form
const form = reactive({
  session_id: null,
  version_id: null,
  shift_id: null,
  class_id: null,
  section_id: null,
  number_of_periods: null,
  off_days: []
})

// Dropdown data
const sessions = ref([])
const versions = ref([])
const shifts = ref([])
const classes = ref([])
const sections = ref([])
const subjects = ref([])
const teachers = ref([])

// Period times
const periodTimes = ref({})

// Computed value for display periods (default to 1 if null)
const displayPeriods = computed(() => form.number_of_periods || 1)

const activeDays = computed(() => {
  if (!form.off_days || form.off_days.length === 0) {
    return days
  }
  return days.filter(day => !form.off_days.includes(day))
})

// Routine data structure - Initialize immediately with all days
const routineData = reactive(
  days.reduce((acc, day) => {
    acc[day] = {
      1: { subject_id: null, teacher_id: null }
    }
    return acc
  }, {})
)

// Computed
const canSave = computed(() => {
  return form.session_id && form.version_id && form.shift_id && 
         form.class_id && form.section_id && displayPeriods.value >= 1
})

// Methods
const fetchDropdownData = async () => {
  try {
    const response = await axios.get('/api/class-routines/dropdown-data')
    
    if (response.data.success) {
      const data = response.data.data
      
      sessions.value = data.sessions || []
      versions.value = data.versions || []
      shifts.value = data.shifts || []
      classes.value = data.classes || []
      sections.value = data.sections || []
      subjects.value = data.subjects || []
      
      teachers.value = (data.teachers || []).filter(teacher => teacher.is_teachers === 1)
    }
  } catch (error) {
    console.error('Error fetching dropdown data:', error)
    showErrorAlert('Error', 'Failed to load dropdown data')
  }
}

const fetchRoutineData = async () => {
  try {
    const routineId = route.params.id
    const response = await axios.get(`/api/class-routines/${routineId}`)
    
    if (response.data.success) {
      const routine = response.data.data
      
      // Set form data
      form.session_id = routine.session_id
      form.version_id = routine.version_id
      form.shift_id = routine.shift_id
      form.class_id = routine.class_id
      form.section_id = routine.section_id
      form.number_of_periods = parseInt(routine.number_of_periods) 
      form.off_days = routine.off_day ? routine.off_day.split(',') : []
      
      // Wait for next tick to ensure form.number_of_periods is set
      await new Promise(resolve => setTimeout(resolve, 10))
      
      // Initialize routine data structure for all periods
      generatePeriodColumns()
      
      // Populate routine details
      if (routine.details && routine.details.length > 0) {
        routine.details.forEach(detail => {
          const day = detail.day_name
          const period = parseInt(detail.period_number) // Convert string to number
          
          if (routineData[day] && routineData[day][period]) {
            routineData[day][period].subject_id = detail.subject_id
            routineData[day][period].teacher_id = detail.teacher_id
          }
          
          // Set period time - each period can have its own time
          periodTimes.value[period] = detail.time || ''
        })
      }
    }
  } catch (error) {
    console.error('Error fetching routine:', error)
    showErrorAlert('Error', 'Failed to load routine data')
    router.push('/class-routines')
  } finally {
    isLoading.value = false
  }
}

const generatePeriodColumns = () => {
  const periods = form.number_of_periods || 1
  
  // Initialize period times
  for (let i = 1; i <= periods; i++) {
    if (!periodTimes.value[i]) {
      periodTimes.value[i] = ''
    }
  }
  
  // Initialize routine data for each day and period
  days.forEach(day => {
    if (!routineData[day]) {
      routineData[day] = {}
    }
    for (let i = 1; i <= periods; i++) {
      if (!routineData[day][i]) {
        routineData[day][i] = {
          subject_id: null,
          teacher_id: null
        }
      }
    }
  })
}

// Watch for changes in number_of_periods
watch(() => form.number_of_periods, (newValue) => {
  if (newValue && newValue >= 1) {
    generatePeriodColumns()
  }
})

const updateRoutine = async () => {
  if (!canSave.value) {
    validationError.value = 'Please fill all required fields'
    return
  }

  // Prepare routine details
  const routine_details = []
  
  activeDays.value.forEach(day => {  
     for (let period = 1; period <= displayPeriods.value; period++) {
      const data = routineData[day]?.[period]
      
      // Only add if subject is selected
      if (data?.subject_id) {
        routine_details.push({
          subject_id: data.subject_id,
          teacher_id: data.teacher_id,
          period_number: period,
          day_name: day,
          time: periodTimes.value[period] || ''
        })
      }
    }
  })

  if (routine_details.length === 0) {
    validationError.value = 'Please add at least one subject to the routine'
    return
  }

  isSaving.value = true
  validationError.value = ''
  showLoadingAlert('Updating class routine...')

  try {
    const routineId = route.params.id
    const payload = {
      session_id: form.session_id,
      version_id: form.version_id,
      shift_id: form.shift_id,
      class_id: form.class_id,
      section_id: form.section_id,
      number_of_periods: displayPeriods.value,
      off_day: form.off_days.join(','),
      routine_details: routine_details
    }

    const response = await axios.put(`/api/class-routines/${routineId}`, payload)

    closeAlert()

    if (response.data.success) {
      await showSuccessAlert('Success!', 'Class routine updated successfully')
      router.push('/class-routines')
    }
  } catch (error) {
    closeAlert()
    console.error('Error updating routine:', error)
    
    if (error.response?.data?.message) {
      showErrorAlert('Error', error.response.data.message)
    } else if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      showErrorAlert('Validation Error', errors.join(', '))
    } else {
      showErrorAlert('Error', 'Failed to update routine. Please try again.')
    }
  } finally {
    isSaving.value = false
  }
}

// Lifecycle
onMounted(async () => {
  await fetchDropdownData()
  await fetchRoutineData()
})
</script>

<style scoped>
.v-select-custom {
  --vs-border-color: #d1d5db;
  --vs-border-radius: 0.5rem;
  --vs-font-size: 0.875rem;
}

.v-select-custom :deep(.vs__dropdown-toggle) {
  border: 1px solid var(--vs-border-color);
  border-radius: var(--vs-border-radius);
  padding: 0.5rem;
  min-height: 38px;
}

.v-select-small {
  --vs-border-color: #d1d5db;
  --vs-border-radius: 0.375rem;
  --vs-font-size: 0.75rem;
}

.v-select-small :deep(.vs__dropdown-toggle) {
  border: 1px solid var(--vs-border-color);
  border-radius: var(--vs-border-radius);
  padding: 0.25rem 0.5rem;
  min-height: 32px;
}

.v-select-small :deep(.vs__selected) {
  font-size: 0.75rem;
  padding: 0;
  margin: 0;
}

.v-select-small :deep(.vs__search) {
  font-size: 0.75rem;
  padding: 2px 0;
  margin: 0;
}

.v-select-small :deep(.vs__actions) {
  padding: 2px 4px;
}

.v-select-small :deep(.vs__clear) {
  display: block;
}

.fa-spinner.fa-spin {
  animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Table layout for perfect width up to 5 periods */
.table-container {
  max-width: 100%;
}

.routine-table {
  width: 100%;
  min-width: fit-content;
}

/* Day column fixed width */
.day-column {
  width: 120px;
  min-width: 120px;
  max-width: 120px;
}

/* Period columns - perfect fit for 5 periods */
.period-column {
  width: 200px;
  min-width: 200px;
  max-width: 200px;
}

/* Custom scrollbar for table */
.table-container::-webkit-scrollbar {
  height: 8px;
}

.table-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Responsive adjustments */
@media (max-width: 1280px) {
  .period-column {
    width: 180px;
    min-width: 180px;
    max-width: 180px;
  }
}

@media (max-width: 768px) {
  .day-column {
    width: 100px;
    min-width: 100px;
    max-width: 100px;
  }
  
  .period-column {
    width: 160px;
    min-width: 160px;
    max-width: 160px;
  }
}
</style>