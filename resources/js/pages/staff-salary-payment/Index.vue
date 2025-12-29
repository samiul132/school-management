<template>
  <AppLayout>
    <!-- Header with Create Button -->
    <div class="mb-8 flex flex-col md:flex-row justify-between md:items-center gap-4">
      <!-- Title Section -->
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Staff Salary Payments</h1>
        <p class="text-gray-600 mt-1">Manage salary payments for staff</p>
      </div>

      <!-- Button -->
      <router-link 
        :to="{ name: 'staff-salary-payment.create' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
            text-white px-6 py-3 rounded-lg font-semibold transition-colors shadow-md w-full md:w-auto"
      >
        <i class="fas fa-plus"></i>
        Create Payment
      </router-link>
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
            class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span>entries</span>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
          <!-- Search Box -->
          <div class="relative">
            <input 
              type="text" 
              v-model="search"
              placeholder="Search payments..." 
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Sheet</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Paid</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="payment in paginatedPayments" :key="payment.id" class="hover:bg-gray-50 transition-colors">
              <!-- ID -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-medium text-gray-900">#{{ payment.id }}</span>
              </td>

              <!-- Date -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ formatDate(payment.date) }}</div>
              </td>

              <!-- Salary Sheet -->
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ payment.salary_sheet ? payment.salary_sheet.title : 'N/A' }}
                </div>
              </td>

              <!-- Total Paid -->
              <td class="px-6 py-4 whitespace-nowrap text-left">
                <span class="text-sm font-semibold text-green-700">৳{{ formatCurrency(payment.total_paid) }}</span>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="relative inline-block">
                  <button
                    @click.stop="toggleDropdown(payment.id)"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium"
                  >
                    Actions
                    <i class="fas fa-chevron-down text-xs"></i>
                  </button>

                  <!-- Dropdown Menu -->
                  <div
                    v-show="openDropdownId === payment.id"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-1 z-50"
                  >
                    <router-link
                      :to="{ name: 'staff-salary-payment.edit', params: { id: payment.id } }"
                      class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                      @click="closeDropdown"
                    >
                      <i class="fas fa-edit w-4"></i>
                      <span>Edit</span>
                    </router-link>

                    <button
                      @click="handleDeletePayment(payment.id)"
                      class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                    >
                      <i class="fas fa-trash w-4"></i>
                      <span>Delete</span>
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-12 text-center">
        <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
        <p class="text-gray-600">Loading payments...</p>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && payments.length === 0" class="p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-money-bill-wave text-4xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Payments Found</h3>
        <p class="text-gray-600 mb-6">Get started by creating your first payment</p>
        <router-link 
          :to="{ name: 'staff-salary-payment.create' }"
          class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
        >
          <i class="fas fa-plus"></i>
          Create First Payment
        </router-link>
      </div>

      <!-- No Search Results -->
      <div v-if="!loading && payments.length > 0 && filteredPayments.length === 0" class="p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-search text-4xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Results Found</h3>
        <p class="text-gray-600 mb-6">No payments match your search criteria</p>
        <button 
          @click="clearFilters"
          class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
        >
          <i class="fas fa-times"></i>
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && filteredPayments.length > 0" class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing <span class="font-semibold">{{ showingStart }}</span> to 
          <span class="font-semibold">{{ showingEnd }}</span> of 
          <span class="font-semibold">{{ filteredPayments.length }}</span> results
        </div>
        
        <div class="flex items-center gap-2">
          <button 
            @click="previousPage"
            :disabled="currentPage === 1"
            class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <i class="fas fa-chevron-left"></i>
          </button>
          
          <div class="flex items-center gap-1">
            <button 
              v-for="page in visiblePages"
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                currentPage === page
                  ? 'bg-blue-600 text-white'
                  : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            
            <span v-if="hasMorePages" class="px-2 text-gray-500">...</span>
          </div>
          
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
const payments = ref([])
const loading = ref(true)
const search = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const openDropdownId = ref(null)

const fetchPayments = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/staff-salary-payment')
    
    if (response.data.success) {
      payments.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      payments.value = response.data
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to load payments')
    }
  } catch (error) {
    console.error('Error fetching payments:', error)
    showErrorAlert('Error', 'Failed to load payments. Please try again.')
  } finally {
    loading.value = false
  }
}

const filteredPayments = computed(() => {
  let filtered = payments.value
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(payment => 
      payment.salary_sheet?.title?.toLowerCase().includes(searchTerm) ||
      payment.id.toString().includes(searchTerm) ||
      formatDate(payment.date).toLowerCase().includes(searchTerm)
    )
  }
  
  return filtered
})

const totalPages = computed(() => Math.ceil(filteredPayments.value.length / itemsPerPage.value))

const paginatedPayments = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredPayments.value.slice(start, end)
})

const showingStart = computed(() => {
  return filteredPayments.value.length === 0 ? 0 : (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredPayments.value.length ? filteredPayments.value.length : end
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

const formatCurrency = (amount) => {
  return Number(amount).toLocaleString('en-BD', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
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

const deletePayment = async (id) => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You won\'t be able to revert this!'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting payment...')
      
      const response = await axios.delete(`/api/staff-salary-payment/${id}`)
      
      if (response.data.success || response.data.status === 'success') {
        payments.value = payments.value.filter(payment => payment.id !== id)
        closeAlert()
        await showSuccessAlert('Deleted!', response.data.message || 'Payment has been deleted.')
      } else {
        closeAlert()
        showErrorAlert('Error', response.data.message || 'Failed to delete payment')
      }
    } catch (error) {
      closeAlert()
      console.error('Error deleting payment:', error)
      showErrorAlert('Error', error.response?.data?.message || 'Failed to delete payment')
    }
  }
}

const handleDeletePayment = async (id) => {
  closeDropdown()
  await deletePayment(id)
}

const toggleDropdown = (id) => {
  openDropdownId.value = openDropdownId.value === id ? null : id
}

const closeDropdown = () => {
  openDropdownId.value = null
}

const clearFilters = () => {
  search.value = ''
  currentPage.value = 1
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

watch([search], () => {
  currentPage.value = 1
})

watch(itemsPerPage, () => {
  currentPage.value = 1
})

onMounted(async () => {
  await fetchPayments()
  document.addEventListener('click', handleClickOutside)
  
  if (route.query.created === 'true') {
    showSuccessAlert('Success!', 'Payment created successfully.')
    router.replace({ name: 'staff-salary-payment.index' })
  }
  
  if (route.query.updated === 'true') {
    showSuccessAlert('Success!', 'Payment updated successfully.')
    router.replace({ name: 'staff-salary-payment.index' })
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>