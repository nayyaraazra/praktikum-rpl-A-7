<template>
  <div class="app-layout">
    <!-- ══ SIDEBAR ══ -->
    <BuyerSidebar />

    <!-- ══ MAIN CONTENT ══ -->
    <main class="main-content">
      <div class="profile-container">
        <div class="page-header">
          <h1 class="page-title">Profil Saya</h1>
          <p class="page-sub">Kelola informasi pribadi dan alamat pengiriman Anda</p>
        </div>

        <div class="profile-card">
          <form @submit.prevent="saveProfile" class="profile-form">
            <div class="form-group">
              <label class="form-label" for="profileName">Nama Lengkap <span class="req">*</span></label>
              <input
                v-model="form.name"
                id="profileName"
                type="text"
                class="form-input-plain"
                placeholder="Masukkan nama lengkap Anda"
                :class="{ 'border-red-400': errors.name }"
              />
              <span v-if="errors.name" class="error-msg">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="profileEmail">Email <span class="req">*</span></label>
              <input
                v-model="form.email"
                id="profileEmail"
                type="email"
                class="form-input-plain"
                placeholder="nama@email.com"
                :class="{ 'border-red-400': errors.email }"
              />
              <span v-if="errors.email" class="error-msg">{{ errors.email }}</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="profilePhone">Nomor Telepon (WhatsApp) <span class="req">*</span></label>
              <input
                v-model="form.phone_number"
                id="profilePhone"
                type="tel"
                class="form-input-plain"
                placeholder="08xxxxxxxxxx"
                :class="{ 'border-red-400': errors.phone_number }"
              />
              <span v-if="errors.phone_number" class="error-msg">{{ errors.phone_number }}</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="profileAddress">Alamat Pengiriman</label>
              <textarea
                v-model="form.address"
                id="profileAddress"
                class="form-input-plain form-textarea"
                rows="4"
                placeholder="Tulis alamat lengkap pengiriman Anda (RT/RW, Kelurahan, Kecamatan, Kota)"
                :class="{ 'border-red-400': errors.address }"
              ></textarea>
              <span v-if="errors.address" class="error-msg">{{ errors.address }}</span>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-save" :disabled="saving">
                {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>

    <!-- Toast Notification -->
    <div :class="['toast', toastType, toastVisible && 'show']" role="alert" aria-live="polite">
      {{ toastMsg }}
    </div>
  </div>
</template>

<script setup>
import BuyerSidebar from '@/components/common/BuyerSidebar.vue'
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { buyerApi } from '@/services/api/buyerApi'

const router = useRouter()
const authStore = useAuthStore()

const saving = ref(false)



const form = reactive({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  phone_number: authStore.user?.phone_number || '',
  address: authStore.user?.address || '',
})

const errors = reactive({
  name: '',
  email: '',
  phone_number: '',
  address: '',
})

// Toast notification state
const toastMsg = ref('')
const toastType = ref('')
const toastVisible = ref(false)
let toastTimer = null



function showToast(msg, type = '', duration = 3200) {
  clearTimeout(toastTimer)
  toastMsg.value = msg
  toastType.value = type
  toastVisible.value = true
  toastTimer = setTimeout(() => { toastVisible.value = false }, duration)
}

function clearErrors() {
  errors.name = ''
  errors.email = ''
  errors.phone_number = ''
  errors.address = ''
}

function validateForm() {
  clearErrors()
  let valid = true

  if (!form.name.trim()) {
    errors.name = 'Nama lengkap wajib diisi.'
    valid = false
  }
  if (!form.email.trim()) {
    errors.email = 'Email wajib diisi.'
    valid = false
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.email = 'Format email tidak valid.'
    valid = false
  }
  if (!form.phone_number.trim()) {
    errors.phone_number = 'Nomor telepon wajib diisi.'
    valid = false
  }

  return valid
}

async function saveProfile() {
  if (!validateForm()) {
    showToast('Harap isi semua field wajib dengan benar.', 'error')
    return
  }

  saving.value = true
  try {
    const res = await buyerApi.updateProfile({
      name: form.name,
      email: form.email,
      phone_number: form.phone_number,
      address: form.address,
    })

    if (res.data.success) {
      showToast('Profil berhasil diperbarui!', 'success')
      await authStore.fetchCurrentUser()
    }
  } catch (err) {
    console.error('Failed to update profile:', err)
    const res = err.response
    if (res?.status === 422) {
      const fieldErrors = res.data.errors ?? {}
      if (fieldErrors.name) errors.name = fieldErrors.name[0]
      if (fieldErrors.email) errors.email = fieldErrors.email[0]
      if (fieldErrors.phone_number) errors.phone_number = fieldErrors.phone_number[0]
      if (fieldErrors.address) errors.address = fieldErrors.address[0]
      showToast(res.data.message || 'Periksa kembali data Anda.', 'error')
    } else {
      showToast(res?.data?.message || 'Gagal memperbarui profil. Silakan coba lagi.', 'error')
    }
  } finally {
    saving.value = false
  }
}







onMounted(() => {
  
  
})
</script>

<style scoped>
:root {
  --brand-50: #E8F1FB; --brand-100: #C8DBED; --brand-200: #9CBEDD; --brand-300: #6FA0CC;
  --brand-400: #3D7DBD; --brand-500: rgb(24, 95, 165); --brand-600: rgb(36, 103, 170);
  --brand-700: #175F9E; --brand-800: #124F85; --brand-900: #0D3D68;
  --red-400: #E24B4A; --red-50: #FCEBEB;
  --gray-0: #FAFAF9; --gray-50: #F4F3F0; --gray-100: #E8E7E2; --gray-200: #CFCEC7;
  --gray-400: #8A8980; --gray-500: #717066; --gray-600: #5C5B54; --gray-700: #3E3D38; --gray-800: #282724; --gray-900: #161514;
  --radius-sm: 8px; --radius-md: 12px; --radius-lg: 16px; --radius-full: 9999px;
  --shadow-xs: 0 1px 2px rgba(0, 0, 0, .06);
}

.app-layout {
  display: flex;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
  background-color: var(--gray-50);
  background-image: radial-gradient(var(--gray-200) 1px, transparent 1px);
  background-size: 20px 20px;
  color: var(--gray-800);
}



/* Main Content */
.main-content { flex: 1; background: var(--gray-50); padding: 32px; box-sizing: border-box; min-width: 0; }
.profile-container { max-width: 650px; margin: 0 auto; }
.page-header { margin-bottom: 28px; }
.page-title { font-size: 22px; font-weight: 700; color: var(--gray-900); letter-spacing: -.5px; }
.page-sub { font-size: 14px; color: var(--gray-400); margin-top: 4px; }

/* Profile Card and Form */
.profile-card {
  background: #fff;
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-100);
  padding: 28px;
  box-shadow: var(--shadow-xs);
}
.form-group {
  margin-bottom: 20px;
}
.form-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-600);
  margin-bottom: 7px;
}
.req {
  color: var(--red-400);
}
.form-input-plain {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid var(--gray-100);
  border-radius: var(--radius-sm);
  font-family: inherit;
  font-size: 14px;
  color: var(--gray-800);
  background: var(--gray-0);
  outline: none;
  transition: all .18s;
  box-sizing: border-box;
}
.form-input-plain:focus {
  border-color: var(--brand-400);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(24, 95, 165, .08);
}
.form-textarea {
  resize: vertical;
  min-height: 100px;
}
.border-red-400 {
  border-color: var(--red-400) !important;
}
.error-msg {
  display: block;
  font-size: 12px;
  color: var(--red-400);
  margin-top: 6px;
}
.form-actions {
  margin-top: 30px;
  display: flex;
  justify-content: flex-end;
}
.btn-save {
  padding: 12px 24px;
  background: var(--brand-600);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
  box-shadow: var(--shadow-xs);
}
.btn-save:hover:not(:disabled) {
  background: var(--brand-700);
}
.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Toast */
.toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  padding: 12px 24px;
  border-radius: var(--radius-sm);
  background: var(--gray-900);
  color: #fff;
  font-size: 14px;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transform: translateY(100px);
  opacity: 0;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 9999;
}
.toast.show {
  transform: translateY(0);
  opacity: 1;
}
.toast.success {
  background: #10B981;
}
.toast.error {
  background: var(--red-400);
}
</style>
