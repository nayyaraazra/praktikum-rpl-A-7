<template>
  <div style="display:flex;min-height:100vh;align-items:stretch;">

    <!-- ══════════════════════════════════════════════════
         KIRI — Brand Panel (biru) — sama persis dengan AuthPage
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
        <div class="brand-wordmark">
          KULAAN<span style="color:rgba(255,255,255,0.45);">.id</span>
        </div>
        <div class="brand-tagline">
          Temukan produk lokal, dukung UMKM sekitar.
        </div>
      </div>

      <!-- Step progress -->
      <div style="position:relative;z-index:1;">
        <div class="steps-label">Langkah Pendaftaran</div>
        <div v-for="(step, i) in steps" :key="i" class="step-item">
          <div :class="['step-dot', step.status]">
            <svg v-if="step.status === 'done'" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            <span v-else>{{ i + 1 }}</span>
          </div>
          <div>
            <div :class="['step-title', step.status]">{{ step.title }}</div>
            <div class="step-desc">{{ step.desc }}</div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="brand-footer" style="position:relative;z-index:1;">
        <p>© 2026 <strong>Kulaan.id</strong> — Kelurahan Jebres, Surakarta</p>
      </div>
    </aside>

    <!-- ══════════════════════════════════════════════════
         KANAN — Form Panel (putih)
    ══════════════════════════════════════════════════ -->
    <main class="form-panel">

      <!-- Header -->
      <div class="form-header">
        <div class="form-header-icon">🏪</div>
        <div>
          <h1 class="form-title">Lengkapi Profil Toko</h1>
          <p class="form-subtitle">
            Isi data toko Anda agar pembeli dapat menemukan dan mempercayai UMKM Anda.
          </p>
        </div>
      </div>

      <!-- Progress bar -->
      <div class="progress-track">
        <div class="progress-bar" :style="{ width: progressPercent + '%' }"></div>
      </div>
      <p class="progress-text">{{ filledCount }} dari {{ totalFields }} data dilengkapi</p>

      <form novalidate @submit.prevent="handleSubmit">

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
              :class="['form-input', errors.store_name && 'error']"
            />
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
              :class="['form-input', 'form-select', errors.category && 'error']"
            >
              <option value="" disabled>Pilih kategori usaha...</option>
              <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                {{ cat.icon }} {{ cat.label }}
              </option>
            </select>
            <span class="select-arrow">
              <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
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
              :class="['form-input', 'form-select', errors.district && 'error']"
            >
              <option value="" disabled>Pilih kelurahan...</option>
              <optgroup label="Kecamatan Jebres">
                <option v-for="d in districts" :key="d.value" :value="d.value">{{ d.label }}</option>
              </optgroup>
            </select>
            <span class="select-arrow">
              <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
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
        <div class="action-row">
          <button type="button" class="btn-skip" @click="handleSkip">
            Lewati, isi nanti
          </button>
          <button type="submit" class="btn-primary" :disabled="isLoading">
            <span v-if="isLoading" style="display:flex;align-items:center;gap:8px;">
              <svg style="width:16px;height:16px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="3"/>
                <path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="3" stroke-linecap="round"/>
              </svg>
              Menyimpan...
            </span>
            <span v-else>Simpan & Lanjutkan →</span>
          </button>
        </div>

      </form>

    </main>

    <!-- Toast -->
    <div :class="['toast', toastType, toastVisible && 'show']" role="alert" aria-live="polite">
      {{ toastMsg }}
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/services/api/index'

const router    = useRouter()
const authStore = useAuthStore()

// ── Step indicators ───────────────────────────────────────────
const steps = [
  { title: 'Buat Akun',        desc: 'Nama, email, & password',    status: 'done' },
  { title: 'Profil Toko',      desc: 'Data operasional toko',       status: 'active' },
  { title: 'Menunggu Verifikasi', desc: 'Admin meninjau dalam 1×24 jam', status: 'pending' },
]

// ── Form state ────────────────────────────────────────────────
const form = reactive({
  store_name:     '',
  category:       '',
  description:    '',
  district:       '',
  address:        '',
  open_every_day: false,
  open_days:      [],
  open_time:      '08:00',
  close_time:     '17:00',
  logo:           null,
})

const errors      = reactive({})
const globalError = ref('')
const isLoading   = ref(false)
const logoPreview = ref(null)
const isDragOver  = ref(false)
const logoInput   = ref(null)

// ── Data options ──────────────────────────────────────────────
const categories = [
  { value: 'makanan_minuman',  icon: '🍱', label: 'Makanan & Minuman' },
  { value: 'fashion_batik',    icon: '👗', label: 'Fashion & Batik' },
  { value: 'kerajinan',        icon: '🧶', label: 'Kerajinan Tangan' },
  { value: 'elektronik',       icon: '📱', label: 'Elektronik & Aksesori' },
  { value: 'kecantikan',       icon: '💄', label: 'Kecantikan & Perawatan' },
  { value: 'pertanian',        icon: '🌾', label: 'Pertanian & Hasil Bumi' },
  { value: 'jasa',             icon: '🛠️', label: 'Jasa & Layanan' },
  { value: 'lainnya',          icon: '📦', label: 'Lainnya' },
]

const districts = [
  { value: 'jebres',         label: 'Jebres' },
  { value: 'tegalharjo',     label: 'Tegalharjo' },
  { value: 'kepatihan_kulon', label: 'Kepatihan Kulon' },
  { value: 'kepatihan_wetan', label: 'Kepatihan Wetan' },
  { value: 'pucang_sawit',   label: 'Pucang Sawit' },
  { value: 'gandekan',       label: 'Gandekan' },
  { value: 'sewu',           label: 'Sewu' },
  { value: 'pucangsawit',    label: 'Pucangsawit' },
  { value: 'jagalan',        label: 'Jagalan' },
  { value: 'mojosongo',      label: 'Mojosongo' },
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

// ── Progress tracker ──────────────────────────────────────────
const totalFields  = 6
const filledCount  = computed(() => {
  let n = 0
  if (form.store_name.trim())  n++
  if (form.category)           n++
  if (form.description.trim()) n++
  if (form.district)           n++
  if (form.address.trim())     n++
  if (form.open_time && form.close_time) n++
  return n
})
const progressPercent = computed(() => Math.round((filledCount.value / totalFields) * 100))

// ── Operating hours summary ───────────────────────────────────
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

// ── Logo handlers ─────────────────────────────────────────────
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

// ── Day toggle ────────────────────────────────────────────────
function toggleDay(day) {
  const idx = form.open_days.indexOf(day)
  if (idx === -1) form.open_days.push(day)
  else form.open_days.splice(idx, 1)
}

// ── Validation ────────────────────────────────────────────────
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

// ── Submit ────────────────────────────────────────────────────
async function handleSubmit() {
  if (!validate()) {
    showToast('Periksa kembali isian form.', 'error')
    return
  }

  isLoading.value = true

  try {
    // Susun operating_hours string
    const dayText = form.open_every_day
      ? 'Setiap hari'
      : form.open_days.map(d => weekdays.find(w => w.value === d)?.label).join(', ')
    const operating_hours = `${dayText}, ${form.open_time} – ${form.close_time}`

    const payload = new FormData()
    payload.append('store_name',      form.store_name)
    payload.append('category',        form.category)
    payload.append('description',     form.description)
    payload.append('district',        form.district)
    payload.append('address',         form.address)
    payload.append('operating_hours', operating_hours)
    if (form.logo) payload.append('logo', form.logo)

    await apiClient.post('/store/setup', payload, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    showToast('Profil toko berhasil disimpan! Menunggu verifikasi admin.', 'success', 3000)

    // Perbarui data user di store agar guard tahu profil sudah diisi
    await authStore.fetchCurrentUser()

    setTimeout(() => router.push({ name: 'seller.dashboard' }), 1800)

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

function handleSkip() {
  // Simpan flag agar guard tahu user melewati onboarding
  localStorage.setItem('store_onboarding_skipped', '1')
  router.push({ name: 'seller.dashboard' })
}

// ── Helpers ───────────────────────────────────────────────────
function clearErrors() {
  Object.keys(errors).forEach(k => delete errors[k])
  globalError.value = ''
}

const toastMsg     = ref('')
const toastType    = ref('')
const toastVisible = ref(false)
let toastTimer     = null

function showToast(msg, type = '', duration = 3200) {
  clearTimeout(toastTimer)
  toastMsg.value     = msg
  toastType.value    = type
  toastVisible.value = true
  toastTimer = setTimeout(() => { toastVisible.value = false }, duration)
}
</script>

<style scoped>
/* ── Brand panel — identik dengan AuthPage ─────────────────── */
.brand-panel {
  flex: 1 1 480px;
  background: var(--blue-600);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 48px 56px;
  position: relative;
  overflow: hidden;
  min-height: 100vh;
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
  font-size: 52px;
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
  font-size: 16px;
  margin-bottom: 48px;
}
.brand-footer {
  border-top: 1px solid rgba(255,255,255,0.12);
  padding-top: 24px;
}
.brand-footer p   { font-size: 12px; color: rgba(255,255,255,0.4); }
.brand-footer strong { color: rgba(255,255,255,0.65); font-weight: 600; }

/* ── Step progress ─────────────────────────────────────────── */
.steps-label {
  font-size: 11px; font-weight: 600;
  color: rgba(255,255,255,0.45);
  text-transform: uppercase; letter-spacing: 0.8px;
  margin-bottom: 16px;
}
.step-item {
  display: flex; align-items: flex-start; gap: 14px;
  margin-bottom: 22px;
}
.step-dot {
  width: 32px; height: 32px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 700;
  flex-shrink: 0;
  transition: all 0.2s;
}
.step-dot.done   { background: #6EE7B7; color: #064E3B; }
.step-dot.active { background: #fff; color: var(--blue-700); box-shadow: 0 0 0 4px rgba(255,255,255,0.2); }
.step-dot.pending { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.4); border: 1.5px solid rgba(255,255,255,0.2); }
.step-dot svg {
  width: 15px; height: 15px;
  fill: none; stroke: currentColor;
  stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
}
.step-title.done   { color: rgba(255,255,255,0.7); }
.step-title.active { color: #fff; font-weight: 700; }
.step-title.pending { color: rgba(255,255,255,0.35); }
.step-title { font-size: 14px; font-weight: 600; margin-bottom: 2px; transition: color 0.2s; }
.step-desc  { font-size: 12px; color: rgba(255,255,255,0.4); line-height: 1.4; }

/* ── Form panel ─────────────────────────────────────────────── */
.form-panel {
  flex: 0 0 580px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding: 52px 60px 60px;
  background: #fff;
  overflow-y: auto;
  max-height: 100vh;
}

/* Form header */
.form-header {
  display: flex; align-items: center; gap: 16px;
  margin-bottom: 8px;
}
.form-header-icon {
  font-size: 36px;
  width: 60px; height: 60px;
  background: var(--blue-50, #E6F1FB);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.form-title    { font-size: 24px; font-weight: 700; color: var(--gray-800, #2D3547); letter-spacing: -0.4px; margin-bottom: 4px; }
.form-subtitle { font-size: 13px; color: var(--gray-400, #9AA2B1); line-height: 1.5; }

/* Progress bar */
.progress-track {
  height: 4px;
  background: var(--gray-100, #EFF1F5);
  border-radius: 100px;
  margin: 20px 0 6px;
  overflow: hidden;
}
.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, var(--blue-400, #378ADD), var(--blue-600, #185FA5));
  border-radius: 100px;
  transition: width 0.4s cubic-bezier(0.4,0,0.2,1);
}
.progress-text {
  font-size: 11px; color: var(--gray-400, #9AA2B1);
  margin-bottom: 28px; text-align: right;
}

/* Section headers */
.section-header {
  display: flex; align-items: flex-start; gap: 12px;
  margin: 28px 0 18px;
  padding-bottom: 14px;
  border-bottom: 1.5px solid var(--gray-100, #EFF1F5);
}
.section-number {
  width: 26px; height: 26px;
  background: var(--blue-600, #185FA5);
  color: #fff;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700;
  flex-shrink: 0; margin-top: 1px;
}
.section-title   { font-size: 15px; font-weight: 700; color: var(--gray-800, #2D3547); margin-bottom: 2px; }
.section-subtitle { font-size: 12px; color: var(--gray-400, #9AA2B1); }

/* Fields */
.field { margin-bottom: 16px; }
.field-label {
  display: flex; align-items: center; justify-content: space-between;
  font-size: 13px; font-weight: 600;
  color: var(--gray-600, #5F6A7D); margin-bottom: 7px;
}
.req { color: var(--red-400, #E24B4A); margin-left: 1px; }
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
.form-textarea {
  padding: 10px 14px 10px 42px;
  resize: vertical; min-height: 72px;
  line-height: 1.5;
}
.form-select { cursor: pointer; padding-right: 36px; }
.select-arrow {
  position: absolute; right: 12px;
  pointer-events: none;
  display: flex; align-items: center;
  color: var(--gray-400, #9AA2B1);
}
.select-arrow svg {
  width: 16px; height: 16px;
  fill: none; stroke: currentColor;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}
.field-error {
  display: block; margin-top: 5px;
  font-size: 12px; color: var(--red-400, #E24B4A);
}

/* Dropzone */
.dropzone {
  border: 2px dashed var(--gray-200, #DDE1E9);
  border-radius: 10px;
  background: var(--gray-50, #F8F9FB);
  cursor: pointer;
  transition: all 0.18s ease;
  overflow: hidden;
}
.dropzone:hover, .dropzone.dragover {
  border-color: var(--blue-400, #378ADD);
  background: var(--blue-50, #E6F1FB);
}
.dropzone.has-file { border-style: solid; border-color: var(--blue-400, #378ADD); }
.dropzone-empty {
  padding: 28px 20px;
  display: flex; flex-direction: column; align-items: center; gap: 6px;
}
.dropzone-icon {
  width: 44px; height: 44px;
  background: #fff;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 4px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.dropzone-icon svg {
  width: 22px; height: 22px;
  fill: none; stroke: var(--blue-400, #378ADD);
  stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round;
}
.dropzone-text { font-size: 13px; font-weight: 600; color: var(--gray-600, #5F6A7D); }
.dropzone-hint { font-size: 11px; color: var(--gray-400, #9AA2B1); }
.dropzone-preview {
  position: relative; height: 120px;
  display: flex; align-items: center; justify-content: center;
}
.dropzone-preview img {
  max-height: 110px; max-width: 100%;
  object-fit: contain; border-radius: 6px;
}
.dropzone-remove {
  position: absolute; top: 8px; right: 8px;
  width: 28px; height: 28px;
  background: rgba(0,0,0,0.5); border: none; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #fff;
  transition: background 0.18s;
}
.dropzone-remove:hover { background: rgba(0,0,0,0.7); }
.dropzone-remove svg {
  width: 14px; height: 14px;
  fill: none; stroke: currentColor;
  stroke-width: 2.5; stroke-linecap: round;
}

/* Toggle switch */
.toggle-row {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px;
  padding: 12px 14px;
  background: var(--gray-50, #F8F9FB);
  border: 1.5px solid var(--gray-100, #EFF1F5);
  border-radius: 8px;
}
.toggle-label { font-size: 13px; font-weight: 500; color: var(--gray-600, #5F6A7D); }
.toggle-switch {
  width: 44px; height: 24px;
  border-radius: 100px;
  background: var(--gray-200, #DDE1E9);
  border: none; cursor: pointer;
  position: relative; flex-shrink: 0;
  transition: background 0.2s;
}
.toggle-switch.active { background: var(--blue-500, #1E6FC5); }
.toggle-knob {
  position: absolute; top: 3px; left: 3px;
  width: 18px; height: 18px;
  background: #fff; border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
  transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1);
}
.toggle-switch.active .toggle-knob { transform: translateX(20px); }

/* Days grid */
.days-grid {
  display: flex; gap: 7px; flex-wrap: wrap;
  margin-bottom: 4px;
}
.day-btn {
  padding: 7px 12px;
  border: 1.5px solid var(--gray-200, #DDE1E9);
  border-radius: 100px;
  background: var(--gray-50, #F8F9FB);
  font-family: inherit; font-size: 13px; font-weight: 600;
  color: var(--gray-600, #5F6A7D);
  cursor: pointer; transition: all 0.15s;
}
.day-btn:hover  { border-color: var(--blue-400, #378ADD); color: var(--blue-600, #185FA5); }
.day-btn.selected {
  border-color: var(--blue-500, #1E6FC5);
  background: var(--blue-50, #E6F1FB);
  color: var(--blue-700, #124880);
  box-shadow: 0 0 0 2px rgba(30,111,197,0.12);
}

/* Time row */
.time-row {
  display: flex; align-items: flex-end; gap: 12px;
  margin-bottom: 4px;
}
.time-separator {
  padding-bottom: 18px; flex-shrink: 0;
  color: var(--gray-400, #9AA2B1);
}
.time-separator svg {
  width: 18px; height: 18px;
  fill: none; stroke: currentColor;
  stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round;
}

/* Hours preview */
.hours-preview {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 14px;
  background: #EAF3DE; border-radius: 8px;
  font-size: 13px; font-weight: 600; color: #3B6D11;
  margin-bottom: 16px;
}
.hours-preview svg {
  width: 16px; height: 16px; flex-shrink: 0;
  fill: none; stroke: currentColor;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}

/* Error box */
.error-box {
  display: flex; align-items: center; gap: 8px;
  padding: 12px 14px;
  background: var(--red-50, #FFF0F0);
  border: 1px solid #FECACA;
  border-radius: 8px;
  font-size: 13px; color: var(--red-400, #E24B4A);
  margin-bottom: 16px;
}
.error-box svg {
  width: 16px; height: 16px; flex-shrink: 0;
  fill: none; stroke: currentColor;
  stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round;
}

/* Action row */
.action-row {
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px; margin-top: 28px;
}
.btn-skip {
  font-size: 13px; font-weight: 500;
  color: var(--gray-400, #9AA2B1); background: none; border: none;
  cursor: pointer; font-family: inherit; padding: 4px;
  transition: color 0.18s;
}
.btn-skip:hover { color: var(--gray-600, #5F6A7D); text-decoration: underline; }
.btn-primary {
  flex: 1; max-width: 220px;
  padding: 12px 20px;
  background: var(--blue-600, #185FA5);
  color: #fff; border: none;
  border-radius: 8px;
  font-family: inherit; font-size: 14px; font-weight: 700;
  cursor: pointer;
  transition: background 0.18s, transform 0.1s, box-shadow 0.18s;
  box-shadow: 0 1px 4px rgba(24,95,165,0.3);
}
.btn-primary:hover:not(:disabled) {
  background: var(--blue-700, #124880);
  box-shadow: 0 4px 12px rgba(24,95,165,0.35);
}
.btn-primary:active:not(:disabled) { transform: scale(0.99); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

/* Spin */
@keyframes spin { to { transform: rotate(360deg); } }

/* Responsive */
@media (max-width: 768px) {
  .brand-panel { display: none; }
  .form-panel  { flex: 1; padding: 32px 20px 48px; max-height: unset; }
  .time-row { flex-direction: column; }
  .time-separator { display: none; }
  .action-row { flex-direction: column-reverse; }
  .btn-primary { max-width: 100%; }
}
</style>