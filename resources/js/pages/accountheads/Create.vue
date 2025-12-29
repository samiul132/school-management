<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Create New Account Head
          </h1>
          <p class="text-gray-600">
            Add a new account head to your system
          </p>
        </div>
        <div class="flex items-center gap-3 mb-2">
          <router-link 
            :to="{ name: 'accountheads.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-semibold"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Account Heads
          </router-link>
        </div>
      </div>
    </div>

    <!-- Form Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Form -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <!-- Form Header -->
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
              Account Head Information
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              Fill in the basic information about the account head
            </p>
          </div>

          <!-- Form Content -->
          <div class="p-6">
            <div class="space-y-6">
              <!-- Head Name -->
              <div>
                <label for="head_name" class="block text-sm font-medium text-gray-700 mb-2">
                  Head Name <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  id="head_name"
                  v-model="form.head_name"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                  :class="{ 'border-red-500': errors.head_name }"
                  placeholder="Enter account head name"
                />
                <p v-if="errors.head_name" class="mt-1 text-sm text-red-600">
                  {{ errors.head_name[0] }}
                </p>
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                  Description
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500 resize-y"
                  :class="{ 'border-red-500': errors.description }"
                  placeholder="Enter description about this account head..."
                ></textarea>
                <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                  {{ errors.description[0] }}
                </p>
              </div>
            </div>
          </div>
          
          <!-- Form Actions -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-end gap-3">

              <!-- Cancel Button -->
              <router-link
                :to="{ name: 'accountheads.index' }"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                      bg-gray-600 hover:bg-gray-700 text-white
                      w-full sm:w-auto text-center"
              >
                <i class="fas fa-times"></i>
                Cancel
              </router-link>

              <!-- Create Button -->
              <button
                @click="submitForm"
                :disabled="loading"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                      bg-blue-600 hover:bg-blue-700 text-white
                      disabled:opacity-50 disabled:cursor-not-allowed
                      w-full sm:w-auto text-center"
              >
                <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-save"></i>
                {{ loading ? 'Creating...' : 'Create Account Head' }}
              </button>

            </div>
          </div>

        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <!-- Status Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-flag text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Account Head Status</h3>
              <p class="text-gray-600 text-sm">Set account head status</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Status -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Status <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.status }"
              >
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <p v-if="errors.status" class="text-red-500 text-sm">{{ errors.status[0] }}</p>
            </div>

            <!-- Status Badge Preview -->
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-600 mb-2">Status Preview:</p>
              <span 
                :class="[
                  'px-3 py-1 rounded-full text-xs font-medium',
                  form.status === 'active' 
                    ? 'bg-green-100 text-green-800' 
                    : form.status === 'inactive'
                    ? 'bg-red-100 text-red-800'
                    : 'bg-gray-100 text-gray-800'
                ]"
              >
                {{ form.status || 'Not selected' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Form Tips -->
        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
              <i class="fas fa-info-circle text-blue-600"></i>
            </div>
            <div>
              <h4 class="font-semibold text-blue-800 mb-2">
                Quick Tips
              </h4>
              <ul class="text-sm text-blue-700 space-y-1">
                <li>• Account head name is required</li>
                <li>• Use descriptive names for easy identification</li>
                <li>• Set appropriate status (active/inactive)</li>
                <li>• Add clear description for future reference</li>
                <li>• Inactive heads won't appear in dropdowns</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Examples -->
        <div class="bg-green-50 rounded-2xl border border-green-200 p-6 mt-6">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
              <i class="fas fa-lightbulb text-green-600"></i>
            </div>
            <div>
              <h4 class="font-semibold text-green-800 mb-2">
                Examples
              </h4>
              <ul class="text-sm text-green-700 space-y-1">
                <li>• <strong>Head Name:</strong> Office Rent</li>
                <li>• <strong>Head Name:</strong> Salary Expense</li>
                <li>• <strong>Head Name:</strong> Utility Bills</li>
                <li>• <strong>Head Name:</strong> Marketing Cost</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()

const form = reactive({
  head_name: '',
  description: '',
  status: 'active'
})

const loading = ref(false)
const errors = ref({})

const resetForm = () => {
  Object.assign(form, { head_name: '', description: '', status: 'active' })
  errors.value = {}
}

const validateForm = () => {
  const newErrors = {}
  if (!form.head_name.trim()) newErrors.head_name = ['Account head name is required']
  if (!form.status) newErrors.status = ['Status is required']
  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

const submitForm = async () => {
  if (!validateForm()) {
    showErrorAlert('Validation Error', 'Please fix the errors in the form')
    return
  }

  loading.value = true
  errors.value = {}

  try {
    showLoadingAlert('Creating account head...')
    const response = await axios.post('/api/accountheads', form)
    closeAlert()
    resetForm()
    router.push({ name: 'accountheads.index', query: { created: 'true' } })

  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors')
    } else {
      console.error('Error creating account head:', error)
      showErrorAlert('Error', 'Failed to create account head. Please try again.')
    }
  }
}
</script>