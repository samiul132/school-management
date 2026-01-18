<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-4 flex flex-col md:flex-row justify-between md:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">ID Card Templates</h1>
        <p class="text-gray-600">Manage teacher and student ID card templates</p>
      </div>

      <router-link 
        :to="{ name: 'card-templates.create' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
          text-white px-4 py-2 rounded-lg font-semibold transition-colors w-full md:w-auto"
      >
        <i class="fas fa-plus"></i>
        Add Template
      </router-link>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
      
      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SL</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher Template</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Template</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(template, index) in templates" :key="template.id" class="hover:bg-gray-50">
              
              <!-- SL -->
              <td class="px-6 py-4 text-sm text-gray-800">{{ index + 1 }}</td>
              
              <!-- Actions -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <router-link 
                    :to="{ name: 'card-templates.edit', params: { id: template.id } }"
                    class="text-blue-600 hover:text-blue-800"
                    title="Edit"
                  >
                    <i class="fas fa-edit"></i>
                  </router-link>
                  
                  <button 
                    @click="deleteTemplate(template.id)"
                    class="text-red-600 hover:text-red-800"
                    title="Delete"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
              
              <!-- Teacher Template -->
              <td class="px-6 py-4">
                <div v-if="template.teacher_image_url" class="flex items-center gap-3">
                  <img 
                    :src="template.teacher_image_url" 
                    alt="Teacher Template"
                    class="w-20 h-28 object-cover rounded border"
                  >
                  <span class="text-xs text-green-600">
                    <i class="fas fa-check-circle"></i> Available
                  </span>
                </div>
                <span v-else class="text-xs text-gray-400">Not set</span>
              </td>
              
              <!-- Student Template -->
              <td class="px-6 py-4">
                <div v-if="template.student_image_url" class="flex items-center gap-3">
                  <img 
                    :src="template.student_image_url" 
                    alt="Student Template"
                    class="w-20 h-28 object-cover rounded border"
                  >
                  <span class="text-xs text-green-600">
                    <i class="fas fa-check-circle"></i> Available
                  </span>
                </div>
                <span v-else class="text-xs text-gray-400">Not set</span>
              </td>
              
              <!-- Created At -->
              <td class="px-6 py-4 text-sm text-gray-800">
                {{ formatDate(template.created_at) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading templates...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && templates.length === 0" class="p-8 text-center">
        <i class="fas fa-id-card text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No templates found.</p>
        <router-link 
          :to="{ name: 'card-templates.create' }"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Template
        </router-link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showConfirmDialog, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const templates = ref([])
const loading = ref(true)

const fetchTemplates = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/card-templates')
    templates.value = response.data.data
  } catch (error) {
    console.error('Error fetching templates:', error)
    showErrorAlert('Error', 'Failed to load templates')
  } finally {
    loading.value = false
  }
}

const deleteTemplate = async (id) => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'This will delete the template permanently.'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting template...')
      await axios.delete(`/api/card-templates/${id}`)
      templates.value = templates.value.filter(t => t.id !== id)
      closeAlert()
      await showSuccessAlert('Deleted!', 'Template has been deleted.')
    } catch (error) {
      closeAlert()
      console.error('Error deleting template:', error)
      showErrorAlert('Error', 'Failed to delete template')
    }
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

onMounted(() => {
  fetchTemplates()
  
  if (route.query.created === 'true') {
    showSuccessAlert('Success!', 'Template created successfully.')
    router.replace({ name: 'card-templates.index' })
  }
  
  if (route.query.updated === 'true') {
    showSuccessAlert('Success!', 'Template updated successfully.')
    router.replace({ name: 'card-templates.index' })
  }
})
</script>