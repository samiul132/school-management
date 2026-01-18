import { reactive } from 'vue'
import axios from 'axios'
import { permissionsStore } from './permissions'

const state = reactive({
  user: null,
  token: localStorage.getItem('token') || null,
  isAuthenticated: !!localStorage.getItem('token')
})  

if (state.token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${state.token}`
}

export const authStore = {
  state,

  async login(credentials) {
    try {
      await axios.get('/sanctum/csrf-cookie')
      const response = await axios.post('/api/login', credentials)
      this.setAuth(response.data)

      await permissionsStore.fetchPermissions()

      return response.data
    } catch (error) {
      throw error
    }
  },

  async register(userData) {
    try {
      await axios.get('/sanctum/csrf-cookie')
      const response = await axios.post('/api/register', userData)
      this.setAuth(response.data)

      await permissionsStore.fetchPermissions()

      return response.data
    } catch (error) {
      throw error
    }
  },

  async logout() {
    try {
      await axios.post('/api/logout')
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      this.clearAuth()

      await permissionsStore.fetchPermissions()

    }
  },

  async fetchUser() {
    try {
      const response = await axios.get('/api/user')
      state.user = response.data
      return response.data
    } catch (error) {
      this.clearAuth()
      throw error
    }
  },
  // User Role Auth Check
  async checkAuth() {
    if (state.token && state.isAuthenticated) {
      try {
        await this.fetchUser()
        
        if (!permissionsStore.state.isLoaded) {
          await permissionsStore.fetchPermissions()
        }
      } catch (error) {
        console.error('Auth check failed:', error)
        this.clearAuth()
      }
    }
  },

  setAuth(data) {
    state.token = data.token
    state.user = data.user
    state.isAuthenticated = true
    localStorage.setItem('token', data.token)
    axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`
  },

  clearAuth() {
    state.token = null
    state.user = null
    state.isAuthenticated = false
    localStorage.removeItem('token')
    delete axios.defaults.headers.common['Authorization']
  }
}

// Axios Response Interceptor (Auto Logout on 401)
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      authStore.clearAuth()
      permissionsStore.clearPermissions()
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)
