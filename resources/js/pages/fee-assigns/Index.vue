<template>
  <AppLayout>
    <!-- Page Header -->
    <div class="mt-2 mb-1">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Fee Assignments</h1>
        </div>
        <router-link 
          to="/fee-assigns/create"
          class="inline-flex items-center gap-2 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
        >
          <i class="fas fa-plus"></i>
          Create New
        </router-link>
      </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-3">
      <div class="px-3 py-2">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <!-- Session Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Session</label>
            <v-select
              v-model="filters.session_id"
              :options="dropdownData.sessions"
              label="name"
              :reduce="session => session.id"
              placeholder="All Sessions"
              :clearable="true"
              class="v-select-custom"
            />
          </div>

          <!-- Version Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Version</label>
            <v-select
              v-model="filters.version_id"
              :options="dropdownData.versions"
              label="name"
              :reduce="version => version.id"
              placeholder="All Versions"
              :clearable="true"
              class="v-select-custom"
            />
          </div>

          <!-- Class Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
            <v-select
              v-model="filters.class_id"
              :options="dropdownData.classes"
              label="name"
              :reduce="cls => cls.id"
              placeholder="All Classes"
              :clearable="true"
              class="v-select-custom"
            />
          </div>

          <!-- Month Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
            <v-select
              v-model="filters.month_id"
              :options="dropdownData.months"
              label="name"
              :reduce="month => month.id"
              placeholder="All Months"
              :clearable="true"
              class="v-select-custom"
            />
          </div>

          <!-- Search Button -->
          <div class="flex items-end">
            <button 
              @click="applyFilters"
              class="w-full mr-2 px-2 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors cursor-pointer"
            >
              Search
            </button>
            <button 
              @click="resetFilters"
              class="w-full px-2 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition-colors cursor-pointer"
            >
              <i class="fas fa-times mr-1"></i>
              Clear
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
      <div class="px-4 py-2 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-base font-semibold text-gray-800">Fee Assignments List</h2>
        
        <div class="flex items-center gap-3">
          <!-- Items per page -->
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600">Show</span>
            <select 
              v-model="perPage"
              @change="loadFeeAssigns"
              class="px-2 py-1 border border-gray-300 rounded text-sm"
            >
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-12 text-center">
        <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading fee assignments...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="feeAssigns.data.length === 0" class="p-12 text-center">
        <i class="fas fa-file-invoice-dollar text-4xl text-gray-400 mb-3"></i>
        <h3 class="text-lg font-medium text-gray-700">No fee assignments found</h3>
        <p class="text-gray-600 mt-1">Get started by creating a new fee assignment</p>
        <router-link 
          to="/fee-assigns/create"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors mt-4"
        >
          <i class="fas fa-plus"></i>
          Create New Fee Assignment
        </router-link>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">SL</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider w-28">Actions</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Session</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Class</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Version</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Month</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Total Amount</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Students</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(item, index) in feeAssigns.data" :key="item.id" class="hover:bg-gray-50">
              <!-- SL -->
              <td class="px-3 py-2 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">
                  {{ (feeAssigns.current_page - 1) * perPage + index + 1 }}
                </div>
              </td>

              <!-- Actions -->
              <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <!-- Dropdown Button -->
                  <button 
                    @click="toggleDropdown(item.id)"
                    class="inline-flex items-center justify-center px-2 py-1 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== item.id,
                      'bg-gray-200 text-black': openDropdownId === item.id
                    }"
                  >
                    Actions
                    <i 
                      class="fas fa-chevron-down text-xs transition-transform duration-200 ml-1"
                      :class="{
                        'rotate-180': openDropdownId === item.id,
                        'text-white': openDropdownId !== item.id,
                        'text-black': openDropdownId === item.id
                      }"
                    ></i>
                  </button>
                  
                  <!-- Dropdown Menu -->
                  <div 
                    v-if="openDropdownId === item.id"
                    class="absolute left-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <!-- View Details Option -->
                      <router-link 
                        :to="`/fee-assigns/${item.id}`"
                        class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-eye text-green-600 text-xs w-4"></i>
                        <span>View Details</span>
                      </router-link>
                      
                      <!-- Edit Option -->
                      <button
                        @click="handleEdit(item)"
                        class="flex items-center gap-2 w-full px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit</span>
                      </button>
                      
                      <!-- Delete Option -->
                      <button 
                        @click="handleDelete(item)"
                        class="flex items-center gap-2 w-full px-3 py-1.5 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Session -->
              <td class="px-3 py-2 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ item.session?.session_name }}</div>
              </td>
              
              <!-- Class -->
              <td class="px-3 py-2 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ item.class?.class_name }}</div>
              </td>
              
              <!-- Version -->
              <td class="px-3 py-2 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ item.version?.version_name }}</div>
              </td>
              
              <!-- Month -->
              <td class="px-3 py-2 whitespace-nowrap">
                <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                  <i class="fas fa-calendar-alt"></i>
                  {{ item.month?.month_name }}
                </span>
              </td>
              
              <!-- Total Amount -->
              <td class="px-3 py-2 whitespace-nowrap">
                <div class="text-base font-bold text-green-600">৳{{ formatNumber(item.total_amount) }}</div>
              </td>
              
              <!-- Students Count -->
              <td class="px-3 py-2 whitespace-nowrap">
                <div class="flex items-center gap-1.5 group/tooltip relative">
                  <i class="fas fa-users text-gray-400 text-sm"></i>
                  <span class="text-sm font-medium text-gray-900">
                    {{ item.student_count || 0 }}
                  </span>
                  
                  <!-- Tooltip -->
                  <div class="absolute left-0 bottom-full mb-2 hidden group-hover/tooltip:block bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                    {{ item.student_count || 0 }} students assigned
                  </div>
                </div>
              </td>
              
              <!-- Created Date -->
              <td class="px-3 py-2 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ formatDate(item.created_at) }}</div>
                <div class="text-xs text-gray-500">{{ formatTime(item.created_at) }}</div>
              </td>
            </tr>
          </tbody>
        </table>
        
        <!-- Pagination -->
        <div v-if="feeAssigns.data.length > 0" class="px-4 py-3 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
          <div class="text-sm text-gray-700">
            Showing {{ feeAssigns.from }} to {{ feeAssigns.to }} of {{ feeAssigns.total }} entries
          </div>
          
          <div class="flex items-center gap-2">
            <button 
              @click="changePage(feeAssigns.current_page - 1)"
              :disabled="!feeAssigns.prev_page_url"
              class="px-3 py-1.5 border border-gray-300 rounded text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Previous
            </button>
            
            <div class="flex items-center gap-1">
              <button 
                v-for="page in getPaginationPages()"
                :key="page"
                @click="changePage(page)"
                :class="[
                  'px-3 py-1.5 border text-sm font-medium rounded',
                  page === feeAssigns.current_page 
                    ? 'bg-blue-600 text-white border-blue-600' 
                    : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
            </div>
            
            <button 
              @click="changePage(feeAssigns.current_page + 1)"
              :disabled="!feeAssigns.next_page_url"
              class="px-3 py-1.5 border border-gray-300 rounded text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { showSuccessAlert, showErrorAlert, showConfirmDialog } from '../../utils/sweetAlert'

const router = useRouter()

// Data
const dropdownData = ref({
  sessions: [],
  versions: [],
  classes: [],
  months: []
})
const feeAssigns = ref({
  data: [],
  current_page: 1,
  from: 0,
  to: 0,
  total: 0,
  per_page: 20,
  last_page: 1,
  prev_page_url: null,
  next_page_url: null
})
const filters = ref({
  session_id: null,
  version_id: null,
  class_id: null,
  month_id: null
})
const loading = ref(false)
const perPage = ref(10)
const openDropdownId = ref(null)

// Methods
const loadDropdownData = async () => {
  try {
    const response = await axios.get('/api/fee-assigns/dropdown-data')
    
    dropdownData.value = {
      sessions: response.data.data.sessions.map(s => ({ ...s, name: s.session_name })),
      versions: response.data.data.versions.map(v => ({ ...v, name: v.version_name })), 
      classes: response.data.data.classes.map(c => ({ ...c, name: c.class_name })),
      months: response.data.data.months.map(m => ({ ...m, name: m.month_name })),
      fee_heads: response.data.data.fee_heads.map(f => ({ ...f, name: f.head_name }))
    }
    
  } catch (error) {
    console.error('Failed to load dropdown data:', error)
    showErrorAlert('Error', 'Failed to load filter data')
  }
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

const loadFeeAssigns = async (page = 1) => {
  loading.value = true
  
  try {
    const params = {
      page,
      per_page: perPage.value,
      ...filters.value
    }

    // Remove null filters
    Object.keys(params).forEach(key => {
      if (params[key] === null || params[key] === '') {
        delete params[key]
      }
    })

    const response = await axios.get('/api/fee-assigns', { params })
    
    if (response.data.success) {
      feeAssigns.value = response.data.data
    } else {
      showErrorAlert('Error', response.data.message)
    }
  } catch (error) {
    console.error('Failed to load fee assigns:', error)
    showErrorAlert('Error', 'Failed to load fee assignments')
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  loadFeeAssigns(1)
}

const resetFilters = () => {
  filters.value = {
    session_id: null,
    version_id: null,
    class_id: null,
    month_id: null
  }
  loadFeeAssigns(1)
}

const changePage = (page) => {
  if (page >= 1 && page <= feeAssigns.value.last_page) {
    loadFeeAssigns(page)
  }
}

const getPaginationPages = () => {
  const current = feeAssigns.value.current_page
  const last = feeAssigns.value.last_page
  const delta = 2
  const range = []
  const rangeWithDots = []
  let l

  for (let i = 1; i <= last; i++) {
    if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
      range.push(i)
    }
  }

  range.forEach(i => {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (i - l !== 1) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    l = i
  })

  return rangeWithDots
}

const formatNumber = (num) => {
  return parseFloat(num || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const handleEdit = (item) => {
  router.push(`/fee-assigns/${item.id}/edit`)
  closeDropdown()
}

const handleDelete = async (item) => {
  const result = await showConfirmDialog(
    'Delete Fee Assignment',
    `Are you sure you want to delete fee assignment for ${item.month?.month_name}?`
  )
  
  if (result.isConfirmed) {
    await deleteFeeAssign(item.id)
  }
  closeDropdown()
}

const deleteFeeAssign = async (id) => {
  try {
    const response = await axios.delete(`/api/fee-assigns/${id}`)
    
    if (response.data.success) {
      await showSuccessAlert('Success!', response.data.message)
      loadFeeAssigns()
    } else {
      showErrorAlert('Error', response.data.message)
    }
  } catch (error) {
    console.error('Failed to delete fee assign:', error)
    showErrorAlert('Error', error.response?.data?.message || 'Failed to delete fee assignment')
  }
}

// Close dropdown when clicking outside
document.addEventListener('click', (event) => {
  if (!event.target.closest('.relative')) {
    openDropdownId.value = null
  }
})

// Lifecycle
onMounted(() => {
  loadDropdownData()
  loadFeeAssigns()
})
</script>

<style scoped>
.v-select-custom {
  --vs-border-color: #d1d5db;
  --vs-border-radius: 0.5rem;
  --vs-font-size: 0.875rem;
  --vs-line-height: 1.25rem;
}

.v-select-custom:hover {
  --vs-border-color: #3b82f6;
}

.v-select-custom :deep(.vs__dropdown-toggle) {
  border: 1px solid var(--vs-border-color);
  border-radius: var(--vs-border-radius);
  padding: 0.625rem 0.75rem;
  min-height: 42px;
}
</style>