<template>
  <div class="min-h-screen bg-[#18181b] flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-[#27272a] py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 border border-gray-800">
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold text-white mb-2">Laravel</h2>
          <h3 class="text-xl font-bold text-white">Sign in</h3>
        </div>

        <form class="space-y-6" @submit.prevent="handleLogin">
          <div v-if="errorMsg" class="p-3 bg-red-900/50 border border-red-500 text-red-200 rounded-lg text-sm">
            {{ errorMsg }}
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-300">
              Email address<span class="text-red-500">*</span>
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" v-model="form.email" required 
                class="appearance-none block w-full px-3 py-2 border border-gray-600 rounded-lg shadow-sm placeholder-gray-400 bg-[#18181b] text-white focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-300">
              Password<span class="text-red-500">*</span>
            </label>
            <div class="mt-1 relative">
              <input id="password" name="password" :type="showPassword ? 'text' : 'password'" v-model="form.password" required 
                class="appearance-none block w-full px-3 py-2 border border-gray-600 rounded-lg shadow-sm placeholder-gray-400 bg-[#18181b] text-white focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm pr-10" />
              <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300">
                <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
          </div>

          <div class="flex items-center">
            <input id="remember-me" name="remember-me" type="checkbox" v-model="form.remember" 
              class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-600 rounded bg-[#18181b]" />
            <label for="remember-me" class="ml-2 block text-sm text-gray-300">
              Remember me
            </label>
          </div>

          <div>
            <button type="submit" :disabled="loading" 
              class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-[#18181b] bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 focus:ring-offset-[#27272a] disabled:opacity-50 transition-colors">
              <span v-if="loading">Signing in...</span>
              <span v-else>Sign in</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const errorMsg = ref('')
const showPassword = ref(false)

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const handleLogin = async () => {
  try {
    loading.value = true
    errorMsg.value = ''
    await authStore.login({
      email: form.email,
      password: form.password
    })
    
    if (authStore.isAdmin) {
      router.push('/admin')
    } else {
      errorMsg.value = 'Anda tidak memiliki akses admin.'
      await authStore.logout()
    }
  } catch (error) {
    errorMsg.value = error.response?.data?.message || 'Email atau password salah.'
  } finally {
    loading.value = false
  }
}
</script>
