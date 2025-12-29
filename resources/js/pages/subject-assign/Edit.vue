<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-1 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-2">
      <div>
        <h1 class="text-xl font-bold text-gray-800">
          Edit Subject Assignment
        </h1>
      </div>
      <router-link 
        :to="{ name: 'subject-assign.index' }"
        class="inline-flex items-center gap-1 px-3 py-1.5 border bg-blue-600 hover:bg-blue-700 rounded-lg text-gray-100 text-sm font-medium transition-colors"
      >
        <i class="fas fa-arrow-left text-xs"></i>
        Back to List
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="bg-white rounded-xl shadow border border-gray-100 p-8">
      <div class="flex flex-col items-center justify-center gap-3">
        <i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i>
        <p class="text-sm text-gray-600">Loading assignment data...</p>
      </div>
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
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
          <!-- Session -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
              Session 
              <span class="text-red-500">*</span>
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
              Version 
              <span class="text-red-500">*</span>
            </label>
            <v-select
              v-model="form.version_id"
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

          <!-- Shift -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-clock text-gray-400 text-xs"></i>
              Shift 
              <span class="text-red-500">*</span>
            </label>
            <v-select
              v-model="form.shift_id"
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

          <!-- Class -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-graduation-cap text-gray-400 text-xs"></i>
              Class 
              <span class="text-red-500">*</span>
            </label>
            <v-select
              v-model="form.class_id"
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

          <!-- Section -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-layer-group text-gray-400 text-xs"></i>
              Section 
              <span class="text-red-500">*</span>
            </label>
            <v-select
              v-model="form.section_id"
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
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-3">
        <!-- Subjects Selection -->
        <div class="mb-4 pb-4 border-b border-gray-200">
          <div class="flex items-center justify-start mb-3">
            <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
              <div class="p-1.5 bg-green-100 rounded-lg">
                <i class="fas fa-book text-green-600 text-sm"></i>
              </div>
              Assign Subjects
              <span class="text-xs text-red-500">*</span>
            </h3>
          </div>

          <!-- Subject Rows -->
          <div class="space-y-2">
            <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
              <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
              Subject 
              <span class="text-red-500">*</span>
            </label>
            <div 
              v-for="(row, index) in subjectRows" 
              :key="row.id"
              class="flex items-center gap-2"
            >
              <div class="flex-1">
                <v-select
                  v-model="row.subject_id"
                  :options="availableSubjects(row.subject_id)"
                  label="subject_name"
                  :reduce="subject => subject.id"
                  placeholder="Select subject..."
                  :clearable="true"
                  class="v-select-custom text-sm"
                >
                  <template #option="{ subject_name, subject_code }">
                    <div class="flex items-center gap-2 text-sm py-1">
                      <i class="fas fa-book text-blue-500 text-xs"></i>
                      <span>{{ subject_name }}</span>
                      <span v-if="subject_code" class="text-xs text-gray-500">({{ subject_code }})</span>
                    </div>
                  </template>
                </v-select>
              </div>
              
              <button
                v-if="subjectRows.length > 1"
                @click="removeSubjectRow(index)"
                class="px-2 py-2 text-sm bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors"
              >
                <i class="fas fa-trash text-xs"></i>
              </button>
            </div>
          </div>
          
          <div v-if="selectedSubjects.length === 0" class="mt-2 text-xs text-red-600">
            Please add at least one subject
          </div>
          <div class="flex items-center justify-between mt-3">
            <button
              @click="addSubjectRow"
              class="px-2 py-1 text-xs bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-1"
            >
              <i class="fas fa-plus text-xs"></i>
              Add More
            </button>
          </div>
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
          All students in the selected class/section will be assigned these subjects
        </div>
        
        <div class="flex items-center gap-2">
          <button 
            @click="updateAssignment"
            :disabled="isSaving"
            class="px-4 py-2 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-1 shadow disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i v-if="isSaving" class="fas fa-spinner fa-spin text-xs"></i>
            <i v-else class="fas fa-save text-xs"></i>
            {{ isSaving ? 'Updating...' : 'Update Assignment' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()

// Data
const isLoading = ref(true)
const validationError = ref('')
const isSaving = ref(false)
let rowIdCounter = 0
const assignmentId = route.params.id

// Form
const form = reactive({
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
const subjects = ref([])

// Subject rows
const subjectRows = ref([
  { id: rowIdCounter++, subject_id: null }
])

// Computed
const selectedSubjects = computed(() => {
  return subjectRows.value
    .map(row => row.subject_id)
    .filter(id => id !== null)
})

const availableSubjects = (currentSubjectId) => {
  const selected = selectedSubjects.value.filter(id => id !== currentSubjectId)
  return subjects.value.filter(subject => !selected.includes(subject.id))
}

const canSave = computed(() => {
  return form.session_id && form.version_id && form.shift_id && 
         form.class_id && form.section_id && selectedSubjects.value.length > 0
})

// Methods
const fetchDropdownData = async () => {
  try {
    // Fetch sessions
    const sessionsRes = await axios.get('/api/session-managements')
    if (Array.isArray(sessionsRes.data)) {
      sessions.value = sessionsRes.data.sort((a, b) => a.session_name.localeCompare(b.session_name))
    } else if (sessionsRes.data.success && sessionsRes.data.data) {
      const sessionData = sessionsRes.data.data
      const data = Array.isArray(sessionData) ? sessionData : (sessionData.data || [])
      sessions.value = data.sort((a, b) => a.session_name.localeCompare(b.session_name))
    }

    // Fetch versions
    const versionsRes = await axios.get('/api/version-managements')
    if (Array.isArray(versionsRes.data)) {
      versions.value = versionsRes.data.sort((a, b) => a.version_name.localeCompare(b.version_name))
    } else if (versionsRes.data.success && versionsRes.data.data) {
      const versionData = versionsRes.data.data
      const data = Array.isArray(versionData) ? versionData : (versionData.data || [])
      versions.value = data.sort((a, b) => a.version_name.localeCompare(b.version_name))
    }

    // Fetch shifts
    const shiftsRes = await axios.get('/api/shift-managements')
    if (Array.isArray(shiftsRes.data)) {
      shifts.value = shiftsRes.data.sort((a, b) => a.shift_name.localeCompare(b.shift_name))
    } else if (shiftsRes.data.success && shiftsRes.data.data) {
      const shiftData = shiftsRes.data.data
      const data = Array.isArray(shiftData) ? shiftData : (shiftData.data || [])
      shifts.value = data.sort((a, b) => a.shift_name.localeCompare(b.shift_name))
    }

    // Fetch classes
    const classesRes = await axios.get('/api/class-managements')
    if (Array.isArray(classesRes.data)) {
      classes.value = classesRes.data.sort((a, b) => a.class_name.localeCompare(b.class_name))
    } else if (classesRes.data.success && classesRes.data.data) {
      const classData = classesRes.data.data
      const data = Array.isArray(classData) ? classData : (classData.data || [])
      classes.value = data.sort((a, b) => a.class_name.localeCompare(b.class_name))
    }

    // Fetch sections
    const sectionsRes = await axios.get('/api/section-managements')
    if (Array.isArray(sectionsRes.data)) {
      sections.value = sectionsRes.data.sort((a, b) => a.section_name.localeCompare(b.section_name))
    } else if (sectionsRes.data.success && sectionsRes.data.data) {
      const sectionData = sectionsRes.data.data
      const data = Array.isArray(sectionData) ? sectionData : (sectionData.data || [])
      sections.value = data.sort((a, b) => a.section_name.localeCompare(b.section_name))
    }

    // Fetch subjects
    const subjectsRes = await axios.get('/api/subjects')
    if (Array.isArray(subjectsRes.data)) {
      subjects.value = subjectsRes.data.sort((a, b) => a.subject_name.localeCompare(b.subject_name))
    } else if (subjectsRes.data.success && subjectsRes.data.data) {
      const subjectData = subjectsRes.data.data
      const data = Array.isArray(subjectData) ? subjectData : (subjectData.data || [])
      subjects.value = data.sort((a, b) => a.subject_name.localeCompare(b.subject_name))
    }
  } catch (error) {
    console.error('Error fetching dropdown data:', error)
    showErrorAlert('Error', 'Failed to load dropdown data')
  }
}

const fetchAssignment = async () => {
  try {
    const response = await axios.get(`/api/subject-assigns/${assignmentId}`)
    
    if (response.data.success) {
      const assignment = response.data.data
      
      // Populate form
      form.session_id = assignment.session_id
      form.version_id = assignment.version_id
      form.shift_id = assignment.shift_id
      form.class_id = assignment.class_id
      form.section_id = assignment.section_id
      
      // Populate subjects
      if (assignment.details && assignment.details.length > 0) {
        subjectRows.value = assignment.details.map(detail => ({
          id: rowIdCounter++,
          subject_id: detail.subject_id
        }))
      }
    }
  } catch (error) {
    console.error('Error fetching assignment:', error)
    showErrorAlert('Error', 'Failed to load assignment data')
    router.push({ name: 'subject-assign.index' })
  }
}

const addSubjectRow = () => {
  subjectRows.value.push({
    id: rowIdCounter++,
    subject_id: null
  })
}

const removeSubjectRow = (index) => {
  if (subjectRows.value.length > 1) {
    subjectRows.value.splice(index, 1)
  }
}

const updateAssignment = async () => {
  if (!canSave.value) {
    validationError.value = 'Please fill all required fields and add at least one subject'
    return
  }

  isSaving.value = true
  validationError.value = ''
  showLoadingAlert('Updating subject assignment...')

  try {
    const data = {
      session_id: form.session_id,
      version_id: form.version_id,
      shift_id: form.shift_id,
      class_id: form.class_id,
      section_id: form.section_id,
      subjects: selectedSubjects.value
    }

    const response = await axios.put(`/api/subject-assigns/${assignmentId}`, data)

    closeAlert()

    if (response.data.success) {
      showSuccessAlert('Success!', 'Subject assignment updated successfully')
      
      setTimeout(() => {
        router.push({ name: 'subject-assign.index' })
      }, 1500)
    }
  } catch (error) {
    closeAlert()
    console.error('Error updating assignment:', error)
    
    if (error.response?.data?.message) {
      showErrorAlert('Error', error.response.data.message)
    } else if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      showErrorAlert('Validation Error', errors.join(', '))
    } else {
      showErrorAlert('Error', 'Failed to update assignment. Please try again.')
    }
  } finally {
    isSaving.value = false
  }
}

// Lifecycle
onMounted(async () => {
  await fetchDropdownData()
  await fetchAssignment()
  isLoading.value = false
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