<template>
  <AppLayout>
    <!-- Header with Create Button -->
    <div class="mb-3 flex flex-col md:flex-row justify-between md:items-center gap-4">
    
    <!-- Title Section -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">
        Cash Transfers
        </h1>
        <p class="text-gray-600">
        Manage all cash and bank transfers
        </p>
    </div>

    <!-- Button -->
    <router-link 
        :to="{ name: 'cash-transfers.create' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
            text-white px-3 py-2 rounded-lg font-semibold transition-colors w-full md:w-auto"
    >
        <i class="fas fa-exchange-alt"></i>
        Create New Transfer
    </router-link>

    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Transfers</p>
            <p class="text-3xl font-bold text-gray-800">{{ totalTransfers }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
            <i class="fas fa-exchange-alt text-white text-xl"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Amount</p>
            <p class="text-3xl font-bold text-green-600">{{ formatCurrency(totalAmount) }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
            <i class="fas fa-money-bill-wave text-white text-xl"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Today's Transfers</p>
            <p class="text-3xl font-bold text-purple-600">{{ todayTransfers }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-500 flex items-center justify-center">
            <i class="fas fa-calendar-day text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Showing</p>
            <p class="text-3xl font-bold text-orange-600">{{ showingCount }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-orange-500 flex items-center justify-center">
            <i class="fas fa-list text-white text-xl"></i>
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
              placeholder="Search transfers..." 
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>

          <!-- Date Filter -->
          <div class="flex gap-2">
            <input 
              type="date" 
              v-model="dateFrom"
              class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
              placeholder="From Date"
            >
            <input 
              type="date" 
              v-model="dateTo"
              class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
              placeholder="To Date"
            >
          </div>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1200px]">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Date</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">From Account</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">To Account</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Amount</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Description</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="transfer in paginatedTransfers" :key="transfer.id" class="hover:bg-gray-50">
              <!-- ID -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ transfer.id }}
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <!-- Dropdown Button -->
                  <button 
                    @click="toggleDropdown(transfer.id)"
                    class="inline-flex items-center justify-center px-2 h-8 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== transfer.id,
                      'bg-gray-200 text-black': openDropdownId === transfer.id
                    }"
                  >
                    Action
                    <i 
                      class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                      :class="{
                        'rotate-180': openDropdownId === transfer.id,
                        'text-white': openDropdownId !== transfer.id,
                        'text-black': openDropdownId === transfer.id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === transfer.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- View Option -->
                      <router-link 
                        :to="{ name: 'cash-transfers.show', params: { id: transfer.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-eye text-green-600 text-xs w-4"></i>
                        <span>View Transfer</span>
                      </router-link>
                      
                      <!-- Edit Option -->
                      <router-link 
                        :to="{ name: 'cash-transfers.edit', params: { id: transfer.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Transfer</span>
                      </router-link>
                      
                      <!-- Delete Option -->
                      <button 
                        @click="handleDeleteTransfer(transfer.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete Transfer</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Date -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ formatDate(transfer.date) }}
              </td>
              
              <!-- From Account -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                  <span>{{ transfer.from_account?.account_name }}</span>
                </div>
              </td>
              
              <!-- To Account -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                  <span>{{ transfer.to_account?.account_name }}</span>
                </div>
              </td>
              
              <!-- Amount -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800 font-mono">
                <span class="text-red-600">
                  {{ formatCurrency(transfer.amount) }}
                </span>
              </td>
              
              <!-- Description -->
              <td class="px-4 py-4 text-sm text-gray-800">
                <div class="truncate max-w-60" :title="transfer.description">
                  <span v-if="transfer.description">
                    {{ transfer.description }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">
                    No description
                  </span>
                </div>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(transfer.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(transfer.created_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading transfers...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && transfers.length === 0" class="p-8 text-center">
        <i class="fas fa-exchange-alt text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No cash transfers found.</p>
        <router-link 
          :to="{ name: 'cash-transfers.create' }"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Create First Transfer
        </router-link>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && transfers.length > 0 && filteredTransfers.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No transfers match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredTransfers.length > 0" class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredTransfers.length }} results
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
const transfers = ref([])
const loading = ref(true)
const search = ref('')
const dateFrom = ref('')
const dateTo = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const openDropdownId = ref(null)

// Computed properties
const totalTransfers = computed(() => transfers.value.length)

const totalAmount = computed(() => 
  transfers.value.reduce((sum, transfer) => sum + parseFloat(transfer.amount || 0), 0)
)

const todayTransfers = computed(() => {
  const today = new Date().toISOString().split('T')[0] 
  console.log('Today:', today)
  console.log('All transfers dates:', transfers.value.map(t => t.date))
  
  const todayTransfers = transfers.value.filter(transfer => {
    let transferDate
    if (transfer.date) {
      if (transfer.date.includes('T')) {
        transferDate = transfer.date.split('T')[0]
      } else {
        transferDate = transfer.date
      }
    }
    console.log(`Transfer ${transfer.id} date:`, transfer.date, '->', transferDate)
    return transferDate === today
  })
  
  console.log('Today transfers count:', todayTransfers.length)
  return todayTransfers.length
})

const showingCount = computed(() => paginatedTransfers.value.length)

const filteredTransfers = computed(() => {
  let filtered = transfers.value
  
  // Search filter
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(transfer => 
      transfer.from_account?.account_name?.toLowerCase().includes(searchTerm) ||
      transfer.to_account?.account_name?.toLowerCase().includes(searchTerm) ||
      transfer.description?.toLowerCase().includes(searchTerm) ||
      transfer.amount?.toString().includes(searchTerm)
    )
  }
  
  // Date range filter
  if (dateFrom.value) {
    filtered = filtered.filter(transfer => transfer.date >= dateFrom.value)
  }
  
  if (dateTo.value) {
    filtered = filtered.filter(transfer => transfer.date <= dateTo.value)
  }
  
  return filtered
})

// Pagination computed properties
const totalPages = computed(() => Math.ceil(filteredTransfers.value.length / itemsPerPage.value))

const paginatedTransfers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredTransfers.value.slice(start, end)
})

const showingStart = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredTransfers.value.length ? filteredTransfers.value.length : end
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
const fetchTransfers = async () => {
  try {
    loading.value = true
    console.log('Fetching cash transfers...')
    const response = await axios.get('/api/cash-transfers')
    console.log('Transfers data:', response.data)
    transfers.value = response.data
  } catch (error) {
    console.error('Error fetching transfers:', error)
    console.error('Error details:', error.response)
    showErrorAlert('Error', 'Failed to load cash transfers')
  } finally {
    loading.value = false
  }
}

const deleteTransfer = async (id) => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You won\'t be able to revert this! This will delete the transfer and reverse the account balances.'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting transfer...')
      
      await axios.delete(`/api/cash-transfers/${id}`)
      transfers.value = transfers.value.filter(transfer => transfer.id !== id)
      
      closeAlert()
      await showSuccessAlert('Deleted!', 'Transfer has been deleted and balances reversed.')
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting transfer:', error)
      showErrorAlert('Error', 'Failed to delete transfer')
    }
  }
}

const handleDeleteTransfer = async (id) => {
  closeDropdown()
  await deleteTransfer(id)
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
  dateFrom.value = ''
  dateTo.value = ''
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
    style: 'currency',
    currency: 'BDT',
    currencyDisplay: 'symbol',
    minimumFractionDigits: 2
  })
    .format(numAmount)
    .replace('BDT', '৳')
}

// Pagination methods
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

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    openDropdownId.value = null
  }
}

// Watchers
watch([search, dateFrom, dateTo], () => {
  currentPage.value = 1
})

watch(itemsPerPage, () => {
  currentPage.value = 1
})

// Lifecycle
onMounted(() => {
  fetchTransfers()
  document.addEventListener('click', handleClickOutside)
  
  if (route.query.created === 'true') {
    showSuccessAlert('Success!', 'Transfer created successfully.')
    router.replace({ name: 'cash-transfers.index' })
  }
  
  if (route.query.updated === 'true') {
    showSuccessAlert('Success!', 'Transfer updated successfully.')
    router.replace({ name: 'cash-transfers.index' })
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>