<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
            Student Details
          </h1>
          <p class="text-sm md:text-base text-gray-600">
            View and manage student information
          </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <router-link 
            :to="{ name: 'student-profiles.index' }"
            class="inline-flex items-center gap-2 px-3 py-2 md:px-4 md:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-medium text-sm md:text-base"
          >
            <i class="fas fa-arrow-left"></i>
            <span class="hidden sm:inline">Back To List</span>
          </router-link>
          <router-link 
            :to="{ name: 'student-profiles.edit', params: { id: student?.id } }"
            class="inline-flex items-center gap-2 px-3 py-2 md:px-4 md:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-medium text-sm md:text-base"
          >
            <i class="fas fa-edit"></i>
            <span class="hidden sm:inline">Edit</span>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-xl md:rounded-2xl shadow p-4 md:p-8 text-center">
      <div class="flex flex-col items-center justify-center space-y-4">
        <div class="w-12 h-12 md:w-16 md:h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
        <p class="text-gray-600 text-sm md:text-base">Loading student details...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl md:rounded-2xl p-4 md:p-6">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
          <i class="fas fa-exclamation-triangle text-red-600 text-sm md:text-base"></i>
        </div>
        <div class="flex-1">
          <h3 class="text-base md:text-lg font-semibold text-red-800">Error Loading Student</h3>
          <p class="text-red-600 text-sm md:text-base mt-1">{{ error }}</p>
          <button @click="fetchStudent" class="mt-3 inline-flex items-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm">
            <i class="fas fa-redo"></i>
            Retry
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else-if="student" class="space-y-6 md:space-y-8">
      <!-- Top Row: Profile and Academic Info -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-linear-to-r from-blue-300 to-blue-400 p-4 md:p-6 text-center">
              <div class="w-24 h-24 md:w-32 md:h-32 mx-auto rounded-full border-4 border-white overflow-hidden bg-white mb-3 md:mb-4">
                <img 
                  v-if="student.student_image_url"
                  :src="student.student_image_url" 
                  alt="Student Photo"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                  <i class="fas fa-user text-gray-400 text-2xl md:text-4xl"></i>
                </div>
              </div>
              <h2 class="text-xl md:text-2xl font-bold text-white mb-1">{{ student.student_name }}</h2>
              <p class="text-white text-sm md:text-base mb-2 md:mb-3">ID: {{ student.id_card_number || 'N/A' }}</p>
              <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-400 bg-opacity-30 rounded-full text-xs md:text-sm">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <span class="text-white">Active Student</span>
              </div>
            </div>

            <!-- Profile Details -->
            <div class="p-4 md:p-6">
              <div class="space-y-4 md:space-y-6">
                <!-- Personal Info -->
                <div>
                  <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-id-card text-blue-600 text-sm md:text-base"></i>
                    Personal Info
                  </h3>
                  <div class="space-y-2">
                    <div class="flex justify-between items-center">
                      <span class="text-xs md:text-sm text-gray-600">Date of Birth</span>
                      <span class="text-xs md:text-sm font-medium text-gray-800">{{ formatDate(student.date_of_birth) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-xs md:text-sm text-gray-600">Gender</span>
                      <span class="text-xs md:text-sm font-medium text-gray-800 capitalize">{{ student.gender || 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-xs md:text-sm text-gray-600">Blood Group</span>
                      <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.blood_group || 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-xs md:text-sm text-gray-600">Religion</span>
                      <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.religion || 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-xs md:text-sm text-gray-600">Nationality</span>
                      <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.nationality || 'N/A' }}</span>
                    </div>
                  </div>
                </div>

                <!-- Contact Info -->
                <div>
                  <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-phone text-green-600 text-sm md:text-base"></i>
                    Contact Info
                  </h3>
                  <div class="space-y-2">
                    <div class="flex items-start gap-2">
                      <i class="fas fa-mobile-alt text-gray-400 text-xs md:text-sm mt-0.5"></i>
                      <div>
                        <p class="text-xs md:text-sm text-gray-600">Mobile</p>
                        <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.mobile_number || 'N/A' }}</p>
                      </div>
                    </div>
                    <div class="flex items-start gap-2">
                      <i class="fas fa-envelope text-gray-400 text-xs md:text-sm mt-0.5"></i>
                      <div>
                        <p class="text-xs md:text-sm text-gray-600">Email</p>
                        <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.email || 'N/A' }}</p>
                      </div>
                    </div>
                    <div class="flex items-start gap-2">
                      <i class="fas fa-certificate text-gray-400 text-xs md:text-sm mt-0.5"></i>
                      <div>
                        <p class="text-xs md:text-sm text-gray-600">Birth Certificate</p>
                        <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.birth_certificate_number || 'N/A' }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- TC Certificate -->
                <div v-if="student.tc_image_url" class="pt-4 border-t">
                  <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-file-certificate text-yellow-600 text-sm md:text-base"></i>
                    Transfer Certificate
                  </h3>
                  <a 
                    :href="student.tc_image_url" 
                    target="_blank"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded-lg transition-colors w-full justify-center text-sm"
                  >
                    <i class="fas fa-external-link-alt"></i>
                    View TC
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Academic Information Card -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 overflow-hidden h-full">
            <div class="flex items-center gap-3 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 bg-linear-to-r from-blue-50 to-indigo-50">
              <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-blue-500 flex items-center justify-center">
                <i class="fas fa-graduation-cap text-white text-sm md:text-base"></i>
              </div>
              <div>
                <h3 class="text-base md:text-lg font-semibold text-gray-800">
                  Academic Information
                </h3>
                <p class="text-xs md:text-sm text-gray-600">
                  Current class and academic details
                </p>
              </div>
            </div>
            
            <div class="p-4 md:p-6">
              <div v-if="student.class_wise_data && student.class_wise_data.length > 0" class="space-y-4 md:space-y-6">
                <div v-for="(data, index) in student.class_wise_data" :key="index" class="bg-gray-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-gray-200">
                  <div class="flex items-center gap-3 mb-3 md:mb-4">
                    <div class="w-6 h-6 md:w-8 md:h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                      <i class="fas fa-school text-blue-600 text-xs md:text-sm"></i>
                    </div>
                    <h4 class="font-semibold text-gray-800 text-sm md:text-base">Academic Record</h4>
                  </div>
                  
                  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                    <div v-if="data.class" class="flex items-center justify-between p-2 md:p-3 bg-white rounded-lg border border-gray-200">
                      <span class="text-xs md:text-sm text-gray-600">Class</span>
                      <span class="text-xs md:text-sm font-medium text-blue-600">{{ data.class.class_name }}</span>
                    </div>
                    
                    <div v-if="data.version" class="flex items-center justify-between p-2 md:p-3 bg-white rounded-lg border border-gray-200">
                      <span class="text-xs md:text-sm text-gray-600">Version</span>
                      <span class="text-xs md:text-sm font-medium text-purple-600">{{ data.version.version_name }}</span>
                    </div>
                    
                    <div v-if="data.session" class="flex items-center justify-between p-2 md:p-3 bg-white rounded-lg border border-gray-200">
                      <span class="text-xs md:text-sm text-gray-600">Session</span>
                      <span class="text-xs md:text-sm font-medium text-green-600">{{ data.session.session_name }}</span>
                    </div>
                    
                    <div v-if="data.section" class="flex items-center justify-between p-2 md:p-3 bg-white rounded-lg border border-gray-200">
                      <span class="text-xs md:text-sm text-gray-600">Section</span>
                      <span class="text-xs md:text-sm font-medium text-yellow-600">{{ data.section.section_name }}</span>
                    </div>
                    
                    <div v-if="data.shift" class="flex items-center justify-between p-2 md:p-3 bg-white rounded-lg border border-gray-200">
                      <span class="text-xs md:text-sm text-gray-600">Shift</span>
                      <span class="text-xs md:text-sm font-medium text-red-600">{{ data.shift.shift_name }}</span>
                    </div>
                    
                    <div v-if="data.class_roll" class="flex items-center justify-between p-2 md:p-3 bg-white rounded-lg border border-gray-200">
                      <span class="text-xs md:text-sm text-gray-600">Class Roll</span>
                      <span class="text-xs md:text-sm font-medium text-pink-600">{{ data.class_roll }}</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-6 md:py-8">
                <div class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                  <i class="fas fa-book-open text-gray-400 text-xl md:text-2xl"></i>
                </div>
                <h4 class="text-base md:text-lg font-semibold text-gray-600 mb-2">No Academic Information</h4>
                <p class="text-gray-500 text-sm md:text-base mb-3 md:mb-4">This student hasn't been assigned to any class yet.</p>
                <router-link 
                  :to="{ name: 'student-profiles.edit', params: { id: student.id } }"
                  class="inline-flex items-center gap-2 px-3 py-2 md:px-4 md:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm"
                >
                  <i class="fas fa-plus"></i>
                  Add Academic Info
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Second Row: Family Information -->
      <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 bg-linear-to-r from-green-50 to-emerald-50">
          <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-green-500 flex items-center justify-center">
            <i class="fas fa-users text-white text-sm md:text-base"></i>
          </div>
          <div>
            <h3 class="text-base md:text-lg font-semibold text-gray-800">
              Family Information
            </h3>
            <p class="text-xs md:text-sm text-gray-600">
              Parent and guardian details
            </p>
          </div>
        </div>
        
        <div class="p-4 md:p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
            <!-- Father's Information -->
            <div class="bg-green-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-green-200">
              <div class="flex items-center gap-3 mb-3 md:mb-4">
                <div class="w-6 h-6 md:w-8 md:h-8 rounded-lg bg-green-100 flex items-center justify-center">
                  <i class="fas fa-male text-green-600 text-xs md:text-sm"></i>
                </div>
                <h4 class="font-semibold text-gray-800 text-sm md:text-base">Father's Information</h4>
              </div>
              
              <div class="space-y-2 md:space-y-3">
                <div v-if="student.father_name" class="flex items-center justify-between">
                  <span class="text-xs md:text-sm text-gray-600">Name</span>
                  <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.father_name }}</span>
                </div>
                <div v-if="student.father_profession" class="flex items-center justify-between">
                  <span class="text-xs md:text-sm text-gray-600">Profession</span>
                  <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.father_profession }}</span>
                </div>
                <div v-if="student.father_mobile_number" class="flex items-center justify-between">
                  <span class="text-xs md:text-sm text-gray-600">Mobile</span>
                  <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.father_mobile_number }}</span>
                </div>
                
                <div v-if="!student.father_name && !student.father_profession && !student.father_mobile_number" class="text-center text-gray-500 text-xs md:text-sm py-3 md:py-4">
                  No father information available
                </div>
              </div>
            </div>

            <!-- Mother's Information -->
            <div class="bg-pink-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-pink-200">
              <div class="flex items-center gap-3 mb-3 md:mb-4">
                <div class="w-6 h-6 md:w-8 md:h-8 rounded-lg bg-pink-100 flex items-center justify-center">
                  <i class="fas fa-female text-pink-600 text-xs md:text-sm"></i>
                </div>
                <h4 class="font-semibold text-gray-800 text-sm md:text-base">Mother's Information</h4>
              </div>
              
              <div class="space-y-2 md:space-y-3">
                <div v-if="student.mother_name" class="flex items-center justify-between">
                  <span class="text-xs md:text-sm text-gray-600">Name</span>
                  <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.mother_name }}</span>
                </div>
                <div v-if="student.mother_profession" class="flex items-center justify-between">
                  <span class="text-xs md:text-sm text-gray-600">Profession</span>
                  <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.mother_profession }}</span>
                </div>
                <div v-if="student.mother_mobile_number" class="flex items-center justify-between">
                  <span class="text-xs md:text-sm text-gray-600">Mobile</span>
                  <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.mother_mobile_number }}</span>
                </div>
                
                <div v-if="!student.mother_name && !student.mother_profession && !student.mother_mobile_number" class="text-center text-gray-500 text-xs md:text-sm py-3 md:py-4">
                  No mother information available
                </div>
              </div>
            </div>

            <!-- LocalGuardian Information -->
            <div class="bg-yellow-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-yellow-200">
              <div class="flex items-center gap-3 mb-3 md:mb-4">
                <div class="w-6 h-6 md:w-8 md:h-8 rounded-lg bg-yellow-100 flex items-center justify-center">
                  <i class="fas fa-user-shield text-yellow-600 text-xs md:text-sm"></i>
                </div>
                <h4 class="font-semibold text-gray-800 text-sm md:text-base">Local Guardian Information</h4>
              </div>
              
              <div class="space-y-2 md:space-y-3">
                <div v-if="student.local_guardian_name" class="flex items-center justify-between">
                  <span class="text-xs md:text-sm text-gray-600">Name</span>
                  <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.local_guardian_name }}</span>
                </div>
                <div v-if="student.local_guardian_profession" class="flex items-center justify-between">
                  <span class="text-xs md:text-sm text-gray-600">Profession</span>
                  <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.local_guardian_profession }}</span>
                </div>
                <div v-if="student.local_guardian_mobile_number" class="flex items-center justify-between">
                  <span class="text-xs md:text-sm text-gray-600">Mobile</span>
                  <span class="text-xs md:text-sm font-medium text-gray-800">{{ student.local_guardian_mobile_number }}</span>
                </div>
                
                <div v-if="!student.local_guardian_name && !student.local_guardian_profession && !student.local_guardian_mobile_number" class="text-center text-gray-500 text-xs md:text-sm py-3 md:py-4">
                  No Local guardian information available
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Third Row: Address Information -->
      <div class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 bg-linear-to-r from-purple-50 to-violet-50">
          <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-purple-500 flex items-center justify-center">
            <i class="fas fa-home text-white text-sm md:text-base"></i>
          </div>
          <div>
            <h3 class="text-base md:text-lg font-semibold text-gray-800">
              Address Information
            </h3>
            <p class="text-xs md:text-sm text-gray-600">
              Present and permanent addresses
            </p>
          </div>
        </div>
        
        <div class="p-4 md:p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <!-- Present Address -->
            <div class="bg-purple-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-purple-200">
              <div class="flex items-center gap-3 mb-3 md:mb-4">
                <div class="w-6 h-6 md:w-8 md:h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                  <i class="fas fa-map-marker-alt text-purple-600 text-xs md:text-sm"></i>
                </div>
                <h4 class="font-semibold text-gray-800 text-sm md:text-base">Present Address</h4>
              </div>
              
              <div class="space-y-2 md:space-y-3">
                <div v-if="student.present_village" class="flex items-start gap-2">
                  <i class="fas fa-vihara text-gray-400 text-xs md:text-sm mt-0.5"></i>
                  <div>
                    <p class="text-xs md:text-sm text-gray-600">Village/Area</p>
                    <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.present_village }}</p>
                  </div>
                </div>
                <div v-if="student.present_post_office" class="flex items-start gap-2">
                  <i class="fas fa-envelope text-gray-400 text-xs md:text-sm mt-0.5"></i>
                  <div>
                    <p class="text-xs md:text-sm text-gray-600">Post Office</p>
                    <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.present_post_office }}</p>
                  </div>
                </div>
                <div v-if="student.present_upazila_id" class="flex items-start gap-2">
                  <i class="fas fa-city text-gray-400 text-xs md:text-sm mt-0.5"></i>
                  <div>
                    <p class="text-xs md:text-sm text-gray-600">Upazila</p>
                    <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.present_upazila?.name || 'N/A' }}</p>
                  </div>
                </div>
                <div v-if="student.present_district_id" class="flex items-start gap-2">
                  <i class="fas fa-map text-gray-400 text-xs md:text-sm mt-0.5"></i>
                  <div>
                    <p class="text-xs md:text-sm text-gray-600">District</p>
                    <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.present_district?.name || 'N/A' }}</p>
                  </div>
                </div>
                
                <div v-if="!student.present_village && !student.present_post_office && !student.present_upazila_id && !student.present_district_id" class="text-center text-gray-500 text-xs md:text-sm py-3 md:py-4">
                  No present address available
                </div>
              </div>
            </div>

            <!-- Permanent Address -->
            <div class="bg-indigo-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-indigo-200">
              <div class="flex items-center gap-3 mb-3 md:mb-4">
                <div class="w-6 h-6 md:w-8 md:h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                  <i class="fas fa-map-marked-alt text-indigo-600 text-xs md:text-sm"></i>
                </div>
                <h4 class="font-semibold text-gray-800 text-sm md:text-base">Permanent Address</h4>
              </div>
              
              <div class="space-y-2 md:space-y-3">
                <div v-if="student.permanent_village" class="flex items-start gap-2">
                  <i class="fas fa-vihara text-gray-400 text-xs md:text-sm mt-0.5"></i>
                  <div>
                    <p class="text-xs md:text-sm text-gray-600">Village/Area</p>
                    <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.permanent_village }}</p>
                  </div>
                </div>
                <div v-if="student.permanent_post_office" class="flex items-start gap-2">
                  <i class="fas fa-envelope text-gray-400 text-xs md:text-sm mt-0.5"></i>
                  <div>
                    <p class="text-xs md:text-sm text-gray-600">Post Office</p>
                    <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.permanent_post_office }}</p>
                  </div>
                </div>
                <div v-if="student.permanent_upazila_id" class="flex items-start gap-2">
                  <i class="fas fa-city text-gray-400 text-xs md:text-sm mt-0.5"></i>
                  <div>
                    <p class="text-xs md:text-sm text-gray-600">Upazila</p>
                    <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.permanent_upazila?.name || 'N/A' }}</p>
                  </div>
                </div>
                <div v-if="student.permanent_district_id" class="flex items-start gap-2">
                  <i class="fas fa-map text-gray-400 text-xs md:text-sm mt-0.5"></i>
                  <div>
                    <p class="text-xs md:text-sm text-gray-600">District</p>
                    <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.permanent_district?.name || 'N/A' }}</p>
                  </div>
                </div>
                
                <div v-if="!student.permanent_village && !student.permanent_post_office && !student.permanent_upazila_id && !student.permanent_district_id" class="text-center text-gray-500 text-xs md:text-sm py-3 md:py-4">
                  No permanent address available
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Previous Institute Card -->
      <div v-if="student.previous_institute_name || student.previous_institute_class || student.previous_institute_section || student.previous_institute_roll || student.previous_institute_result" class="bg-white rounded-xl md:rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 bg-linear-to-r from-yellow-50 to-orange-50">
          <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-yellow-500 flex items-center justify-center">
            <i class="fas fa-school text-white text-sm md:text-base"></i>
          </div>
          <div>
            <h3 class="text-base md:text-lg font-semibold text-gray-800">
              Previous Institute Information
            </h3>
            <p class="text-xs md:text-sm text-gray-600">
              Educational background before admission
            </p>
          </div>
        </div>
        
        <div class="p-4 md:p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div class="space-y-3 md:space-y-4">
              <div v-if="student.previous_institute_name" class="flex items-start gap-2">
                <i class="fas fa-university text-gray-400 text-xs md:text-sm mt-0.5"></i>
                <div>
                  <p class="text-xs md:text-sm text-gray-600">Institute Name</p>
                  <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.previous_institute_name }}</p>
                </div>
              </div>
              
              <div v-if="student.previous_institute_class" class="flex items-start gap-2">
                <i class="fas fa-graduation-cap text-gray-400 text-xs md:text-sm mt-0.5"></i>
                <div>
                  <p class="text-xs md:text-sm text-gray-600">Class</p>
                  <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.previous_institute_class }}</p>
                </div>
              </div>
            </div>
            
            <div class="space-y-3 md:space-y-4">
              <div v-if="student.previous_institute_section" class="flex items-start gap-2">
                <i class="fas fa-chalkboard text-gray-400 text-xs md:text-sm mt-0.5"></i>
                <div>
                  <p class="text-xs md:text-sm text-gray-600">Section</p>
                  <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.previous_institute_section }}</p>
                </div>
              </div>
              
              <div v-if="student.previous_institute_roll" class="flex items-start gap-2">
                <i class="fas fa-list-ol text-gray-400 text-xs md:text-sm mt-0.5"></i>
                <div>
                  <p class="text-xs md:text-sm text-gray-600">Roll Number</p>
                  <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.previous_institute_roll }}</p>
                </div>
              </div>
              
              <div v-if="student.previous_institute_result" class="flex items-start gap-2">
                <i class="fas fa-chart-line text-gray-400 text-xs md:text-sm mt-0.5"></i>
                <div>
                  <p class="text-xs md:text-sm text-gray-600">Result/GPA</p>
                  <p class="text-xs md:text-sm font-medium text-gray-800">{{ student.previous_institute_result }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showConfirmDialog } from '../../utils/sweetAlert'

const route = useRoute()
const router = useRouter()

// Reactive data
const student = ref(null)
const loading = ref(true)
const error = ref(null)

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-BD', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Fetch student data
const fetchStudent = async () => {
  loading.value = true
  error.value = null
  
  try {
    console.log('Fetching student with ID:', route.params.id)
    const response = await axios.get(`/api/student-profiles/${route.params.id}`)
    
    if (response.data.success) {
      student.value = response.data.data
      console.log('Student data loaded:', student.value)
    } else {
      error.value = response.data.message || 'Failed to load student data'
    }
  } catch (err) {
    console.error('Error fetching student:', err)
    if (err.response?.status === 404) {
      error.value = 'Student not found'
    } else if (err.response?.status === 500) {
      error.value = 'Server error occurred'
    } else {
      error.value = 'Failed to load student data. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

// Delete student
const confirmDelete = async () => {
  const confirmed = await showConfirmDialog(
      'Delete Student',
      'Are you sure you want to delete this student? This action cannot be undone.'
    )
  
  if (confirmed) {
    try {
      await axios.delete(`/api/student-profiles/${route.params.id}`)
      
      showSuccessAlert('Success', 'Student deleted successfully')
      
      // Redirect to student list
      setTimeout(() => {
        router.push({ name: 'student-profiles.index' })
      }, 1500)
      
    } catch (err) {
      console.error('Error deleting student:', err)
      showErrorAlert('Error', 'Failed to delete student. Please try again.')
    }
  }
}

// Initialize
onMounted(() => {
  fetchStudent()
})
</script>

<style scoped>
/* Custom styles */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>