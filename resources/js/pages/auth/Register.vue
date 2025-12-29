<template>
  <div class="bg-linear-to-br from-blue-50 to-indigo-100 flex items-center justify-center min-h-screen p-2">
    <div class="flex flex-col lg:flex-row w-full max-w-6xl shadow-lg rounded-xl overflow-hidden bg-white">
      
      <!-- Image Section (Hidden on mobile) -->
      <div class="lg:w-5/12 hidden lg:flex items-stretch">
        <img 
          src="/public/assets/defultimage.jpg" 
          alt="Sign Up Image" 
          class="object-cover w-full h-full"
        />
      </div>

      <!-- Form Section -->
      <div class="lg:w-7/12 w-full flex flex-col justify-center items-center p-6 md:p-8 text-gray-900">
        
        <h1 class="text-2xl font-bold text-blue-600 mb-1">DesigncodeIT</h1>
        <h2 class="text-xl font-semibold mb-4 text-center text-gray-800">Create Your Account</h2>

        <form @submit.prevent="handleRegister" class="w-full max-w-md space-y-3">
          
          <!-- Error Message -->
          <div v-if="error" class="p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
            {{ error }}
          </div>

          <!-- Name Field -->
          <div>
            <label class="block mb-1 text-sm font-medium" for="name">Full Name</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-2.5">
                <i class="bx bx-user text-gray-400"></i>
              </span>
              <input 
                v-model="form.name" 
                type="text" 
                id="name" 
                name="name" 
                placeholder="Enter your full name"
                required
                class="w-full pl-10 pr-3 py-2.5 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm transition"
              />
            </div>
          </div>

          <!-- Email Field -->
          <div>
            <label class="block mb-1 text-sm font-medium" for="email">Email</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-2.5">
                <i class="bx bx-envelope text-gray-400"></i>
              </span>
              <input 
                v-model="form.email" 
                type="email" 
                id="email" 
                name="email" 
                placeholder="Enter your email"
                required
                class="w-full pl-10 pr-3 py-2.5 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm transition"
              />
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <label class="block mb-1 text-sm font-medium" for="password">Password</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-2.5">
                <i class="bx bx-lock-alt text-gray-400"></i>
              </span>
              <input 
                v-model="form.password" 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter your password"
                required
                class="w-full pl-10 pr-3 py-2.5 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm transition"
              />
            </div>
          </div>

          <!-- Confirm Password Field -->
          <div>
            <label class="block mb-1 text-sm font-medium" for="confirm_password">Confirm Password</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-2.5">
                <i class="bx bx-lock-alt text-gray-400"></i>
              </span>
              <input 
                v-model="form.password_confirmation" 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                placeholder="Confirm your password"
                required
                class="w-full pl-10 pr-3 py-2.5 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm transition"
              />
            </div>
          </div>

          <!-- Register Button -->
          <button 
            type="submit" 
            :disabled="loading"
            class="w-full py-2.5 bg-linear-to-br from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-lg font-bold text-white uppercase tracking-wide text-sm transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow disabled:from-gray-400 disabled:to-gray-500 disabled:hover:translate-y-0 disabled:hover:shadow-none"
          >
            {{ loading ? 'Loading...' : 'Sign Up' }}
          </button>

          <!-- Divider -->
          <div class="flex items-center justify-between pt-1">
            <span class="w-1/5 border-b"></span>
            <span class="text-xs text-center text-gray-500 uppercase">Or sign up with</span>
            <span class="w-1/5 border-b"></span>
          </div>

          <!-- Social Register Buttons -->
          <div class="flex justify-center space-x-2">
            <button type="button" class="flex items-center justify-center w-full py-2 px-2 rounded-lg border border-gray-300 hover:bg-gray-100 transition text-sm">
              <i class="fab fa-google text-red-500 mr-1.5"></i> Google
            </button>
            <button type="button" class="flex items-center justify-center w-full py-2 px-2 rounded-lg border border-gray-300 hover:bg-gray-100 transition text-sm">
              <i class="fab fa-facebook text-blue-600 mr-1.5"></i> Facebook
            </button>
          </div>

          <!-- Login Link -->
          <p class="text-center text-xs text-gray-600 pt-1">
            Already have an account? 
            <router-link to="/login" class="text-blue-600 hover:underline font-medium text-xs">Login</router-link>
          </p>

        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { authStore } from '../../stores/auth'

const router = useRouter()
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})
const error = ref('')
const loading = ref(false)

const handleRegister = async () => {
  loading.value = true
  error.value = ''
  
  try {
    await authStore.register(form.value)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>