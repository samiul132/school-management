<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-4">
      <h1 class="text-3xl font-bold text-gray-800">Edit ID Card Template</h1>
      <p class="text-gray-600">Update templates for teacher and student ID cards</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 text-center">
      <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
      <p class="mt-2 text-gray-600">Loading template...</p>
    </div>

    <!-- Form Container -->
    <div v-else class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
      <form @submit.prevent="handleSubmit">
        
        <!-- Grid Layout: Teacher | Student -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
          
          <!-- Teacher Template Section -->
          <div class="border-2 border-blue-200 rounded-lg p-4">
            <h2 class="text-xl font-bold text-blue-700 mb-4 flex items-center gap-2">
              <i class="fas fa-chalkboard-teacher"></i>
              Teacher Template
            </h2>
            
            <!-- Current Template -->
            <div v-if="currentTeacherImage && !teacherRemoved" class="mb-4 p-3 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-700 mb-2 flex items-center justify-between">
                <span>Current Template:</span>
                <button
                  type="button"
                  @click="removeCurrentTeacherTemplate"
                  class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition-colors"
                >
                  <i class="fas fa-trash mr-1"></i> Remove
                </button>
              </p>
              <div class="flex justify-center">
                <img 
                  :src="currentTeacherImage" 
                  alt="Current Teacher Template"
                  class="max-w-[200px] h-auto rounded border-2 border-gray-400"
                >
              </div>
            </div>

            <!-- Selected New Template -->
            <div v-if="selectedTeacherTemplate" class="mb-4 p-3 bg-blue-50 rounded-lg">
              <p class="text-sm text-gray-700 mb-2 flex items-center justify-between">
                <span class="font-semibold text-blue-700">New Selected Template:</span>
                <button
                  type="button"
                  @click="clearTeacherSelection"
                  class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition-colors"
                >
                  <i class="fas fa-times mr-1"></i> Clear
                </button>
              </p>
              <div class="flex justify-center">
                <img 
                  :src="selectedTeacherTemplate" 
                  alt="New Teacher Template"
                  class="max-w-[200px] h-auto rounded border-2 border-blue-500"
                >
              </div>
            </div>

            <!-- Template Options -->
            <p class="text-sm text-gray-600 mb-2">Choose a new template:</p>
            <div class="grid grid-cols-2 gap-3">
              <div 
                v-for="(template, index) in teacherTemplates"
                :key="index"
                @click="selectTeacherTemplate(template)"
                :class="[
                  'cursor-pointer rounded-lg border-2 p-2 transition-all',
                  selectedTeacherTemplate === template 
                    ? 'border-blue-600 bg-blue-50' 
                    : 'border-gray-300 hover:border-blue-400'
                ]"
              >
                <img 
                  :src="template" 
                  :alt="`Teacher Template ${index + 1}`"
                  class="w-full h-auto rounded"
                >
                <p class="text-xs text-center mt-1 text-gray-600">
                  Template {{ index + 1 }}
                </p>
              </div>
            </div>

            <!-- No Templates Found -->
            <div v-if="teacherTemplates.length === 0" class="text-center py-8 text-gray-500">
              <i class="fas fa-image text-4xl mb-2"></i>
              <p>No teacher templates found</p>
            </div>
          </div>

          <!-- Student Template Section -->
          <div class="border-2 border-green-200 rounded-lg p-4">
            <h2 class="text-xl font-bold text-green-700 mb-4 flex items-center gap-2">
              <i class="fas fa-user-graduate"></i>
              Student Template
            </h2>
            
            <!-- Current Template -->
            <div v-if="currentStudentImage && !studentRemoved" class="mb-4 p-3 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-700 mb-2 flex items-center justify-between">
                <span>Current Template:</span>
                <button
                  type="button"
                  @click="removeCurrentStudentTemplate"
                  class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition-colors"
                >
                  <i class="fas fa-trash mr-1"></i> Remove
                </button>
              </p>
              <div class="flex justify-center">
                <img 
                  :src="currentStudentImage" 
                  alt="Current Student Template"
                  class="max-w-[200px] h-auto rounded border-2 border-gray-400"
                >
              </div>
            </div>

            <!-- Selected New Template -->
            <div v-if="selectedStudentTemplate" class="mb-4 p-3 bg-green-50 rounded-lg">
              <p class="text-sm text-gray-700 mb-2 flex items-center justify-between">
                <span class="font-semibold text-green-700">New Selected Template:</span>
                <button
                  type="button"
                  @click="clearStudentSelection"
                  class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition-colors"
                >
                  <i class="fas fa-times mr-1"></i> Clear
                </button>
              </p>
              <div class="flex justify-center">
                <img 
                  :src="selectedStudentTemplate" 
                  alt="New Student Template"
                  class="max-w-[200px] h-auto rounded border-2 border-green-500"
                >
              </div>
            </div>

            <!-- Template Options -->
            <p class="text-sm text-gray-600 mb-2">Choose a new template:</p>
            <div class="grid grid-cols-2 gap-3">
              <div 
                v-for="(template, index) in studentTemplates"
                :key="index"
                @click="selectStudentTemplate(template)"
                :class="[
                  'cursor-pointer rounded-lg border-2 p-2 transition-all',
                  selectedStudentTemplate === template 
                    ? 'border-green-600 bg-green-50' 
                    : 'border-gray-300 hover:border-green-400'
                ]"
              >
                <img 
                  :src="template" 
                  :alt="`Student Template ${index + 1}`"
                  class="w-full h-auto rounded"
                >
                <p class="text-xs text-center mt-1 text-gray-600">
                  Template {{ index + 1 }}
                </p>
              </div>
            </div>

            <!-- No Templates Found -->
            <div v-if="studentTemplates.length === 0" class="text-center py-8 text-gray-500">
              <i class="fas fa-image text-4xl mb-2"></i>
              <p>No student templates found</p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t">
          <router-link 
            :to="{ name: 'card-templates.index' }"
            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Cancel
          </router-link>
          
          <button 
            type="submit"
            :disabled="submitting"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
          >
            <i class="fas fa-save" v-if="!submitting"></i>
            <i class="fas fa-spinner fa-spin" v-else></i>
            {{ submitting ? 'Updating...' : 'Update Templates' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const loading = ref(true)
const submitting = ref(false)

// Template ID from route
const templateId = route.params.id

// Current Images
const currentTeacherImage = ref(null)
const currentStudentImage = ref(null)

// Removal flags
const teacherRemoved = ref(false)
const studentRemoved = ref(false)

// Template Lists
const teacherTemplates = ref([])
const studentTemplates = ref([])

// Selected Templates
const selectedTeacherTemplate = ref(null)
const selectedStudentTemplate = ref(null)

// Load current template data
const loadTemplate = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/card-templates/${templateId}`)
    
    if (response.data.success) {
      currentTeacherImage.value = response.data.data.teacher_image_url
      currentStudentImage.value = response.data.data.student_image_url
    }
  } catch (error) {
    console.error('Error loading template:', error)
    showErrorAlert('Error', 'Failed to load template')
    router.push({ name: 'card-templates.index' })
  } finally {
    loading.value = false
  }
}

// Load available templates from backend
const loadAvailableTemplates = async () => {
  try {
    const response = await axios.get('/api/card-templates/available-templates')
    
    if (response.data.success) {
      teacherTemplates.value = response.data.data.teacher
      studentTemplates.value = response.data.data.student
    }
  } catch (error) {
    console.error('Error loading templates:', error)
    showErrorAlert('Error', 'Failed to load available templates')
  }
}

// Select teacher template (only if not already selected)
const selectTeacherTemplate = (template) => {
  if (!selectedTeacherTemplate.value) {
    selectedTeacherTemplate.value = template
  }
}

// Select student template (only if not already selected)
const selectStudentTemplate = (template) => {
  if (!selectedStudentTemplate.value) {
    selectedStudentTemplate.value = template
  }
}

// Clear selections
const clearTeacherSelection = () => {
  selectedTeacherTemplate.value = null
}

const clearStudentSelection = () => {
  selectedStudentTemplate.value = null
}

// Remove current templates
const removeCurrentTeacherTemplate = () => {
  teacherRemoved.value = true
}

const removeCurrentStudentTemplate = () => {
  studentRemoved.value = true
}

// Convert URL to File object
const urlToFile = async (url, filename) => {
  try {
    const response = await fetch(url)
    const blob = await response.blob()
    return new File([blob], filename, { type: blob.type })
  } catch (error) {
    console.error('Error converting URL to file:', error)
    return null
  }
}

// Handle form submit
// Handle form submit
const handleSubmit = async () => {
  if (!selectedTeacherTemplate.value && !selectedStudentTemplate.value && !teacherRemoved.value && !studentRemoved.value) {
    showErrorAlert('Error', 'Please make at least one change')
    return
  }

  try {
    submitting.value = true
    showLoadingAlert('Updating templates...')

    const formData = new FormData()
    formData.append('_method', 'PUT')
    formData.append('school_id', '1')

    // Handle teacher template
    if (teacherRemoved.value) {
      formData.append('remove_teacher', 'true')
    } else if (selectedTeacherTemplate.value) {
      // Extract filename from URL
      const filename = selectedTeacherTemplate.value.split('/').pop()
      formData.append('teacher_template_file', filename)
    }

    // Handle student template
    if (studentRemoved.value) {
      formData.append('remove_student', 'true')
    } else if (selectedStudentTemplate.value) {
      // Extract filename from URL
      const filename = selectedStudentTemplate.value.split('/').pop()
      formData.append('student_template_file', filename)
    }

    await axios.post(`/api/card-templates/${templateId}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    closeAlert()
    await showSuccessAlert('Success!', 'Templates updated successfully')
    router.push({ name: 'card-templates.index', query: { updated: 'true' } })

  } catch (error) {
    closeAlert()
    console.error('Error updating templates:', error)
    
    if (error.response?.data?.message) {
      showErrorAlert('Error', error.response.data.message)
    } else {
      showErrorAlert('Error', 'Failed to update templates')
    }
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadTemplate()
  loadAvailableTemplates()
})
</script>