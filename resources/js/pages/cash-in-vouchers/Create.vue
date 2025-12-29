<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Create Cash In Voucher
          </h1>
          <p class="text-gray-600">
            Record incoming cash transactions
          </p>
        </div>
        <div class="flex items-center gap-3 mb-2">
          <router-link 
            :to="{ name: 'cash-in-vouchers.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-semibold"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Cash In Vouchers
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
          <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
            <div class="w-10 h-10 rounded-xl bg-green-500 flex items-center justify-center">
              <i class="fas fa-download text-white"></i>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">
                Cash In Information
              </h3>
              <p class="text-sm text-gray-600">
                Fill in the details of incoming cash transaction
              </p>
            </div>
          </div>

          <!-- Form Content -->
          <div class="p-6">
            <div class="space-y-6">
              <!-- Date and Auto Credit Type -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date -->
                <div>
                  <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                    Date <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="date"
                    id="date"
                    v-model="form.date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                    :class="{ 'border-red-500': errors.date }"
                  />
                  <p v-if="errors.date" class="mt-1 text-sm text-red-600">
                    {{ errors.date[0] }}
                  </p>
                </div>

                <!-- Voucher Type (Auto CREDIT - Readonly) -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Voucher Type
                  </label>
                  <div class="w-full px-4 py-2 border border-green-300 rounded-lg bg-green-50 text-green-800 font-semibold">
                    <div class="flex items-center gap-2">
                      <i class="fas fa-arrow-down text-green-600"></i>
                      <span>CREDIT</span>
                    </div>
                  </div>
                  <input type="hidden" v-model="form.voucher_type" />
                </div>
              </div>

              <!-- Amount -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Amount <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="number"
                    id="amount"
                    v-model="form.amount"
                    step="0.01"
                    min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                    :class="{ 'border-red-500': errors.amount }"
                    placeholder="0.00"
                  />
                  <p v-if="errors.amount" class="mt-1 text-sm text-red-600">
                    {{ errors.amount[0] }}
                  </p>
                </div>
                <div></div> <!-- Empty div for spacing -->
              </div>

              <!-- Account with v-select -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700">
                    Account <span class="text-red-500">*</span>
                  </label>
                  <v-select
                    v-model="form.account_id"
                    :options="accounts"
                    :filterable="true"
                    :searchable="true"
                    label="name"
                    :reduce="account => account.id"
                    placeholder="Search or select account..."
                    :class="{ 'border-red-500': errors.account_id }"
                    @search="searchAccounts"
                  >
                    <template #option="option">
                      <div>
                        <div class="font-medium text-gray-800">
                          <span class="text-blue-600 font-semibold mr-2 text-xs">#{{ option.id }}</span>
                          {{ option.name }}
                        </div>
                        <div v-if="option.account_number" class="text-xs text-gray-500 mt-1">
                          Account No: {{ option.account_number }}
                        </div>
                        <div v-if="option.bank_name" class="text-xs text-gray-500 mt-1">
                          Bank: {{ option.bank_name }}
                        </div>
                        <div class="text-xs text-green-600 font-medium mt-1">
                          Balance: {{ formatCurrency(option.balance || 0) }}
                        </div>
                      </div>
                    </template>
                    <template #no-options="{ search }">
                      <div class="p-3 text-center text-gray-500 text-sm">
                        <template v-if="search">
                          <i class="fas fa-search mb-2 text-lg"></i>
                          <p>No accounts found for "{{ search }}"</p>
                        </template>
                        <template v-else>
                          <i class="fas fa-building mb-2 text-lg"></i>
                          <p>Type to search accounts</p>
                        </template>
                      </div>
                    </template>
                  </v-select>
                  <p v-if="errors.account_id" class="mt-1 text-sm text-red-600">
                    {{ errors.account_id[0] }}
                  </p>
                </div>

                <!-- Subsidiary with v-select -->
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700">
                    Subsidiary
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
                    @search="searchSubsidiaries"
                  >
                    <template #option="option">
                      <div>
                        <div class="font-medium text-gray-800">
                          <span class="text-blue-600 font-semibold mr-2 text-xs">#{{ option.id }}</span>
                          {{ option.name }}
                        </div>
                        <div v-if="option.code" class="text-xs text-gray-500 mt-1">
                          Code: {{ option.code }}
                        </div>
                        <div v-if="option.address" class="text-xs text-gray-500 mt-1 line-clamp-1">
                          {{ option.address }}
                        </div>
                      </div>
                    </template>
                    <template #no-options="{ search }">
                      <div class="p-3 text-center text-gray-500 text-sm">
                        <template v-if="search">
                          <i class="fas fa-search mb-2 text-lg"></i>
                          <p>No subsidiaries found for "{{ search }}"</p>
                        </template>
                        <template v-else>
                          <i class="fas fa-sitemap mb-2 text-lg"></i>
                          <p>Type to search subsidiaries</p>
                        </template>
                      </div>
                    </template>
                  </v-select>
                  <p v-if="errors.subsidiaries_id" class="mt-1 text-sm text-red-600">
                    {{ errors.subsidiaries_id[0] }}
                  </p>
                </div>
              </div>

              <!-- Head with v-select -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700">
                    Account Head
                  </label>
                  <v-select
                    v-model="form.head_id"
                    :options="heads"
                    :filterable="true"
                    :searchable="true"
                    label="name"
                    :reduce="head => head.id"
                    placeholder="Search or select account head..."
                    :class="{ 'border-red-500': errors.head_id }"
                    @search="searchHeads"
                  >
                    <template #option="option">
                      <div>
                        <div class="font-medium text-gray-800">
                          <span class="text-blue-600 font-semibold mr-2 text-xs">#{{ option.id }}</span>
                          {{ option.name }}
                        </div>
                        <div v-if="option.code" class="text-xs text-gray-500 mt-1">
                          Code: {{ option.code }}
                        </div>
                        <div v-if="option.type" class="text-xs text-gray-500 mt-1">
                          Type: {{ option.type }}
                        </div>
                        <div v-if="option.parent_head" class="text-xs text-gray-500 mt-1">
                          Parent: {{ option.parent_head }}
                        </div>
                      </div>
                    </template>
                    <template #no-options="{ search }">
                      <div class="p-3 text-center text-gray-500 text-sm">
                        <template v-if="search">
                          <i class="fas fa-search mb-2 text-lg"></i>
                          <p>No account heads found for "{{ search }}"</p>
                        </template>
                        <template v-else>
                          <i class="fas fa-chart-line mb-2 text-lg"></i>
                          <p>Type to search account heads</p>
                        </template>
                      </div>
                    </template>
                  </v-select>
                  <p v-if="errors.head_id" class="mt-1 text-sm text-red-600">
                    {{ errors.head_id[0] }}
                  </p>
                </div>
                <div></div>
              </div>

              <!-- Module Reference -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="module_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Source / Module Name
                  </label>
                  <input
                    type="text"
                    id="module_name"
                    v-model="form.module_name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                    :class="{ 'border-red-500': errors.module_name }"
                    placeholder="e.g., Sales, Customer Payment, Investment, etc."
                  />
                  <p v-if="errors.module_name" class="mt-1 text-sm text-red-600">
                    {{ errors.module_name[0] }}
                  </p>
                </div>

                <div>
                  <label for="ref_module_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Reference ID
                  </label>
                  <input
                    type="number"
                    id="ref_module_id"
                    v-model="form.ref_module_id"
                    min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                    :class="{ 'border-red-500': errors.ref_module_id }"
                    placeholder="Reference ID"
                  />
                  <p v-if="errors.ref_module_id" class="mt-1 text-sm text-red-600">
                    {{ errors.ref_module_id[0] }}
                  </p>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                  Description / Notes
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500 resize-y"
                  :class="{ 'border-red-500': errors.description }"
                  placeholder="Enter description of the cash in transaction..."
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
                :to="{ name: 'cash-in-vouchers.index' }"
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
                      bg-green-600 hover:bg-green-700 text-white
                      disabled:opacity-50 disabled:cursor-not-allowed
                      w-full sm:w-auto text-center"
              >
                <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-download"></i>
                {{ loading ? 'Recording...' : 'Record Cash In' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <!-- Cash In Summary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-green-500 flex items-center justify-center">
              <i class="fas fa-download text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Cash In Summary</h3>
              <p class="text-gray-600 text-sm">Incoming transaction overview</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Voucher Type Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Transaction Type
              </label>
              <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                <div class="flex items-center gap-2">
                  <i class="fas fa-arrow-down text-green-600"></i>
                  <span class="text-green-800 font-semibold">CASH IN (CREDIT)</span>
                </div>
              </div>
            </div>

            <!-- Amount Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Amount
              </label>
              <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                <p class="text-lg font-bold text-green-800">
                  {{ formatCurrency(form.amount || 0) }}
                </p>
              </div>
            </div>

            <!-- Date Preview -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Transaction Date
              </label>
              <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                <p class="text-sm text-green-800">
                  {{ form.date ? formatDisplayDate(form.date) : 'Not set' }}
                </p>
              </div>
            </div>

            <!-- Account Balance Preview -->
            <div v-if="selectedAccount" class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Current Account Balance
              </label>
              <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm font-medium text-blue-800">
                  {{ formatCurrency(selectedAccount.balance || 0) }}
                </p>
                <p class="text-xs text-blue-600 mt-1">
                  After transaction: {{ formatCurrency((selectedAccount.balance || 0) + (parseFloat(form.amount) || 0)) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Selected Items Summary -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
          <h4 class="font-semibold text-gray-800 mb-4">Selected Items</h4>
          <div class="space-y-3">
            <div v-if="selectedAccount" class="flex items-center justify-between p-2 bg-green-50 rounded border border-green-200">
              <span class="text-sm text-green-800 font-medium">Account</span>
              <span class="text-sm text-green-600">{{ selectedAccount.name }}</span>
            </div>
            <div v-if="selectedSubsidiary" class="flex items-center justify-between p-2 bg-blue-50 rounded border border-blue-200">
              <span class="text-sm text-blue-800 font-medium">Subsidiary</span>
              <span class="text-sm text-blue-600">{{ selectedSubsidiary.name }}</span>
            </div>
            <div v-if="selectedHead" class="flex items-center justify-between p-2 bg-purple-50 rounded border border-purple-200">
              <span class="text-sm text-purple-800 font-medium">Head</span>
              <span class="text-sm text-purple-600">{{ selectedHead.name }}</span>
            </div>
            <div v-if="!selectedAccount && !selectedSubsidiary && !selectedHead" class="text-center text-gray-500 text-sm py-4">
              No items selected
            </div>
          </div>
        </div>

        <!-- Cash In Tips -->
        <div class="bg-green-50 rounded-2xl border border-green-200 p-6">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
              <i class="fas fa-info-circle text-green-600"></i>
            </div>
            <div>
              <h4 class="font-semibold text-green-800 mb-2">
                Cash In Tips
              </h4>
              <ul class="text-sm text-green-700 space-y-1">
                <li>• Date and amount are required</li>
                <li>• Voucher type is automatically CREDIT</li>
                <li>• Select the receiving account</li>
                <li>• Account balance will increase</li>
                <li>• Add source description for tracking</li>
                <li>• Use reference ID for related documents</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()

// Form data
const form = reactive({
  date: new Date().toISOString().split('T')[0],
  voucher_type: 'CREDIT', // Always CREDIT for cash in
  account_id: null,
  subsidiaries_id: null,
  head_id: null,
  amount: 0,
  description: '',
  module_name: '',
  ref_module_id: null
})

// Dropdown data
const accounts = ref([])
const subsidiaries = ref([])
const heads = ref([])

// Selected items for display
const selectedAccount = computed(() => {
  return accounts.value.find(acc => acc.id === form.account_id) || null
})

const selectedSubsidiary = computed(() => {
  return subsidiaries.value.find(sub => sub.id === form.subsidiaries_id) || null
})

const selectedHead = computed(() => {
  return heads.value.find(head => head.id === form.head_id) || null
})

// Loading and errors
const loading = ref(false)
const errors = ref({})

// Helper functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-BD', {
    style: 'currency',
    currency: 'BDT'
  }).format(amount || 0)
}

const formatDisplayDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-BD', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Fetch dropdown data
const fetchDropdownData = async () => {
  try {
    const response = await axios.get('/api/vouchers/dropdown/data')
    accounts.value = response.data.accounts || []
    subsidiaries.value = response.data.subsidiaries || []
    heads.value = response.data.heads || []
  } catch (error) {
    console.error('Error fetching dropdown data:', error)
    showErrorAlert('Error', 'Failed to load dropdown data')
  }
}

// Search functions for v-select
const searchAccounts = async (search, loading) => {
  try {
    const response = await axios.get('/api/accounts/search', {
      params: { q: search }
    })
    accounts.value = response.data.data || response.data.accounts || []
  } catch (error) {
    console.error('Error searching accounts:', error)
  }
}

const searchSubsidiaries = async (search, loading) => {
  try {
    const response = await axios.get('/api/subsidiaries/search', {
      params: { q: search }
    })
    subsidiaries.value = response.data.data || response.data.subsidiaries || []
  } catch (error) {
    console.error('Error searching subsidiaries:', error)
  }
}

const searchHeads = async (search, loading) => {
  try {
    const response = await axios.get('/api/heads/search', {
      params: { q: search }
    })
    heads.value = response.data.data || response.data.heads || []
  } catch (error) {
    console.error('Error searching heads:', error)
  }
}

// Form validation
const validateForm = () => {
  const newErrors = {}

  if (!form.date) {
    newErrors.date = ['Date is required']
  }

  if (!form.amount || form.amount <= 0) {
    newErrors.amount = ['Amount must be greater than 0']
  }

  if (!form.account_id) {
    newErrors.account_id = ['Account is required']
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

// Submit form
const submitForm = async () => {
  if (!validateForm()) {
    showErrorAlert('Validation Error', 'Please fix the errors in the form')
    return
  }

  loading.value = true

  try {
    showLoadingAlert('Recording cash in transaction...')
    
    const formData = {
      ...form,
      amount: parseFloat(form.amount),
      ref_module_id: form.ref_module_id ? parseInt(form.ref_module_id) : null,
      voucher_type: 'CREDIT' // Ensure it's always CREDIT
    }

    await axios.post('/api/vouchers', formData)

    closeAlert()
    showSuccessAlert('Success', 'Cash in voucher created successfully!')
    
    router.push({ 
      name: 'cash-in-vouchers.index',
      query: { created: 'true' }
    })

  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors')
    } else {
      console.error('Error creating cash in voucher:', error)
      showErrorAlert('Error', 'Failed to record cash in transaction. Please try again.')
    }
  }
}

// Initialize
onMounted(() => {
  fetchDropdownData()
})
</script>