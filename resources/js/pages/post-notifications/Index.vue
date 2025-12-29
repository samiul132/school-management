<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Post Notifications</h1>
        <p class="text-gray-600 mt-2">Send notifications to mobile apps</p>
      </div>
      <button
        @click="openModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-semibold"
      >
        <i class="fas fa-plus mr-2"></i>
        New Notification
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Notifications</p>
            <p class="text-3xl font-bold text-gray-800">{{ notifications.length }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
            <i class="fas fa-bell text-white text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Latest Notification</p>
            <p class="text-lg font-bold text-gray-800 truncate">
              {{ notifications[0]?.name || 'No notifications yet' }}
            </p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
            <i class="fas fa-paper-plane text-white text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-2xl shadow-lg">
      <div class="p-6 border-b">
        <input 
          type="text" 
          v-model="search"
          placeholder="Search notifications..." 
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
        >
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="item in filteredNotifications" :key="item.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 font-medium text-gray-800">{{ item.name }}</td>
              <td class="px-6 py-4 text-gray-600">
                <div class="max-w-md truncate">{{ item.description }}</div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ formatDate(item.created_at) }}
              </td>
              <td class="px-6 py-4">
                <button 
                  @click="deleteNotification(item.id)"
                  class="text-red-600 hover:text-red-800"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="loading" class="p-8 text-center">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
      </div>

      <div v-if="!loading && notifications.length === 0" class="p-8 text-center">
        <i class="fas fa-bell-slash text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No notifications yet</p>
      </div>
    </div>

    <!-- Create Modal -->
    <div 
      v-if="showModal" 
      class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-semibold">Send Notification</h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="createNotification">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
            <input 
              type="text" 
              v-model="form.name"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              placeholder="Notification title"
              required
            >
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea 
              v-model="form.description"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              placeholder="Notification description"
              rows="4"
            ></textarea>
          </div>

          <div class="flex gap-3">
            <button 
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2 border rounded-lg hover:bg-gray-50"
            >
              Cancel
            </button>
            <button 
              type="submit"
              :disabled="submitting"
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              <i v-if="submitting" class="fas fa-spinner fa-spin mr-2"></i>
              Send Notification
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showConfirmDialog } from '../../utils/sweetAlert'

const notifications = ref([])
const loading = ref(true)
const search = ref('')
const showModal = ref(false)
const submitting = ref(false)

const form = ref({
  name: '',
  description: ''
})

const fetchNotifications = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/post-notifications')
    notifications.value = response.data?.data || []
  } catch (error) {
    showErrorAlert('Error', 'Failed to load notifications')
  } finally {
    loading.value = false
  }
}

const createNotification = async () => {
  try {
    submitting.value = true
    const response = await axios.post('/api/post-notifications', form.value)
    
    notifications.value.unshift(response.data.data)
    closeModal()
    showSuccessAlert('Success!', 'Notification sent to all mobile apps!')
    
    form.value = { name: '', description: '' }
  } catch (error) {
    showErrorAlert('Error', error.response?.data?.message || 'Failed to send notification')
  } finally {
    submitting.value = false
  }
}

const deleteNotification = async (id) => {
  const result = await showConfirmDialog('Delete?', 'Are you sure?', 'warning')
  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/post-notifications/${id}`)
      notifications.value = notifications.value.filter(n => n.id !== id)
      showSuccessAlert('Deleted!', 'Notification deleted successfully')
    } catch (error) {
      showErrorAlert('Error', 'Failed to delete notification')
    }
  }
}

const openModal = () => {
  showModal.value = true
  document.body.style.overflow = 'hidden'
}

const closeModal = () => {
  showModal.value = false
  document.body.style.overflow = 'auto'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const filteredNotifications = computed(() => {
  if (!search.value) return notifications.value
  return notifications.value.filter(n => 
    n.name?.toLowerCase().includes(search.value.toLowerCase()) ||
    n.description?.toLowerCase().includes(search.value.toLowerCase())
  )
})

onMounted(() => {
  fetchNotifications()
})
</script>