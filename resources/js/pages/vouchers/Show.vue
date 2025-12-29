<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Voucher Details
          </h1>
          <p class="text-gray-600">
            View voucher information
          </p>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
          <!-- Back Button -->
          <router-link 
            :to="{ name: 'vouchers.index' }"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-300 
                  text-gray-700 hover:bg-gray-100 
                  transition-colors w-full sm:w-auto text-center"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Vouchers
          </router-link>

          <!-- Edit Button -->
          <router-link
            :to="{ name: 'vouchers.edit', params: { id: $route.params.id } }"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 
                  text-white font-semibold transition-colors w-full sm:w-auto text-center"
          >
            <i class="fas fa-edit"></i>
            Edit Voucher
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
      <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
      <p class="text-gray-800 font-semibold">Loading voucher details...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
      <i class="fas fa-exclamation-triangle text-3xl text-red-600 mb-4"></i>
      <p class="text-gray-800 font-semibold mb-4">{{ error }}</p>
      <router-link 
        :to="{ name: 'vouchers.index' }"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
      >
        <i class="fas fa-arrow-left"></i>
        Back to Vouchers
      </router-link>
    </div>

    <!-- Voucher Details -->
    <div v-else-if="voucher" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Information -->
      <div class="lg:col-span-2">
        <!-- Basic Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
              Voucher Information
            </h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Voucher Type -->
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">
                  Voucher Type
                </label>
                <span 
                  :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                    voucher.voucher_type === 'DEBIT' 
                      ? 'bg-red-100 text-red-800' 
                      : 'bg-green-100 text-green-800'
                  ]"
                >
                  <i 
                    :class="[
                      'fas mr-2',
                      voucher.voucher_type === 'DEBIT' ? 'fa-arrow-down' : 'fa-arrow-up'
                    ]"
                  ></i>
                  {{ voucher.voucher_type }}
                </span>
              </div>

              <!-- Amount -->
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">
                  Amount
                </label>
                <p class="text-2xl font-bold text-gray-800">
                  {{ formatCurrency(voucher.amount) }}
                </p>
              </div>

              <!-- Date -->
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">
                  Transaction Date
                </label>
                <p class="text-lg font-semibold text-gray-800">
                  {{ formatDisplayDate(voucher.date) }}
                </p>
              </div>

              <!-- Account -->
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">
                  Account
                </label>
                <p class="text-lg font-semibold text-gray-800">
                  {{ voucher.account?.account_name || 'N/A' }}
                </p>
              </div>

              <!-- Subsidiary -->
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">
                  Subsidiary
                </label>
                <p class="text-lg font-semibold text-gray-800">
                  {{ voucher.subsidiary?.name || 'N/A' }}
                </p>
              </div>

              <!-- Account Head -->
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">
                  Account Head
                </label>
                <p class="text-lg font-semibold text-gray-800">
                  {{ voucher.head?.head_name || 'N/A' }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Module Reference Card -->
        <div v-if="voucher.module_name || voucher.ref_module_id" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
              Module Reference
            </h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Module Name -->
              <div v-if="voucher.module_name">
                <label class="block text-sm font-medium text-gray-500 mb-1">
                  Module Name
                </label>
                <p class="text-lg font-semibold text-gray-800">
                  {{ voucher.module_name }}
                </p>
              </div>

              <!-- Reference ID -->
              <div v-if="voucher.ref_module_id">
                <label class="block text-sm font-medium text-gray-500 mb-1">
                  Reference ID
                </label>
                <p class="text-lg font-semibold text-gray-800">
                  #{{ voucher.ref_module_id }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Description Card -->
        <div v-if="voucher.description" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
              Description
            </h3>
          </div>
          <div class="p-6">
            <p class="text-gray-800 whitespace-pre-wrap">
              {{ voucher.description }}
            </p>
          </div>
        </div>

        <!-- No Description Message -->
        <div v-else class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <div class="p-6 text-center">
            <i class="fas fa-sticky-note text-3xl text-gray-300 mb-3"></i>
            <p class="text-gray-500 font-medium">No description provided</p>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <!-- Transaction Summary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
              Transaction Summary
            </h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <!-- Amount -->
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">Amount:</span>
                <span class="text-lg font-bold text-gray-800">
                  {{ formatCurrency(voucher.amount) }}
                </span>
              </div>

              <!-- Type -->
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">Type:</span>
                <span 
                  :class="[
                    'px-2 py-1 rounded text-xs font-medium',
                    voucher.voucher_type === 'DEBIT' 
                      ? 'bg-red-100 text-red-800' 
                      : 'bg-green-100 text-green-800'
                  ]"
                >
                  {{ voucher.voucher_type }}
                </span>
              </div>

              <!-- Date -->
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">Date:</span>
                <span class="text-sm text-gray-800">
                  {{ formatDate(voucher.date) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Meta Information -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
              Meta Information
            </h3>
          </div>
          <div class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">
                Voucher ID
              </label>
              <p class="text-sm font-mono text-gray-800 bg-gray-100 px-2 py-1 rounded inline-block">
                #{{ voucher.id }}
              </p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">
                Created At
              </label>
              <p class="text-sm text-gray-800">
                {{ formatDate(voucher.created_at) }}
              </p>
              <p class="text-xs text-gray-500">
                {{ formatTime(voucher.created_at) }}
              </p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">
                Last Updated
              </label>
              <p class="text-sm text-gray-800">
                {{ formatDate(voucher.updated_at) }}
              </p>
              <p class="text-xs text-gray-500">
                {{ formatTime(voucher.updated_at) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()

// Data
const voucher = ref(null)
const loading = ref(true)
const error = ref(null)

// Methods
const fetchVoucher = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get(`/api/vouchers/${route.params.id}`)
    console.log('Voucher API Response:', response.data)
    voucher.value = response.data
    
  } catch (err) {
    console.error('Error fetching voucher:', err)
    
    if (err.response?.status === 404) {
      error.value = 'Voucher not found'
    } else if (err.response?.status === 500) {
      error.value = 'Server error occurred'
    } else {
      error.value = 'Failed to load voucher details'
    }
  } finally {
    loading.value = false
  }
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-BD', {
    style: 'currency',
    currency: 'BDT'
  }).format(amount || 0)
}

const formatDisplayDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-BD', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
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

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Watch for route changes
watch(
  () => route.params.id,
  (newId) => {
    if (newId) {
      fetchVoucher()
    }
  }
)

// Lifecycle
onMounted(() => {
  fetchVoucher()
})
</script>