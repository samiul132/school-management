<template>
  <AppLayout>
    <!-- Page Header -->
    <div class="mb-2">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-2">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Fee Assignment Details</h1>
          <p class="text-xs text-gray-600 mt-1">
            <i class="fas fa-info-circle"></i> View complete fee assignment information
          </p>
        </div>
        <div class="flex gap-2">
          <router-link 
            :to="`/fee-assigns/${feeAssignId}/edit`"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-300 rounded-lg text-white bg-blue-600 hover:bg-blue-700 font-medium transition-colors"
          >
            <i class="fas fa-edit"></i>
            Edit
          </router-link>
          <router-link 
            to="/fee-assigns"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-300 rounded-lg text-gray-100 bg-blue-600 hover:bg-blue-700 font-medium transition-colors"
          >
            <i class="fas fa-arrow-left"></i>
            Back To List
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-lg shadow border border-gray-100 p-6">
      <div class="flex flex-col items-center justify-center">
        <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-3"></i>
        <p class="text-sm text-gray-600">Loading...</p>
      </div>
    </div>

    <!-- Content -->
    <div v-else class="space-y-3">
      <!-- Basic Information Card -->
      <div class="bg-white rounded-lg shadow border border-gray-100 p-3">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
            <i class="fas fa-info-circle text-blue-600 text-sm"></i>
          </div>
          <h2 class="text-base font-bold text-gray-800">Basic Information</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
          <!-- Session -->
          <div class="p-2 bg-blue-50 rounded border border-blue-200">
            <p class="text-xs text-gray-600 mb-1">
              <i class="fas fa-calendar-alt"></i> Session
            </p>
            <p class="text-sm font-bold text-gray-800">{{ feeAssign.session?.session_name }}</p>
          </div>

          <!-- Class -->
          <div class="p-2 bg-green-50 rounded border border-green-200">
            <p class="text-xs text-gray-600 mb-1">
              <i class="fas fa-school"></i> Class
            </p>
            <p class="text-sm font-bold text-gray-800">{{ feeAssign.class?.class_name }}</p>
          </div>

          <!-- Version -->
          <div class="p-2 bg-purple-50 rounded border border-purple-200">
            <p class="text-xs text-gray-600 mb-1">
              <i class="fas fa-book"></i> Version
            </p>
            <p class="text-sm font-bold text-gray-800">{{ feeAssign.version?.version_name }}</p>
          </div>

          <!-- Month (Single) -->
          <div class="p-2 bg-orange-50 rounded border border-orange-200">
            <p class="text-xs text-gray-600 mb-1">
              <i class="fas fa-calendar-day"></i> Month
            </p>
            <p class="text-sm font-bold text-gray-800">{{ feeAssign.month?.month_name }}</p>
          </div>
        </div>

        <!-- Total Amount Summary -->
        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
          <!-- Total Waver -->
          <div class="p-2 bg-orange-50 rounded border border-orange-200">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full bg-orange-600 flex items-center justify-center">
                <i class="fas fa-percent text-white text-xs"></i>
              </div>
              <div>
                <p class="text-xs text-gray-600">Total Waver</p>
                <p class="text-lg font-bold text-orange-600">৳{{ formatCurrency(calculateTotalWaver()) }}</p>
              </div>
            </div>
          </div>

          <!-- Total Payable -->
          <div class="p-2 bg-green-50 rounded border border-green-200">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full bg-green-600 flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-white text-xs"></i>
              </div>
              <div>
                <p class="text-xs text-gray-600">Total Payable</p>
                <p class="text-lg font-bold text-green-600">৳{{ formatCurrency(feeAssign.total_amount) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Fee Heads Card -->
      <div class="bg-white rounded-lg shadow border border-gray-100 p-3">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
            <i class="fas fa-file-invoice-dollar text-green-600 text-sm"></i>
          </div>
          <h2 class="text-base font-bold text-gray-800">Fee Heads ({{ feeAssign.details?.length || 0 }})</h2>
        </div>

        <!-- Fee Heads Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-12">
                  SL
                </th>
                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                  Fee Head Name
                </th>
                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">
                  Amount (৳)
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr 
                v-for="(detail, index) in feeAssign.details" 
                :key="detail.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-2 py-2 whitespace-nowrap">
                  <div class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100">
                    <span class="text-xs font-medium text-gray-700">{{ index + 1 }}</span>
                  </div>
                </td>
                
                <td class="px-2 py-2">
                  <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded bg-green-100 flex items-center justify-center">
                      <i class="fas fa-file-invoice-dollar text-green-600 text-xs"></i>
                    </div>
                    <div>
                      <div class="text-xs font-semibold text-gray-900">{{ detail.head?.head_name }}</div>
                    </div>
                  </div>
                </td>
                
                <td class="px-2 py-2 text-right whitespace-nowrap">
                  <span class="text-sm font-bold text-green-600">৳{{ formatCurrency(detail.amount) }}</span>
                </td>
              </tr>
            </tbody>
            
            <!-- Total Footer -->
            <tfoot class="bg-gradient-to-r from-indigo-50 to-purple-50 border-t-2 border-indigo-200">
              <tr>
                <td colspan="2" class="px-2 py-2 text-right text-sm font-bold text-gray-800">
                  <i class="fas fa-calculator mr-2 text-indigo-600"></i>
                  Total Amount:
                </td>
                <td class="px-2 py-2 text-right text-sm font-bold text-green-600">
                  ৳{{ formatCurrency(calculateTotalAmount()) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- No Fee Heads Message -->
        <div v-if="!feeAssign.details || feeAssign.details.length === 0" class="text-center py-6">
          <i class="fas fa-file-invoice text-3xl text-gray-400 mb-2"></i>
          <p class="text-sm text-gray-600">No fee heads assigned</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showErrorAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()

// Data
const loading = ref(true)
const feeAssign = ref({})
const feeAssignId = ref(null)
const students = ref({})

// Methods
const loadFeeAssignData = async () => {
  try {
    loading.value = true
    const id = route.params.id
    feeAssignId.value = id

    const response = await axios.get(`/api/fee-assigns/${id}`)
    feeAssign.value = response.data.data.fee_assign
    students.value = response.data.data.students || {}
    
    console.log('Loaded fee assignment:', feeAssign.value)
    console.log('Students data:', students.value)
    
    loading.value = false
  } catch (error) {
    console.error('Failed to load fee assign data:', error)
    showErrorAlert('Error', 'Failed to load fee assignment details')
    router.push('/fee-assigns')
  }
}

const calculateTotalAmount = () => {
  if (!feeAssign.value.details || feeAssign.value.details.length === 0) {
    return 0
  }
  
  return feeAssign.value.details.reduce((sum, detail) => {
    return sum + parseFloat(detail.amount || 0)
  }, 0)
}

const calculateTotalWaver = () => {
  if (!students.value || Object.keys(students.value).length === 0) {
    return 0
  }
  
  let totalWaver = 0
  Object.values(students.value).forEach(studentFees => {
    studentFees.forEach(fee => {
      totalWaver += parseFloat(fee.waver_amount || 0)
    })
  })
  
  return totalWaver
}

const formatCurrency = (amount) => {
  const numAmount = parseFloat(amount || 0)
  return numAmount.toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

// Lifecycle
onMounted(() => {
  loadFeeAssignData()
})
</script>

<style scoped>
table {
  min-width: 600px;
}

tbody tr {
  transition: all 0.2s ease;
}

.fa-spinner.fa-spin {
  animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>