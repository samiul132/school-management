import { reactive } from 'vue'
import axios from 'axios'

const state = reactive({
  permittedRoutes: [],
  isLoaded: false
})

export const permissionsStore = {
  state,

  async fetchPermissions() {
    try {
      const response = await axios.get('/api/user/menus')
      state.permittedRoutes = response.data.data.routes || []
      state.isLoaded = true
      return state.permittedRoutes
    } catch (error) {
      console.error('Failed to load permissions:', error)
      state.permittedRoutes = []
      state.isLoaded = false
      return []
    }
  },

  hasPermission(routeName) {
    if (!Array.isArray(state.permittedRoutes)) {
      return false
    }
    return state.permittedRoutes.includes(routeName)
  },

  clearPermissions() {
    state.permittedRoutes = []
    state.isLoaded = false
  }
}