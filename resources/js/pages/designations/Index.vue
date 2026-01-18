<template>
  <AppLayout>
    <div class="mb-3 flex flex-col md:flex-row justify-between md:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">
          All Designation
        </h1>
        <p class="text-gray-600">
          Manage all Designation
        </p>
      </div>

      <router-link 
        :to="{ name: 'designations.create' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
            text-white px-3 py-2 rounded-lg font-semibold transition-colors w-full md:w-auto"
      >
        <i class="fas fa-plus"></i>
        Create New Designation
      </router-link>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
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
          <div class="relative">
            <input 
              type="text" 
              v-model="search"
              placeholder="Search Designations..." 
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-64"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">SL</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Designation Name</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Created At</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Updated At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="designation in paginatedDesignations" :key="designation.id" class="hover:bg-gray-50">
              
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-mono">
                {{ getRowNumber(designation) }}
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                <div class="relative">
                  <button 
                    @click="toggleDropdown(designation.id)"
                    class="inline-flex items-center justify-center px-2 h-8 rounded-lg transition-colors gap-1 cursor-pointer"
                    :class="{
                      'bg-blue-600 text-white': openDropdownId !== designation.id,
                      'bg-gray-200 text-black': openDropdownId === designation.id
                    }"
                  >
                    Action
                    <i 
                      class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                      :class="{
                        'rotate-180': openDropdownId === designation.id,
                        'text-white': openDropdownId !== designation.id,
                        'text-black': openDropdownId === designation.id
                      }"
                    ></i>
                  </button>
                  
                  <div 
                    v-if="openDropdownId === designation.id"
                    class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                    @click.stop
                  >
                    <div class="py-1">
                      <router-link 
                        :to="{ name: 'designations.edit', params: { id: designation.id } }"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                        @click="closeDropdown"
                      >
                        <i class="fas fa-edit text-blue-600 text-xs w-4"></i>
                        <span>Edit</span>
                      </router-link>
                      
                      <button 
                        @click="handleDeleteDesignations(designation.id)"
                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors text-left"
                      >
                        <i class="fas fa-trash text-xs w-4"></i>
                        <span>Delete</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                <div class="truncate max-w-[180px]" :title="designation.name">
                  {{ designation.name }}
                </div>
              </td>
              
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(designation.created_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(designation.created_at) }}</span>
                </div>
              </td>

              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">
                <div class="flex flex-col">
                  <span>{{ formatDate(designation.updated_at) }}</span>
                  <span class="text-xs text-gray-500">{{ formatTime(designation.updated_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Loading Designation...</p>
      </div>

      <div v-if="!loading && designations.length === 0" class="p-8 text-center">
        <i class="fas fa-building text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No Designation Found.</p>
        <router-link 
          :to="{ name: 'designations.create' }"
          class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Add First Designation
        </router-link>
      </div>

      <div v-if="!loading && designations.length > 0 && filteredDesignations.length === 0" class="p-8 text-center">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No Designation match your search criteria.</p>
        <button 
          @click="clearFilters"
          class="inline-block mt-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <div v-if="!loading && filteredDesignations.length > 0" class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing {{ showingStart }} to {{ showingEnd }} of {{ filteredDesignations.length }} results
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
import { ref, computed, onMounted, watch, onUnmounted, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showConfirmDialog, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const designations = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const openDropdownId = ref(null)

const alertShown = ref({
  created: false,
  updated: false
})

const totalDesignations = computed(() => designations.value.length)


const showingCount = computed(() => paginatedDesignations.value.length)

const filteredDesignations = computed(() => {
  let filtered = designations.value
  
  if (search.value) {
    const searchTerm = search.value.toLowerCase()
    filtered = filtered.filter(designation => 
      designation.name?.toLowerCase().includes(searchTerm)
    )
  }
  
  
  return filtered
})

const totalPages = computed(() => Math.ceil(filteredDesignations.value.length / itemsPerPage.value))

const paginatedDesignations = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredDesignations.value.slice(start, end)
})

const showingStart = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value + 1
})

const showingEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value
  return end > filteredDesignations.value.length ? filteredDesignations.value.length : end
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

const fetchDesignations = async () => {
  try {
    loading.value = true
    //console.log('Fetching Designations...')
    const response = await axios.get('/api/designations')
    //console.log('Designation data:', response.data)

    designations.value = response.data.data
  } catch (error) {
    console.error('Error fetching designations:', error)
    showErrorAlert('Error', 'Failed to load designations')
  } finally {
    loading.value = false
  }
}

const showSuccessMessage = () => {
  if (route.query.created === 'true' && !alertShown.value.created) {
    nextTick(() => {
      showSuccessAlert('Success!', 'Designation created successfully.')
      alertShown.value.created = true
      
      router.replace({ name: 'designations.index' })
    })
  }
  
  if (route.query.updated === 'true' && !alertShown.value.updated) {
    nextTick(() => {
      showSuccessAlert('Success!', 'Designation updated successfully.')
      alertShown.value.updated = true
      
      router.replace({ name: 'designations.index' })
    })
  }
}

const deleteDesignations = async (id) => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You won\'t be able to revert this!'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting Designation...')
      
      await axios.delete(`/api/designations/${id}`)
      designations.value = designations.value.filter(designation => designation.id !== id)
      
      closeAlert()
      await showSuccessAlert('Deleted!', 'Designation has been deleted.')
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting Designation:', error)
      showErrorAlert('Error', 'Failed to delete Designation')
    }
  }
}

const handleDeleteDesignations = async (id) => {
  closeDropdown()
  await deleteDesignations(id)
}

const getRowNumber = (designation) => {
  const index = filteredDesignations.value.findIndex(s => s.id === designation.id)
  return index + 1
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
  statusFilter.value = ''
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

watch([search, statusFilter], () => {
  currentPage.value = 1
})

watch(itemsPerPage, () => {
  currentPage.value = 1
})

watch(
  () => route.query,
  (newQuery) => {
    if (newQuery.created === 'true' || newQuery.updated === 'true') {
      showSuccessMessage()
    }
  },
  { immediate: true }
)

onMounted(() => {
  fetchDesignations()
  document.addEventListener('click', handleClickOutside)
  
  showSuccessMessage()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>