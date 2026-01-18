<template>
  <div class="flex flex-col items-center gap-8 print:flex-row print:gap-4 print:justify-center">
    <!-- Front Side -->
    <div class="id-card">
      <div 
        class="id-card-inner front"
        :style="frontBackground"
      >
        <!-- Content overlay -->
        <div class="content-overlay">
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
          <div class="card-title">{{ cardTitle }} ID CARD</div>

          <!-- Person Photo -->
          <div class="person-photo-container">
            <img 
              v-if="personImage"
              :src="personImage" 
              :alt="personName"
              class="person-photo"
            >
            <div v-else class="person-photo-placeholder">
              <i class="fas fa-user text-4xl text-gray-400"></i>
            </div>
          </div>

          <!-- Person Details -->
          <div class="person-details">
            <div class="detail-row">
              <span class="detail-label">Name:</span>
              <span class="detail-value">{{ personName }}</span>
            </div>
            <div class="detail-row" v-if="designation">
              <span class="detail-label">Designation:</span>
              <span class="detail-value">{{ designation }}</span>
            </div>
            <div class="detail-row" v-if="classInfo">
              <span class="detail-label">Class:</span>
              <span class="detail-value">{{ classInfo }}</span>
            </div>
            <div class="detail-row" v-if="rollNumber">
              <span class="detail-label">Roll:</span>
              <span class="detail-value">{{ rollNumber }}</span>
            </div>
            <div class="detail-row" v-if="qualification">
              <span class="detail-label">Qualification:</span>
              <span class="detail-value">{{ qualification }}</span>
            </div>
            <div class="detail-row" v-if="phone">
              <span class="detail-label">Phone:</span>
              <span class="detail-value">{{ phone }}</span>
            </div>
          </div>

          <!-- Signature -->
          <div class="signature">
            <div class="signature-line"></div>
            <p class="signature-text">Principal's Signature</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Back Side -->
    <div class="id-card">
      <div class="id-card-inner back">
        <!-- Lost Notice -->
        <div class="lost-notice">
          <p class="lost-text">
            If this identity card is lost, please return it to the Principal of
          </p>
        </div>

        <!-- School Details -->
        <div class="logo-container-back">
          <img :src="schoolLogo" :alt="schoolSettings?.school_name" class="school-logo-back" />
        </div>

        <h3 class="school-name-back">{{ schoolSettings?.school_name }}</h3>

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
        </div>

        <!-- Emergency Contact -->
        <div class="emergency-section">
          <p class="emergency-title">In Case of Emergency</p>
          <p class="emergency-contact" v-if="emergencyPhone">
            Contact: {{ emergencyPhone }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useSchoolStore } from '@/stores/schoolStore'
import { showErrorAlert } from '@/utils/sweetAlert'

const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => ['teacher', 'student'].includes(value)
  },
  person: {
    type: Object,
    required: true
  }
})

const schoolStore = useSchoolStore()
const cardTemplate = ref(null)
const loading = ref(true)

// Computed properties
const schoolSettings = computed(() => schoolStore.settings)

const schoolLogo = computed(() => {
  return schoolSettings.value?.logo_url || '/assets/favicon.png'
})

const frontBackground = computed(() => {
  if (!cardTemplate.value) return {}
  
  const imageUrl = props.type === 'teacher' 
    ? cardTemplate.value.teacher_image_url 
    : cardTemplate.value.student_image_url
    
  if (imageUrl) {
    return {
      backgroundImage: `url(${imageUrl})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat'
    }
  }
  return {}
})

const cardTitle = computed(() => {
  if (props.type === 'teacher') {
    return props.person.designation?.name || 'TEACHER'
  }
  return 'STUDENT'
})

const personName = computed(() => {
  if (props.type === 'teacher') {
    return `${props.person.first_name} ${props.person.last_name}`
  }
  return props.person.student_name
})

const personImage = computed(() => {
  if (props.type === 'teacher') {
    return props.person.image_url
  }
  return props.person.student_image_url
})

const designation = computed(() => {
  if (props.type === 'teacher') {
    return props.person.designation?.name || null
  }
  return null
})

const classInfo = computed(() => {
  if (props.type === 'student' && props.person.class_wise_data?.[0]) {
    return props.person.class_wise_data[0].class?.class_name
  }
  return null
})

const rollNumber = computed(() => {
  if (props.type === 'student' && props.person.class_wise_data?.[0]) {
    return props.person.class_wise_data[0].class_roll
  }
  return null
})

const qualification = computed(() => {
  if (props.type === 'teacher') {
    return props.person.qualification
  }
  return null
})

const phone = computed(() => {
  if (props.type === 'teacher') {
    return props.person.phone
  }
  return props.person.mobile_number
})

const emergencyPhone = computed(() => {
  if (props.type === 'teacher') {
    return props.person.phone
  }
  return props.person.father_mobile_number || props.person.mother_mobile_number
})

// Load template from database
const loadCardTemplate = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/card-templates')
    
    if (response.data.success && response.data.data.length > 0) {
      cardTemplate.value = response.data.data[0]
    }
  } catch (error) {
    console.error('Error loading card template:', error)
    showErrorAlert('Error', 'Failed to load ID card template')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await schoolStore.fetchSchoolSettings()
  await loadCardTemplate()
})
</script>

<style scoped>
.id-card {
  width: 53.98mm;
  height: 85.6mm;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
}

.id-card-inner {
  width: 100%;
  height: 100%;
  padding: 7px;
  position: relative;
}

.front {
  position: relative;
}

.content-overlay {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100%;
}

.logo-container {
  margin-bottom: 6px;
}

.school-logo {
  height: 40px;
  width: auto;
}

.school-name {
  font-size: 13px;
  font-weight: bold;
  text-align: center;
  color: #fff;
  margin-bottom: 6px;
}

.card-title {
  font-size: 7px;
  font-weight: bold;
  color: #fff;
  margin-bottom: 8px;
  text-transform: uppercase;
}

.person-photo-container {
  width: 90px;
  height: 90px;
  border: 2px solid #0f4c1d;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 4px;
  background: #f3f4f6;
}

.person-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.person-photo-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.person-details {
  width: 100%;
  font-size: 8px;
  padding: 0 4px;
}

.detail-row {
  display: flex;
  margin-bottom: 3px;
  padding: 1px 4px;
}

.detail-label {
  font-weight: 600;
  color: #000;
  width: 65px;
  flex-shrink: 0;
}

.detail-value {
  color: #000;
  font-weight: 500;
  flex: 1;
}

.signature {
  position: absolute;
  bottom: 4px;
  right: 14px;
}

.signature-line {
  width: auto;
  height: 1px;
  background: #000;
  margin-bottom: 2px;
}

.signature-text {
  font-size: 7px;
  color: #fff;
}

/* Back side styles */
.back {
  padding: 12px 14px;
  background: #f8fafc;
}

.lost-notice, .school-details, .emergency-section {
  margin-bottom: 8px;
}

.lost-text, .school-detail-row {
  font-size: 7.5px;
  color: #374151;
}

.school-logo-back {
  height: 26px;
}

.school-name-back {
  font-size: 10px;
  font-weight: bold;
  margin-bottom: 8px;
}

.school-detail-row {
  display: flex;
  align-items: center;
  margin-bottom: 4px;
}

.school-detail-row .icon {
  width: 12px;
  color: #667eea;
  margin-right: 5px;
  font-size: 7px;
}

.emergency-section {
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

@media print {
  .id-card {
    box-shadow: none;
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

  @page {
    margin: 0.5cm;
    size: A4 portrait !important;
  }
}
</style>