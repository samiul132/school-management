<template>
  <AppLayout>
    <!-- Header with Create Button -->
    <div class="mb-3 flex flex-col md:flex-row justify-between md:items-center gap-4">
    
    <!-- Title Section -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">
        Cash & Bank Accounts
        </h1>
        <p class="text-gray-600">
        Manage all cash and bank accounts
        </p>
    </div>

    <!-- Button -->
    <router-link 
        :to="{ name: 'cash-banks.create' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
            text-white px-3 py-2 rounded-lg font-semibold transition-colors w-full md:w-auto"
    >
        <i class="fas fa-plus"></i>
        Create New Account
    </router-link>

    </div>


    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Accounts</p>
            <p class="text-3xl font-bold text-gray-800">{{ totalAccounts }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
            <i class="fas fa-wallet text-white text-xl"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Balance</p>
            <p class="text-3xl font-bold text-green-600">{{ formatCurrency(totalBalance) }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
            <i class="fas fa-money-bill-wave text-white text-xl"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Opening Balance</p>
            <p class="text-3xl font-bold text-purple-600">{{ formatCurrency(totalOpeningBalance) }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-500 flex items-center justify-center">
            <i class="fas fa-chart-line text-white text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <!-- Table Header with Search -->
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <!-- Items Per Page -->
        <div class="flex items-center gap-2 text-sm text-gray-700">
          <span>Show:</span>
          <select 
            v-model="itemsPerPage"
            class="px-2 py-1 border border-gray-300 rounded bg-white text-gray-800"
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span>per page</span>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
          <!-- Search Box -->
          <div class="relative">
            <input 
              type="text" 
              v-model="search"
              placeholder="Search accounts..." 
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>

          <!-- Balance Filter -->
          <select 
            v-model="balanceFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
          >
            <option value="">All Balances</option>
            <option value="positive">Positive Balance</option>
            <option value="zero">Zero Balance</option>
            <option value="negative">Negative Balance</option>
          </select>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1000px]">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">SL</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Account Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Opening Balance</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Current Balance</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Description</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Balance Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="account in paginatedAccounts" :key="account.id" class="hover:bg-gray-50">
              <!-- ID -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ getRowNumber(account) }}
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <!-- Dropdown Button -->
                  <button 
                    @click="toggleDropdown(account.id)"
                    class="inline-flex items-center justify-center px-2 h-8 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== account.id,
                      'bg-gray-200 text-black': openDropdownId === account.id
                    }"
                  >
                    Action
                    <i 
                      class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                      :class="{
                        'rotate-180': openDropdownId === account.id,
                        'text-white': openDropdownId !== account.id,
                        'text-black': openDropdownId === account.id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === account.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- View Option -->
                      <router-link 
                        :to="{ name: 'cash-banks.show', params: { id: account.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-eye text-green-600 text-xs w-4"></i>
                        <span>View Account</span>
                      </router-link>
                      
                      <!-- Edit Option -->
                      <router-link 
                        :to="{ name: 'cash-banks.edit', params: { id: account.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Account</span>
                      </router-link>
                      
                      <!-- Delete Option -->
                      <button 
                        @click="handleDeleteAccount(account.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete Account</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Account Name -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                <div class="truncate max-w-[180px]" :title="account.account_name">
                  {{ account.account_name }}
                </div>
              </td> 
              
              <!-- Opening Balance -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ formatCurrency(account.opening_balance) }}
              </td>
              
              <!-- Current Balance -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800 font-mono">
                <span :class="getBalanceColor(account.current_balance)">
                  {{ formatCurrency(account.current_balance) }}
                </span>
              </td>
              
              <!-- Description -->
              <td class="px-4 py-4 text-sm text-gray-800">
                <div class="truncate max-w-60" :title="account.description">
                  <span v-if="account.description">
                    {{ account.description }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">
                    No description
                  </span>
                </div>
              </td>
              
              <!-- Balance Status -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <span 
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-medium',
                    getBalanceStatusClass(account.current_balance)
                  ]"
                >
                  {{ getBalanceStatus(account.current_balance) }}
                </span>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(account.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(account.created_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading accounts...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && accounts.length === 0" class="p-8 text-center">
        <i class="fas fa-wallet text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No cash/bank accounts found.</p>
        <router-link 
          :to="{ name: 'cash-banks.create' }"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Account
        </router-link>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && accounts.length > 0 && filteredAccounts.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No accounts match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredAccounts.length > 0" class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredAccounts.length }} results
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
import { ref, computed, onMounted, watch, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showConfirmDialog, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const accounts = ref([])
const loading = ref(true)
const search = ref('')
const balanceFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const openDropdownId = ref(null)

// Computed properties
const totalAccounts = computed(() => accounts.value.length)

const totalBalance = computed(() => 
  accounts.value.reduce((sum, account) => sum + parseFloat(account.current_balance || 0), 0)
)

const totalOpeningBalance = computed(() => 
  accounts.value.reduce((sum, account) => sum + parseFloat(account.opening_balance || 0), 0)
)

const showingCount = computed(() => paginatedAccounts.value.length)

const filteredAccounts = computed(() => {
  let filtered = accounts.value
  
  // Search filter
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(account => 
      account.account_name?.toLowerCase().includes(searchTerm) ||
      account.description?.toLowerCase().includes(searchTerm) ||
      account.account_type?.toLowerCase().includes(searchTerm)
    )
  }
  
  // Balance filter
  if (balanceFilter.value) {
    filtered = filtered.filter(account => {
      const balance = parseFloat(account.current_balance || 0)
      switch (balanceFilter.value) {
        case 'positive':
          return balance > 0
        case 'zero':
          return balance === 0
        case 'negative':
          return balance < 0
        default:
          return true
      }
    })
  }
  
  return filtered
})

// Pagination computed properties
const totalPages = computed(() => Math.ceil(filteredAccounts.value.length / itemsPerPage.value))

const paginatedAccounts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredAccounts.value.slice(start, end)
})

const showingStart = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredAccounts.value.length ? filteredAccounts.value.length : end
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
const fetchAccounts = async () => {
  try {
    loading.value = true
    // console.log('Fetching cash/bank accounts...')
    const response = await axios.get('/api/cash-banks')
    // console.log('Accounts data:', response.data)
    accounts.value = response.data
  } catch (error) {
    console.error('Error fetching accounts:', error)
    console.error('Error details:', error.response)
    showErrorAlert('Error', 'Failed to load cash/bank accounts')
  } finally {
    loading.value = false
  }
}

const deleteAccount = async (id) => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You won\'t be able to revert this! This will delete the account and all associated transactions.'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting account...')
      
      await axios.delete(`/api/cash-banks/${id}`)
      accounts.value = accounts.value.filter(account => account.id !== id)
      
      closeAlert()
      await showSuccessAlert('Deleted!', 'Account has been deleted.')
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting account:', error)
      showErrorAlert('Error', 'Failed to delete account')
    }
  }
}

const handleDeleteAccount = async (id) => {
  closeDropdown()
  await deleteAccount(id)
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

const clearFilters = () => {
  search.value = ''
  balanceFilter.value = ''
  currentPage.value = 1
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

const formatCurrency = (amount) => {
  const numAmount = parseFloat(amount || 0)
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  }).format(numAmount) + ' /='
}

const getBalanceColor = (balance) => {
  const numBalance = parseFloat(balance || 0)
  if (numBalance > 0) return 'text-green-600'
  if (numBalance < 0) return 'text-red-600'
  return 'text-gray-600'
}

const getBalanceStatus = (balance) => {
  const numBalance = parseFloat(balance || 0)
  if (numBalance > 0) return 'Positive'
  if (numBalance < 0) return 'Negative'
  return 'Zero'
}

const getBalanceStatusClass = (balance) => {
  const numBalance = parseFloat(balance || 0)
  if (numBalance > 0) return 'bg-green-100 text-green-800'
  if (numBalance < 0) return 'bg-red-100 text-red-800'
  return 'bg-gray-100 text-gray-800'
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

// Watchers
watch([search, balanceFilter], () => {
  currentPage.value = 1
})

watch(itemsPerPage, () => {
  currentPage.value = 1
})

const getRowNumber = (account) => {
  const index = filteredAccounts.value.findIndex(s => s.id === account.id)
  return index + 1
}

// Lifecycle
onMounted(() => {
  fetchAccounts()
  document.addEventListener('click', handleClickOutside)
  
  if (route.query.created === 'true') {
    showSuccessAlert('Success!', 'Account created successfully.')
    router.replace({ name: 'cash-banks.index' })
  }
  
  if (route.query.updated === 'true') {
    showSuccessAlert('Success!', 'Account updated successfully.')
    router.replace({ name: 'cash-banks.index' })
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>