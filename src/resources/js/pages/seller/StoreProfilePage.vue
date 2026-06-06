<template>
  <div style="display:flex;min-height:100vh;align-items:stretch;background:#f0f4f8;">

    <!-- ══════════════════════════════════════════════════
         KIRI — Brand Panel (biru)
         ══════════════════════════════════════════════════ -->
    <aside class="brand-panel">
      <div class="brand-circle brand-circle--top"></div>
      <div class="brand-circle brand-circle--bottom"></div>

      <!-- Logo area -->
      <div style="position:relative;z-index:1;">
        <div class="brand-badge">
          <div class="pulse-dot" style="width:7px;height:7px;background:#6EE7B7;border-radius:50%;"></div>
          <span style="color:rgba(255,255,255,0.85);font-size:12px;font-weight:500;letter-spacing:0.3px;">
            Katalog UMKM Lokal Jebres
          </span>
        </div>
        <div class="brand-wordmark" @click="goToDashboard" style="cursor:pointer;">
          KULAAN<span style="color:rgba(255,255,255,0.45);">.id</span>
        </div>
        <div class="brand-tagline">
          Temukan produk lokal, dukung UMKM sekitar.
        </div>
      </div>

      <!-- Owner Account Info (Replacement of Step Progress) -->
      <div style="position:relative;z-index:1;background:rgba(255,255,255,0.08);padding:20px;border-radius:12px;border:1px solid rgba(255,255,255,0.12);" class="space-y-4">
        <h3 class="text-xs font-bold text-blue-200 uppercase tracking-wider mb-3">Informasi Akun Pemilik</h3>
        
        <div>
          <span class="block text-[11px] text-blue-200 font-semibold uppercase tracking-wider">Nama Lengkap</span>
          <span class="text-white text-sm font-bold">{{ authStore.user?.name || '-' }}</span>
        </div>

        <div>
          <span class="block text-[11px] text-blue-200 font-semibold uppercase tracking-wider mt-2">Email Terdaftar</span>
          <span class="text-white text-sm font-bold">{{ authStore.user?.email || '-' }}</span>
        </div>

        <div>
          <span class="block text-[11px] text-blue-200 font-semibold uppercase tracking-wider mt-2">Status Verifikasi</span>
          <span :class="['inline-block mt-1 px-3 py-1 rounded-full text-xs font-bold border', statusBadgeClass]">
            {{ formatStatus(storeStatus) }}
          </span>
        </div>
      </div>

      <!-- Footer -->
      <div class="brand-footer" style="position:relative;z-index:1;">
        <button @click="goToDashboard" class="text-xs text-blue-200 hover:text-white mb-4 block underline font-medium">
          &larr; Kembali ke Kelola Pesanan
        </button>
        <p>© 2026 <strong>Kulaan.id</strong> — Kelurahan Jebres, Surakarta</p>
      </div>
    </aside>

    <!-- ══════════════════════════════════════════════════
         KANAN — Form Panel (putih)
         ══════════════════════════════════════════════════ -->
    <main class="form-panel">

      <!-- Header & Verification Badge -->
      <div class="form-header flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="form-header-icon">🏪</div>
          <div>
            <h1 class="form-title">Pengaturan Profil Toko</h1>
            <p class="form-subtitle">Perbarui informasi toko Anda untuk meningkatkan transaksi.</p>
          </div>
        </div>
        
        <!-- Verification Status Badge -->
        <span :class="['px-3 py-1.5 rounded-full text-xs font-extrabold border uppercase tracking-wider', statusBadgeClass]">
          {{ formatStatus(storeStatus) }}
        </span>
      </div>

      <!-- Info Box Blue (If verified) -->
      <div v-if="storeStatus === 'disetujui'" class="notice-box mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-start gap-3">
        <span class="text-xl">ℹ️</span>
        <p class="text-xs text-blue-800 font-medium leading-relaxed">
          Nama toko, kategori usaha, dan kecamatan/kelurahan tidak dapat diubah setelah toko Anda <strong>Disetujui</strong> oleh admin untuk menjamin keabsahan katalog.
        </p>
      </div>

      <form novalidate @submit.prevent="handleSubmit" class="mt-6">

        <!-- ── SEKSI 1: Identitas Toko ── -->
        <div class="section-header">
          <div class="section-number">1</div>
          <div>
            <div class="section-title">Identitas Toko</div>
            <div class="section-subtitle">Informasi dasar yang tampil di katalog publik</div>
          </div>
        </div>

        <!-- Logo Toko -->
        <div class="field">
          <label class="field-label">Logo / Foto Toko</label>
          <div
            class="dropzone"
            :class="{ 'dragover': isDragOver, 'has-file': logoPreview }"
            @dragover.prevent="isDragOver = true"
            @dragleave.prevent="isDragOver = false"
            @drop.prevent="handleDrop"
            @click="$refs.logoInput.click()"
          >
            <input ref="logoInput" type="file" accept="image/*" style="display:none;" @change="handleLogoChange" />

            <div v-if="!logoPreview" class="dropzone-empty">
              <div class="dropzone-icon">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              </div>
              <p class="dropzone-text">Klik atau drag foto ke sini</p>
              <p class="dropzone-hint">PNG, JPG, WEBP — maks. 2 MB</p>
            </div>

            <div v-else class="dropzone-preview">
              <img :src="logoPreview" alt="Preview logo toko" />
              <button type="button" class="dropzone-remove" @click.stop="removeLogo">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
          </div>
          <span v-if="errors.logo" class="field-error">{{ errors.logo }}</span>
        </div>

        <!-- Nama Toko -->
        <div class="field">
          <label class="field-label" for="storeName">Nama Toko <span class="req">*</span></label>
          <div class="input-wrap">
            <span class="input-icon">
              <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </span>
            <input
              v-model="form.store_name"
              type="text" id="storeName"
              placeholder="mis. Warung Batik Bu Sari"
              :disabled="storeStatus === 'disetujui'"
              :class="['form-input', errors.store_name && 'error', storeStatus === 'disetujui' && 'disabled-input']"
            />
            <span v-if="storeStatus === 'disetujui'" class="lock-icon" title="Terkunci setelah disetujui">🔒</span>
          </div>
          <span v-if="errors.store_name" class="field-error">{{ errors.store_name }}</span>
        </div>

        <!-- Kategori Usaha -->
        <div class="field">
          <label class="field-label" for="category">Kategori Usaha <span class="req">*</span></label>
          <div class="input-wrap">
            <span class="input-icon">
              <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </span>
            <select
              v-model="form.category"
              id="category"
              :disabled="storeStatus === 'disetujui'"
              :class="['form-input', 'form-select', errors.category && 'error', storeStatus === 'disetujui' && 'disabled-input']"
            >
              <option value="" disabled>Pilih kategori usaha...</option>
              <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                {{ cat.icon }} {{ cat.label }}
              </option>
            </select>
            <span v-if="storeStatus !== 'disetujui'" class="select-arrow">
              <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
            <span v-else class="lock-icon" title="Terkunci setelah disetujui">🔒</span>
          </div>
          <span v-if="errors.category" class="field-error">{{ errors.category }}</span>
        </div>

        <!-- Deskripsi Singkat -->
        <div class="field">
          <label class="field-label" for="description">
            Deskripsi Singkat
            <span class="char-count">{{ form.description.length }}/200</span>
          </label>
          <textarea
            v-model="form.description"
            id="description"
            rows="3"
            maxlength="200"
            placeholder="Ceritakan secara singkat produk unggulan dan keistimewaan toko Anda..."
            :class="['form-input', 'form-textarea', errors.description && 'error']"
          ></textarea>
          <span v-if="errors.description" class="field-error">{{ errors.description }}</span>
        </div>

        <!-- ── SEKSI 2: Lokasi & Alamat ── -->
        <div class="section-header">
          <div class="section-number">2</div>
          <div>
            <div class="section-title">Lokasi & Alamat</div>
            <div class="section-subtitle">Agar pembeli tahu di mana menemukan Anda</div>
          </div>
        </div>

        <!-- Kecamatan / Kelurahan -->
        <div class="field">
          <label class="field-label" for="district">Kecamatan / Kelurahan <span class="req">*</span></label>
          <div class="input-wrap">
            <span class="input-icon">
              <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </span>
            <select
              v-model="form.district"
              id="district"
              :disabled="storeStatus === 'disetujui'"
              :class="['form-input', 'form-select', errors.district && 'error', storeStatus === 'disetujui' && 'disabled-input']"
            >
              <option value="" disabled>Pilih kelurahan...</option>
              <optgroup label="Kecamatan Jebres">
                <option v-for="d in districts" :key="d.value" :value="d.value">{{ d.label }}</option>
              </optgroup>
            </select>
            <span v-if="storeStatus !== 'disetujui'" class="select-arrow">
              <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
            <span v-else class="lock-icon" title="Terkunci setelah disetujui">🔒</span>
          </div>
          <span v-if="errors.district" class="field-error">{{ errors.district }}</span>
        </div>

        <!-- Alamat Lengkap -->
        <div class="field">
          <label class="field-label" for="address">Alamat Lengkap <span class="req">*</span></label>
          <div class="input-wrap">
            <span class="input-icon" style="top:14px;align-items:flex-start;">
              <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            </span>
            <textarea
              v-model="form.address"
              id="address"
              rows="2"
              placeholder="mis. Jl. Surya No. 12, RT 03/RW 05, Jebres"
              :class="['form-input', 'form-textarea', errors.address && 'error']"
              style="padding-top:10px;"
            ></textarea>
          </div>
          <span v-if="errors.address" class="field-error">{{ errors.address }}</span>
        </div>

        <!-- ── SEKSI 3: Jam Operasional ── -->
        <div class="section-header">
          <div class="section-number">3</div>
          <div>
            <div class="section-title">Jam Operasional</div>
            <div class="section-subtitle">Pembeli akan tahu kapan toko Anda buka</div>
          </div>
        </div>

        <!-- Toggle buka setiap hari -->
        <div class="toggle-row">
          <label class="toggle-label" for="openEveryDay">Buka setiap hari (termasuk hari libur)</label>
          <button
            type="button"
            role="switch"
            :aria-checked="form.open_every_day"
            id="openEveryDay"
            :class="['toggle-switch', form.open_every_day && 'active']"
            @click="form.open_every_day = !form.open_every_day"
          >
            <span class="toggle-knob"></span>
          </button>
        </div>

        <!-- Hari buka (jika tidak setiap hari) -->
        <div v-if="!form.open_every_day" class="field">
          <label class="field-label">Hari Buka</label>
          <div class="days-grid">
            <button
              v-for="day in weekdays"
              :key="day.value"
              type="button"
              :aria-pressed="form.open_days.includes(day.value)"
              :class="['day-btn', form.open_days.includes(day.value) && 'selected']"
              @click="toggleDay(day.value)"
            >
              {{ day.label }}
            </button>
          </div>
          <span v-if="errors.open_days" class="field-error">{{ errors.open_days }}</span>
        </div>

        <!-- Jam buka & tutup -->
        <div class="time-row">
          <div class="field" style="flex:1;">
            <label class="field-label" for="openTime">Jam Buka <span class="req">*</span></label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </span>
              <input
                v-model="form.open_time"
                type="time" id="openTime"
                :class="['form-input', errors.open_time && 'error']"
              />
            </div>
            <span v-if="errors.open_time" class="field-error">{{ errors.open_time }}</span>
          </div>

          <div class="time-separator">
            <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>

          <div class="field" style="flex:1;">
            <label class="field-label" for="closeTime">Jam Tutup <span class="req">*</span></label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 8 14"/></svg>
              </span>
              <input
                v-model="form.close_time"
                type="time" id="closeTime"
                :class="['form-input', errors.close_time && 'error']"
              />
            </div>
            <span v-if="errors.close_time" class="field-error">{{ errors.close_time }}</span>
          </div>
        </div>

        <!-- Preview jam operasional -->
        <div v-if="operatingHoursSummary" class="hours-preview">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span>{{ operatingHoursSummary }}</span>
        </div>

        <!-- Error global -->
        <div v-if="globalError" class="error-box">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span>{{ globalError }}</span>
        </div>

        <!-- Tombol submit -->
        <div class="action-row pt-6 border-t border-gray-100 flex justify-end">
          <button type="submit" class="btn-primary" :disabled="isLoading">
            <span v-if="isLoading" style="display:flex;align-items:center;gap:8px;">
              <svg style="width:16px;height:16px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="3"/>
                <path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="3" stroke-linecap="round"/>
              </svg>
              Menyimpan...
            </span>
            <span v-else>Simpan Perubahan</span>
          </button>
        </div>

      </form>
    </main>

    <!-- Toast -->
    <div :class="['toast', toastType, toastVisible && 'show']" role="alert">
      {{ toastMsg }}
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/services/api/index'

const router = useRouter()
const authStore = useAuthStore()

const storeStatus = computed(() => authStore.user?.store?.verification_status || 'belum_daftar')

// Form State
const form = reactive({
  store_name: '',
  category: '',
  description: '',
  district: '',
  address: '',
  open_every_day: false,
  open_days: [],
  open_time: '08:00',
  close_time: '17:00',
  logo: null,
})

const errors = reactive({})
const globalError = ref('')
const isLoading = ref(false)
const logoPreview = ref(null)
const isDragOver = ref(false)
const logoInput = ref(null)

// Data options
const categories = [
  { value: 'makanan_minuman', icon: '🍱', label: 'Makanan & Minuman' },
  { value: 'fashion_batik', icon: '👗', label: 'Fashion & Batik' },
  { value: 'kerajinan', icon: '🧶', label: 'Kerajinan Tangan' },
  { value: 'elektronik', icon: '📱', label: 'Elektronik & Aksesori' },
  { value: 'kecantikan', icon: '💄', label: 'Kecantikan & Perawatan' },
  { value: 'pertanian', icon: '🌾', label: 'Pertanian & Hasil Bumi' },
  { value: 'jasa', icon: '🛠️', label: 'Jasa & Layanan' },
  { value: 'lainnya', icon: '📦', label: 'Lainnya' },
]

const districts = [
  { value: 'jebres', label: 'Jebres' },
  { value: 'tegalharjo', label: 'Tegalharjo' },
  { value: 'kepatihan_kulon', label: 'Kepatihan Kulon' },
  { value: 'kepatihan_wetan', label: 'Kepatihan Wetan' },
  { value: 'pucang_sawit', label: 'Pucang Sawit' },
  { value: 'gandekan', label: 'Gandekan' },
  { value: 'sewu', label: 'Sewu' },
  { value: 'pucangsawit', label: 'Pucangsawit' },
  { value: 'jagalan', label: 'Jagalan' },
  { value: 'mojosongo', label: 'Mojosongo' },
]

const weekdays = [
  { value: 'sen', label: 'Sen' },
  { value: 'sel', label: 'Sel' },
  { value: 'rab', label: 'Rab' },
  { value: 'kam', label: 'Kam' },
  { value: 'jum', label: "Jum'" },
  { value: 'sab', label: 'Sab' },
  { value: 'min', label: 'Min' },
]

// Fetch data saat load
const loadStoreProfile = () => {
  const store = authStore.user?.store
  if (store) {
    form.store_name = store.store_name || ''
    form.category = store.store_category || ''
    form.description = store.description || ''
    form.district = store.district || ''
    form.address = store.address || ''
    
    // Parse operating_hours
    // format: "Setiap hari, 08:00 – 17:00" atau "Sen, Sel, Rab, 08:00 – 17:00"
    if (store.operating_hours) {
      const parts = store.operating_hours.split(',')
      if (parts.length >= 2) {
        const daysText = parts[0].trim()
        const timesText = parts[1].trim()
        
        if (daysText === 'Setiap hari') {
          form.open_every_day = true
          form.open_days = []
        } else {
          form.open_every_day = false
          // petakan hari
          form.open_days = daysText.split(',').map(d => {
            const match = weekdays.find(w => w.label === d.trim())
            return match ? match.value : null
          }).filter(Boolean)
        }

        const times = timesText.split('–')
        if (times.length === 2) {
          form.open_time = times[0].trim()
          form.close_time = times[1].trim()
        }
      }
    }
  }
}

const operatingHoursSummary = computed(() => {
  if (!form.open_time || !form.close_time) return ''
  const dayText = form.open_every_day
    ? 'Setiap hari'
    : form.open_days.length === 0
      ? ''
      : form.open_days.map(d => weekdays.find(w => w.value === d)?.label).join(', ')
  if (!dayText) return `${form.open_time} – ${form.close_time}`
  return `${dayText}, ${form.open_time} – ${form.close_time}`
})

// Logo handlers
function handleLogoChange(e) {
  const file = e.target.files[0]
  if (file) processLogo(file)
}

function handleDrop(e) {
  isDragOver.value = false
  const file = e.dataTransfer.files[0]
  if (file && file.type.startsWith('image/')) processLogo(file)
}

function processLogo(file) {
  if (file.size > 2 * 1024 * 1024) {
    errors.logo = 'Ukuran file maksimal 2 MB.'
    return
  }
  delete errors.logo
  form.logo = file
  const reader = new FileReader()
  reader.onload = (e) => { logoPreview.value = e.target.result }
  reader.readAsDataURL(file)
}

function removeLogo() {
  logoPreview.value = null
  form.logo = null
  if (logoInput.value) logoInput.value.value = ''
}

// Day toggle
function toggleDay(day) {
  const idx = form.open_days.indexOf(day)
  if (idx === -1) form.open_days.push(day)
  else form.open_days.splice(idx, 1)
}

// Validation
function validate() {
  clearErrors()
  let valid = true

  if (!form.store_name.trim()) {
    errors.store_name = 'Nama toko wajib diisi.'
    valid = false
  }
  if (!form.category) {
    errors.category = 'Pilih kategori usaha.'
    valid = false
  }
  if (!form.district) {
    errors.district = 'Pilih kecamatan/kelurahan.'
    valid = false
  }
  if (!form.address.trim()) {
    errors.address = 'Alamat lengkap wajib diisi.'
    valid = false
  }
  if (!form.open_time) {
    errors.open_time = 'Jam buka wajib diisi.'
    valid = false
  }
  if (!form.close_time) {
    errors.close_time = 'Jam tutup wajib diisi.'
    valid = false
  }
  if (form.open_time && form.close_time && form.open_time >= form.close_time) {
    errors.close_time = 'Jam tutup harus lebih akhir dari jam buka.'
    valid = false
  }
  if (!form.open_every_day && form.open_days.length === 0) {
    errors.open_days = 'Pilih minimal satu hari buka.'
    valid = false
  }

  return valid
}

// Submit
async function handleSubmit() {
  if (!validate()) {
    showToast('Periksa kembali isian form.', 'error')
    return
  }

  isLoading.value = true

  try {
    const dayText = form.open_every_day
      ? 'Setiap hari'
      : form.open_days.map(d => weekdays.find(w => w.value === d)?.label).join(', ')
    const operating_hours = `${dayText}, ${form.open_time} – ${form.close_time}`

    // Gunakan payload FormData karena ada file upload logo (atau method PUT spoofing)
    // Laravel terkadang butuh _method = PUT jika mengirim FormData
    const payload = new FormData()
    payload.append('_method',         'PUT')
    payload.append('store_name',      form.store_name)
    payload.append('category',        form.category)
    payload.append('description',     form.description || '')
    payload.append('district',        form.district)
    payload.append('address',         form.address)
    payload.append('operating_hours', operating_hours)
    if (form.logo) payload.append('logo', form.logo)

    const response = await apiClient.post('/store/profile', payload, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    if (response.data.success) {
      showToast('Profil toko berhasil diperbarui!', 'success')
      await authStore.fetchCurrentUser()
      loadStoreProfile()
    }

  } catch (err) {
    const res = err.response
    if (res?.status === 422) {
      const fieldErrors = res.data.errors ?? {}
      Object.keys(fieldErrors).forEach(f => { errors[f] = fieldErrors[f][0] })
      showToast('Periksa kembali isian form.', 'error')
    } else {
      globalError.value = 'Terjadi kesalahan server. Silakan coba lagi.'
      showToast('Gagal menyimpan. Coba lagi.', 'error')
    }
  } finally {
    isLoading.value = false
  }
}

// Helpers
function clearErrors() {
  Object.keys(errors).forEach(k => delete errors[k])
  globalError.value = ''
}

const toastMsg = ref('')
const toastType = ref('')
const toastVisible = ref(false)
let toastTimer = null

function showToast(msg, type = '', duration = 3000) {
  clearTimeout(toastTimer)
  toastMsg.value = msg
  toastType.value = type
  toastVisible.value = true
  toastTimer = setTimeout(() => { toastVisible.value = false }, duration)
}

const formatStatus = (status) => {
  if (status === 'menunggu') return 'Menunggu Verifikasi'
  if (status === 'disetujui') return 'Disetujui'
  if (status === 'dibatalkan') return 'Ditolak'
  return 'Belum Mengisi Profil'
}

const statusBadgeClass = computed(() => {
  const status = storeStatus.value
  if (status === 'menunggu') return 'bg-yellow-50 text-yellow-700 border-yellow-200'
  if (status === 'disetujui') return 'bg-green-50 text-green-700 border-green-200'
  if (status === 'dibatalkan') return 'bg-red-50 text-red-700 border-red-200'
  return 'bg-gray-50 text-gray-700 border-gray-200'
})

const goToDashboard = () => {
  router.push({ name: 'seller.orders' })
}

onMounted(async () => {
  await authStore.fetchCurrentUser()
  loadStoreProfile()
})
</script>

<style scoped>
/* ── Brand panel ─────────────────── */
.brand-panel {
  flex: 1 1 380px;
  background: var(--blue-600);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 48px;
  position: relative;
  overflow: hidden;
}
.brand-circle {
  position: absolute;
  border-radius: 50%;
}
.brand-circle--top {
  top: -120px; right: -120px;
  width: 480px; height: 480px;
  background: rgba(255,255,255,0.05);
}
.brand-circle--bottom {
  bottom: -80px; left: -80px;
  width: 320px; height: 320px;
  background: rgba(255,255,255,0.04);
}
.brand-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.12);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 100px;
  padding: 6px 14px;
  margin-bottom: 40px;
}
.brand-wordmark {
  font-size: 44px;
  font-weight: 800;
  color: #fff;
  letter-spacing: -1.5px;
  line-height: 1;
  margin-bottom: 6px;
}
.brand-tagline {
  font-family: 'Lora', serif;
  font-style: italic;
  color: rgba(255,255,255,0.65);
  font-size: 14px;
  margin-bottom: 48px;
}
.brand-footer {
  border-top: 1px solid rgba(255,255,255,0.12);
  padding-top: 24px;
}
.brand-footer p { font-size: 12px; color: rgba(255,255,255,0.4); }
.brand-footer strong { color: rgba(255,255,255,0.65); font-weight: 600; }

/* ── Form panel ─────────────────────────────────────────────── */
.form-panel {
  flex: 0 0 680px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding: 48px 56px;
  background: #fff;
  overflow-y: auto;
  max-height: 100vh;
}

.form-header-icon {
  font-size: 32px;
  width: 52px; height: 52px;
  background: var(--blue-50, #E6F1FB);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.form-title { font-size: 22px; font-weight: 800; color: var(--gray-800, #2D3547); letter-spacing: -0.4px; }
.form-subtitle { font-size: 13px; color: var(--gray-400, #9AA2B1); }

/* Section headers */
.section-header {
  display: flex; align-items: flex-start; gap: 12px;
  margin: 28px 0 18px;
  padding-bottom: 14px;
  border-bottom: 1.5px solid var(--gray-100, #EFF1F5);
}
.section-number {
  width: 24px; height: 24px;
  background: var(--blue-600, #185FA5);
  color: #fff;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 700;
  flex-shrink: 0;
}
.section-title { font-size: 14px; font-weight: 700; color: var(--gray-800, #2D3547); }
.section-subtitle { font-size: 11px; color: var(--gray-400, #9AA2B1); }

/* Fields */
.field { margin-bottom: 16px; }
.field-label {
  display: flex; align-items: center; justify-content: space-between;
  font-size: 13px; font-weight: 600;
  color: var(--gray-600, #5F6A7D); margin-bottom: 7px;
}
.req { color: var(--red-400, #E24B4A); }
.char-count { font-size: 11px; font-weight: 400; color: var(--gray-400, #9AA2B1); }
.input-wrap { position: relative; display: flex; align-items: center; }
.input-icon {
  position: absolute; left: 13px;
  display: flex; align-items: center;
  pointer-events: none; z-index: 1;
}
.input-icon svg {
  width: 16px; height: 16px;
  fill: none; stroke: var(--gray-400, #9AA2B1);
  stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round;
}
.form-input {
  width: 100%;
  padding: 10px 14px 10px 42px;
  border: 1.5px solid var(--gray-200, #DDE1E9);
  border-radius: 8px;
  font-family: inherit; font-size: 14px;
  color: var(--gray-800, #2D3547);
  background: var(--gray-50, #F8F9FB);
  outline: none;
  transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
  appearance: none;
}
.form-input:focus {
  border-color: var(--blue-400, #378ADD);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(55,138,221,0.12);
}
.form-input.error {
  border-color: var(--red-400, #E24B4A);
  background: var(--red-50, #FFF0F0);
}
.form-input.disabled-input {
  background-color: var(--gray-100, #EFF1F5);
  color: var(--gray-400, #9AA2B1);
  border-color: var(--gray-200, #DDE1E9);
  cursor: not-allowed;
}
.lock-icon {
  position: absolute;
  right: 14px;
  font-size: 14px;
  user-select: none;
}
.form-textarea {
  padding: 10px 14px 10px 42px;
  resize: vertical; min-height: 72px;
  line-height: 1.5;
}
.form-select { cursor: pointer; padding-right: 36px; }
.select-arrow {
  position: absolute; right: 14px;
  display: flex; align-items: center;
  pointer-events: none; z-index: 1;
}
.select-arrow svg {
  width: 16px; height: 16px;
  fill: none; stroke: var(--gray-400, #9AA2B1);
  stroke-width: 1.8;
}

/* Time inputs */
.time-row { display: flex; align-items: flex-start; gap: 12px; }
.time-separator {
  margin-top: 34px;
  display: flex; align-items: center;
  color: var(--gray-400, #9AA2B1);
}
.time-separator svg { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 1.8; }

/* Operating Hours Preview */
.hours-preview {
  margin: 14px 0 20px;
  background: var(--blue-50, #E6F1FB);
  border: 1px solid var(--blue-100, #C2DCF5);
  border-radius: 8px;
  padding: 10px 14px;
  display: flex; align-items: center; gap: 10px;
  color: var(--blue-700, #134B82);
  font-size: 13px; font-weight: 600;
}
.hours-preview svg {
  width: 16px; height: 16px;
  fill: none; stroke: currentColor; stroke-width: 2;
}

/* Toggle Switch */
.toggle-row {
  display: flex; align-items: center; justify-content: space-between;
  margin: 12px 0 18px;
}
.toggle-label { font-size: 13px; font-weight: 600; color: var(--gray-600, #5F6A7D); }
.toggle-switch {
  width: 44px; height: 24px;
  background: var(--gray-200, #DDE1E9);
  border-radius: 100px;
  position: relative;
  cursor: pointer; border: none; outline: none;
  transition: background-color 0.2s;
}
.toggle-switch.active { background: #34D399; }
.toggle-knob {
  width: 18px; height: 18px;
  background: #fff;
  border-radius: 50%;
  position: absolute; top: 3px; left: 3px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.15);
  transition: transform 0.2s cubic-bezier(0.4,0,0.2,1);
}
.toggle-switch.active .toggle-knob { transform: translateX(20px); }

/* Day Buttons */
.days-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 6px;
  margin-top: 6px;
}
.day-btn {
  height: 36px;
  background: var(--gray-50, #F8F9FB);
  border: 1.5px solid var(--gray-200, #DDE1E9);
  border-radius: 6px;
  font-family: inherit; font-size: 12px; font-weight: 600;
  color: var(--gray-600, #5F6A7D);
  cursor: pointer; outline: none;
  transition: all 0.18s;
  display: flex; align-items: center; justify-content: center;
}
.day-btn:hover { border-color: var(--gray-400, #9AA2B1); }
.day-btn.selected {
  background: var(--blue-600, #185FA5);
  border-color: var(--blue-600, #185FA5);
  color: #fff;
  box-shadow: var(--shadow-sm);
}

/* Dropzone (Logo) */
.dropzone {
  border: 2px dashed var(--gray-200, #DDE1E9);
  background: var(--gray-50, #F8F9FB);
  border-radius: 12px;
  padding: 24px;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s, background-color 0.2s;
}
.dropzone:hover, .dropzone.dragover { border-color: var(--blue-400, #378ADD); background: var(--blue-50/10); }
.dropzone-empty { display: flex; flex-direction: column; align-items: center; gap: 8px; }
.dropzone-icon svg { width: 32px; height: 32px; fill: none; stroke: var(--gray-400, #9AA2B1); stroke-width: 1.5; }
.dropzone-text { font-size: 13px; font-weight: 600; color: var(--gray-600, #5F6A7D); }
.dropzone-hint { font-size: 11px; color: var(--gray-400, #9AA2B1); }
.dropzone-preview { position: relative; display: inline-block; max-width: 120px; border-radius: 8px; overflow: hidden; }
.dropzone-preview img { width: 100%; object-cover: cover; max-height: 120px; }
.dropzone-remove {
  position: absolute; top: 4px; right: 4px;
  width: 20px; height: 20px;
  background: rgba(0,0,0,0.6);
  border: none; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #fff;
}
.dropzone-remove svg { width: 12px; height: 12px; fill: none; stroke: currentColor; stroke-width: 2.5; }

/* Buttons */
.btn-primary {
  padding: 11px 24px;
  background: var(--blue-600, #185FA5);
  color: #white;
  font-weight: 700; font-size: 13px;
  border: none; border-radius: 8px;
  cursor: pointer; outline: none;
  box-shadow: var(--shadow-sm);
  transition: all 0.2s;
}
.btn-primary:hover:not(:disabled) { background: #155594; box-shadow: var(--shadow-md); transform: translateY(-0.5px); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

/* Error box */
.error-box {
  margin: 20px 0;
  background: var(--red-50, #FFF0F0);
  border: 1px solid var(--red-200, #FCD5D5);
  border-radius: 8px;
  padding: 12px 16px;
  display: flex; align-items: center; gap: 10px;
  color: var(--red-700, #C53030);
  font-size: 13px; font-weight: 600;
}
.error-box svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2; flex-shrink: 0; }
.field-error { font-size: 11px; font-weight: 600; color: var(--red-400, #E24B4A); margin-top: 4px; display: block; }

/* Toast */
.toast {
  position: fixed;
  bottom: 32px; right: 32px;
  background: #333; color: #fff;
  padding: 14px 24px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  font-size: 13px; font-weight: 600;
  z-index: 9999;
  opacity: 0; transform: translateY(12px);
  pointer-events: none;
  transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
}
.toast.show { opacity: 1; transform: translateY(0); pointer-events: auto; }
.toast.success { background: #10B981; }
.toast.error   { background: #EF4444; }

.space-y-4 > * + * { margin-top: 1rem; }
</style>
