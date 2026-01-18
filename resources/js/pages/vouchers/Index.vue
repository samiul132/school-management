<template>
  <AppLayout>
    <!-- Header with Create Button -->
    <div class="mb-3 flex flex-col md:flex-row justify-between md:items-center gap-4">
    
    <!-- Title Section -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">
        All Vouchers
        </h1>
        <p class="text-gray-600">
        Manage all financial vouchers
        </p>
    </div>

    <!-- Button -->
    <router-link 
        :to="{ name: 'vouchers.create' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
            text-white px-3 py-2 rounded-lg font-semibold transition-colors w-full md:w-auto"
    >
        <i class="fas fa-plus"></i>
        Create New Voucher
    </router-link>

    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Vouchers</p>
            <p class="text-3xl font-bold text-gray-800">{{ totalVouchers }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
            <i class="fas fa-file-invoice text-white text-xl"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Credit Vouchers</p>
            <p class="text-3xl font-bold text-green-600">{{ creditVouchers }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
            <i class="fas fa-arrow-down text-white text-xl"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Debit Vouchers</p>
            <p class="text-3xl font-bold text-red-600">{{ debitVouchers }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-red-500 flex items-center justify-center">
            <i class="fas fa-arrow-up text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Amount</p>
            <p class="text-3xl font-bold text-purple-600">{{ formatCurrency(totalAmount) }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-500 flex items-center justify-center">
            <i class="fas fa-money-bill-wave text-white text-xl"></i>
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
              placeholder="Search vouchers..." 
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>

          <!-- Voucher Type Filter -->
          <select 
            v-model="voucherTypeFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
          >
            <option value="">All Types</option>
            <option value="DEBIT">Debit</option>
            <option value="CREDIT">Credit</option>
          </select>

          <!-- Date Range Filter -->
          <div class="flex gap-2">
            <input 
              type="date" 
              v-model="dateFrom"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
              placeholder="From Date"
            >
            <input 
              type="date" 
              v-model="dateTo"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
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
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">SL</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Date</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Voucher Type</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Account</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Subsidiary</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Head</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Amount</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Description</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Module</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="voucher in paginatedVouchers" :key="voucher.id" class="hover:bg-gray-50">
              <!-- ID -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ getRowNumber(voucher) }}
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <!-- Dropdown Button -->
                  <button 
                    @click="toggleDropdown(voucher.id)"
                    class="inline-flex items-center justify-center px-2 h-8 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== voucher.id,
                      'bg-gray-200 text-black': openDropdownId === voucher.id
                    }"
                  >
                    Action
                    <i 
                      class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                      :class="{
                        'rotate-180': openDropdownId === voucher.id,
                        'text-white': openDropdownId !== voucher.id,
                        'text-black': openDropdownId === voucher.id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === voucher.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- View Option -->
                      <router-link 
                        :to="{ name: 'vouchers.show', params: { id: voucher.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-eye text-green-600 text-xs w-4"></i>
                        <span>View Voucher</span>
                      </router-link>
                      
                      <!-- Edit Option -->
                      <router-link 
                        :to="{ name: 'vouchers.edit', params: { id: voucher.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit Voucher</span>
                      </router-link>
                      
                      <!-- Delete Option -->
                      <button 
                        @click="handleDeleteVoucher(voucher.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete Voucher</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Date -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ formatDate(voucher.date) }}
              </td>
              
              <!-- Voucher Type -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <span 
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-medium',
                    voucher.voucher_type === 'DEBIT' 
                      ? 'bg-red-100 text-red-800' 
                      : 'bg-green-100 text-green-800'
                  ]"
                >
                  {{ voucher.voucher_type }}
                </span>
              </td>
              
              <!-- Account -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="truncate max-w-[180px]" :title="getAccountName(voucher)">
                    {{ getAccountName(voucher) }}
                </div>
                </td>

                <!-- Subsidiary -->
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="truncate max-w-[180px]" :title="getSubsidiaryName(voucher)">
                    {{ getSubsidiaryName(voucher) }}
                </div>
                </td>

                <!-- Head -->
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="truncate max-w-[180px]" :title="getHeadName(voucher)">
                    {{ getHeadName(voucher) }}
                </div>
              </td>
              
              <!-- Amount -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono font-semibold">
                {{ formatCurrency(voucher.amount) }}
              </td>
              
              <!-- Description -->
              <td class="px-4 py-4 text-sm text-gray-800">
                <div class="truncate max-w-60" :title="voucher.description">
                  <span v-if="voucher.description">
                    {{ voucher.description }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">
                    No description
                  </span>
                </div>
              </td>
              
              <!-- Module -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span class="font-medium">{{ voucher.module_name || 'N/A' }}</span>
                  <span v-if="voucher.ref_module_id" class="text-xs text-gray-500">
                  </span>
                </div>
              </td>
              
              <!-- Created At -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(voucher.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(voucher.created_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading vouchers...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && vouchers.length === 0" class="p-8 text-center">
        <i class="fas fa-file-invoice text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No vouchers found.</p>
        <router-link 
          :to="{ name: 'vouchers.create' }"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Create First Voucher
        </router-link>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && vouchers.length > 0 && filteredVouchers.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No vouchers match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredVouchers.length > 0" class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredVouchers.length }} results
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
const vouchers = ref([])
const loading = ref(true)
const search = ref('')
const voucherTypeFilter = ref('')
const dateFrom = ref('')
const dateTo = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const openDropdownId = ref(null)

const totalVouchers = computed(() => vouchers.value.length)

const debitVouchers = computed(() => 
  vouchers.value.filter(voucher => voucher.voucher_type === 'DEBIT').length
)

const creditVouchers = computed(() => 
  vouchers.value.filter(voucher => voucher.voucher_type === 'CREDIT').length
)

const totalAmount = computed(() => 
  vouchers.value.reduce((sum, voucher) => sum + parseFloat(voucher.amount), 0)
)

const showingCount = computed(() => paginatedVouchers.value.length)

const filteredVouchers = computed(() => {
  let filtered = vouchers.value
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(voucher => {
      const accountName = voucher.account?.name || voucher.account?.account_name || ''
      const subsidiaryName = voucher.subsidiary?.name || ''
      const headName = voucher.head?.name || voucher.head?.head_name || ''
      
      return (
        accountName.toLowerCase().includes(searchTerm) ||
        subsidiaryName.toLowerCase().includes(searchTerm) ||
        headName.toLowerCase().includes(searchTerm) ||
        voucher.description?.toLowerCase().includes(searchTerm) ||
        voucher.module_name?.toLowerCase().includes(searchTerm) ||
        voucher.voucher_type?.toLowerCase().includes(searchTerm)
      )
    })
  }
  
  if (voucherTypeFilter.value) {
    filtered = filtered.filter(voucher => 
      voucher.voucher_type === voucherTypeFilter.value
    )
  }
  
  if (dateFrom.value) {
    filtered = filtered.filter(voucher => 
      new Date(voucher.date) >= new Date(dateFrom.value)
    )
  }
  
  if (dateTo.value) {
    filtered = filtered.filter(voucher => 
      new Date(voucher.date) <= new Date(dateTo.value)
    )
  }
  
  return filtered
})

const totalPages = computed(() => Math.ceil(filteredVouchers.value.length / itemsPerPage.value))

const paginatedVouchers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredVouchers.value.slice(start, end)
})

const showingStart = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredVouchers.value.length ? filteredVouchers.value.length : end
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

const fetchVouchers = async () => {
  try {
    loading.value = true
    // console.log('Fetching vouchers...')
    const response = await axios.get('/api/vouchers')
    // console.log('Vouchers API response:', response.data)
    
    // if (response.data.length > 0) {
    //   console.log('First voucher data:', response.data[0])
    //   console.log('Account data:', response.data[0].account)
    //   console.log('Subsidiary data:', response.data[0].subsidiary)
    //   console.log('Head data:', response.data[0].head)
    // }
    
    vouchers.value = response.data
  } catch (error) {
    console.error('Error fetching vouchers:', error)
    console.error('Error details:', error.response)
    showErrorAlert('Error', 'Failed to load vouchers')
  } finally {
    loading.value = false
  }
}

const deleteVoucher = async (id) => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You won\'t be able to revert this!'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting voucher...')
      
      await axios.delete(`/api/vouchers/${id}`)
      vouchers.value = vouchers.value.filter(voucher => voucher.id !== id)
      
      closeAlert()
      await showSuccessAlert('Deleted!', 'Voucher has been deleted.')
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting voucher:', error)
      showErrorAlert('Error', 'Failed to delete voucher')
    }
  }
}

const handleDeleteVoucher = async (id) => {
  closeDropdown()
  await deleteVoucher(id)
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
  voucherTypeFilter.value = ''
  dateFrom.value = ''
  dateTo.value = ''
  currentPage.value = 1
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-BD', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('en-BD', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatCurrency = (amount) => {
  const formattedAmount = new Intl.NumberFormat('en-BD', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount || 0)
  
  return `৳ ${formattedAmount}`
}

const getAccountName = (voucher) => {
  if (!voucher.account) return 'N/A'
  return voucher.account.name || voucher.account.account_name || 'N/A'
}

const getSubsidiaryName = (voucher) => {
  if (!voucher.subsidiary) return 'N/A'
  return voucher.subsidiary.name || 'N/A'
}

const getHeadName = (voucher) => {
  if (!voucher.head) return 'N/A'
  return voucher.head.name || voucher.head.head_name || 'N/A'
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

const getRowNumber = (voucher) => {
  const index = filteredVouchers.value.findIndex(s => s.id === voucher.id)
  return index + 1
}

watch([search, voucherTypeFilter, dateFrom, dateTo], () => {
  currentPage.value = 1
})

watch(itemsPerPage, () => {
  currentPage.value = 1
})

onMounted(() => {
  fetchVouchers()
  document.addEventListener('click', handleClickOutside)
  
  if (route.query.created === 'true') {
    showSuccessAlert('Success!', 'Voucher created successfully.')
    router.replace({ name: 'vouchers.index' })
  }
  
  if (route.query.updated === 'true') {
    showSuccessAlert('Success!', 'Voucher updated successfully.')
    router.replace({ name: 'vouchers.index' })
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>