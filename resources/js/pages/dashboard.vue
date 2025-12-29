<template>
  <AppLayout>
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard Overview</h1>
      <p class="text-gray-600">Welcome back! Here's what's happening with your business today.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      
      <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-users text-white text-xl"></i>
          </div>
          <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
            <i class="fas fa-arrow-up mr-1"></i>12.5%
          </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Total Users</h3>
        <p class="text-3xl font-bold text-gray-800 mb-2">1,247</p>
        <div class="flex items-center text-xs text-gray-500">
          <i class="fas fa-clock mr-1"></i>
          <span>Updated 5 min ago</span>
        </div>
        <div class="mt-4 bg-gray-200 rounded-full h-2 overflow-hidden">
          <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full rounded-full" style="width: 78%"></div>
        </div>
      </div>

      <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-shopping-cart text-white text-xl"></i>
          </div>
          <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
            <i class="fas fa-arrow-up mr-1"></i>8.3%
          </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Total Orders</h3>
        <p class="text-3xl font-bold text-gray-800 mb-2">3,428</p>
        <div class="flex items-center text-xs text-gray-500">
          <i class="fas fa-clock mr-1"></i>
          <span>Updated 2 min ago</span>
        </div>
        <div class="mt-4 bg-gray-200 rounded-full h-2 overflow-hidden">
          <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-full rounded-full" style="width: 65%"></div>
        </div>
      </div>

      <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-dollar-sign text-white text-xl"></i>
          </div>
          <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
            <i class="fas fa-arrow-up mr-1"></i>24.7%
          </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Total Revenue</h3>
        <p class="text-3xl font-bold text-gray-800 mb-2">$47,582</p>
        <div class="flex items-center text-xs text-gray-500">
          <i class="fas fa-clock mr-1"></i>
          <span>Updated just now</span>
        </div>
        <div class="mt-4 bg-gray-200 rounded-full h-2 overflow-hidden">
          <div class="bg-gradient-to-r from-green-500 to-green-600 h-full rounded-full" style="width: 88%"></div>
        </div>
      </div>

      <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-star text-white text-xl"></i>
          </div>
          <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
            <i class="fas fa-arrow-down mr-1"></i>3.2%
          </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Customer Feedback</h3>
        <p class="text-3xl font-bold text-gray-800 mb-2">892</p>
        <div class="flex items-center text-xs text-gray-500">
          <i class="fas fa-clock mr-1"></i>
          <span>Updated 15 min ago</span>
        </div>
        <div class="mt-4 bg-gray-200 rounded-full h-2 overflow-hidden">
          <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-full rounded-full" style="width: 72%"></div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      
      <div class="stat-card bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-3">
          <i class="fas fa-signal text-3xl opacity-80"></i>
          <div class="text-right">
            <p class="text-sm opacity-90">Active Sessions</p>
            <p class="text-2xl font-bold">1,847</p>
          </div>
        </div>
        <div class="flex items-center justify-between text-sm mt-4">
          <span class="opacity-90">Live Users</span>
          <span class="font-semibold">247</span>
        </div>
      </div>

      <div class="stat-card bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-3">
          <i class="fas fa-chart-line text-3xl opacity-80"></i>
          <div class="text-right">
            <p class="text-sm opacity-90">Conversion Rate</p>
            <p class="text-2xl font-bold">3.84%</p>
          </div>
        </div>
        <div class="flex items-center justify-between text-sm mt-4">
          <span class="opacity-90">Goal</span>
          <span class="font-semibold">4.5%</span>
        </div>
      </div>

      <div class="stat-card bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-3">
          <i class="fas fa-receipt text-3xl opacity-80"></i>
          <div class="text-right">
            <p class="text-sm opacity-90">Avg. Order Value</p>
            <p class="text-2xl font-bold">$156.32</p>
          </div>
        </div>
        <div class="flex items-center justify-between text-sm mt-4">
          <span class="opacity-90">Previous</span>
          <span class="font-semibold">$142.18</span>
        </div>
      </div>

      <div class="stat-card bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-3">
          <i class="fas fa-user-plus text-3xl opacity-80"></i>
          <div class="text-right">
            <p class="text-sm opacity-90">New Customers</p>
            <p class="text-2xl font-bold">342</p>
          </div>
        </div>
        <div class="flex items-center justify-between text-sm mt-4">
          <span class="opacity-90">This Month</span>
          <span class="font-semibold">+28%</span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 w-full">

    <!-- Sales Performance (Left Section) -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-md p-6 border border-gray-100">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h3 class="text-xl font-bold text-gray-800">Sales Performance</h3>
          <p class="text-sm text-gray-500">Monthly revenue overview</p>
        </div>

        <select class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full sm:w-auto">
          <option>Last 7 Days</option>
          <option>Last 30 Days</option>
          <option>Last 90 Days</option>
        </select>
      </div>

      <div class="w-full" style="height: 300px;">
        <Line :data="salesChartData" :options="salesChartOptions" />
      </div>
    </div>

    <!-- Top Products (Right Section) -->
    <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
      <h3 class="text-xl font-bold text-gray-800 mb-6">Top Products</h3>

      <div class="space-y-4">

        <!-- Single Product Row -->
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold">1</div>
            <div>
              <p class="font-semibold text-gray-800">Pro Plan</p>
              <p class="text-xs text-gray-500">1,247 sales</p>
            </div>
          </div>
          <span class="text-green-600 font-semibold">$42,180</span>
        </div>

        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">2</div>
            <div>
              <p class="font-semibold text-gray-800">Enterprise</p>
              <p class="text-xs text-gray-500">892 sales</p>
            </div>
          </div>
          <span class="text-green-600 font-semibold">$38,920</span>
        </div>

        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center text-white font-bold">3</div>
            <div>
              <p class="font-semibold text-gray-800">Basic Plan</p>
              <p class="text-xs text-gray-500">2,341 sales</p>
            </div>
          </div>
          <span class="text-green-600 font-semibold">$28,430</span>
        </div>

        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold">4</div>
            <div>
              <p class="font-semibold text-gray-800">Starter</p>
              <p class="text-xs text-gray-500">3,156 sales</p>
            </div>
          </div>
          <span class="text-green-600 font-semibold">$18,780</span>
        </div>

      </div>
    </div>

  </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Recent Transactions</h3>
        <div class="space-y-3">
          
          <div class="flex items-center justify-between p-3 border-b border-gray-100 hover:bg-gray-50 rounded-lg transition-colors">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-arrow-up text-green-600"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800">Payment Received</p>
                <p class="text-xs text-gray-500">Invoice #INV-2847</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-bold text-green-600">+$2,400</p>
              <p class="text-xs text-gray-500">2 hours ago</p>
            </div>
          </div>

          <div class="flex items-center justify-between p-3 border-b border-gray-100 hover:bg-gray-50 rounded-lg transition-colors">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-arrow-down text-red-600"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800">Server Hosting</p>
                <p class="text-xs text-gray-500">Monthly subscription</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-bold text-red-600">-$850</p>
              <p class="text-xs text-gray-500">5 hours ago</p>
            </div>
          </div>

          <div class="flex items-center justify-between p-3 border-b border-gray-100 hover:bg-gray-50 rounded-lg transition-colors">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-arrow-up text-green-600"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800">Client Payment</p>
                <p class="text-xs text-gray-500">Invoice #INV-2846</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-bold text-green-600">+$5,200</p>
              <p class="text-xs text-gray-500">1 day ago</p>
            </div>
          </div>

          <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-arrow-down text-red-600"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800">Marketing Tools</p>
                <p class="text-xs text-gray-500">Software subscription</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-bold text-red-600">-$340</p>
              <p class="text-xs text-gray-500">2 days ago</p>
            </div>
          </div>

        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Customer Satisfaction</h3>
        <div class="flex items-center justify-center mb-6">
          <div class="relative">
            <svg class="transform -rotate-90" width="200" height="200">
              <circle cx="100" cy="100" r="80" stroke="#e5e7eb" stroke-width="20" fill="none" />
              <circle cx="100" cy="100" r="80" stroke="url(#gradient)" stroke-width="20" fill="none" 
                      stroke-dasharray="502.4" stroke-dashoffset="125.6" class="progress-ring" stroke-linecap="round" />
              <defs>
                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" style="stop-color: #3B82F6; stop-opacity: 1" />
                  <stop offset="100%" style="stop-color: #8B5CF6; stop-opacity: 1" />
                </linearGradient>
              </defs>
            </svg>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
              <p class="text-4xl font-bold text-gray-800">4.8</p>
              <p class="text-sm text-gray-500">out of 5.0</p>
            </div>
          </div>
        </div>
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">5 stars</span>
              <div class="flex text-yellow-400">
                <i class="fas fa-star text-xs"></i>
              </div>
            </div>
            <div class="flex items-center gap-2 flex-1 mx-4">
              <div class="bg-gray-200 rounded-full h-2 flex-1">
                <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-full rounded-full" style="width: 75%"></div>
              </div>
              <span class="text-sm text-gray-600 w-12 text-right">75%</span>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">4 stars</span>
              <div class="flex text-yellow-400">
                <i class="fas fa-star text-xs"></i>
              </div>
            </div>
            <div class="flex items-center gap-2 flex-1 mx-4">
              <div class="bg-gray-200 rounded-full h-2 flex-1">
                <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-full rounded-full" style="width: 15%"></div>
              </div>
              <span class="text-sm text-gray-600 w-12 text-right">15%</span>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">3 stars</span>
              <div class="flex text-yellow-400">
                <i class="fas fa-star text-xs"></i>
              </div>
            </div>
            <div class="flex items-center gap-2 flex-1 mx-4">
              <div class="bg-gray-200 rounded-full h-2 flex-1">
                <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-full rounded-full" style="width: 6%"></div>
              </div>
              <span class="text-sm text-gray-600 w-12 text-right">6%</span>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">2 stars</span>
              <div class="flex text-yellow-400">
                <i class="fas fa-star text-xs"></i>
              </div>
            </div>
            <div class="flex items-center gap-2 flex-1 mx-4">
              <div class="bg-gray-200 rounded-full h-2 flex-1">
                <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-full rounded-full" style="width: 3%"></div>
              </div>
              <span class="text-sm text-gray-600 w-12 text-right">3%</span>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">1 star</span>
              <div class="flex text-yellow-400">
                <i class="fas fa-star text-xs"></i>
              </div>
            </div>
            <div class="flex items-center gap-2 flex-1 mx-4">
              <div class="bg-gray-200 rounded-full h-2 flex-1">
                <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-full rounded-full" style="width: 1%"></div>
              </div>
              <span class="text-sm text-gray-600 w-12 text-right">1%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend } from 'chart.js'
import { Line } from 'vue-chartjs'
import AppLayout from '../Layouts/AppLayout.vue'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend)

const salesChartData = ref({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  datasets: [{
    label: 'Revenue',
    data: [12000, 19000, 15000, 25000, 22000, 30000, 28000, 35000, 32000, 40000, 38000, 47582],
    borderColor: '#3B82F6',
    backgroundColor: (context) => {
      const ctx = context.chart.ctx
      const gradient = ctx.createLinearGradient(0, 0, 0, 400)
      gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)')
      gradient.addColorStop(1, 'rgba(59, 130, 246, 0)')
      return gradient
    },
    borderWidth: 3,
    fill: true,
    tension: 0.4,
    pointRadius: 0,
    pointHoverRadius: 6,
    pointHoverBackgroundColor: '#3B82F6',
    pointHoverBorderColor: '#fff',
    pointHoverBorderWidth: 2
  }]
})

const salesChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      padding: 12,
      cornerRadius: 8,
      callbacks: {
        label: (context) => '$' + context.parsed.y.toLocaleString()
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      },
      ticks: {
        color: '#9CA3AF'
      }
    },
    y: {
      grid: {
        color: '#F3F4F6'
      },
      ticks: {
        color: '#9CA3AF',
        callback: (value) => '$' + (value / 1000) + 'k'
      }
    }
  },
  interaction: {
    intersect: false,
    mode: 'index'
  }
}))
</script>

<style scoped>
.stat-card {
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
}
</style>