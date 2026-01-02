<template>
  <AppLayout>
    <!-- Header with Create Button -->
    <div class="mb-3 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-4">
      <!-- Title Section -->
      <div>
        <h1 class="text-3xl font-bold text-gray-800">
          All Students
        </h1>
      </div>

      <!-- Button -->
      <router-link 
        :to="{ name: 'student-profiles.create' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
          text-white px-3 py-2 rounded-lg font-semibold transition-colors w-full md:w-auto"
      >
        <i class="fas fa-plus"></i>
        Add New Student
      </router-link>
    </div>

    <!-- Advanced Filters Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-2 mb-3">
      <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-2 mb-1">
        <div class="flex items-center gap-2">
          <i class="fas fa-filter text-blue-600 text-lg"></i>
          <h3 class="text-lg font-semibold text-gray-800">Advanced Filters</h3>
        </div>
        
        <div class="flex items-center gap-2">
          <!-- Apply Filter Button -->
          <button 
            @click="applyFilters"
            class="px-2 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-2 cursor-pointer"
          >
            <i class="fas fa-filter text-white-600 text-xs"></i>
            Apply Filter
          </button>

          <!-- Clear Filters Button -->
          <!-- <button 
            @click="clearFilters"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition-colors flex items-center gap-2"
          >
            <i class="fas fa-times"></i>
            Clear
          </button> -->
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-1">
        

        <!-- Session Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Session</label>
          <v-select
            v-model="tempSessionFilter"
            :options="availableSessions"
            label="session_name"
            :reduce="session => session.id"
            placeholder="Select Session"
            class="v-select-custom"
          >
            <template #option="{ session_name }">
              {{ session_name }}
            </template>
          </v-select>
        </div>

        <!-- Version Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
          <v-select
            v-model="tempVersionFilter"
            :options="availableVersions"
            label="version_name"
            :reduce="version => version.id"
            placeholder="Select Version"
            class="v-select-custom"
          >
            <template #option="{ version_name }">
              {{ version_name }}
            </template>
          </v-select>
        </div>

        <!-- Section Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
          <v-select
            v-model="tempSectionFilter"
            :options="availableSections"
            label="section_name"
            :reduce="section => section.id"
            placeholder="Select Section"
            class="v-select-custom"
          >
            <template #option="{ section_name }">
              {{ section_name }}
            </template>
          </v-select>
        </div>

        <!-- Shift Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
          <v-select
            v-model="tempShiftFilter"
            :options="availableShifts"
            label="shift_name"
            :reduce="shift => shift.id"
            placeholder="Select Shift"
            class="v-select-custom"
          >
            <template #option="{ shift_name }">
              {{ shift_name }}
            </template>
          </v-select>
        </div>

        <!-- Class Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
          <v-select
            v-model="tempClassFilter"
            :options="availableClasses"
            label="class_name"
            :reduce="classItem => classItem.id"
            placeholder="Select Class"
            class="v-select-custom"
          >
            <template #option="{ class_name }">
              {{ class_name }}
            </template>
          </v-select>
        </div>

        <!-- Class Roll Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Class Roll</label>
          <input 
            type="number" 
            v-model="tempClassRollFilter"
            placeholder="Enter Class Roll" 
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full [appearance:textfield]
                  [&::-webkit-outer-spin-button]:appearance-none
                  [&::-webkit-inner-spin-button]:appearance-none"
            min="1"
          >
        </div>

      </div>

      <!-- Active Filters Display -->
      <div v-if="hasActiveFilters" class="mt-4 pt-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <i class="fas fa-tags text-gray-500"></i>
            <span class="text-sm font-medium text-gray-700">Active Filters:</span>
          </div>
          <button 
            @click="clearFilters"
            class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1"
          >
            <i class="fas fa-times-circle"></i>
            Clear All
          </button>
        </div>
        
        <div class="flex flex-wrap gap-2 mt-2">
          <span 
            v-if="search"
            class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm"
          >
            Search: {{ search }}
            <button @click="search = ''" class="text-blue-800 hover:text-blue-900">
              <i class="fas fa-times text-xs"></i>
            </button>
          </span>
          
          <span 
            v-if="appliedFilters.class"
            class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm"
          >
            Class: {{ getClassNameById(appliedFilters.class) }}
            <button @click="clearFilter('class')" class="text-green-800 hover:text-green-900">
              <i class="fas fa-times text-xs"></i>
            </button>
          </span>
          
          <span 
            v-if="appliedFilters.session"
            class="inline-flex items-center gap-1 px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm"
          >
            Session: {{ getSessionNameById(appliedFilters.session) }}
            <button @click="clearFilter('session')" class="text-purple-800 hover:text-purple-900">
              <i class="fas fa-times text-xs"></i>
            </button>
          </span>
          
          <span 
            v-if="appliedFilters.version"
            class="inline-flex items-center gap-1 px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm"
          >
            Version: {{ getVersionNameById(appliedFilters.version) }}
            <button @click="clearFilter('version')" class="text-orange-800 hover:text-orange-900">
              <i class="fas fa-times text-xs"></i>
            </button>
          </span>
          
          
          <span 
            v-if="appliedFilters.classRoll"
            class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm"
          >
            Roll: {{ appliedFilters.classRoll }}
            <button @click="clearFilter('classRoll')" class="text-indigo-800 hover:text-indigo-900">
              <i class="fas fa-times text-xs"></i>
            </button>
          </span>
          
          <span 
            v-if="appliedFilters.section"
            class="inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm"
          >
            Section: {{ getSectionNameById(appliedFilters.section) }}
            <button @click="clearFilter('section')" class="text-teal-800 hover:text-teal-900">
              <i class="fas fa-times text-xs"></i>
            </button>
          </span>
          
          <span 
            v-if="appliedFilters.shift"
            class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm"
          >
            Shift: {{ getShiftNameById(appliedFilters.shift) }}
            <button @click="clearFilter('shift')" class="text-yellow-800 hover:text-yellow-900">
              <i class="fas fa-times text-xs"></i>
            </button>
          </span>
          
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <!-- Table Header with Pagination -->
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <!-- Items Per Page -->
        <div class="flex items-center gap-2 text-sm text-gray-700">
          <span>Show:</span>
          <select 
            v-model="itemsPerPage"
            @change="currentPage = 1"
            class="px-2 py-1 border border-gray-300 rounded bg-white text-gray-800"
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span>per page</span>
        </div>
        
        <!-- Search Box -->
        <div class="relative">
          <input 
            type="text" 
            v-model="search"
            @input="applySearch"
            placeholder="Search students..." 
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
          >
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1200px]">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">SL</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Image</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Student Info</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Academic Info</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Family Info</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Contact</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Date of Birth</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Gender</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Blood Group</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">ID Card</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(student, index) in paginatedStudents" :key="student.id" class="hover:bg-gray-50">
              <!-- SL -->
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">
                  {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                </div>
              </td>
              <!-- Student Image -->
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-gray-200">
                  <img 
                    v-if="student.student_image_url"
                    :src="student.student_image_url" 
                    :alt="student.student_name"
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-user text-gray-400 text-lg"></i>
                  </div>
                </div>
              </td>
              
              <!-- Student Info -->
              <td class="px-4 py-4">
                <div class="flex flex-col">
                  <div class="font-semibold text-gray-800 text-sm mb-1">
                    {{ student.student_name }}
                  </div>
                  <div v-if="student.id_card_number" class="text-xs text-gray-500">
                    ID: {{ student.id_card_number }}
                  </div>
                  <div v-if="student.birth_certificate_number" class="text-xs text-gray-500 mt-1">
                    BCN: {{ student.birth_certificate_number }}
                  </div>
                </div>
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <!-- Dropdown Button -->
                  <button 
                    @click="toggleDropdown(student.id)"
                    class="inline-flex items-center justify-center px-2 h-8 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== student.id,
                      'bg-gray-200 text-black': openDropdownId === student.id
                    }"
                  >
                    Action
                    <i 
                      class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                      :class="{
                        'rotate-180': openDropdownId === student.id,
                        'text-white': openDropdownId !== student.id,
                        'text-black': openDropdownId === student.id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === student.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- View Option -->
                      <router-link 
                        :to="{ name: 'student-profiles.show', params: { id: student.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-eye text-green-600 text-xs w-4"></i>
                        <span>View Details</span>
                      </router-link>

                      <!-- View Option -->
                      <router-link 
                        :to="{ name: 'student-profiles.idcardshow', params: { id: student.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-eye text-green-600 text-xs w-4"></i>
                        <span>View Id card</span>
                      </router-link>
                      
                      <!-- Edit Option -->
                      <router-link 
                        :to="{ name: 'student-profiles.edit', params: { id: student.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Student</span>
                      </router-link>
                      
                      <!-- Delete Option -->
                      <button 
                        @click="handleDeleteStudent(student.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete Student</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Academic Info -->
              <td class="px-4 py-4 whitespace-nowrap">
                <div v-if="student.class_wise_data && student.class_wise_data.length > 0" class="flex flex-col gap-1">
                  <div class="text-sm text-gray-800">
                    <i class="fas fa-graduation-cap text-gray-400 mr-2 text-xs"></i>
                    {{ getClassName(student) }}
                  </div>
                  <div class="text-xs text-gray-600">
                    <i class="fas fa-book text-gray-400 mr-2 text-xs"></i>
                    {{ getVersionName(student) }}
                  </div>
                  <div class="text-xs text-gray-600">
                    <i class="fas fa-calendar-alt text-gray-400 mr-2 text-xs"></i>
                    {{ getSessionName(student) }}
                  </div>
                  <div class="text-xs text-gray-600">
                    <i class="fas fa-hashtag text-gray-400 mr-2 text-xs"></i>
                    Roll: {{ getClassRoll(student) }}
                  </div>
                </div>
                <span v-else class="text-xs text-red-500 bg-red-50 px-2 py-1 rounded">
                  Not Assigned
                </span>
              </td>
              
              <!-- Family Info -->
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="flex flex-col gap-1">
                  <div class="text-sm text-gray-800">
                    <i class="fas fa-user-friends text-gray-400 mr-2 text-xs"></i>
                    {{ student.father_name || 'N/A' }}
                  </div>
                  <div class="text-xs text-gray-600">
                    <i class="fas fa-user text-gray-400 mr-2 text-xs"></i>
                    {{ student.mother_name || 'N/A' }}
                  </div>
                  <div v-if="student.local_guardian_name" class="text-xs text-gray-600">
                    <i class="fas fa-user-shield text-gray-400 mr-2 text-xs"></i>
                    {{ student.local_guardian_name }}
                  </div>
                </div>
              </td>
              
              <!-- Contact -->
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="flex flex-col gap-1">
                  <div v-if="student.mobile_number" class="text-sm text-gray-800">
                    <i class="fas fa-phone text-gray-400 mr-2 text-xs"></i>
                    {{ student.mobile_number }}
                  </div>
                  <div v-if="student.father_mobile_number" class="text-xs text-gray-600">
                    <i class="fas fa-user-tie text-gray-400 mr-2 text-xs"></i>
                    {{ student.father_mobile_number }}
                  </div>
                </div>
              </td>
              
              <!-- Date of Birth -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div v-if="student.date_of_birth">
                  {{ formatDate(student.date_of_birth) }}
                  <div class="text-xs text-gray-500">
                    Age: {{ calculateAge(student.date_of_birth) }}
                  </div>
                </div>
                <span v-else class="text-gray-400">N/A</span>
              </td>
              
              <!-- Gender -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <span 
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-medium',
                    student.gender === 'male' 
                      ? 'bg-blue-100 text-blue-800' 
                      : student.gender === 'female'
                      ? 'bg-pink-100 text-pink-800'
                      : 'bg-gray-100 text-gray-800'
                  ]"
                >
                  <i 
                    :class="[
                      'fas mr-1',
                      student.gender === 'male' ? 'fa-male' : 
                      student.gender === 'female' ? 'fa-female' : 'fa-user'
                    ]"
                  ></i>
                  {{ student.gender ? student.gender.charAt(0).toUpperCase() + student.gender.slice(1) : 'N/A' }}
                </span>
              </td>
              
              <!-- Blood Group -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <span 
                  v-if="student.blood_group"
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-medium',
                    student.blood_group.includes('+') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                  ]"
                >
                  {{ student.blood_group }}
                </span>
                <span v-else class="text-gray-400 text-sm">N/A</span>
              </td>
              
              <!-- ID Card -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex items-center gap-2">
                  <span v-if="student.id_card_number" class="font-mono bg-gray-100 px-2 py-1 rounded">
                    {{ student.id_card_number }}
                  </span>
                  <span v-else class="text-gray-400">No ID</span>
                </div>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(student.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(student.created_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading students...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && students.length === 0" class="p-8 text-center">
        <i class="fas fa-user-graduate text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No students found.</p>
        <router-link 
          :to="{ name: 'student-profiles.create' }"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Student
        </router-link>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && students.length > 0 && filteredStudents.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No students match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredStudents.length > 0" class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredStudents.length }} students
        </div>
        
        <div class="flex items-center gap-2">
          <!-- Previous Button -->
          <button 
            @click="previousPage"
            :disabled="currentPage === 1"
            class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <i class="fas fa-chevron-left"></i>
          </button>
          
          <!-- Page Numbers -->
          <div class="flex items-center gap-1">
            <button 
              v-for="page in visiblePages"
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                currentPage === page
                  ? 'bg-blue-600 text-white'
                  : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            
            <span v-if="hasMorePages" class="px-2 text-gray-500">...</span>
          </div>
          
          <!-- Next Button -->
          <button 
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { showSuccessAlert, showErrorAlert, showConfirmDialog, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const students = ref([])
const loading = ref(true)

// Filter variables - temporary for UI
const tempClassFilter = ref('')
const tempSessionFilter = ref('')
const tempVersionFilter = ref('')
const tempGenderFilter = ref('')
const tempClassRollFilter = ref('')
const tempSectionFilter = ref('')
const tempShiftFilter = ref('')
const tempBloodGroupFilter = ref('')

// Applied filters - actually used for filtering
const appliedFilters = ref({
  class: '',
  session: '',
  version: '',
  gender: '',
  classRoll: '',
  section: '',
  shift: '',
  bloodGroup: ''
})

// Search filter (auto-applies)
const search = ref('')

// Available filters data
const availableClasses = ref([])
const availableSessions = ref([])
const availableVersions = ref([])
const availableSections = ref([])
const availableShifts = ref([])

const currentPage = ref(1)
const itemsPerPage = ref(10)
const openDropdownId = ref(null)

// Options for select filters
const genderOptions = ref([
  { label: 'Male', value: 'male' },
  { label: 'Female', value: 'female' },
  { label: 'Other', value: 'other' }
])

const bloodGroupOptions = ref([
  { label: 'A+', value: 'A+' },
  { label: 'A-', value: 'A-' },
  { label: 'B+', value: 'B+' },
  { label: 'B-', value: 'B-' },
  { label: 'AB+', value: 'AB+' },
  { label: 'AB-', value: 'AB-' },
  { label: 'O+', value: 'O+' },
  { label: 'O-', value: 'O-' }
])

// Computed properties
const hasActiveFilters = computed(() => {
  return !!search.value || 
         Object.values(appliedFilters.value).some(filter => !!filter)
})

// Filtered students with advanced filtering
const filteredStudents = computed(() => {
  let filtered = students.value
  
  // Search filter (auto-applies)
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(student => {
      return (
        student.student_name?.toLowerCase().includes(searchTerm) ||
        student.id_card_number?.toLowerCase().includes(searchTerm) ||
        student.mobile_number?.toLowerCase().includes(searchTerm) ||
        student.father_name?.toLowerCase().includes(searchTerm) ||
        student.father_mobile_number?.toLowerCase().includes(searchTerm) ||
        student.mother_name?.toLowerCase().includes(searchTerm) ||
        student.mother_mobile_number?.toLowerCase().includes(searchTerm)
      )
    })
  }
  
  // Apply advanced filters only if they are set
  if (appliedFilters.value.class) {
    filtered = filtered.filter(student => {
      if (student.class_wise_data && student.class_wise_data.length > 0) {
        return student.class_wise_data.some(data => data.class_id == appliedFilters.value.class)
      }
      return false
    })
  }
  
  if (appliedFilters.value.session) {
    filtered = filtered.filter(student => {
      if (student.class_wise_data && student.class_wise_data.length > 0) {
        return student.class_wise_data.some(data => data.session_id == appliedFilters.value.session)
      }
      return false
    })
  }
  
  if (appliedFilters.value.version) {
    filtered = filtered.filter(student => {
      if (student.class_wise_data && student.class_wise_data.length > 0) {
        return student.class_wise_data.some(data => data.version_id == appliedFilters.value.version)
      }
      return false
    })
  }
  
  if (appliedFilters.value.gender) {
    filtered = filtered.filter(student => 
      student.gender === appliedFilters.value.gender
    )
  }
  
  if (appliedFilters.value.classRoll) {
    filtered = filtered.filter(student => {
      if (student.class_wise_data && student.class_wise_data.length > 0) {
        return student.class_wise_data.some(data => 
          data.class_roll && data.class_roll.toString() === appliedFilters.value.classRoll.toString()
        )
      }
      return false
    })
  }
  
  if (appliedFilters.value.section) {
    filtered = filtered.filter(student => {
      if (student.class_wise_data && student.class_wise_data.length > 0) {
        return student.class_wise_data.some(data => data.section_id == appliedFilters.value.section)
      }
      return false
    })
  }
  
  if (appliedFilters.value.shift) {
    filtered = filtered.filter(student => {
      if (student.class_wise_data && student.class_wise_data.length > 0) {
        return student.class_wise_data.some(data => data.shift_id == appliedFilters.value.shift)
      }
      return false
    })
  }
  
  if (appliedFilters.value.bloodGroup) {
    filtered = filtered.filter(student => 
      student.blood_group === appliedFilters.value.bloodGroup
    )
  }
  
  return filtered
})

const totalPages = computed(() => Math.ceil(filteredStudents.value.length / itemsPerPage.value))

const paginatedStudents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredStudents.value.slice(start, end)
})

const showingStart = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredStudents.value.length ? filteredStudents.value.length : end
})

const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  let start = Math.max(1, currentPage.value - 2)
  let end = Math.min(total, start + 4)
  
  if (end - start < 4) {
    start = Math.max(1, end - 4)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

const hasMorePages = computed(() => {
  return visiblePages.value[visiblePages.value.length - 1] < totalPages.value
})

// Methods
const fetchStudents = async () => {
  try {
    loading.value = true
    console.log('Fetching students...')
    const response = await axios.get('/api/student-profiles')
    console.log('Students API response:', response.data)
    
    if (response.data.success) {
      students.value = response.data.data.data || []
      
      // Extract unique data for filter dropdowns
      extractAvailableFilters()
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to load students')
    }
  } catch (error) {
    console.error('Error fetching students:', error)
    showErrorAlert('Error', 'Failed to load students')
  } finally {
    loading.value = false
  }
}

const extractAvailableFilters = () => {
  const classesMap = new Map()
  const sessionsMap = new Map()
  const versionsMap = new Map()
  const sectionsMap = new Map()
  const shiftsMap = new Map()
  
  students.value.forEach(student => {
    if (student.class_wise_data && student.class_wise_data.length > 0) {
      student.class_wise_data.forEach(data => {
        if (data.class && data.class.id) {
          classesMap.set(data.class.id, data.class)
        }
        if (data.session && data.session.id) {
          sessionsMap.set(data.session.id, data.session)
        }
        if (data.version && data.version.id) {
          versionsMap.set(data.version.id, data.version)
        }
        if (data.section && data.section.id) {
          sectionsMap.set(data.section.id, data.section)
        }
        if (data.shift && data.shift.id) {
          shiftsMap.set(data.shift.id, data.shift)
        }
      })
    }
  })
  
  availableClasses.value = Array.from(classesMap.values())
  availableSessions.value = Array.from(sessionsMap.values())
  availableVersions.value = Array.from(versionsMap.values())
  availableSections.value = Array.from(sectionsMap.values())
  availableShifts.value = Array.from(shiftsMap.values())
}

const applyFilters = () => {
  // Copy temporary filter values to applied filters
  appliedFilters.value = {
    class: tempClassFilter.value,
    session: tempSessionFilter.value,
    version: tempVersionFilter.value,
    gender: tempGenderFilter.value,
    classRoll: tempClassRollFilter.value,
    section: tempSectionFilter.value,
    shift: tempShiftFilter.value,
    bloodGroup: tempBloodGroupFilter.value
  }
  
  currentPage.value = 1
}

const applySearch = () => {
  currentPage.value = 1
}

const clearFilter = (filterName) => {
  // Clear specific filter from applied filters
  appliedFilters.value[filterName] = ''
  
  // Also clear from temporary filters
  switch(filterName) {
    case 'class':
      tempClassFilter.value = ''
      break
    case 'session':
      tempSessionFilter.value = ''
      break
    case 'version':
      tempVersionFilter.value = ''
      break
    case 'gender':
      tempGenderFilter.value = ''
      break
    case 'classRoll':
      tempClassRollFilter.value = ''
      break
    case 'section':
      tempSectionFilter.value = ''
      break
    case 'shift':
      tempShiftFilter.value = ''
      break
    case 'bloodGroup':
      tempBloodGroupFilter.value = ''
      break
  }
}

const clearFilters = () => {
  // Clear temporary filters
  tempClassFilter.value = ''
  tempSessionFilter.value = ''
  tempVersionFilter.value = ''
  tempGenderFilter.value = ''
  tempClassRollFilter.value = ''
  tempSectionFilter.value = ''
  tempShiftFilter.value = ''
  tempBloodGroupFilter.value = ''
  
  // Clear applied filters
  appliedFilters.value = {
    class: '',
    session: '',
    version: '',
    gender: '',
    classRoll: '',
    section: '',
    shift: '',
    bloodGroup: ''
  }
  
  // Clear search
  search.value = ''
  
  currentPage.value = 1
}

// Helper methods to get names by ID
const getClassNameById = (id) => {
  const classItem = availableClasses.value.find(item => item.id == id)
  return classItem ? classItem.class_name : ''
}

const getSessionNameById = (id) => {
  const session = availableSessions.value.find(item => item.id == id)
  return session ? session.session_name : ''
}

const getVersionNameById = (id) => {
  const version = availableVersions.value.find(item => item.id == id)
  return version ? version.version_name : ''
}

const getSectionNameById = (id) => {
  const section = availableSections.value.find(item => item.id == id)
  return section ? section.section_name : ''
}

const getShiftNameById = (id) => {
  const shift = availableShifts.value.find(item => item.id == id)
  return shift ? shift.shift_name : ''
}

const getGenderLabel = (value) => {
  const gender = genderOptions.value.find(option => option.value === value)
  return gender ? gender.label : value
}

const getBloodGroupLabel = (value) => {
  const bg = bloodGroupOptions.value.find(option => option.value === value)
  return bg ? bg.label : value
}

const deleteStudent = async (id) => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'This will permanently delete the student and all related data. This action cannot be undone!'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting student...')
      
      await axios.delete(`/api/student-profiles/${id}`)
      students.value = students.value.filter(student => student.id !== id)
      
      closeAlert()
      await showSuccessAlert('Deleted!', 'Student has been deleted successfully.')
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting student:', error)
      showErrorAlert('Error', 'Failed to delete student')
    }
  }
}

const handleDeleteStudent = async (id) => {
  closeDropdown()
  await deleteStudent(id)
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

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-BD', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('en-BD', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const calculateAge = (dateOfBirth) => {
  if (!dateOfBirth) return 'N/A'
  const birthDate = new Date(dateOfBirth)
  const today = new Date()
  let age = today.getFullYear() - birthDate.getFullYear()
  const monthDiff = today.getMonth() - birthDate.getMonth()
  
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--
  }
  
  return age + ' years'
}

const getClassName = (student) => {
  if (student.class_wise_data && student.class_wise_data.length > 0) {
    const classData = student.class_wise_data[0]
    return classData.class?.class_name || 'N/A'
  }
  return 'Not Assigned'
}

const getVersionName = (student) => {
  if (student.class_wise_data && student.class_wise_data.length > 0) {
    const classData = student.class_wise_data[0]
    return classData.version?.version_name || 'N/A'
  }
  return 'N/A'
}

const getSessionName = (student) => {
  if (student.class_wise_data && student.class_wise_data.length > 0) {
    const classData = student.class_wise_data[0]
    return classData.session?.session_name || 'N/A'
  }
  return 'N/A'
}

const getClassRoll = (student) => {
  if (student.class_wise_data && student.class_wise_data.length > 0) {
    const classData = student.class_wise_data[0]
    return classData.class_roll || 'N/A'
  }
  return 'N/A'
}

const goToPage = (page) => {
  currentPage.value = page
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    openDropdownId.value = null
  }
}

// Lifecycle hooks
onMounted(() => {
  fetchStudents()
  document.addEventListener('click', handleClickOutside)
  
  if (route.query.created === 'true') {
    showSuccessAlert('Success!', 'Student admission created successfully.')
    router.replace({ name: 'student-profiles.index' })
  }
  
  if (route.query.updated === 'true') {
    showSuccessAlert('Success!', 'Student admission updated successfully.')
    router.replace({ name: 'student-profiles.index' })
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Custom styles for the student table */
.table-container {
  max-height: 600px;
  overflow-y: auto;
}

tr:hover {
  background-color: #f9fafb;
}

/* Custom scrollbar */
.table-container::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.table-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>