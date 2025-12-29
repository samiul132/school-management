<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Transfer Details
          </h1>
          <p class="text-gray-600">
            View complete transfer information
          </p>
        </div>
        <div class="flex items-center gap-3 mb-2">
          <router-link 
            :to="{ name: 'cash-transfers.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-semibold"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Transfers
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
      <p class="text-gray-600">Loading transfer details...</p>
    </div>

    <!-- Content -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Content -->
      <div class="lg:col-span-2">
        <!-- Transfer Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
          <!-- Card Header -->
          <div class="px-6 py-4 border-b border-gray-200 bg-linear-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
                  <i class="fas fa-exchange-alt text-white text-xl"></i>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-gray-800">Transfer #{{ transfer.id }}</h2>
                  <p class="text-gray-600">Fund Transfer Details</p>
                </div>
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold text-blue-600">
                  {{ formatCurrency(transfer.amount) }}
                </div>
                <div class="text-sm text-gray-600">
                  {{ formatDate(transfer.date) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Transfer Details -->
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- From Account -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <i class="fas fa-arrow-up text-red-500"></i>
                  From Account
                </h3>
                
                <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                  <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                      <i class="fas fa-wallet text-red-600"></i>
                    </div>
                    <div>
                      <p class="font-semibold text-red-800">{{ transfer.from_account?.account_name || 'N/A' }}</p>
                      <p class="text-sm text-red-600">Source Account</p>
                    </div>
                  </div>
                  
                  <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                      <span class="text-red-700">Previous Balance:</span>
                      <span class="font-medium text-red-800">
                        {{ formatCurrency(previousFromBalance) }}
                      </span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-red-700">After Transfer:</span>
                      <span class="font-medium text-red-800">
                        {{ formatCurrency(currentFromBalance) }}
                      </span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-red-700">Balance Change:</span>
                      <span class="font-bold text-red-800">
                        - {{ formatCurrency(transfer.amount) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- To Account -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <i class="fas fa-arrow-down text-green-500"></i>
                  To Account
                </h3>
                
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                  <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                      <i class="fas fa-wallet text-green-600"></i>
                    </div>
                    <div>
                      <p class="font-semibold text-green-800">{{ transfer.to_account?.account_name || 'N/A' }}</p>
                      <p class="text-sm text-green-600">Destination Account</p>
                    </div>
                  </div>
                  
                  <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                      <span class="text-green-700">Previous Balance:</span>
                      <span class="font-medium text-green-800">
                        {{ formatCurrency(previousToBalance) }}
                      </span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-green-700">After Transfer:</span>
                      <span class="font-medium text-green-800">
                        {{ formatCurrency(currentToBalance) }}
                      </span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-green-700">Balance Change:</span>
                      <span class="font-bold text-green-800">
                        + {{ formatCurrency(transfer.amount) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Transfer Metadata -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Transfer Information</h3>
                
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-500">Transfer ID:</span>
                    <span class="font-mono text-gray-800">#{{ transfer.id }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-500">Transfer Date:</span>
                    <span class="text-gray-800">{{ formatDate(transfer.date) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-500">Amount:</span>
                    <span class="font-bold text-blue-600">{{ formatCurrency(transfer.amount) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-500">Status:</span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      Completed
                    </span>
                  </div>
                </div>
              </div>

              <div class="space-y-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Timeline</h3>
                
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-500">Created:</span>
                    <span class="text-gray-800">{{ formatDateTime(transfer.created_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-500">Last Updated:</span>
                    <span class="text-gray-800">{{ formatDateTime(transfer.updated_at) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="mt-6 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800 mb-3">Transfer Description</h3>
              <p v-if="transfer.description" class="text-gray-700 leading-relaxed">
                {{ transfer.description }}
              </p>
              <p v-else class="text-gray-500 italic">
                No description provided for this transfer.
              </p>
            </div>
          </div>

          <!-- Card Footer -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
              <div class="text-sm text-gray-500">
                Transfer processed on: {{ formatDateTime(transfer.created_at) }}
              </div>
              <div class="flex items-center gap-3">
                <!-- Edit Button -->
                <router-link
                  :to="{ name: 'cash-transfers.edit', params: { id: transfer.id } }"
                  class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors"
                >
                  <i class="fas fa-edit"></i>
                  Edit Transfer
                </router-link>

                <!-- Delete Button -->
                <button
                  @click="deleteTransfer"
                  class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors"
                >
                  <i class="fas fa-trash"></i>
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <!-- Transfer Summary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Transfer Summary</h3>
          
          <div class="space-y-4">
            <!-- Transfer Flow -->
            <div class="text-center p-4 rounded-lg bg-blue-50 border border-blue-200">
              <div class="flex items-center justify-center gap-3 mb-3">
                <div class="text-center">
                  <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-1">
                    <i class="fas fa-arrow-up text-white text-xs"></i>
                  </div>
                  <p class="text-xs text-red-600 font-medium truncate">
                    {{ transfer.from_account?.account_name || 'N/A' }}
                  </p>
                </div>
                
                <i class="fas fa-exchange-alt text-blue-500 text-xl"></i>
                
                <div class="text-center">
                  <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-1">
                    <i class="fas fa-arrow-down text-white text-xs"></i>
                  </div>
                  <p class="text-xs text-green-600 font-medium truncate">
                    {{ transfer.to_account?.account_name || 'N/A' }}
                  </p>
                </div>
              </div>
              
              <div class="text-center">
                <p class="text-2xl font-bold text-blue-600 mb-1">
                  {{ formatCurrency(transfer.amount) }}
                </p>
                <p class="text-xs text-gray-500">Transferred Amount</p>
              </div>
            </div>

            <!-- Quick Stats -->
            <div class="space-y-3">
              <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                <span class="text-sm text-gray-500">From Account Balance:</span>
                <span class="font-medium text-red-600">
                  {{ formatCurrency(currentFromBalance) }}
                </span>
              </div>
              <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                <span class="text-sm text-gray-500">To Account Balance:</span>
                <span class="font-medium text-green-600">
                  {{ formatCurrency(currentToBalance) }}
                </span>
              </div>
              <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                <span class="text-sm text-gray-500">Transfer Date:</span>
                <span class="font-medium text-gray-800">
                  {{ formatDate(transfer.date) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
          
          <div class="space-y-3">
            

            <button
              @click="viewAccountDetails(transfer.from_account?.id)"
              class="w-full flex items-center gap-3 px-4 py-3 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition-colors group"
            >
              <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-wallet text-white text-sm"></i>
              </div>
              <div class="flex-1 text-left">
                <p class="font-medium text-purple-700 group-hover:text-purple-800">
                  View From Account
                </p>
                <p class="text-xs text-purple-600">
                  {{ transfer.from_account?.account_name || 'N/A' }}
                </p>
              </div>
            </button>

            <button
              @click="viewAccountDetails(transfer.to_account?.id)"
              class="w-full flex items-center gap-3 px-4 py-3 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-lg transition-colors group"
            >
              <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-wallet text-white text-sm"></i>
              </div>
              <div class="flex-1 text-left">
                <p class="font-medium text-orange-700 group-hover:text-orange-800">
                  View To Account
                </p>
                <p class="text-xs text-orange-600">
                  {{ transfer.to_account?.account_name || 'N/A' }}
                </p>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, showConfirmDialog, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()

// Data
const transfer = ref({})
const loading = ref(true)

// Computed properties
const previousFromBalance = computed(() => {
  const current = parseFloat(transfer.value.from_account?.current_balance) || 0
  const amount = parseFloat(transfer.value.amount) || 0
  return current + amount
})

const currentFromBalance = computed(() => {
  return parseFloat(transfer.value.from_account?.current_balance) || 0
})

const previousToBalance = computed(() => {
  const current = parseFloat(transfer.value.to_account?.current_balance) || 0
  const amount = parseFloat(transfer.value.amount) || 0
  return current - amount
})

const currentToBalance = computed(() => {
  return parseFloat(transfer.value.to_account?.current_balance) || 0
})

// Methods
const formatCurrency = (amount) => {
  const numAmount = parseFloat(amount) || 0
  return new Intl.NumberFormat('en-BD', {
    style: 'currency',
    currency: 'BDT',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(numAmount)
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

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchTransfer = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/cash-transfers/${route.params.id}`)
    transfer.value = response.data
    console.log('Transfer data:', transfer.value)
  } catch (error) {
    console.error('Error fetching transfer:', error)
    showErrorAlert('Error', 'Failed to load transfer details')
    router.push({ name: 'cash-transfers.index' })
  } finally {
    loading.value = false
  }
}

const deleteTransfer = async () => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You won\'t be able to revert this! The transfer will be permanently deleted and account balances will be reversed.',
    'warning',
    'Yes, delete it!'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting transfer...')
      await axios.delete(`/api/cash-transfers/${route.params.id}`)
      
      closeAlert()
      await showSuccessAlert('Deleted!', 'Transfer has been deleted successfully.')
      
      router.push({ name: 'cash-transfers.index' })
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting transfer:', error)
      showErrorAlert('Error', 'Failed to delete transfer. Please try again.')
    }
  }
}

const createSimilarTransfer = () => {
  // Navigate to create page with pre-filled data
  router.push({
    name: 'cash-transfers.create',
    query: {
      from_account: transfer.value.from_account?.id || transfer.value.from_account,
      to_account: transfer.value.to_account?.id || transfer.value.to_account,
      amount: transfer.value.amount
    }
  })
}

const viewAccountDetails = (accountId) => {
  if (accountId) {
    router.push({
      name: 'cash-banks.show',
      params: { id: accountId }
    })
  } else {
    showErrorAlert('Error', 'Account information not available')
  }
}

// Lifecycle
onMounted(() => {
  fetchTransfer()
})
</script>