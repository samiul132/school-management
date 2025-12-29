<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Edit Staff #{{ staffId }}</h1>
          <p class="text-gray-600 mt-2">Update staff information</p>
        </div>

        <div class="mb-6">
          <router-link
            :to="{ name: 'staffs.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Staffs
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !form.first_name" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Main Form Section -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column - Staff Information -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Personal Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-user text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Personal Information</h3>
              <p class="text-gray-600 text-sm">Update staff's basic details</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- First Name -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                First Name <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                v-model="form.first_name"
                placeholder="Enter first name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                :class="{ 'border-red-500': errors.first_name }"
              >
              <p v-if="errors.first_name" class="text-red-500 text-sm">{{ errors.first_name[0] }}</p>
            </div>

            <!-- Last Name -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Last Name <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                v-model="form.last_name"
                placeholder="Enter last name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                :class="{ 'border-red-500': errors.last_name }"
              >
              <p v-if="errors.last_name" class="text-red-500 text-sm">{{ errors.last_name[0] }}</p>
            </div>

            <!-- Is Teacher Checkbox -->
            <div class="space-y-2 md:col-span-2">
              <label
                for="is_teachers"
                class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-200 cursor-pointer hover:bg-blue-100 transition"
              >
                <input 
                  type="checkbox" 
                  id="is_teachers"
                  v-model="form.is_teachers"
                  :true-value="1"
                  :false-value="0"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                >

                <span class="text-sm font-medium text-gray-700 flex items-center">
                  <i class="fas fa-chalkboard-teacher text-blue-600 mr-2"></i>
                  This staff member is a teacher
                </span>
              </label>
            </div>


            <!-- Father's Name -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Father's Name</label>
              <input 
                type="text" 
                v-model="form.fathers_name"
                placeholder="Enter father's name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
              >
            </div>

            <!-- Mother's Name -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Mother's Name</label>
              <input 
                type="text" 
                v-model="form.mothers_name"
                placeholder="Enter mother's name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
              >
            </div>

            <!-- Date of Birth -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
              <input 
                type="date" 
                v-model="form.date_of_birth"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
              >
            </div>

            <!-- Marital Status -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Marital Status</label>
              <select 
                v-model="form.maritial_status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
              >
                <option value="">Select status...</option>
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="divorced">Divorced</option>
                <option value="widowed">Widowed</option>
              </select>
            </div>

            <!-- NID -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">NID</label>
              <input 
                type="text" 
                v-model="form.nid"
                placeholder="Enter NID number"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
              >
            </div>

            <!-- Qualification -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Qualification</label>
              <input 
                type="text" 
                v-model="form.qualification"
                placeholder="Enter qualification"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
              >
            </div>

            <!-- Address -->
            <div class="space-y-2 md:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Address</label>
              <textarea 
                v-model="form.address"
                rows="3"
                placeholder="Enter full address"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500 resize-none"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Contact Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-green-500 flex items-center justify-center">
              <i class="fas fa-phone text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Contact Information</h3>
              <p class="text-gray-600 text-sm">Update contact details</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Phone -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Phone</label>
              <input 
                type="text" 
                v-model="form.phone"
                placeholder="Enter phone number"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
              >
            </div>

            <!-- Email -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input 
                type="email" 
                v-model="form.email"
                placeholder="Enter email address"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
              >
            </div>
          </div>
        </div>

        <!-- Employment Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-briefcase text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Employment Information</h3>
              <p class="text-gray-600 text-sm">Update job details</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Designation -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Designation <span class="text-red-500">*</span>
              </label>
              <v-select
                  v-model="form.designation"
                  :options="designations"
                  :filterable="true"
                  :searchable="true"
                  label="name"
                  :reduce="designation => designation.id"
                  placeholder="Search or select designation..."
                  :class="{ 'border-red-500': errors.designation }"
                >
                <template #option="option">
                  <div class="font-medium text-gray-800">
                    <span class="text-blue-600 font-semibold mr-2 text-xs">#{{ option.id }}</span>
                    {{ option.name }}
                    <div 
                      v-if="option.description" 
                      class="text-xs text-gray-500 block mt-1 line-clamp-2"
                    >
                      {{ removeHTMLTags(option.description) }}
                    </div>
                  </div>
                </template>
                <template #no-options="{ search }">
                  <div class="p-2 text-gray-500 text-sm">
                    <p v-if="search">No Designation found for "{{ search }}"</p>
                    <p v-else>Type to search Designation</p>
                  </div>
                </template>
              </v-select>
              <p v-if="errors.designation" class="text-red-500 text-sm">{{ errors.designation[0] }}</p>
            </div>

            <!-- Joining Date -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Joining Date</label>
              <input 
                type="date" 
                v-model="form.joining_date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
              >
            </div>

            <!-- Basic Salary -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Basic Salary (৳) <span class="text-red-500">*</span>
              </label>
              <input 
                type="number" 
                v-model="form.basic_salary"
                step="0.01"
                min="0"
                placeholder="0.00"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                :class="{ 'border-red-500': errors.basic_salary }"
              >
              <p v-if="errors.basic_salary" class="text-red-500 text-sm">{{ errors.basic_salary[0] }}</p>
            </div>

            <!-- Working Hour -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Working Hour <span class="text-red-500">*</span>
              </label>
              <input 
                type="number" 
                v-model="form.working_hour"
                min="0"
                max="24"
                placeholder="8"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                :class="{ 'border-red-500': errors.working_hour }"
              >
              <p v-if="errors.working_hour" class="text-red-500 text-sm">{{ errors.working_hour[0] }}</p>
            </div>

            <!-- Subsidiaries ID -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Subsidiary <span class="text-red-500">*</span>
              </label>
              <v-select
                v-model="form.subsidiaries_id"
                :options="subsidiaries"
                :filterable="true"
                :searchable="true"
                label="name"
                :reduce="subsidiary => subsidiary.id"
                placeholder="Search or select subsidiary..."
                :class="{ 'border-red-500': errors.subsidiaries_id }"
              >
                <template #option="option">
                  <div class="font-medium text-gray-800">
                    <span class="text-blue-600 font-semibold mr-2 text-xs">#{{ option.id }}</span>
                    {{ option.name }}
                    <div 
                      v-if="option.description" 
                      class="text-xs text-gray-500 block mt-1 line-clamp-2"
                    >
                      {{ removeHTMLTags(option.description) }}
                    </div>
                  </div>
                </template>
                <template #no-options="{ search }">
                  <div class="p-2 text-gray-500 text-sm">
                    <p v-if="search">No Subsidiaries found for "{{ search }}"</p>
                    <p v-else>Type to search Subsidiaries</p>
                  </div>
                </template>
              </v-select>
              <p v-if="errors.subsidiaries_id" class="text-red-500 text-sm">{{ errors.subsidiaries_id[0] }}</p>
            </div>
            
          </div>
        </div>

        <!-- Image Upload Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-indigo-500 flex items-center justify-center">
              <i class="fas fa-image text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Staff Photo</h3>
              <p class="text-gray-600 text-sm">Update staff photo</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Current Photo -->
            <div v-if="currentPhoto" class="space-y-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Current Photo</label>
              <div class="w-32 h-32 rounded-lg overflow-hidden border border-gray-200">
                <img :src="currentPhoto" :alt="form.first_name" class="w-full h-full object-cover">
              </div>
              <button 
                @click="removeCurrentPhoto"
                type="button"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
              >
                <i class="fas fa-trash text-xs"></i>
                Remove Current Photo
              </button>
            </div>

            <!-- Dropify Image Upload -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ currentPhoto ? 'Upload New Photo' : 'Staff Photo' }}
              </label>
              <input 
                type="file" 
                ref="imageInput"
                class="dropify" 
                data-height="200"
                data-max-file-size="2M"
                data-allowed-file-extensions="jpg jpeg png gif"
                @change="handleImageUpload"
                accept="image/*"
              />
              <p class="text-xs text-gray-500 mt-1">
                Recommended size: 500x500px. Max file size: 2MB
              </p>
              <p v-if="errors.image" class="text-red-500 text-sm">{{ errors.image[0] }}</p>
            </div>

            <!-- New Photo Preview -->
            <div v-if="form.imagePreview && form.imagePreview !== currentPhoto" class="mt-4">
              <p class="text-sm font-medium text-gray-700 mb-2">New Photo Preview:</p>
              <div class="w-32 h-32 rounded-lg overflow-hidden border border-gray-200">
                <img :src="form.imagePreview" alt="Preview" class="w-full h-full object-cover">
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex justify-end"> 
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
              <router-link
                :to="{ name: 'staffs.index' }"
                class="inline-flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg font-semibold transition-colors"
              >
                <i class="fas fa-times"></i>
                Cancel
              </router-link>

              <button 
                @click="submitForm"
                :disabled="loading"
                class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-3 py-2 rounded-lg font-semibold transition-colors flex justify-center items-center gap-2 w-full sm:w-auto"
              >
                <i class="fas fa-save"></i>
                {{ loading ? 'Updating...' : 'Update Staff' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Status & Actions -->
      <div class="space-y-8">
        <!-- Status Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center">
              <i class="fas fa-flag text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Staff Status</h3>
              <p class="text-gray-600 text-sm">Update staff status</p>
            </div>
          </div>

          <div class="space-y-4">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Status <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.status }"
              >
                <option value="" disabled>Select status...</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <p v-if="errors.status" class="text-red-500 text-sm">{{ errors.status[0] }}</p>
            </div>

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
                {{ form.status === 'active' ? 'Active' : form.status === 'inactive' ? 'Inactive' : 'Not selected' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Salary Summary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center">
              <i class="fas fa-money-bill-wave text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Salary Summary</h3>
              <p class="text-gray-600 text-sm">Salary information</p>
            </div>
          </div>

          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="text-center p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-600">Basic Salary</p>
                <p class="text-2xl font-bold text-blue-700">৳{{ form.basic_salary || 0 }}</p>
              </div>
              <div class="text-center p-3 bg-green-50 rounded-lg">
                <p class="text-sm text-green-600">Working Hour</p>
                <p class="text-2xl font-bold text-green-700">{{ form.working_hour || 0 }}h</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Staff Info -->
        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-info-circle text-gray-500 text-xl"></i>
            <h4 class="text-lg font-semibold text-gray-800">Staff Information</h4>
          </div>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Created:</span>
              <span class="text-gray-800 font-medium">{{ formatDate(staffData.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Last Updated:</span>
              <span class="text-gray-800 font-medium">{{ formatDate(staffData.updated_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Staff ID:</span>
              <span class="text-gray-800 font-mono">{{ staffId }}</span>
            </div>
          </div>
        </div>

        <!-- Quick Tips -->
        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-lightbulb text-blue-500 text-xl"></i>
            <h4 class="text-lg font-semibold text-blue-800">Quick Tips</h4>
          </div>
          <ul class="space-y-2 text-blue-700 text-sm">
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Update all necessary information</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Upload new photo to replace the current one</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Set status to inactive to hide staff</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import Dropify from 'dropify'
import 'dropify/dist/css/dropify.css'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const imageInput = ref(null)
const dropify = ref(null)
const subsidiaries = ref([])
const designations = ref([])

const staffId = ref(route.params.id)
const currentPhoto = ref('')
const staffData = ref({
  created_at: '',
  updated_at: ''
})

const form = reactive({
  first_name: '',
  last_name: '',
  fathers_name: '',
  mothers_name: '',
  is_teachers: 0,
  designation: null, 
  maritial_status: '',
  date_of_birth: '',
  joining_date: '',
  phone: '',
  email: '',
  qualification: '',
  address: '',
  nid: '',
  subsidiaries_id: null, // Changed to null
  basic_salary: '',
  working_hour: '',
  status: 'active',
  image: null,
  imagePreview: null,
  remove_photo: false
})

const errors = ref({})

// Helper function to remove HTML tags
const removeHTMLTags = (str) => {
  if (!str) return ''
  return str.replace(/<[^>]*>/g, '')
}

// Helper function to format date for input fields (YYYY-MM-DD)
const formatDateForInput = (dateString) => {
  if (!dateString) return ''
  
  // If it's already in YYYY-MM-DD format, return as is
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
    return dateString
  }
  
  // Try to parse and format the date
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return ''
    
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    
    return `${year}-${month}-${day}`
  } catch (error) {
    console.error('Date formatting error:', error)
    return ''
  }
}

const initDropify = () => {
  if (imageInput.value) {
    dropify.value = new Dropify(imageInput.value, {
      messages: {
        'default': 'Drag and drop a photo or click to browse',
        'replace': 'Drag and drop or click to replace',
        'remove': 'Remove',
        'error': 'Ooops, something wrong happened.'
      },
      error: {
        'fileSize': 'The file size is too big ({{ value }} max).',
        'fileExtension': 'The file format is not allowed ({{ value }} only).'
      }
    })

    imageInput.value.addEventListener('change', handleImageUpload)
  }
}

const fetchSubsidiaries = async () => {
  try {
    const response = await axios.get('/api/subsidiaries')
    subsidiaries.value = response.data.data || []
  } catch (error) {
    console.error('Failed to fetch subsidiaries:', error)
    showErrorAlert('Error', 'Failed to load subsidiaries')
  }
}

const fetchDesignations = async () => {
  try {
    const response = await axios.get('/api/designations')
    designations.value = response.data.data || []
  } catch (error) {
    console.error('Failed to fetch designations:', error)
    showErrorAlert('Error', 'Failed to load designations')
  }
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      showErrorAlert('Error', 'File size must be less than 2MB')
      if (dropify.value) {
        dropify.value.destroy()
        initDropify()
      }
      return
    }

    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']
    if (!allowedTypes.includes(file.type)) {
      showErrorAlert('Error', 'Only JPG, PNG, and GIF images are allowed')
      if (dropify.value) {
        dropify.value.destroy()
        initDropify()
      }
      return
    }

    form.image = file
    form.imagePreview = URL.createObjectURL(file)
    form.remove_photo = false
  } else {
    form.image = null
    form.imagePreview = currentPhoto.value
  }
}
const removeCurrentPhoto = () => {
  form.remove_photo = true
  form.image = null
  form.imagePreview = null
  currentPhoto.value = ''
  
  if (dropify.value) {
    dropify.value.destroy()
    initDropify()
  }
  
  showSuccessAlert('Success', 'Current photo will be removed on update.')
}

const fetchStaff = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/staffs/${staffId.value}`)
    
    let staff
    if (response.data.success) {
      staff = response.data.data
    } else {
      staff = response.data
    }

    if (!staff) {
      throw new Error('Staff not found')
    }

    form.first_name = staff.first_name
    form.last_name = staff.last_name
    form.fathers_name = staff.fathers_name || ''
    form.mothers_name = staff.mothers_name || ''
    form.is_teachers = staff.is_teachers || 0
    
    form.designation = typeof staff.designation === 'object' && staff.designation !== null 
      ? staff.designation.id 
      : parseInt(staff.designation)
    
    form.maritial_status = staff.maritial_status || ''
    form.date_of_birth = formatDateForInput(staff.date_of_birth) || ''
    form.joining_date = formatDateForInput(staff.joining_date) || ''
    form.phone = staff.phone || ''
    form.email = staff.email || ''
    form.qualification = staff.qualification || ''
    form.address = staff.address || ''
    form.nid = staff.nid || ''
    form.subsidiaries_id = staff.subsidiaries_id ? parseInt(staff.subsidiaries_id) : null
    form.basic_salary = staff.basic_salary
    form.working_hour = staff.working_hour
    form.status = staff.status

    staffData.value = {
      created_at: staff.created_at,
      updated_at: staff.updated_at
    }

    if (staff.photo) {
      currentPhoto.value = getImageUrl(staff.photo)
      form.imagePreview = currentPhoto.value
    }
  } catch (error) {
    console.error('Error fetching staff:', error)
    showErrorAlert('Error', 'Failed to load staff data')
    router.push({ name: 'staffs.index' })
  } finally {
    loading.value = false
  }
}

const getImageUrl = (imagePath) => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http')) return imagePath
  if (!imagePath.includes('/')) return `/assets/images/staffs/${imagePath}`
  return imagePath
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

const submitForm = async () => {
  loading.value = true
  errors.value = {}

  try {
    if (!form.first_name.trim()) {
      errors.value.first_name = ['The first name field is required.']
      loading.value = false
      return
    }

    if (!form.last_name.trim()) {
      errors.value.last_name = ['The last name field is required.']
      loading.value = false
      return
    }

    if (!form.designation) {
      errors.value.designation = ['The designation field is required.']
      loading.value = false
      return
    }

    if (!form.basic_salary || parseFloat(form.basic_salary) < 0) {
      errors.value.basic_salary = ['The basic salary field is required and must be at least 0.']
      loading.value = false
      return
    }

    if (!form.working_hour || parseInt(form.working_hour) < 0) {
      errors.value.working_hour = ['The working hour field is required and must be at least 0.']
      loading.value = false
      return
    }

    showLoadingAlert('Updating staff...')

    const formData = new FormData()
    formData.append('first_name', form.first_name)
    formData.append('last_name', form.last_name)
    formData.append('fathers_name', form.fathers_name || '')
    formData.append('mothers_name', form.mothers_name || '')
    formData.append('is_teachers', form.is_teachers)
    formData.append('designation', form.designation)
    formData.append('maritial_status', form.maritial_status || '')
    formData.append('date_of_birth', form.date_of_birth || '')
    formData.append('joining_date', form.joining_date || '')
    formData.append('phone', form.phone || '')
    formData.append('email', form.email || '')
    formData.append('qualification', form.qualification || '')
    formData.append('address', form.address || '')
    formData.append('nid', form.nid || '')
    formData.append('subsidiaries_id', form.subsidiaries_id || '')
    formData.append('basic_salary', form.basic_salary)
    formData.append('working_hour', form.working_hour)
    formData.append('status', form.status)
    formData.append('_method', 'PUT')
    
    if (form.image) {
      formData.append('image', form.image)
    }
    
    if (form.remove_photo) {
      formData.append('remove_photo', '1')
    }

    const response = await axios.post(`/api/staffs/${staffId.value}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    closeAlert()
    
    if (response.data.success) {
      showSuccessAlert('Success', 'Staff updated successfully')
      router.push({ 
        name: 'staffs.index',
        query: { updated: 'true' }
      })
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to update staff')
    }
    
  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else if (error.response && error.response.data) {
      showErrorAlert('Error', error.response.data.message || 'Failed to update staff. Please try again.')
    } else {
      showErrorAlert('Error', 'Failed to update staff. Please try again.')
    }
  }
}

onMounted(async () => {
  await fetchSubsidiaries()
  await fetchDesignations()
  await fetchStaff()
  nextTick(() => {
    initDropify()
  })
})

onUnmounted(() => {
  if (dropify.value) {
    dropify.value.destroy()
  }
  
  if (form.imagePreview && form.imagePreview !== currentPhoto.value) {
    URL.revokeObjectURL(form.imagePreview)
  }
})
</script>

<style scoped>
:deep(.dropify-wrapper .dropify-message p) {
  font-size: 0.85rem;
  line-height: 1.3;
}

:deep(.dropify-wrapper .dropify-message span.file-icon) {
  font-size: 1.5rem;
}
</style>
        