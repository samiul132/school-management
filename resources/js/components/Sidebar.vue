<template>
  <div>
    <!-- Backdrop overlay for mobile -->
    <transition name="backdrop">
      <div
        v-if="isMobile && sidebarOpen"
        @click="closeSidebar"
        class="fixed inset-0 bg-black/50 z-50 transition-opacity duration-300"
      ></div>
    </transition>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 flex flex-col overflow-visible h-screen bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out z-50 sidebar-container',
        isMobile 
          ? 'w-64 ' + (sidebarOpen ? 'translate-x-0' : '-translate-x-full')
          : 'transition-all ' + (sidebarOpen ? 'w-60' : 'w-28')
      ]"
    >
      <!-- Toggle -->
      <div class="relative flex items-center p-3 border-b border-gray-200">
        <div class="flex items-center min-w-0 flex-1">
          <router-link to="/" class="flex items-center space-x-1">
            <!-- Logo (show when sidebar open) -->
            <div v-if="sidebarOpen" class="w-40 h-full flex items-center">
              <img 
                :src="logoUrl" 
                alt="Logo"
                class="w-full h-auto"
              />
            </div>
            
            <!-- Icon (show when sidebar closed) -->
            <div v-else class="flex items-center justify-center w-10 h-10">
              <img 
                :src="iconUrl" 
                alt="Icon"
                class="w-40 h-full"
              />
            </div>
          </router-link>
        </div>
        <!-- Menu button - show only when sidebar is open -->
        <button
          v-if="sidebarOpen"
          @click="closeSidebar"
          class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 transition-all duration-300 shadow-sm hover:shadow-md shrink-0 ml-2 cursor-pointer"
        >
          <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <!-- Navigation -->
      <nav class="relative flex-1 mt-4 px-3 overflow-y-auto custom-scrollbar" style="overflow-y: auto; overflow-x: visible;">
        
        <!-- Dynamic Menu -->
        <div v-for="menu in primaryMenus" :key="menu.id"
         class="mb-1 relative dashboard-container overflow-visible">
          <div class="relative overflow-visible admission-item">
            <router-link v-if="!subMenus[menu.id]"
              :to="{ name: menu.frontend_route }"
              @click="handleLinkClick"
              @mouseenter="(e) => updateTooltipPosition(e, menu.title)"
              @mouseleave="hideTooltip"
              :class="[
                'flex items-center py-2 rounded-lg transition-all duration-300 group relative admission-item',
                isActiveRouteNamed(menu.frontend_route)
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
            <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    isDropdownOpen(menu.id)
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                      : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                  <span v-html="menu.icon"></span>
                </div>
               </div> 
              <span v-if="sidebarOpen">{{ menu.title }}</span>
              
              <!-- Tooltip for collapsed sidebar -->
              <div v-if="!sidebarOpen && activeTooltip === menu.title" 
                  class="tooltip-wrapper"
                  :style="tooltipStyle">
                <div class="tooltip-content">
                  {{ menu.title }}
                </div>
              </div>
            </router-link>

            <button v-if="subMenus[menu.id]"
              @click="toggleDropdown(menu.id)"
              @mouseenter="(e) => updateTooltipPosition(e, menu.title)"
              @mouseleave="hideTooltip"
              :class="[
                'group w-full flex items-center justify-between py-2 rounded-xl transition-all duration-300',
                isDropdownOpen(menu.id)
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
              <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    isDropdownOpen(menu.id)
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                      : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                  <span v-html="menu.icon"></span>
                </div>
                <span v-if="sidebarOpen" class="ml-1 font-medium whitespace-nowrap">{{ menu.title }}</span>
              </div>

              <!-- Dropdown chevron: always visible, even when collapsed -->
              <span v-if="subMenus[menu.id]" class="transition-transform duration-300 shrink-0 ml-auto">
                <svg v-if="isDropdownOpen(menu.id)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>

            <!-- Tooltip - FIXED POSITIONING -->
            <div v-if="!sidebarOpen && activeTooltip === menu.title" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">{{ menu.title }}</div>
            </div>
          </div>


          <!-- Expanded Dropdown (Sidebar Open) -->
          <transition v-if="subMenus[menu.id]" name="dropdown">
            <div v-if="isDropdownOpen(menu.id) && sidebarOpen"
              class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 space-y-1 overflow-visible">
              
              <router-link v-for="submenu in subMenus[menu.id]" :key="submenu.id"
                :to="{ name: submenu.frontend_route }"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRouteNamed(submenu.frontend_route)
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <span v-html="submenu.icon"></span>
                <span class="ml-1">{{ submenu.title }}</span>
              </router-link>

            </div>
          </transition>

          <!-- Compact Dropdown (Sidebar Collapsed) -->
          <transition v-if="subMenus[menu.id]" name="dropdown">
            <div v-if="isDropdownOpen(menu.id) && !sidebarOpen" class="mt-1 space-y-1 overflow-visible">

              <!-- Order List -->
              <router-link v-for="submenu in subMenus[menu.id]" :key="submenu.id"
                :to="{ name: submenu.frontend_route }"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, submenu.title)"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center py-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRouteNamed(submenu.frontend_route)
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <span v-html="submenu.icon"></span>
                <div v-if="!sidebarOpen && activeTooltip === submenu.title" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">{{submenu.title}}</div>
                </div>
              </router-link>

            </div>
          </transition>
        </div>

      </nav>
    </aside>
  </div>
</template>

<script>
import axios from 'axios'
import { onMounted} from 'vue'
 
const primaryMenus = []
const subMenus = []
  
export default {
  name: 'Sidebar',
  props: {
    sidebarOpen: {
      type: Boolean,
      default: true
    },
    isMobile: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      iconUrl: '/assets/favicon.png',
      logoUrl: '/assets/smart_campus_logo.png',
      activeTooltip: null,
      tooltipStyle: {},
      primaryMenus: [],
      subMenus: {},
      dropdownStates: {}
    }
  },
  computed: {
    dashboardOpenComputed() {
      return this.dashboardOpen;
    }
  },

  watch: {
    $route: {
      immediate: true,
      handler(to) {
        this.checkDynamicMenuStates(to);
      }
    }
  },

  methods: {
    isActiveRoute(path) {
      return this.$route?.path === path
    },
    isActiveRouteNamed(name) {
      return this.$route?.name === name
    },
    closeSidebar() {
      this.$emit('update:sidebarOpen', false)
    },
    handleLinkClick() {
      if (this.isMobile) {
        this.closeSidebar()
      }
    },
    updateTooltipPosition(event, tooltipName) {
      if (this.sidebarOpen) return;
      
      const element = event.currentTarget;
      const rect = element.getBoundingClientRect();
      
      this.activeTooltip = tooltipName;
      this.tooltipStyle = {
        left: `${rect.right + 10}px`,
        top: `${rect.top + (rect.height / 2)}px`,
        transform: 'translateY(-50%)'
      };
    },
    hideTooltip() {
      this.activeTooltip = null;
    },
    async fetchMenuData() {
      //console.log('Fetching menu data...')
      try {
        const response = await axios.get('/api/user/menus')
        this.primaryMenus = response.data.data.primaryMenu || []
        this.subMenus = response.data.data.subMenu
        
        if (Array.isArray(this.primaryMenus)) {
          this.primaryMenus.forEach(menu => {
            if (this.subMenus[menu.id]) {
              this.dropdownStates[menu.id] = false
            }
          })
        }
        
        this.checkDynamicMenuStates(this.$route)
      } catch (error) {
        console.error('Error fetching menu data:', error)
      }
    },

    toggleDropdown(menuId) {
      this.dropdownStates[menuId] = !this.dropdownStates[menuId]
    },

    checkDynamicMenuStates(route) {
      if (!Array.isArray(this.primaryMenus)) {
        return;
      }

      this.primaryMenus.forEach(menu => {
        if (this.subMenus[menu.id]) {
          const subMenuRoutes = this.subMenus[menu.id].map(sm => sm.frontend_route)
          const shouldOpen = subMenuRoutes.includes(route.name)
          this.dropdownStates[menu.id] = shouldOpen
        }
      })
    },

    isDropdownOpen(menuId) {
      return this.dropdownStates[menuId] || false
    }
  },

  mounted() {
    this.fetchMenuData()
  }
    
  }
</script>