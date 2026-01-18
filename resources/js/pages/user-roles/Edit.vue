<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-3 flex flex-col md:flex-row justify-between md:items-center gap-4">
      <!-- Title Section -->
      <div>
        <h1 class="text-3xl font-bold text-gray-800">
          Edit User Role
        </h1>
        <p class="text-gray-600">
          Update user role and permissions
        </p>
      </div>

      <!-- Back Button -->
      <router-link 
        :to="{ name: 'user-roles.index' }"
        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
            text-white px-3 py-2 rounded-lg font-semibold transition-colors w-full md:w-auto"
      >
        <i class="fas fa-arrow-left"></i>
        Back To List
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loadingRole" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
      <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
      <p class="mt-4 text-gray-600">Loading role data...</p>
    </div>

    <!-- Form Section -->
    <div v-else class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <form @submit.prevent="handleSubmit">
        <div class="p-6">
          <!-- Role Name Input -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Role Name <span class="text-red-500">*</span>
            </label>
            <input 
              type="text" 
              v-model="formData.role_name"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white text-gray-800"
              placeholder="Enter role name..."
              required
            >
            <p v-if="errors.role_name" class="mt-1 text-sm text-red-600">{{ errors.role_name }}</p>
          </div>

          <!-- Permissions Section -->
          <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-gray-800">Permissions</h3>
              <div class="flex gap-2">
                <button 
                  type="button"
                  @click="selectAll"
                  class="px-3 py-1 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
                >
                  <i class="fas fa-check-double mr-1"></i> Select All
                </button>
                <button 
                  type="button"
                  @click="deselectAll"
                  class="px-3 py-1 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                >
                  <i class="fas fa-times mr-1"></i> Deselect All
                </button>
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="loadingMenus" class="text-center py-8">
              <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
              <p class="mt-2 text-gray-600">Loading permissions...</p>
            </div>

            <!-- Permissions Table -->
            <div v-else class="overflow-x-auto border border-gray-200 rounded-lg">
              <table class="w-full min-w-[800px]">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">SL</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modules</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permission</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <template v-for="(menu, index) in primaryMenus" :key="menu.id">
                    <!-- Primary Menu Row -->
                    <tr class="bg-blue-50">
                      <td class="px-4 py-4 font-bold text-gray-800">
                        {{ index + 1 }}
                      </td>
                      <td class="px-4 py-4 font-bold text-lg text-gray-800">
                        <div class="flex items-center gap-2">
                          <div v-html="menu.icon"></div>
                          <span>{{ menu.title }}</span>
                        </div>
                      </td>
                      <td class="px-4 py-4">
                        <div class="flex flex-wrap gap-3">
                          <!-- Primary Menu Checkbox -->
                          <div class="flex items-center">
                            <input 
                              :id="`menu_${menu.id}`"
                              type="checkbox"
                              v-model="formData.permitted_menu[menu.id]"
                              class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                            >
                            <label 
                              :for="`menu_${menu.id}`" 
                              class="ml-2 text-sm font-medium text-gray-700 cursor-pointer"
                            >
                              {{ menu.permition }}
                            </label>
                          </div>
                          
                          <!-- Primary Menu Permissions (non-navbar) -->
                          <template v-for="permission in getPrimaryMenuPermissions(menu.id)" :key="permission.id">
                            <div class="flex items-center">
                              <input 
                                :id="`menu_${permission.id}`"
                                type="checkbox"
                                v-model="formData.permitted_menu[permission.id]"
                                class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                              >
                              <label 
                                :for="`menu_${permission.id}`" 
                                class="ml-2 text-sm font-medium text-gray-700 cursor-pointer"
                              >
                                {{ permission.permition }}
                              </label>
                            </div>
                          </template>
                        </div>
                      </td>
                    </tr>

                    <!-- Sub Menus -->
                    <template v-for="(subMenu, subIndex) in getSubMenus(menu.id)" :key="subMenu.id">
                      <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800 pl-12">
                          {{ index + 1 }}.{{ subIndex + 1 }}
                        </td>
                        <td class="px-4 py-3 text-gray-800">
                          <div class="flex items-center gap-2">
                            <div v-html="subMenu.icon"></div>
                            <span>{{ subMenu.title }}</span>
                          </div>
                        </td>
                        <td class="px-4 py-3">
                          <div class="flex flex-wrap gap-3">
                            <!-- Sub Menu Checkbox -->
                            <div class="flex items-center">
                              <input 
                                :id="`menu_${subMenu.id}`"
                                type="checkbox"
                                v-model="formData.permitted_menu[subMenu.id]"
                                class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                              >
                              <label 
                                :for="`menu_${subMenu.id}`" 
                                class="ml-2 text-sm font-medium text-gray-700 cursor-pointer"
                              >
                                {{ subMenu.permition }}
                              </label>
                            </div>
                            
                            <!-- Sub Menu Permissions (non-navbar) -->
                            <template v-for="permission in getSubMenuPermissions(subMenu.id)" :key="permission.id">
                              <div class="flex items-center">
                                <input 
                                  :id="`menu_${permission.id}`"
                                  type="checkbox"
                                  v-model="formData.permitted_menu[permission.id]"
                                  class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                                >
                                <label 
                                  :for="`menu_${permission.id}`" 
                                  class="ml-2 text-sm font-medium text-gray-700 cursor-pointer"
                                >
                                  {{ permission.permition }}
                                </label>
                              </div>
                            </template>
                          </div>
                        </td>
                      </tr>
                    </template>
                  </template>
                </tbody>
              </table>
            </div>
            <p v-if="errors.permitted_menu" class="mt-2 text-sm text-red-600">{{ errors.permitted_menu }}</p>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end gap-3">
            <router-link 
              :to="{ name: 'user-roles.index' }"
              class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold"
            >
              Cancel
            </router-link>
            <button 
              type="submit"
              :disabled="submitting"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <i v-if="submitting" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-save mr-2"></i>
              {{ submitting ? 'Updating...' : 'Update Role' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()

// Form data
const formData = ref({
  role_name: '',
  permitted_menu: {}
})

const menuList = ref([])
const loadingRole = ref(true)
const loadingMenus = ref(true)
const submitting = ref(false)
const errors = ref({})
const roleId = route.params.id

// Computed properties
const primaryMenus = computed(() => {
  return menuList.value.filter(menu => 
    menu.is_primary_menu === 1 && 
    menu.show_on_navbar === 1 &&
    menu.parent_id === null // শুধু root level primary menus
  )
})

// Methods
const fetchRole = async () => {
  try {
    loadingRole.value = true
    const response = await axios.get(`/api/user-roles/${roleId}`)
    
    console.log('Role data:', response.data)
    
    formData.value.role_name = response.data.role_name
    
    // Set permitted menus from role_details
    if (response.data.role_details && Array.isArray(response.data.role_details)) {
      response.data.role_details.forEach(detail => {
        formData.value.permitted_menu[detail.menu_id] = true
      })
    }
    
    console.log('Permitted menus:', formData.value.permitted_menu)
    
  } catch (error) {
    console.error('Error fetching role:', error)
    showErrorAlert('Error', 'Failed to load role data')
    router.push({ name: 'user-roles.index' })
  } finally {
    loadingRole.value = false
  }
}

const fetchMenus = async () => {
  try {
    loadingMenus.value = true
    const response = await axios.get('/api/user-roles/menus/all')
    
    // Check if response is valid JSON array
    if (Array.isArray(response.data)) {
      menuList.value = response.data
      console.log('Menus loaded:', menuList.value)
    } else {
      console.error('Invalid response format:', response.data)
      throw new Error('Invalid response format from server')
    }
  } catch (error) {
    console.error('Error fetching menus:', error)
    console.error('Error response:', error.response)
    showErrorAlert('Error', 'Failed to load menu permissions. Please check your API configuration.')
    menuList.value = [] // Set empty array to prevent filter errors
  } finally {
    loadingMenus.value = false
  }
}

const getSubMenus = (parentId) => {
  return menuList.value.filter(menu => 
    menu.parent_id === parentId && menu.show_on_navbar === 1
  )
}

const getPrimaryMenuPermissions = (parentId) => {
  return menuList.value.filter(menu => 
    menu.parent_id === parentId && menu.show_on_navbar === 0
  )
}

const getSubMenuPermissions = (parentId) => {
  return menuList.value.filter(menu => 
    menu.parent_id === parentId && menu.show_on_navbar === 0
  )
}

const selectAll = () => {
  menuList.value.forEach(menu => {
    formData.value.permitted_menu[menu.id] = true
  })
}

const deselectAll = () => {
  formData.value.permitted_menu = {}
}

const handleSubmit = async () => {
  try {
    errors.value = {}
    
    // Validation
    if (!formData.value.role_name.trim()) {
      errors.value.role_name = 'Role name is required'
      return
    }

    const selectedMenus = Object.keys(formData.value.permitted_menu)
      .filter(key => formData.value.permitted_menu[key])
      .map(key => parseInt(key))

    if (selectedMenus.length === 0) {
      errors.value.permitted_menu = 'Please select at least one permission'
      showErrorAlert('Validation Error', 'Please select at least one permission')
      return
    }

    submitting.value = true
    showLoadingAlert('Updating role...')

    const payload = {
      role_name: formData.value.role_name,
      permitted_menu: selectedMenus
    }

    console.log('Submitting payload:', payload)

    const response = await axios.put(`/api/user-roles/${roleId}`, payload)
    
    closeAlert()
    await showSuccessAlert('Success!', 'Role updated successfully')
    
    router.push({ name: 'user-roles.index', query: { updated: 'true' } })
    
  } catch (error) {
    closeAlert()
    submitting.value = false
    console.error('Error updating role:', error)
    
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
      const errorMessages = Object.values(error.response.data.errors).flat().join('\n')
      showErrorAlert('Validation Error', errorMessages)
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to update role')
    }
  }
}

// Lifecycle
onMounted(async () => {
  // Load menus first
  await fetchMenus()
  // Then load role data
  await fetchRole()
})
</script>

<style scoped>
/* Custom checkbox styling */
input[type="checkbox"]:checked {
  background-color: #2563eb;
  border-color: #2563eb;
}

input[type="checkbox"]:focus {
  ring-color: #3b82f6;
}

/* Table responsiveness */
@media (max-width: 768px) {
  table {
    font-size: 0.875rem;
  }
  
  .px-4 {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
  }
}
</style>