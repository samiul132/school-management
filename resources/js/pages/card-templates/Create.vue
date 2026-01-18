<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-4">
      <h1 class="text-3xl font-bold text-gray-800">Add ID Card Template</h1>
      <p class="text-gray-600">Select templates for teacher and student ID cards</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
      <form @submit.prevent="handleSubmit">
        
        <!-- Grid Layout: Teacher | Student -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
          
          <!-- Teacher Template Section -->
          <div class="border-2 border-blue-200 rounded-lg p-4">
            <h2 class="text-xl font-bold text-blue-700 mb-4 flex items-center gap-2">
              <i class="fas fa-chalkboard-teacher"></i>
              Teacher Template
            </h2>
            
            <!-- Selected Template Preview -->
            <div v-if="selectedTeacherTemplate" class="mb-4 p-3 bg-blue-50 rounded-lg">
              <p class="text-sm text-gray-700 mb-2 flex items-center justify-between">
                <span>Selected Template:</span>
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
                  alt="Teacher Template"
                  class="max-w-[200px] h-auto rounded border-2 border-blue-500"
                >
              </div>
            </div>

            <!-- Template Options -->
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
            
            <!-- Selected Template Preview -->
            <div v-if="selectedStudentTemplate" class="mb-4 p-3 bg-green-50 rounded-lg">
              <p class="text-sm text-gray-700 mb-2 flex items-center justify-between">
                <span>Selected Template:</span>
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
                  alt="Student Template"
                  class="max-w-[200px] h-auto rounded border-2 border-green-500"
                >
              </div>
            </div>

            <!-- Template Options -->
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
            :disabled="submitting || (!selectedTeacherTemplate && !selectedStudentTemplate)"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
          >
            <i class="fas fa-save" v-if="!submitting"></i>
            <i class="fas fa-spinner fa-spin" v-else></i>
            {{ submitting ? 'Saving...' : 'Save Templates' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const submitting = ref(false)

// Template Lists
const teacherTemplates = ref([])
const studentTemplates = ref([])

// Selected Templates
const selectedTeacherTemplate = ref(null)
const selectedStudentTemplate = ref(null)

// Load available templates from backend
const loadTemplates = async () => {
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

// Select teacher template
const selectTeacherTemplate = (template) => {
  selectedTeacherTemplate.value = template
}

// Select student template
const selectStudentTemplate = (template) => {
  selectedStudentTemplate.value = template
}

// Clear selections
const clearTeacherSelection = () => {
  selectedTeacherTemplate.value = null
}

const clearStudentSelection = () => {
  selectedStudentTemplate.value = null
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
const handleSubmit = async () => {
  if (!selectedTeacherTemplate.value && !selectedStudentTemplate.value) {
    showErrorAlert('Error', 'Please select at least one template')
    return
  }

  try {
    submitting.value = true
    showLoadingAlert('Saving templates...')

    const formData = new FormData()
    formData.append('school_id', '1')

    // Send filename instead of file
    if (selectedTeacherTemplate.value) {
      const filename = selectedTeacherTemplate.value.split('/').pop()
      formData.append('teacher_template_file', filename)
    }

    if (selectedStudentTemplate.value) {
      const filename = selectedStudentTemplate.value.split('/').pop()
      formData.append('student_template_file', filename)
    }

    await axios.post('/api/card-templates', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    closeAlert()
    await showSuccessAlert('Success!', 'Templates saved successfully')
    router.push({ name: 'card-templates.index', query: { created: 'true' } })

  } catch (error) {
    closeAlert()
    console.error('Error saving templates:', error)
    
    if (error.response?.data?.message) {
      showErrorAlert('Error', error.response.data.message)
    } else {
      showErrorAlert('Error', 'Failed to save templates')
    }
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadTemplates()
})
</script>