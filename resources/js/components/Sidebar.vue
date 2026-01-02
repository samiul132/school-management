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
        
        <!-- Dashboard -->
        <div class="relative overflow-visible">
          <router-link 
            to="/"
            @click="handleLinkClick"
            @mouseenter="(e) => updateTooltipPosition(e, 'Dashboard')"
            @mouseleave="hideTooltip"
            :class="[
              'flex items-center px-2 py-3 rounded-lg transition-all duration-300 group relative admission-item',
              isActiveRoute('/')
                ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
            ]"
          >
            <i class="fas fa-home mr-3"></i>
            <span v-if="sidebarOpen">Dashboard</span>

            <!-- Tooltip for collapsed mode -->
            <div v-if="!sidebarOpen && activeTooltip === 'Dashboard'" 
                 class="tooltip-wrapper"
                 :style="tooltipStyle">
              <div class="tooltip-content">
                Dashboard
              </div>
            </div>
          </router-link>
        </div>

        <!-- School Settings -->
        <div class="relative overflow-visible">
          <router-link 
            to="/school-settings"
            @click="handleLinkClick"
            @mouseenter="(e) => updateTooltipPosition(e, 'School Settings')"
            @mouseleave="hideTooltip"
            :class="[
              'flex items-center px-2 py-3 rounded-lg transition-all duration-300 group relative admission-item',
              isActiveRoute('/school-settings')
                ? 'bg-blue-50 text-blue-600 font-semibold'
                : 'text-gray-900 hover:bg-gray-100'
            ]"
          >
            <i class="fas fa-list mr-3"></i>
            <span v-if="sidebarOpen">School List</span>

            <!-- Tooltip for collapsed mode -->
            <div v-if="!sidebarOpen && activeTooltip === 'School Settings'" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">
                School List
              </div>
            </div>
          </router-link>
        </div>

        <!-- My School Settings Dropdown Menu -->
        <div class="mb-1 relative dashboard-container overflow-visible">
          <div class="relative overflow-visible admission-item">
            <button
              @click="myschoolSettingsOpen = !myschoolSettingsOpen"
              @mouseenter="(e) => updateTooltipPosition(e, 'School Settings')"
              @mouseleave="hideTooltip"
              :class="[
                'group w-full flex items-center justify-between p-2 rounded-xl transition-all duration-300',
                myschoolSettingsOpen
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
              <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-8 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    myschoolSettingsOpen
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                <i class="fa-solid fa-school-flag"></i>
                </div>
                <span v-if="sidebarOpen" class="font-medium whitespace-nowrap">School Settings</span>
              </div>

              <!-- Dropdown chevron: always visible, even when collapsed -->
              <span class="transition-transform duration-300 shrink-0 ml-auto">
                <svg v-if="myschoolSettingsOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>

            <!-- Tooltip - FIXED POSITIONING -->
            <div v-if="!sidebarOpen && activeTooltip === 'School Settings'" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">School Settings</div>
            </div>
          </div>

          <!-- Expanded Dropdown (Sidebar Open) -->
          <transition name="dropdown">
            <div v-if="myschoolSettingsOpen && sidebarOpen"
              class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 space-y-1 overflow-visible">
              
              <router-link
                to="/my-school-settings"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/my-school-settings')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-school"></i>
                <span class="ml-3">My Campus</span>
              </router-link>

              <router-link
                to="/class-routines"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/class-routines')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fa-solid fa-calendar-week"></i>
                <span class="ml-3">Class Routines</span>
              </router-link>

            </div>
          </transition>

          <!-- Compact Dropdown (Sidebar Collapsed) -->
          <transition name="dropdown">
            <div v-if="myschoolSettingsOpen && !sidebarOpen" class="mt-1 space-y-1 overflow-visible">

              <!-- >My Campus -->
              <router-link
                to="/my-school-settings"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'My Campus')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/my-school-settings')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fa-solid fa-calendar-week"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'My Campus'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">My Campus</div>
                </div>
              </router-link>

              <!-- >My Campus -->
              <router-link
                to="/class-routines"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Class Routiness')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/class-routines')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fa-solid fa-calendar-week"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Class Routiness'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Class Routiness</div>
                </div>
              </router-link>
              
            </div>
          </transition>
        </div>

        <!-- Students Dropdown Menu -->
        <div class="mb-1 relative dashboard-container overflow-visible">
          <div class="relative overflow-visible admission-item">
            <button
              @click="studentOpen = !studentOpen"
              @mouseenter="(e) => updateTooltipPosition(e, 'Students')"
              @mouseleave="hideTooltip"
              :class="[
                'group w-full flex items-center justify-between p-2 rounded-xl transition-all duration-300',
                studentOpen
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
              <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-8 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    studentOpen
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                <i class="fas fa-user-graduate"></i>
                </div>
                <span v-if="sidebarOpen" class="font-medium whitespace-nowrap">Students</span>
              </div>

              <!-- Dropdown chevron: always visible, even when collapsed -->
              <span class="transition-transform duration-300 shrink-0 ml-auto">
                <svg v-if="studentOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>

            <!-- Tooltip - FIXED POSITIONING -->
            <div v-if="!sidebarOpen && activeTooltip === 'Students'" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">Students</div>
            </div>
          </div>

          <!-- Expanded Dropdown (Sidebar Open) -->
          <transition name="dropdown">
            <div v-if="studentOpen && sidebarOpen"
              class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 space-y-1 overflow-visible">
              
              <router-link
                to="/student-profiles"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/student-profiles')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-user-friends"></i>
                <span class="ml-3">Student List</span>
              </router-link>

              <router-link
                to="/student-attendance"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/student-attendance')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-user-clock"></i>
                <span class="ml-3">Student Attendance</span>
              </router-link>

            </div>
          </transition>

          <!-- Compact Dropdown (Sidebar Collapsed) -->
          <transition name="dropdown">
            <div v-if="studentOpen && !sidebarOpen" class="mt-1 space-y-1 overflow-visible">

              <!-- Student List -->
              <router-link
                to="/student-profiles"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Student List')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/student-profiles')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-user-friends"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Student List'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Student List</div>
                </div>
              </router-link>

              <!-- Student Attendence -->
              <router-link
                to="/student-attendance"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Student Attendance')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/student-attendance')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-user-clock"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Student Attendance'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Student Attendance</div>
                </div>
              </router-link>
              
            </div>
          </transition>
        </div>

        <!-- Configuration Dropdown -->
        <div class="mb-1 relative dashboard-container overflow-visible">
          <div class="relative overflow-visible admission-item">
            <button
              @click="admissionConfigOpen = !admissionConfigOpen"
              @mouseenter="(e) => updateTooltipPosition(e, 'Configuration')"
              @mouseleave="hideTooltip"
              :class="[
                'group w-full flex items-center justify-between p-2 rounded-xl transition-all duration-300',
                admissionConfigOpen
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
              <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-8 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    admissionConfigOpen
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                      : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                  <i class="fas fa-cog"></i>
                </div>
                <span v-if="sidebarOpen" class="font-medium whitespace-nowrap">Configuration</span>
              </div>

              <!-- Dropdown chevron -->
              <span class="transition-transform duration-300 shrink-0 ml-auto">
                <svg v-if="admissionConfigOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>

            <!-- Tooltip for collapsed mode -->
            <div v-if="!sidebarOpen && activeTooltip === 'Configuration'" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">Configuration</div>
            </div>
          </div>

          <!-- Expanded Dropdown (Sidebar Open) -->
          <transition name="dropdown">
            <div v-if="admissionConfigOpen && sidebarOpen"
              class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 space-y-1 overflow-visible">
              
              <!-- Session Management -->
              <router-link
                to="/session-managements"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/session-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-calendar-alt mr-3"></i>
                <span>Session Management</span>
              </router-link>

              <!-- Month Management -->
              <router-link
                to="/month-managements"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/month-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-calendar-days mr-3"></i>
                <span>Month Management</span>
              </router-link>

              <!-- Shift Management -->
              <router-link
                to="/shift-managements"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/shift-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-clock mr-3"></i>
                <span>Shift Management</span>
              </router-link>

              <!-- Class Management -->
              <router-link
                to="/class-managements"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/class-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-chalkboard-teacher mr-3"></i>
                <span>Class Management</span>
              </router-link>

              <!-- Version Management -->
              <router-link
                to="/version-managements"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/version-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-sync-alt mr-3"></i>
                <span>Version Management</span>
              </router-link>

              <!-- Section Management -->
              <router-link
                to="/section-managements"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/section-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-layer-group mr-3"></i>
                <span>Section Management</span>
              </router-link>

            </div>
          </transition>

          <!-- Compact Dropdown (Sidebar Collapsed) -->
          <transition name="dropdown">
            <div v-if="admissionConfigOpen && !sidebarOpen" class="mt-1 space-y-1 overflow-visible">

              <!-- Session Management -->
              <router-link
                to="/session-managements"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Session Management')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/session-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-calendar-alt"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Session Management'" 
                    class="tooltip-wrapper"
                    :style="tooltipStyle">
                  <div class="tooltip-content">Session Management</div>
                </div>
              </router-link>

              <!-- Month Management -->
              <router-link
                to="/month-managements"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Month Management')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/month-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-calendar-alt"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Month Management'" 
                    class="tooltip-wrapper"
                    :style="tooltipStyle">
                  <div class="tooltip-content">Month Management</div>
                </div>
              </router-link>

              <!-- Shift Management -->
              <router-link
                to="/shift-managements"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Shift Management')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/shift-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-clock"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Shift Management'" 
                    class="tooltip-wrapper"
                    :style="tooltipStyle">
                  <div class="tooltip-content">Shift Management</div>
                </div>
              </router-link>

              <!-- Class Management -->
              <router-link
                to="/class-managements"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Class Management')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/class-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-chalkboard-teacher"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Class Management'" 
                    class="tooltip-wrapper"
                    :style="tooltipStyle">
                  <div class="tooltip-content">Class Management</div>
                </div>
              </router-link>

              <!-- Version Management -->
              <router-link
                to="/version-managements"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Version Management')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/version-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-sync-alt"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Version Management'" 
                    class="tooltip-wrapper"
                    :style="tooltipStyle">
                  <div class="tooltip-content">Version Management</div>
                </div>
              </router-link>

              <!-- Section Management -->
              <router-link
                to="/section-managements"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Section Management')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/section-managements')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-layer-group"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Section Management'" 
                    class="tooltip-wrapper"
                    :style="tooltipStyle">
                  <div class="tooltip-content">Section Management</div>
                </div>
              </router-link>

            </div>
          </transition>
        </div>

        <!-- Manage Accounts Dropdown -->
        <div class="mb-1 relative dashboard-container overflow-visible">
          <div class="relative overflow-visible admission-item">
            <button
              @click="manageAccountOpen = !manageAccountOpen"
              @mouseenter="(e) => updateTooltipPosition(e, 'Manage Accounts')"
              @mouseleave="hideTooltip"
              :class="[
                'group w-full flex items-center justify-between p-2 rounded-xl transition-all duration-300',
                manageAccountOpen
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
              <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-8 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    manageAccountOpen
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                      : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                  <i class="fas fa-cogs"></i>
                </div>
                <span v-if="sidebarOpen" class="font-medium whitespace-nowrap">Manage Accounts</span>
              </div>

              <!-- Dropdown chevron: always visible, even when collapsed -->
              <span class="transition-transform duration-300 shrink-0 ml-auto">
                <svg v-if="manageAccountOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>

            <!-- Tooltip - FIXED POSITIONING -->
            <div v-if="!sidebarOpen && activeTooltip === 'Manage Accounts'" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">Manage Accounts</div>
            </div>
          </div>

          <!-- Expanded Dropdown (Sidebar Open) -->
          <transition name="dropdown">
            <div v-if="manageAccountOpen && sidebarOpen"
              class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 space-y-1 overflow-visible">
              
              <router-link
                to="/cash-banks"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/cash-banks')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-university"></i>
                <span class="ml-3">Cash & Banks</span>
              </router-link>

              <router-link
                to="/cash-transfers"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/cash-transfers')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-exchange-alt"></i>
                <span class="ml-3">Cash Transfer</span>
              </router-link>

              <router-link
                to="/accountheads"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/accountheads')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-list"></i>
                <span class="ml-3">Account Heads</span>
              </router-link>

              <router-link
                to="/subsidiaries"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/subsidiaries')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-project-diagram"></i>
                <span class="ml-3">Subsidiaries</span>
              </router-link>

            </div>
          </transition>

          <!-- Compact Dropdown (Sidebar Collapsed) -->
          <transition name="dropdown">
            <div v-if="manageAccountOpen && !sidebarOpen" class="mt-1 space-y-1 overflow-visible">

              <!-- Cash & Banks -->
              <router-link
                to="/cash-banks"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Cash & Banks')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/cash-banks')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-university"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Cash & Banks'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Cash & Banks</div>
                </div>
              </router-link>

              <!-- Cash Transfer -->
              <router-link
                to="/cash-transfers"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Cash Transfer')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/cash-transfers')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-exchange-alt"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Cash Transfer'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Cash Transfer</div>
                </div>
              </router-link>

              <!-- Account Heads -->
              <router-link
                to="/accountheads"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Account Heads')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/accountheads')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-list"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Account Heads'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Account Heads</div>
                </div>
              </router-link>

              <!-- Subsidiaries -->
              <router-link
                to="/subsidiaries"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Subsidiaries')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/subsidiaries')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-project-diagram"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Subsidiaries'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Subsidiaries</div>
                </div>
              </router-link>

            </div>
          </transition>
        </div>

        <!-- Fees Dropdown Menu -->
        <div class="mb-1 relative dashboard-container overflow-visible">
          <div class="relative overflow-visible admission-item">
            <button
              @click="feesOpen = !feesOpen"
              @mouseenter="(e) => updateTooltipPosition(e, 'Fees')"
              @mouseleave="hideTooltip"
              :class="[
                'group w-full flex items-center justify-between p-2 rounded-xl transition-all duration-300',
                feesOpen
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
              <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-8 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    feesOpen
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                  <i class="fas fa-money-check-alt"></i>
                </div>
                <span v-if="sidebarOpen" class="font-medium whitespace-nowrap">Fees</span>
              </div>

              <!-- Dropdown chevron: always visible, even when collapsed -->
              <span class="transition-transform duration-300 shrink-0 ml-auto">
                <svg v-if="feesOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>

            <!-- Tooltip - FIXED POSITIONING -->
            <div v-if="!sidebarOpen && activeTooltip === 'Fees'" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">Fees</div>
            </div>
          </div>

          <!-- Expanded Dropdown (Sidebar Open) -->
          <transition name="dropdown">
            <div v-if="feesOpen && sidebarOpen"
              class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 space-y-1 overflow-visible">
              <!-- Fee Head -->
              <router-link
                to="/fee-heads"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/fee-heads')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-user"></i>
                <span class="ml-3">Fee Head</span>
              </router-link>

              <!-- Waver -->
              <router-link
                to="/wavers"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/wavers')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-percent"></i>
                <span class="ml-3">Waiver Management</span>
              </router-link>

              <!-- Fee Assign -->
              <router-link
                to="/fee-assigns"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/fee-assigns')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-clipboard-list"></i>
                <span class="ml-3">Fee Assign</span>
              </router-link>

              <!-- Fee Collection -->
              <router-link
                to="/payments"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/payments')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-file-invoice-dollar"></i>
                <span class="ml-3">Fee Collection</span>
              </router-link>


            </div>
          </transition>

          <!-- Compact Dropdown (Sidebar Collapsed) -->
          <transition name="dropdown">
            <div v-if="feesOpen && !sidebarOpen" class="mt-1 space-y-1 overflow-visible">

              <!-- Fee Head -->
              <router-link
                to="/fee-heads"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Fee Head')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/fee-heads')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-user"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Fee Head'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Fee Head</div>
                </div>
              </router-link>

              <!-- Waver -->
              <router-link
                to="/wavers"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Waiver Management')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/wavers')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-percent"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Waiver Management'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Waiver Management</div>
                </div>
              </router-link>

              <!-- Fee Assign -->
              <router-link
                to="/fee-assigns"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Fee Assign')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/fee-assigns')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-clipboard-list"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Fee Assign'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Fee Assign</div>
                </div>
              </router-link>

              <!-- Fee Collection -->
              <router-link
                to="/payments"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Fee Collection')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/payments')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-file-invoice-dollar"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Fee Collection'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Fee Collection</div>
                </div>
              </router-link>

            </div>
          </transition>
        </div>

        <!-- Voucher Dropdown Menu -->
        <div class="mb-1 relative dashboard-container overflow-visible">
          <div class="relative overflow-visible admission-item">
            <button
              @click="voucherOpen = !voucherOpen"
              @mouseenter="(e) => updateTooltipPosition(e, 'Voucher')"
              @mouseleave="hideTooltip"
              :class="[
                'group w-full flex items-center justify-between p-2 rounded-xl transition-all duration-300',
                voucherOpen
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
              <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-8 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    voucherOpen
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                  <i class="fas fa-file-invoice"></i>
                </div>
                <span v-if="sidebarOpen" class="font-medium whitespace-nowrap">Voucher</span>
              </div>

              <!-- Dropdown chevron: always visible, even when collapsed -->
              <span class="transition-transform duration-300 shrink-0 ml-auto">
                <svg v-if="voucherOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>

            <!-- Tooltip - FIXED POSITIONING -->
            <div v-if="!sidebarOpen && activeTooltip === 'Voucher'" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">Voucher</div>
            </div>
          </div>

          <!-- Expanded Dropdown (Sidebar Open) -->
          <transition name="dropdown">
            <div v-if="voucherOpen && sidebarOpen"
              class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 space-y-1 overflow-visible">
              
              <router-link
                to="/vouchers"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/vouchers')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-clipboard-list"></i>
                <span class="ml-3">All Vouchers</span>
              </router-link>

              <router-link
                to="/cash-in-vouchers"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/cash-in-vouchers')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-cash-register"></i>
                <span class="ml-3">Cash In Voucher</span>
              </router-link>

              <router-link
                to="/cash-out-vouchers"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/cash-out-vouchers')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-arrow-up"></i>
                <span class="ml-3">Cash Out Voucher</span>
              </router-link>

            </div>
          </transition>

          <!-- Compact Dropdown (Sidebar Collapsed) -->
          <transition name="dropdown">
            <div v-if="voucherOpen && !sidebarOpen" class="mt-1 space-y-1 overflow-visible">

              <!-- All Vouchers -->
              <router-link
                to="/vouchers"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'All Vouchers')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/vouchers')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-clipboard-list"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'All Vouchers'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">All Vouchers</div>
                </div>
              </router-link>

              <!-- Cash In Voucher -->
              <router-link
                to="/cash-in-vouchers"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Cash In Voucher')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/cash-in-vouchers')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-cash-register"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Cash In Voucher'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Cash In Voucher</div>
                </div>
              </router-link>

              <!-- Cash Out Voucher -->
              <router-link
                to="/cash-out-vouchers"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Cash Out Voucher')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/cash-out-vouchers')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-arrow-up"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Cash Out Voucher'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Cash Out Voucher</div>
                </div>
              </router-link>

            </div>
          </transition>
        </div>

        <!-- Subject Settings Dropdown Menu -->
        <div class="mb-1 relative dashboard-container overflow-visible">
          <div class="relative overflow-visible admission-item">
            <button
              @click="subjectOpen = !subjectOpen"
              @mouseenter="(e) => updateTooltipPosition(e, 'Subject Settings')"
              @mouseleave="hideTooltip"
              :class="[
                'group w-full flex items-center justify-between p-2 rounded-xl transition-all duration-300',
                subjectOpen
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
              <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-8 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    subjectOpen
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                  <i class="fas fa-book"></i>
                </div>
                <span v-if="sidebarOpen" class="font-medium whitespace-nowrap">Subject Settings</span>
              </div>

              <!-- Dropdown chevron: always visible, even when collapsed -->
              <span class="transition-transform duration-300 shrink-0 ml-auto">
                <svg v-if="subjectOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>

            <!-- Tooltip - FIXED POSITIONING -->
            <div v-if="!sidebarOpen && activeTooltip === 'Subject Settings'" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">Subject Settings</div>
            </div>
          </div>

          <!-- Expanded Dropdown (Sidebar Open) -->
          <transition name="dropdown">
            <div v-if="subjectOpen && sidebarOpen"
              class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 space-y-1 overflow-visible">
              
              <router-link
                to="/subjects"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/subjects')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-book-open"></i>
                <span class="ml-3">Subject Management</span>
              </router-link>

              <router-link
                to="/subject-assign"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/subject-assign')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-book-open"></i>
                <span class="ml-3">Subject Assign</span>
              </router-link>

            </div>
          </transition>

          <!-- Compact Dropdown (Sidebar Collapsed) -->
          <transition name="dropdown">
            <div v-if="subjectOpen && !sidebarOpen" class="mt-1 space-y-1 overflow-visible">

              <!-- Subject Management -->
              <router-link
                to="/subjects"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Subject Management')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/subjects')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-book-open"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Subject Management'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Subject Management</div>
                </div>
              </router-link>

              <!-- Subject Assign -->
              <router-link
                to="/subject-assign"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Subject Assign')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/subject-assign')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-book-open"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Subject Assign'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Subject Assign</div>
                </div>
              </router-link>
              
            </div>
          </transition>
        </div>

        <!-- HR Payroll Dropdown -->
        <div class="mb-1 relative dashboard-container overflow-visible">
          <div class="relative overflow-visible admission-item">
            <button 
              @click="hrPayrollOpen = !hrPayrollOpen"
              @mouseenter="(e) => updateTooltipPosition(e, 'HR Payroll')"
              @mouseleave="hideTooltip"
              :class="[
                'group w-full flex items-center justify-between p-2 rounded-xl transition-all duration-300',
                hrPayrollOpen
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
              <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-8 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    hrPayrollOpen
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                <i class="fas fa-money-check-alt"></i>
                </div>
                <span v-if="sidebarOpen" class="font-medium whitespace-nowrap">HR Payroll</span>
              </div>

              <!-- Dropdown chevron: always visible, even when collapsed -->
              <span class="transition-transform duration-300 shrink-0 ml-auto">
                <svg v-if="hrPayrollOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>

            <!-- Tooltip - FIXED POSITIONING -->
            <div v-if="!sidebarOpen && activeTooltip === 'HR Payroll'" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">HR Payroll</div>
            </div>
          </div>

          <!-- Expanded Dropdown (Sidebar Open) -->
          <transition name="dropdown">
            <div v-if="hrPayrollOpen && sidebarOpen"
              class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 space-y-1 overflow-visible">
              <!-- Designation -->
              <router-link
                to="/designations"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/designations')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-award"></i> 
                <span class="ml-1">Designation</span>
              </router-link>

              <!-- Staffs -->
              <router-link
                to="/staffs"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/staffs')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-users"></i>
                <span class="ml-1">Staffs</span>
              </router-link>

              <!-- Attendance -->
              <router-link
                to="/attendance"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/attendance')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-user-clock"></i>
                <span class="ml-1">Attendance</span>
              </router-link>

              <!-- Leave -->
              <router-link
                to="/leave"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/leave')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-plane-departure"></i>
                <span class="ml-1">Leave</span>
              </router-link>

              <!-- Advance Payment -->
              <router-link
                to="/advance-payment"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/advance-payment')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-hand-holding-usd"></i>
                <span class="ml-1">Advance Payment</span>
              </router-link>

              <!-- Over Time -->
              <router-link
                to="/over-time"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/over-time')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-business-time"></i> 
                <span class="ml-1">Over Time</span>
              </router-link>

              <!-- Staff Salary -->
              <router-link
                to="/staff-salary"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/staff-salary')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-coins"></i>
                <span class="ml-1">Staff Salary</span>
              </router-link>

              <!-- Salary Payment -->
              <router-link
                to="/staff-salary-payment"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/staff-salary-payment')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-money-check-alt"></i>
                <span class="ml-1">Staff Salary Payment</span>
              </router-link>

            </div>
          </transition>

          <!-- Compact Dropdown (Sidebar Collapsed) -->
          <transition name="dropdown">
            <div v-if="hrPayrollOpen && !sidebarOpen" class="mt-1 space-y-1 overflow-visible">

              <!-- Designation -->
              <router-link
                to="/designations"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Designation')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center py-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/designations')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-award"></i> 
                <div v-if="!sidebarOpen && activeTooltip === 'Designation'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Designation</div>
                </div>
              </router-link>

              <!-- Staffs -->
              <router-link
                to="/staffs"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Staffs')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center py-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/staffs')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-users"></i> 
                <div v-if="!sidebarOpen && activeTooltip === 'Staffs'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Staffs</div>
                </div>
              </router-link>

              <!-- Attendance -->
              <router-link
                to="/attendance"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Attendance')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center py-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/attendance')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-user-clock"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Attendance'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Attendance</div>
                </div>
              </router-link>

              <!-- Leave -->
              <router-link
                to="/leave"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Leave')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center py-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/leave')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-plane-departure"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Leave'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Leave</div>
                </div>
              </router-link>

              <!-- Advance Payment -->
              <router-link
                to="/advance-payment"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Advance Payment')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center py-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/advance-payment')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-hand-holding-usd"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Advance Payment'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Advance Payment</div>
                </div>
              </router-link>
              
              <!-- Over Time -->
              <router-link
                to="/over-time"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Over Time')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center py-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/over-time')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-business-time"></i> 
                <div v-if="!sidebarOpen && activeTooltip === 'Over Time'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Over Time</div>
                </div>
              </router-link>

              <!-- Staff Salary -->
              <router-link
                to="/staff-salary"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Staff Salary')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center py-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/staff-salary')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-coins"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Staff Salary'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Staff Salary</div>
                </div>
              </router-link>

              <!-- Salary Payment -->
              <router-link
                to="/staff-salary-payment"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Staff Salary')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center py-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/staff-salary-payment')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-money-check-alt"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Staff Salary'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Staff Salary Payment</div>
                </div>
              </router-link>


            </div>
          </transition>
        </div>

        <!-- Settings Dropdown Menu -->
        <div class="mb-1 relative dashboard-container overflow-visible">
          <div class="relative overflow-visible admission-item">
            <button
              @click="settingsOpen = !settingsOpen"
              @mouseenter="(e) => updateTooltipPosition(e, 'Settings')"
              @mouseleave="hideTooltip"
              :class="[
                'group w-full flex items-center justify-between p-2 rounded-xl transition-all duration-300',
                settingsOpen
                  ? 'bg-blue-50 text-blue-600 font-semibold'
                  : 'text-gray-900 hover:bg-gray-100'
              ]"
            >
              <div class="flex items-center min-w-0">
                <div
                  :class="[
                    'w-8 h-10 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0',
                    settingsOpen
                      ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                  ]"
                >
                  <i class="fas fa-book"></i>
                </div>
                <span v-if="sidebarOpen" class="font-medium whitespace-nowrap">Settings</span>
              </div>

              <!-- Dropdown chevron: always visible, even when collapsed -->
              <span class="transition-transform duration-300 shrink-0 ml-auto">
                <svg v-if="settingsOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>

            <!-- Tooltip - FIXED POSITIONING -->
            <div v-if="!sidebarOpen && activeTooltip === 'Settings'" 
                class="tooltip-wrapper"
                :style="tooltipStyle">
              <div class="tooltip-content">Settings</div>
            </div>
          </div>

          <!-- Expanded Dropdown (Sidebar Open) -->
          <transition name="dropdown">
            <div v-if="settingsOpen && sidebarOpen"
              class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 space-y-1 overflow-visible">
              
              <router-link
                to="/post-notifications"
                @click="handleLinkClick"
                :class="[
                  'group flex items-center py-2 px-2 text-sm rounded-lg',
                  isActiveRoute('/post-notifications')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-book-open"></i>
                <span class="ml-3">Post Notification</span>
              </router-link>

            </div>
          </transition>

          <!-- Compact Dropdown (Sidebar Collapsed) -->
          <transition name="dropdown">
            <div v-if="settingsOpen && !sidebarOpen" class="mt-1 space-y-1 overflow-visible">

              <!-- Post Notification -->
              <router-link
                to="/post-notifications"
                @click="handleLinkClick"
                @mouseenter="(e) => updateTooltipPosition(e, 'Post Notification')"
                @mouseleave="hideTooltip"
                :class="[
                  'group relative flex items-center p-2 rounded-xl transition-all duration-300 dropdown-item',
                  isActiveRoute('/post-notifications')
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-gray-900 hover:bg-gray-100'
                ]"
              >
                <i class="fas fa-book-open"></i>
                <div v-if="!sidebarOpen && activeTooltip === 'Post Notification'" 
                     class="tooltip-wrapper"
                     :style="tooltipStyle">
                  <div class="tooltip-content">Post Notification</div>
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
      dashboardOpen: false,
      purchaseOpen: false,
      orderOpen: false,
      manageAccountOpen: false,
      voucherOpen: false,
      subjectOpen: false,
      studentOpen: false,
      hrPayrollOpen: false,
      feesOpen: false,
      admissionConfigOpen: false,
      settingsOpen: false,
      myschoolSettingsOpen: false,
      activeTooltip: null,
      tooltipStyle: {}
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
        const dashboardRoutes = [
          '/',
          // dashborad
        ];
        this.dashboardOpen = dashboardRoutes.includes(to.path);

        const schoolSettingsRoutes = [
          '/school-settings'
        ];
        this.schoolSettingsActive = schoolSettingsRoutes.includes(to.path);
        
        const myschoolSettingsRoutes = [
          '/my-school-settings',
          '/class-routines',
        ];
        this.myschoolSettingsOpen = myschoolSettingsRoutes.includes(to.path);
        const studentProfilesRoutes = [
          '/student-profiles'
        ];
        this.studentProfilesActive = studentProfilesRoutes.includes(to.path);
        
        const admissionConfigRoutes = [
          '/session-managements',
          '/month-managements',
          '/shift-managements',
          '/class-managements',
          '/version-managements',
          '/section-managements'
        ];
        this.admissionConfigOpen = admissionConfigRoutes.includes(to.path);

        const manageAccountRoutes = [
          '/cash-banks',
          '/cash-transfers',
          '/accountheads',
          '/subsidiaries'
        ];
        this.manageAccountOpen = manageAccountRoutes.includes(to.path);

        const feesRoutes = [
          '/fee-heads',
          '/wavers',
          '/fee-assigns',
          '/payments'
        ];
        this.feesOpen = feesRoutes.includes(to.path);

        const voucherRoutes = [
          '/vouchers',
          '/cash-in-vouchers',
          '/cash-out-vouchers'
        ];
        this.voucherOpen = voucherRoutes.includes(to.path);

        const subjectRoutes = [
          '/subjects',
          '/subject-assign',
        ];
        this.subjectOpen = subjectRoutes.includes(to.path);

        const studentRoutes = [
          '/student-profiles',
          '/student-attendance',
        ];
        this.studentOpen = studentRoutes.includes(to.path);

        const hrPayrollRoutes = [
          '/designations',
          '/staffs',
          '/attendance',
          '/leave',
          '/advance-payment',
          '/over-time',
          '/staff-salary',
          '/staff-salary-payment',
        ];
        this.hrPayrollOpen = hrPayrollRoutes.includes(to.path);

        const settingsRoutes = [
          '/designations',
        ];
        this.settingsOpen = settingsRoutes.includes(to.path);
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
    }
  }
}
</script>