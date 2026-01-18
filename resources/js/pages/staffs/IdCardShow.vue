<template>
  <AppLayout>
    <!-- Header with Print Button -->
    <div class="mb-4 flex justify-between items-center print:hidden">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ staff?.is_teachers ? 'Teacher' : 'Staff' }} ID Card</h1>
        <p class="text-sm text-gray-600 mt-1">{{ staff?.first_name }} {{ staff?.last_name }}</p>
      </div>
      <div class="flex gap-2">
        <button 
          @click="printCard"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2 cursor-pointer"
        >
          <i class="fas fa-print"></i>
          Print ID Card
        </button>
        <router-link 
          :to="{ name: 'staffs.index' }"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
        >
          <i class="fas fa-arrow-left mr-2"></i>Back
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading || templateLoading" class="flex justify-center items-center py-20">
      <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
    </div>

    <!-- ID Card Container -->
    <div v-else-if="staff" class="flex flex-col items-center gap-8 print:flex-row print:gap-4 print:justify-center">
      <!-- Front Side -->
      <div class="id-card">
        <div 
          class="id-card-inner front"
          :style="cardBackgroundStyle"
        >
          <!-- School Logo -->
          <div class="logo-container">
            <img 
              :src="schoolLogo" 
              :alt="schoolSettings?.school_name" 
              class="school-logo"
            />
          </div>

          <!-- School Name -->
          <h2 class="school-name">{{ schoolSettings?.school_name || 'SCHOOL NAME' }}</h2>
          
          <!-- ID Card Title -->
          <div class="card-title text-center">{{ getDesignationName(staff) }} ID CARD</div>

          <!-- Staff Photo -->
          <div class="staff-photo-container">
            <img 
              v-if="staff.image_url"
              :src="staff.image_url" 
              :alt="staff.first_name + ' ' + staff.last_name"
              class="staff-photo"
            >
            <div v-else class="staff-photo-placeholder">
              <i class="fas fa-user text-4xl text-gray-400"></i>
            </div>
          </div>

          <!-- Staff Details -->
          <div class="staff-details">
            <div class="detail-row">
              <span class="detail-label">Name:</span>
              <span class="detail-value">{{ staff.first_name }} {{ staff.last_name }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Designation:</span>
              <span class="detail-value">{{ getDesignationName(staff) }}</span>
            </div>
            <!-- <div class="detail-row">
              <span class="detail-label">Type:</span>
              <span class="detail-value">{{ staff.is_teachers ? 'Teacher' : "Other's Staff" }}</span>
            </div> -->
            <div class="detail-row">
              <span class="detail-label">Qualification:</span>
              <span class="detail-value">{{ staff.qualification || 'N/A' }}</span>
            </div>
            <div class="detail-row" v-if="staff.phone">
              <span class="detail-label">Phone:</span>
              <span class="detail-value">{{ staff.phone }}</span>
            </div>
            <div class="detail-row" v-if="staff.email">
              <span class="detail-label">Email:</span>
              <span class="detail-value">{{ staff.email }}</span>
            </div>
          </div>

          <!-- Principal Signature -->
          <div class="signature">
            <div class="signature-line"></div>
            <p class="signature-text">Principal's Signature</p>
          </div>
        </div>
      </div>

      <!-- Back Side -->
      <div class="id-card">
        <div class="id-card-inner back">
          <!-- Lost Card Notice -->
          <div class="lost-notice">
            <p class="lost-text">
              If this identity card is lost, it is kindly requested to return it to the Principal of
            </p>
          </div>

          <!-- School Logo -->
          <div class="logo-container-back">
            <img 
              :src="schoolLogo" 
              :alt="schoolSettings?.school_name" 
              class="school-logo-back"
            />
          </div>

          <!-- School Name -->
          <h3 class="school-name-back">{{ schoolSettings?.school_name || 'SCHOOL NAME' }}</h3>

          <!-- School Details -->
          <div class="school-details">
            <div class="school-detail-row" v-if="schoolSettings?.address">
              <i class="fas fa-map-marker-alt icon"></i>
              <span>{{ schoolSettings.address }}</span>
            </div>
            <div class="school-detail-row" v-if="schoolSettings?.mobile_number">
              <i class="fas fa-phone icon"></i>
              <span>{{ schoolSettings.mobile_number }}</span>
            </div>
            <div class="school-detail-row" v-if="schoolSettings?.email">
              <i class="fas fa-envelope icon"></i>
              <span>{{ schoolSettings.email }}</span>
            </div>
            <div class="school-detail-row">
              <i class="fas fa-globe icon"></i>
              <span>www.iqramodelschool.edu.com</span>
            </div>
          </div>

          <!-- Emergency Contact -->
          <div class="emergency-section">
            <p class="emergency-title">In Case of Emergency</p>
            <p class="emergency-contact" v-if="staff.phone">
              Contact: {{ staff.phone }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-20">
      <i class="fas fa-exclamation-circle text-5xl text-red-500 mb-4"></i>
      <p class="text-gray-600">Staff not found</p>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { useSchoolStore } from '@/stores/schoolStore'
import { showErrorAlert } from '../../utils/sweetAlert'

const route = useRoute()
const schoolStore = useSchoolStore()

const staff = ref(null)
const loading = ref(true)
const templateLoading = ref(true)
const cardTemplate = ref(null)

// Fetch school settings
onMounted(async () => {
  await schoolStore.fetchSchoolSettings()
  await fetchCardTemplate()
  await fetchStaff()
})

// Computed properties
const schoolSettings = computed(() => schoolStore.settings)

const schoolLogo = computed(() => {
  if (schoolSettings.value?.logo_url) {
    return schoolSettings.value.logo_url
  }
  return '/assets/favicon.png'
})

// Dynamic card background style
const cardBackgroundStyle = computed(() => {
  if (cardTemplate.value?.teacher_image_url) {
    return {
      backgroundImage: `url(${cardTemplate.value.teacher_image_url})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat'
    }
  }
  // Fallback to default image
  return {
    backgroundImage: 'url(/assets/img/teacher_id_card_bg.png)',
    backgroundSize: 'cover',
    backgroundPosition: 'center',
    backgroundRepeat: 'no-repeat'
  }
})

// Fetch card template
const fetchCardTemplate = async () => {
  try {
    templateLoading.value = true
    const response = await axios.get('/api/card-templates')
    
    if (response.data.success && response.data.data.length > 0) {
      cardTemplate.value = response.data.data[0]
    }
  } catch (error) {
    console.error('Error fetching card template:', error)
  } finally {
    templateLoading.value = false
  }
}

// Fetch staff data
const fetchStaff = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/staffs/${route.params.id}`)
    
    if (response.data.success) {
      staff.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching staff:', error)
    showErrorAlert('Error', 'Failed to load staff data')
  } finally {
    loading.value = false
  }
}

// Helper methods
const getDesignationName = (staff) => {
  if (staff.designation) {
    if (typeof staff.designation === 'object' && staff.designation.name) {
      return staff.designation.name
    }
    if (staff.designation_relation && staff.designation_relation.name) {
      return staff.designation_relation.name
    }
  }
  return 'N/A'
}

// Print function
const printCard = () => {
  window.print()
}
</script>

<style scoped>
/* ATM Card Size Portrait: 53.98mm x 85.6mm (2.125" x 3.375") */
.id-card {
  width: 53.98mm;
  height: 85.6mm;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
  color-adjust: exact !important;
}

.id-card-inner {
  width: 100%;
  height: 100%;
  padding: 7px;
  position: relative;
}

/* Front Side Styles */
.front {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  /* background-image: url('/assets/img/teacher_id_card_bg.png'); */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
  color-adjust: exact !important;
}

.front::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.05); 
  z-index: 0;
}

.front > * {
  position: relative;
  z-index: 1;
}

.logo-container {
  margin-bottom: 6px;
}

.school-logo {
  height: 40px;
  width: auto;
  object-fit: contain;
}

.school-name {
  font-size: 13px;
  font-weight: bold;
  text-align: center;
  color: #fff;
  margin-top: -2px;
  margin-bottom: 6px;
  line-height: 1.2;
  padding: 0 4px;
}

.card-title {
  font-size: 7px;
  font-weight: bold;
  color: #fff;
  margin-bottom: 16px;
  text-transform: uppercase;
}

.staff-photo-container {
  width: 90px;
  height: 90px;
  border: 1px solid #0f4c1d;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 4px;
  background: #f3f4f6;
}

.staff-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.staff-photo-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #e5e7eb;
}

.staff-details {
  width: 100%;
  font-size: 8px;
  line-height: 1;
  padding: 0 4px;
}

.detail-row {
  display: flex;
  margin-bottom: 3px;
  padding: 1px 4px;
}

.detail-label {
  font-weight: 600;
  color: #000000;
  width: 65px;
  flex-shrink: 0;
}

.detail-value {
  color: #000000;
  font-weight: 500;
  flex: 1;
  word-break: break-word;
}

.signature {
  position: absolute;
  bottom: 4px;
  right: 14px;
  text-align: center;
}

.signature-line {
  width: auto;
  height: 1px;
  background: #393d44;
  margin-bottom: 2px;
}

.signature-text {
  font-size: 7px;
  color: #fff;
}

/* Back Side Styles */
.back {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 12px 14px;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
  color-adjust: exact !important;
}

.lost-notice {
  margin-bottom: 10px;
  text-align: center;
}

.lost-text {
  font-size: 7.5px;
  color: #374151;
  line-height: 1.5;
  font-weight: 500;
}

.logo-container-back {
  margin-bottom: 6px;
}

.school-logo-back {
  height: 26px;
  width: auto;
  object-fit: contain;
}

.school-name-back {
  font-size: 10px;
  font-weight: bold;
  text-align: center;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.2;
  padding: 0 4px;
}

.school-details {
  width: 100%;
  margin-bottom: 8px;
}

.school-detail-row {
  display: flex;
  align-items: center;
  font-size: 7.5px;
  color: #374151;
  margin-bottom: 4px;
  line-height: 1.4;
}

.school-detail-row .icon {
  width: 12px;
  color: #667eea;
  margin-right: 5px;
  flex-shrink: 0;
  font-size: 7px;
}

.emergency-section {
  width: 100%;
  margin-top: auto;
  padding-top: 6px;
  border-top: 1px solid #cbd5e1;
  text-align: center;
}

.emergency-title {
  font-size: 7.5px;
  font-weight: 600;
  color: #dc2626;
  margin-bottom: 3px;
}

.emergency-contact {
  font-size: 8px;
  color: #374151;
  font-weight: 600;
}

/* Print Styles */
@media print {
  * {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color-adjust: exact !important;
  }

  body {
    margin: 0;
    padding: 0;
  }

  .id-card {
    box-shadow: none;
    margin: 0;
    page-break-inside: avoid;
  }

  /* Hide non-printable elements */
  .print\:hidden {
    display: none !important;
  }

  .print\:flex-row {
    display: flex !important;
    flex-direction: row !important;
  }

  .print\:gap-4 {
    gap: 4mm !important;
  }

  .print\:justify-center {
    justify-content: center !important;
  }

  /* Page setup for side by side printing */
  @page {
    margin: 0.5cm;
    size: A4 portrait !important;
  }
}
</style>