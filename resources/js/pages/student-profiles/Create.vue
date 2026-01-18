<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Create New Student Admission
          </h1>
          <p class="text-gray-600">
            Add a new student to the system
          </p>
        </div>
        <div class="flex items-center gap-3 mb-2">
          <router-link 
            :to="{ name: 'student-profiles.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-semibold"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Students
          </router-link>
        </div>
      </div>
    </div>

    <!-- Form Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Form -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
          <!-- Form Header -->
          <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-user-plus text-white"></i>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">
                Student Admission Form
              </h3>
              <p class="text-sm text-gray-600">
                Fill in the student details
              </p>
            </div>
          </div>

          <!-- Form Content -->
          <div class="p-6">
            <div class="space-y-8">
              <!-- Student Information Section -->
              <div>
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <i class="fas fa-user-circle text-blue-600"></i>
                  Student Information
                </h4>
                <div class="space-y-6">
                  <!-- Student Name and ID Card -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label for="student_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Student Name <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="text"
                        id="student_name"
                        v-model="form.student_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        :class="{ 'border-red-500': errors.student_name }"
                        placeholder="Enter student name"
                        required
                      />
                      <p v-if="errors.student_name" class="mt-1 text-sm text-red-600">
                        {{ errors.student_name[0] }}
                      </p>
                    </div>

                    <div>
                      <label for="id_card_number" class="block text-sm font-medium text-gray-700 mb-2">
                        ID Card Number
                        <span v-if="loadingNextId" class="text-blue-600 text-xs ml-2">
                          <i class="fas fa-spinner fa-spin"></i> Generating...
                        </span>
                      </label>
                      <div class="relative">
                        <input
                          type="text"
                          id="id_card_number"
                          v-model="form.id_card_number"
                          readonly
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-800 cursor-not-allowed"
                          :placeholder="loadingNextId ? 'Generating ID...' : 'Auto-generated ID'"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                          <i class="fas fa-lock text-gray-400"></i>
                        </div>
                      </div>
                      <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle"></i> 
                         ID will be generated automatically
                      </p>
                    </div>
                  </div>

                  <!-- Student Image Upload -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Student Photo
                    </label>
                    <div class="mt-1 flex items-center gap-4">
                      <div class="relative">
                        <input
                          type="file"
                          id="student_image"
                          ref="studentImageInput"
                          @change="handleStudentImageChange"
                          class="hidden"
                          accept="image/*"
                        />
                        <button
                          type="button"
                          @click="$refs.studentImageInput.click()"
                          class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium"
                        >
                          <i class="fas fa-upload"></i>
                          Upload Photo
                        </button>
                      </div>
                      <div v-if="form.student_image || studentImagePreview" class="flex items-center gap-2">
                        <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gray-200">
                          <img 
                            v-if="studentImagePreview"
                            :src="studentImagePreview" 
                            alt="Student preview"
                            class="w-full h-full object-cover"
                          />
                          <div v-else-if="form.student_image" class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-gray-400 text-xl"></i>
                          </div>
                        </div>
                        <button
                          v-if="form.student_image"
                          @click="removeStudentImage"
                          class="text-red-500 hover:text-red-700"
                          type="button"
                        >
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Personal Information -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                      <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                        Date of Birth
                      </label>
                      <input
                        type="date"
                        id="date_of_birth"
                        v-model="form.date_of_birth"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                      />
                    </div>

                    <div>
                      <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                        Gender
                      </label>
                      <select
                        id="gender"
                        v-model="form.gender"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                      >
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                      </select>
                    </div>
                  
                  </div>

                  <div>
                      <label for="birth_certificate_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Birth Certificate Number
                      </label>
                      <input
                        type="text"
                        id="birth_certificate_number"
                        v-model="form.birth_certificate_number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter birth certificate number"
                      />
                    </div>

                  <!-- Religion, Nationality, Birth Certificate -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                      <label for="religion" class="block text-sm font-medium text-gray-700 mb-2">
                        Religion
                      </label>
                      <v-select
                        v-model="form.religion"
                        :options="religionOptions"
                        :reduce="option => option.value"
                        :filterable="true"
                        :searchable="true"
                        :taggable="true"
                        @tag="addCustomReligion"
                        placeholder="Select or type custom religion"
                        class="custom-select"
                      >
                        <template #option="option">
                          <div v-if="option.isCustom" class="flex items-center">
                            <span class="text-blue-600 mr-2">
                              <i class="fas fa-plus"></i>
                            </span>
                            <span>Add: "{{ option.label }}"</span>
                          </div>
                          <div v-else>
                            {{ option.label }}
                          </div>
                        </template>
                      </v-select>
                    </div>

                    <div>
                      <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">
                        Nationality
                      </label>
                      <v-select
                        v-model="form.nationality"
                        :options="nationalityOptions"
                        :reduce="option => option.value"
                        :filterable="true"
                        :searchable="true"
                        :taggable="true"
                        @tag="addCustomNationality"
                        placeholder="Select or type custom nationality"
                        class="custom-select"
                      >
                        <template #option="option">
                          <div v-if="option.isCustom" class="flex items-center">
                            <span class="text-blue-600 mr-2">
                              <i class="fas fa-plus"></i>
                            </span>
                            <span>Add: "{{ option.label }}"</span>
                          </div>
                          <div v-else>
                            {{ option.label }}
                          </div>
                        </template>
                      </v-select>
                    </div>
                    <div>
                      <label for="blood_group" class="block text-sm font-medium text-gray-700 mb-2">
                        Blood Group
                      </label>
                      <v-select
                        v-model="form.blood_group"
                        :options="bloodGroupOptions"
                        :reduce="option => option.value"
                        :filterable="true"
                        :searchable="true"
                        :taggable="true"
                        @tag="addCustomBloodGroup"
                        placeholder="Select or type custom blood group"
                        class="custom-select"
                      >
                        <template #option="option">
                          <div v-if="option.isCustom" class="flex items-center">
                            <span class="text-blue-600 mr-2">
                              <i class="fas fa-plus"></i>
                            </span>
                            <span>Add: "{{ option.label }}"</span>
                          </div>
                          <div v-else>
                            {{ option.label }}
                          </div>
                        </template>
                      </v-select>
                    </div>
                  </div>

                  <!-- Mobile Number and Email -->
                  <div class="flex flex-col md:flex-row gap-6">
                    
                    <!-- Mobile -->
                    <div class="md:basis-[40%]">
                      <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Mobile Number
                      </label>
                      <input
                        type="tel"
                        id="mobile_number"
                        v-model="form.mobile_number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter mobile number"
                      />
                    </div>

                    <!-- Email -->
                    <div class="md:basis-[60%]">
                      <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                      </label>
                      <input
                        type="email"
                        id="email"
                        v-model="form.email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter email"
                      />
                    </div>

                  </div>


                </div>
              </div>

              <!-- Family Information Section -->
              <div class="border-t pt-8">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <i class="fas fa-users text-green-600"></i>
                  Family Information
                </h4>
                <div class="space-y-6">
                  <!-- Father Information -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                      <label for="father_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Father's Name
                      </label>
                      <input
                        type="text"
                        id="father_name"
                        v-model="form.father_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter father's name"
                      />
                    </div>
                    <div>
                      <label for="father_profession" class="block text-sm font-medium text-gray-700 mb-2">
                        Father's Profession
                      </label>
                      <input
                        type="text"
                        id="father_profession"
                        v-model="form.father_profession"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter profession"
                      />
                    </div>
                    <div>
                      <label for="father_mobile_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Father's Mobile
                      </label>
                      <input
                        type="tel"
                        id="father_mobile_number"
                        v-model="form.father_mobile_number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter mobile number"
                      />
                    </div>
                  </div>

                  <!-- Mother Information -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                      <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Mother's Name
                      </label>
                      <input
                        type="text"
                        id="mother_name"
                        v-model="form.mother_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter mother's name"
                      />
                    </div>
                    <div>
                      <label for="mother_profession" class="block text-sm font-medium text-gray-700 mb-2">
                        Mother's Profession
                      </label>
                      <input
                        type="text"
                        id="mother_profession"
                        v-model="form.mother_profession"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter profession"
                      />
                    </div>
                    <div>
                      <label for="mother_mobile_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Mother's Mobile
                      </label>
                      <input
                        type="tel"
                        id="mother_mobile_number"
                        v-model="form.mother_mobile_number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter mobile number"
                      />
                    </div>
                  </div>

                  <!-- Local Guardian Information -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                      <label for="local_guardian_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Local Guardian Name
                      </label>
                      <input
                        type="text"
                        id="local_guardian_name"
                        v-model="form.local_guardian_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter guardian name"
                      />
                    </div>
                    <div>
                      <label for="local_guardian_profession" class="block text-sm font-medium text-gray-700 mb-2">
                        Guardian Profession
                      </label>
                      <input
                        type="text"
                        id="local_guardian_profession"
                        v-model="form.local_guardian_profession"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter profession"
                      />
                    </div>
                    <div>
                      <label for="local_guardian_mobile_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Guardian Mobile
                      </label>
                      <input
                        type="tel"
                        id="local_guardian_mobile_number"
                        v-model="form.local_guardian_mobile_number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter mobile number"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Address Information Section -->
              <div class="border-t pt-8">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <i class="fas fa-home text-purple-600"></i>
                  Address Information
                </h4>
                <div class="space-y-6">
                  <!-- Present Address -->
                  <div>
                    <h5 class="text-md font-medium text-gray-700 mb-3">Present Address</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label for="present_village" class="block text-sm font-medium text-gray-700 mb-2">
                          Village/Area
                        </label>
                        <input
                          type="text"
                          id="present_village"
                          v-model="form.present_village"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                          placeholder="Enter village or area"
                        />
                      </div>
                      <div>
                        <label for="present_post_office" class="block text-sm font-medium text-gray-700 mb-2">
                          Post Office
                        </label>
                        <input
                          type="text"
                          id="present_post_office"
                          v-model="form.present_post_office"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                          placeholder="Enter post office"
                        />
                      </div>
                      
                      <!-- Present District -->
                      <div>
                        <label for="present_district_id" class="block text-sm font-medium text-gray-700 mb-2">
                          District
                        </label>
                        <v-select
                          v-model="form.present_district_id"
                          :options="districts"
                          :filterable="true"
                          :searchable="true"
                          label="name"
                          :reduce="district => district.id"
                          placeholder="Select district"
                        >
                          <template #option="option">
                            <div>
                              <div class="font-medium text-gray-800">
                                {{ option.name }}
                              </div>
                              <div class="text-xs text-gray-500">
                                {{ option.bn_name }}
                              </div>
                            </div>
                          </template>
                        </v-select>
                      </div>

                      <!-- Present Upazila -->
                      <div>
                        <label for="present_upazila_id" class="block text-sm font-medium text-gray-700 mb-2">
                          Upazila
                        </label>
                        <v-select
                          v-model="form.present_upazila_id"
                          :options="presentUpazilas"
                          :filterable="true"
                          :searchable="true"
                          label="name"
                          :reduce="upazila => upazila.id"
                          placeholder="Select upazila"
                          :disabled="!form.present_district_id"
                        >
                          <template #option="option">
                            <div>
                              <div class="font-medium text-gray-800">
                                {{ option.name }}
                              </div>
                              <div class="text-xs text-gray-500">
                                {{ option.bn_name }}
                              </div>
                            </div>
                          </template>
                        </v-select>
                      </div>
                    </div>
                  </div>

                  <!-- Same Address Checkbox -->
                  <div class="mb-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                      <input 
                        type="checkbox" 
                        v-model="sameAsPresentAddress" 
                        @change="copyPresentToPermanent"
                        class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                      />
                      <span class="text-sm font-medium text-gray-700">
                        Permanent Address same as Present Address
                      </span>
                    </label>
                  </div>

                  <!-- Permanent Address -->
                  <div>
                    <h5 class="text-md font-medium text-gray-700 mb-3">Permanent Address</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label for="permanent_village" class="block text-sm font-medium text-gray-700 mb-2">
                          Village/Area
                        </label>
                        <input
                          type="text"
                          id="permanent_village"
                          v-model="form.permanent_village"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                          placeholder="Enter village or area"
                          :disabled="sameAsPresentAddress"
                        />
                      </div>
                      <div>
                        <label for="permanent_post_office" class="block text-sm font-medium text-gray-700 mb-2">
                          Post Office
                        </label>
                        <input
                          type="text"
                          id="permanent_post_office"
                          v-model="form.permanent_post_office"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                          placeholder="Enter post office"
                          :disabled="sameAsPresentAddress"
                        />
                      </div>
                      
                      <!-- Permanent District -->
                      <div>
                        <label for="permanent_district_id" class="block text-sm font-medium text-gray-700 mb-2">
                          District
                        </label>
                        <v-select
                          v-model="form.permanent_district_id"
                          :options="districts"
                          :filterable="true"
                          :searchable="true"
                          label="name"
                          :reduce="district => district.id"
                          placeholder="Select district"
                          :disabled="sameAsPresentAddress"
                        >
                          <template #option="option">
                            <div>
                              <div class="font-medium text-gray-800">
                                {{ option.name }}
                              </div>
                              <div class="text-xs text-gray-500">
                                {{ option.bn_name }}
                              </div>
                            </div>
                          </template>
                        </v-select>
                      </div>

                      <!-- Permanent Upazila -->
                      <div>
                        <label for="permanent_upazila_id" class="block text-sm font-medium text-gray-700 mb-2">
                          Upazila
                        </label>
                        <v-select
                          v-model="form.permanent_upazila_id"
                          :options="permanentUpazilas"
                          :filterable="true"
                          :searchable="true"
                          label="name"
                          :reduce="upazila => upazila.id"
                          placeholder="Select upazila"
                          :disabled="sameAsPresentAddress"
                        >
                          <template #option="option">
                            <div>
                              <div class="font-medium text-gray-800">
                                {{ option.name }}
                              </div>
                              <div class="text-xs text-gray-500">
                                {{ option.bn_name }}
                              </div>
                            </div>
                          </template>
                        </v-select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Previous Institute Section -->
              <div class="border-t pt-8">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <i class="fas fa-school text-yellow-600"></i>
                  Previous Institute Information
                </h4>
                <div class="space-y-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label for="previous_institute_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Institute Name
                      </label>
                      <input
                        type="text"
                        id="previous_institute_name"
                        v-model="form.previous_institute_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter previous institute name"
                      />
                    </div>
                    <div>
                      <label for="previous_institute_class" class="block text-sm font-medium text-gray-700 mb-2">
                        Class
                      </label>
                      <input
                        type="text"
                        id="previous_institute_class"
                        v-model="form.previous_institute_class"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter class"
                      />
                    </div>
                    <div>
                      <label for="previous_institute_section" class="block text-sm font-medium text-gray-700 mb-2">
                        Section
                      </label>
                      <input
                        type="text"
                        id="previous_institute_section"
                        v-model="form.previous_institute_section"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter section"
                      />
                    </div>
                    <div>
                      <label for="previous_institute_roll" class="block text-sm font-medium text-gray-700 mb-2">
                        Roll Number
                      </label>
                      <input
                        type="text"
                        id="previous_institute_roll"
                        v-model="form.previous_institute_roll"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter roll number"
                      />
                    </div>
                    <div class="md:col-span-2">
                      <label for="previous_institute_result" class="block text-sm font-medium text-gray-700 mb-2">
                        Result
                      </label>
                      <input
                        type="text"
                        id="previous_institute_result"
                        v-model="form.previous_institute_result"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter result or GPA"
                      />
                    </div>
                  </div>

                  <!-- TC Certificate Upload -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Transfer Certificate (TC)
                    </label>
                    <div class="mt-1 flex items-center gap-4">
                      <div class="relative">
                        <input
                          type="file"
                          id="tc_image"
                          ref="tcImageInput"
                          @change="handleTcImageChange"
                          class="hidden"
                          accept="image/*"
                        />
                        <button
                          type="button"
                          @click="$refs.tcImageInput.click()"
                          class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium"
                        >
                          <i class="fas fa-upload"></i>
                          Upload TC Certificate
                        </button>
                      </div>
                      <div v-if="form.tc_image || tcImagePreview" class="flex items-center gap-2">
                        <div class="w-16 h-16 bg-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                          <img 
                            v-if="tcImagePreview"
                            :src="tcImagePreview" 
                            alt="TC preview"
                            class="w-full h-full object-cover"
                          />
                          <div v-else-if="form.tc_image" class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-file text-gray-400 text-xl"></i>
                          </div>
                        </div>
                        <button
                          v-if="form.tc_image"
                          @click="removeTcImage"
                          class="text-red-500 hover:text-red-700"
                          type="button"
                        >
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Academic Information Section -->
              <div class="border-t pt-8">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <i class="fas fa-graduation-cap text-red-600"></i>
                  Academic Information
                </h4>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Session Selection -->
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">
                        Session
                      </label>
                      <v-select
                        v-model="form.academic_data.session_id"
                        :options="dropdownData.sessions"
                        :filterable="true"
                        :searchable="true"
                        label="session_name"
                        :reduce="session => session.id"
                        placeholder="Select session (optional)"
                      >
                        <template #option="option">
                          <div>
                            <div class="font-medium text-gray-800">
                              {{ option.session_name }}
                            </div>
                          </div>
                        </template>
                      </v-select>
                    </div>

                    
                    <!-- Section Selection -->
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">
                        Section
                      </label>
                      <v-select
                        v-model="form.academic_data.section_id"
                        :options="dropdownData.sections"
                        :filterable="true"
                        :searchable="true"
                        label="section_name"
                        :reduce="section => section.id"
                        placeholder="Select section (optional)"
                      >
                        <template #option="option">
                          <div>
                            <div class="font-medium text-gray-800">
                              {{ option.section_name }}
                            </div>
                          </div>
                        </template>
                      </v-select>
                    </div>

                    <!-- Shift Selection -->
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">
                        Shift
                      </label>
                      <v-select
                        v-model="form.academic_data.shift_id"
                        :options="dropdownData.shifts"
                        :filterable="true"
                        :searchable="true"
                        label="shift_name"
                        :reduce="shift => shift.id"
                        placeholder="Select shift (optional)"
                      >
                        <template #option="option">
                          <div>
                            <div class="font-medium text-gray-800">
                              {{ option.shift_name }}
                            </div>
                            <div v-if="option.start_time" class="text-xs text-gray-500 mt-1">
                              Time: {{ option.start_time }} - {{ option.end_time }}
                            </div>
                          </div>
                        </template>
                      </v-select>
                    </div>

                    <!-- Version Selection -->
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">
                        Version
                      </label>
                      <v-select
                        v-model="form.academic_data.version_id"
                        :options="dropdownData.versions"
                        :filterable="true"
                        :searchable="true"
                        label="version_name"
                        :reduce="version => version.id"
                        placeholder="Select version (optional)"
                      >
                        <template #option="option">
                          <div>
                            <div class="font-medium text-gray-800">
                              {{ option.version_name }}
                            </div>
                          </div>
                        </template>
                      </v-select>
                    </div>

                    <!-- Class Selection -->
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-gray-700">
                        Class
                      </label>
                      <v-select
                        v-model="form.academic_data.class_id"
                        :options="dropdownData.classes"
                        :filterable="true"
                        :searchable="true"
                        label="class_name"
                        :reduce="classItem => classItem.id"
                        placeholder="Select class (optional)"
                      >
                        <template #option="option">
                          <div>
                            <div class="font-medium text-gray-800">
                              {{ option.class_name }}
                            </div>
                          </div>
                        </template>
                      </v-select>
                    </div>

                    <!-- Class Roll -->
                    <div class="space-y-2">
                      <label for="class_roll" class="block text-sm font-medium text-gray-700">
                        Class Roll
                      </label>
                      <input
                        type="number"
                        id="class_roll"
                        v-model="form.academic_data.class_roll"
                        min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                        placeholder="Enter class roll (optional)"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Form Actions -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-end gap-3">
              <!-- Cancel Button -->
              <router-link
                :to="{ name: 'student-profiles.index' }"
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
                {{ loading ? 'Creating...' : 'Create Student' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <!-- Student Summary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-user-graduate text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Student Summary</h3>
              <p class="text-gray-600 text-sm">Admission overview</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Student Photo Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Student Photo
              </label>
              <div class="flex justify-center">
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200">
                  <img 
                    v-if="studentImagePreview"
                    :src="studentImagePreview" 
                    alt="Student preview"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-user text-gray-400 text-3xl"></i>
                  </div>
                </div>
              </div>
            </div>

            <!-- Student Name Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Student Name
              </label>
              <div class="p-3 bg-gray-50 rounded-lg">
                <p class="text-lg font-bold text-gray-800">
                  {{ form.student_name || 'Not entered' }}
                </p>
              </div>
            </div>

            <!-- Personal Info Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Personal Information
              </label>
              <div class="space-y-2">
                <div v-if="form.religion" class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Religion</span>
                  <span class="text-sm font-medium text-gray-800">{{ form.religion }}</span>
                </div>
                <div v-if="form.nationality" class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Nationality</span>
                  <span class="text-sm font-medium text-gray-800">{{ form.nationality }}</span>
                </div>
                <div v-if="form.birth_certificate_number" class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Birth Certificate</span>
                  <span class="text-sm font-medium text-gray-800">{{ form.birth_certificate_number }}</span>
                </div>
                <div v-if="!form.date_of_birth && !form.religion && !form.nationality" class="text-center text-gray-500 text-sm py-2">
                  No personal info added
                </div>
              </div>
            </div>

            <!-- Address Info Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Address Information
              </label>
              <div class="space-y-2">
                <div v-if="selectedPresentDistrict" class="flex items-center justify-between p-2 bg-blue-50 rounded">
                  <span class="text-sm text-blue-800 font-medium">Present District</span>
                  <span class="text-sm text-blue-600">{{ selectedPresentDistrict.name }}</span>
                </div>
                <div v-if="selectedPresentUpazila" class="flex items-center justify-between p-2 bg-green-50 rounded">
                  <span class="text-sm text-green-800 font-medium">Present Upazila</span>
                  <span class="text-sm text-green-600">{{ selectedPresentUpazila.name }}</span>
                </div>
                <div v-if="selectedPermanentDistrict" class="flex items-center justify-between p-2 bg-purple-50 rounded">
                  <span class="text-sm text-purple-800 font-medium">Permanent District</span>
                  <span class="text-sm text-purple-600">{{ selectedPermanentDistrict.name }}</span>
                </div>
                <div v-if="selectedPermanentUpazila" class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                  <span class="text-sm text-yellow-800 font-medium">Permanent Upazila</span>
                  <span class="text-sm text-yellow-600">{{ selectedPermanentUpazila.name }}</span>
                </div>
                <div v-if="!selectedPresentDistrict && !selectedPermanentDistrict" class="text-center text-gray-500 text-sm py-2">
                  No address info added
                </div>
              </div>
            </div>

            <!-- Academic Info Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Academic Information
              </label>
              <div class="space-y-2">
                <div v-if="selectedClass" class="flex items-center justify-between p-2 bg-green-50 rounded">
                  <span class="text-sm text-green-800 font-medium">Class</span>
                  <span class="text-sm text-green-600">{{ selectedClass.class_name }}</span>
                </div>
                <div v-if="selectedShift" class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                  <span class="text-sm text-yellow-800 font-medium">Shift</span>
                  <span class="text-sm text-yellow-600">{{ selectedShift.shift_name }}</span>
                </div>
                <div v-if="selectedSection" class="flex items-center justify-between p-2 bg-blue-50 rounded">
                  <span class="text-sm text-blue-800 font-medium">Section</span>
                  <span class="text-sm text-blue-600">{{ selectedSection.section_name }}</span>
                </div>
                <div v-if="selectedSession" class="flex items-center justify-between p-2 bg-purple-50 rounded">
                  <span class="text-sm text-purple-800 font-medium">Session</span>
                  <span class="text-sm text-purple-600">{{ selectedSession.session_name }}</span>
                </div>
                <div v-if="form.academic_data.class_roll" class="flex items-center justify-between p-2 bg-pink-50 rounded">
                  <span class="text-sm text-pink-800 font-medium">Class Roll</span>
                  <span class="text-sm text-pink-600">{{ form.academic_data.class_roll }}</span>
                </div>
                <div v-if="!selectedClass && !selectedShift && !selectedSection && !selectedSession && !form.academic_data.class_roll" class="text-center text-gray-500 text-sm py-2">
                  No academic info added
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Tips -->
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
                <li>• Only student name is required (*)</li>
                <li>• All other fields are optional</li>
                <li>• You can add more details later</li>
                <li>• Upload clear student photo</li>
                <li>• Academic info can be added later</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()

// Form data
const form = reactive({
  student_name: '',
  id_card_number: '',
  student_image: null,
  date_of_birth: '',
  gender: '',
  religion: '',
  birth_certificate_number: '',
  nationality: '',
  blood_group: '',
  mobile_number: '',
  email: '',
  father_name: '',
  father_profession: '',
  father_mobile_number: '',
  mother_name: '',
  mother_profession: '',
  mother_mobile_number: '',
  local_guardian_name: '',
  local_guardian_profession: '',
  local_guardian_mobile_number: '',
  present_village: '',
  present_post_office: '',
  present_upazila_id: '',
  present_district_id: '',
  permanent_village: '',
  permanent_post_office: '',
  permanent_upazila_id: '',
  permanent_district_id: '',
  previous_institute_name: '',
  previous_institute_class: '',
  previous_institute_section: '',
  previous_institute_roll: '',
  previous_institute_result: '',
  tc_image: null,
  
  academic_data: {
    class_id: null,
    shift_id: null,
    version_id: null,
    session_id: null,
    section_id: null,
    class_roll: null
  }
})

// Dropdown data
const dropdownData = reactive({
  classes: [],
  shifts: [],
  versions: [],
  sessions: [],
  sections: []
})

// District and Upazila data
const districts = ref([])
const presentUpazilas = ref([])
const permanentUpazilas = ref([])

// Image previews
const studentImagePreview = ref(null)
const tcImagePreview = ref(null)

// School ID
const nextStudentId = ref('')
const loadingNextId = ref(false)

// fetchNextStudentId 
const fetchNextStudentId = async () => {
  loadingNextId.value = true
  try {
    const response = await axios.get('/api/school-settings/next-student-id')
    if (response.data.success) {
      nextStudentId.value = response.data.student_id
      form.id_card_number = response.data.student_id
    }
  } catch (error) {
    console.error('Failed to fetch next student ID:', error)
    showErrorAlert('Error', 'Could not generate student ID. You can enter manually.')
  } finally {
    loadingNextId.value = false
  }
}

// Selected items for display
const selectedClass = computed(() => {
  return dropdownData.classes.find(cls => cls.id === form.academic_data.class_id) || null
})

const selectedShift = computed(() => {
  return dropdownData.shifts.find(shift => shift.id === form.academic_data.shift_id) || null
})

const selectedVersion = computed(() => {
  return dropdownData.versions.find(ver => ver.id === form.academic_data.version_id) || null
})

const selectedSession = computed(() => {
  return dropdownData.sessions.find(sess => sess.id === form.academic_data.session_id) || null
})

const selectedSection = computed(() => {
  return dropdownData.sections.find(sect => sect.id === form.academic_data.section_id) || null
})

const selectedPresentDistrict = computed(() => {
  return districts.value.find(district => district.id === parseInt(form.present_district_id)) || null
})

const selectedPresentUpazila = computed(() => {
  return presentUpazilas.value.find(upazila => upazila.id === parseInt(form.present_upazila_id)) || null
})

const selectedPermanentDistrict = computed(() => {
  return districts.value.find(district => district.id === parseInt(form.permanent_district_id)) || null
})

const selectedPermanentUpazila = computed(() => {
  return permanentUpazilas.value.find(upazila => upazila.id === parseInt(form.permanent_upazila_id)) || null
})

// Address check box
const sameAsPresentAddress = ref(false)

const copyPresentToPermanent = () => {
  if (sameAsPresentAddress.value) {
    form.permanent_village = form.present_village
    form.permanent_post_office = form.present_post_office
    form.permanent_district_id = form.present_district_id
    form.permanent_upazila_id = form.present_upazila_id
    
    if (form.present_district_id) {
      if (presentUpazilas.value.length > 0) {
        permanentUpazilas.value = [...presentUpazilas.value]
      } else {
        fetchUpazilasForPermanent(form.present_district_id)
      }
    }
  } else {
    // Clear permanent address if unchecked
    form.permanent_village = ''
    form.permanent_post_office = ''
    form.permanent_district_id = ''
    form.permanent_upazila_id = ''
    permanentUpazilas.value = []
  }
}

const fetchUpazilasForPermanent = async (districtId) => {
  if (!districtId) {
    permanentUpazilas.value = []
    return
  }

  try {
    const response = await axios.get(`/api/address/upazilas/${districtId}`)
    
    if (response.data.success) {
      const upazilas = response.data.data.map(upazila => ({
        ...upazila,
        display_name: `${upazila.name} (${upazila.bn_name})`
      }))
      
      permanentUpazilas.value = upazilas
    }
  } catch (error) {
    permanentUpazilas.value = []
  }
}

// Present upazila and districts chnaging
watch(() => form.present_district_id, (newDistrictId) => {
  if (sameAsPresentAddress.value && newDistrictId) {
    fetchUpazilasForPermanent(newDistrictId)
    form.permanent_upazila_id = form.present_upazila_id
  }
})

watch(() => form.present_upazila_id, (newUpazilaId) => {
  if (sameAsPresentAddress.value) {
    form.permanent_upazila_id = newUpazilaId
  }
})

// Loading and errors
const loading = ref(false)
const errors = ref({})

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

// Image handling
const handleStudentImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.student_image = file
    const reader = new FileReader()
    reader.onload = (e) => {
      studentImagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeStudentImage = () => {
  form.student_image = null
  studentImagePreview.value = null
}

const handleTcImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.tc_image = file
    const reader = new FileReader()
    reader.onload = (e) => {
      tcImagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeTcImage = () => {
  form.tc_image = null
  tcImagePreview.value = null
}

// Fetch districts
const fetchDistricts = async () => {
  try {
    const response = await axios.get('/api/address/districts')
    
    if (response.data.success) {
      districts.value = response.data.data.map(district => ({
        ...district,
        display_name: `${district.name} (${district.bn_name})`
      }))
    }
  } catch (error) {
    showErrorAlert('Error', 'Failed to load district data')
  }
}

// Fetch upazilas by district
const fetchUpazilasByDistrict = async (districtId, type = 'present') => {
  if (!districtId) {
    if (type === 'present') {
      presentUpazilas.value = []
      if (sameAsPresentAddress.value) {
        permanentUpazilas.value = []
      }
    } else {
      permanentUpazilas.value = []
    }
    return
  }

  try {
    const response = await axios.get(`/api/address/upazilas/${districtId}`)
    
    if (response.data.success) {
      const upazilas = response.data.data.map(upazila => ({
        ...upazila,
        display_name: `${upazila.name} (${upazila.bn_name})`
      }))
      
      if (type === 'present') {
        presentUpazilas.value = upazilas
        
        if (sameAsPresentAddress.value && form.present_district_id === districtId) {
          permanentUpazilas.value = [...upazilas]
        }
      } else {
        permanentUpazilas.value = upazilas
      }
    }
  } catch (error) {
    if (type === 'present') {
      presentUpazilas.value = []
    } else {
      permanentUpazilas.value = []
    }
  }
}

// Fetch dropdown data
const fetchDropdownData = async () => {
  try {
    try {
      const studentResponse = await axios.get('/api/student-profiles/dropdown/data')
      
      if (studentResponse.data?.success) {
        dropdownData.classes = studentResponse.data.data?.classes || []
        dropdownData.versions = studentResponse.data.data?.versions || []
        dropdownData.sessions = studentResponse.data.data?.sessions || []
        dropdownData.sections = studentResponse.data.data?.sections || []
        dropdownData.shifts = studentResponse.data.data?.shifts || []
        return
      }
    } catch {
      // Fallback to Voucher endpoint
      const voucherResponse = await axios.get('/api/vouchers/dropdown/data')
      
      if (voucherResponse.data) {
        dropdownData.classes = voucherResponse.data.data?.classes || voucherResponse.data.classes || []
        dropdownData.versions = voucherResponse.data.data?.versions || voucherResponse.data.versions || []
        dropdownData.sessions = voucherResponse.data.data?.sessions || voucherResponse.data.sessions || []
        dropdownData.sections = voucherResponse.data.data?.sections || voucherResponse.data.sections || []
        
        // Fetch shifts separately
        try {
          const shiftsResponse = await axios.get('/api/shift-managements')
          if (shiftsResponse.data?.success) {
            dropdownData.shifts = shiftsResponse.data.data || []
          }
        } catch {}
      }
    }
    
  } catch (error) {
    showErrorAlert('Error', 'Failed to load academic data. Some fields may be empty.')
  }
}

// Form validation 
const validateForm = () => {
  const newErrors = {}

  if (!form.student_name || form.student_name.trim() === '') {
    newErrors.student_name = ['Student name is required']
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

// Helper method to display value correctly
const displayValue = (value) => {
  if (!value) return ''
  return typeof value === 'object' ? value.label || value.value : value
}

// Submit form
const submitForm = async () => {
  if (!validateForm()) {
    showErrorAlert('Validation Error', 'Student name is required')
    return
  }
  if (!form.nationality) {
    form.nationality = 'Bangladeshi'
  }

  loading.value = true

  try {
    showLoadingAlert('Creating student admission...')
    
    const formData = new FormData()
    
    // Append all form fields
    Object.keys(form).forEach(key => {
      if (key === 'academic_data') {
        // Check if academic_data has any value
        const hasAcademicData = Object.values(form.academic_data).some(
          value => value !== null && value !== '' && value !== undefined
        )
        
        if (hasAcademicData) {
          Object.keys(form.academic_data).forEach(subKey => {
            const value = form.academic_data[subKey]
            if (value !== null && value !== '' && value !== undefined) {
              formData.append(`academic_data[${subKey}]`, value)
            }
          })
        }
      } else if (form[key] !== null && form[key] !== '' && form[key] !== undefined) {
        // Handle files separately
        if (key === 'student_image' || key === 'tc_image') {
          formData.append(key, form[key])
        } else {
          formData.append(key, form[key])
        }
      }
    })

    const response = await axios.post('/api/student-profiles', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    // Show success message with user credentials if available
    if (response.data.success) {
      closeAlert()

      router.push({ 
        name: 'student-profiles.index',
        query: { created: 'true' }
      })
    }
    
  } catch (error) {
    closeAlert()
    loading.value = false
    
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to create student admission. Please try again.')
    }
  }
}
// Dropdown options
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

const religionOptions = ref([
  { label: 'Islam', value: 'Islam' },
  { label: 'Hindu', value: 'Hindu' },
  { label: 'Christian', value: 'Christian' },
  { label: 'Buddhist', value: 'Buddhist' },
  { label: 'Other', value: 'Other' }
])

const nationalityOptions = ref([
  { label: 'Bangladeshi', value: 'Bangladeshi' },
  { label: 'Indian', value: 'Indian' },
  { label: 'Pakistani', value: 'Pakistani' },
  { label: 'American', value: 'American' },
  { label: 'British', value: 'British' },
  { label: 'Canadian', value: 'Canadian' }
])

// Custom value handlers
const addCustomBloodGroup = (newTag) => {
  const tag = {
    label: newTag,
    value: newTag,
    isCustom: true
  }
  bloodGroupOptions.value.push(tag)
  form.blood_group = newTag
}

const addCustomReligion = (newTag) => {
  const tag = {
    label: newTag,
    value: newTag,
    isCustom: true
  }
  religionOptions.value.push(tag)
  form.religion = newTag
}

const addCustomNationality = (newTag) => {
  const tag = {
    label: newTag,
    value: newTag,
    isCustom: true
  }
  nationalityOptions.value.push(tag)
  form.nationality = newTag
}
onMounted(() => {
  form.nationality = 'Bangladeshi'
})

watch(() => form.nationality, (newVal) => {
  if (!newVal) {
    form.nationality = 'Bangladeshi'
  }
})

// Watch for district changes
watch(() => form.present_district_id, (newDistrictId, oldDistrictId) => {
  // Clear upazila when district changes
  form.present_upazila_id = ''
  
  if (newDistrictId) {
    fetchUpazilasByDistrict(newDistrictId, 'present')
  } else {
    presentUpazilas.value = []
    if (sameAsPresentAddress.value) {
      permanentUpazilas.value = []
    }
  }
})

watch(() => form.permanent_district_id, (newDistrictId, oldDistrictId) => {
  if (!sameAsPresentAddress.value) {
    form.permanent_upazila_id = ''
    
    if (newDistrictId) {
      fetchUpazilasByDistrict(newDistrictId, 'permanent')
    } else {
      permanentUpazilas.value = []
    }
  }
})

// Initialize
onMounted(() => {
  fetchDropdownData()
  fetchDistricts()
  fetchNextStudentId()
})
</script>

<style scoped>
/* Custom styles for form elements */
.line-clamp-1 {
  display: -webkit-box;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
/* Section borders */
.border-t {
  border-top-width: 1px;
}

.pt-8 {
  padding-top: 2rem;
}
</style>