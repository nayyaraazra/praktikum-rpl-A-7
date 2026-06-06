<template>
  <div style="display:flex;min-height:100vh;align-items:stretch;">

    <!-- ══════════════════════════════════════════════════
         KIRI — Brand Panel (biru)
    ══════════════════════════════════════════════════ -->
    <aside class="brand-panel">
      <!-- Decorative circles -->
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

      <!-- Feature list -->
      <div style="position:relative;z-index:1;">
        <div v-for="f in features" :key="f.label" class="feature-item">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" v-html="f.icon"></svg>
          </div>
          <div>
            <div class="feature-text-label">{{ f.label }}</div>
            <div class="feature-text-desc">{{ f.desc }}</div>
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
      <div style="margin-bottom:32px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
          <h1 class="form-title">{{ activeTab === 'masuk' ? 'Selamat Datang' : 'Buat Akun Baru' }}</h1>
        </div>
        <p class="form-subtitle">
          {{ activeTab === 'masuk'
            ? 'Masuk ke akun Anda untuk melanjutkan.'
            : 'Daftarkan diri Anda untuk mulai menggunakan KULAAN.id.' }}
        </p>
      </div>

      <!-- Tab bar -->
      <div class="tab-bar" role="tablist">
        <button
          id="tabMasuk"
          role="tab"
          :aria-selected="activeTab === 'masuk'"
          :class="['tab-btn', activeTab === 'masuk' && 'active']"
          @click="switchTab('masuk')"
        >Masuk</button>
        <button
          id="tabDaftar"
          role="tab"
          :aria-selected="activeTab === 'daftar'"
          :class="['tab-btn', activeTab === 'daftar' && 'active']"
          @click="switchTab('daftar')"
        >Daftar</button>
      </div>

      <!-- Role selector — tampil di kedua tab -->
      <div style="margin-bottom:24px;">
        <div class="role-label">Pilih Peran</div>
        <div class="role-grid">
          <button
            id="rolePembeli"
            type="button"
            :aria-pressed="selectedRole === 'pembeli'"
            :class="['role-card', selectedRole === 'pembeli' && 'selected']"
            @click="selectRole('pembeli')"
          >
            <div class="role-icon">
              <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            </div>
            <div class="role-name">Pembeli</div>
            <div class="role-desc">Cari &amp; Pesan</div>
          </button>
          <button
            id="rolePemilik"
            type="button"
            :aria-pressed="selectedRole === 'pemilik'"
            :class="['role-card', selectedRole === 'pemilik' && 'selected']"
            @click="selectRole('pemilik')"
          >
            <div class="role-icon">
              <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div class="role-name">Pemilik UMKM</div>
            <div class="role-desc">Daftarkan toko</div>
          </button>
        </div>
      </div>

      <!-- ── FORM MASUK ── -->
      <form v-if="activeTab === 'masuk'" novalidate @submit.prevent="handleLogin">

        <div class="field">
          <label class="field-label" for="loginEmail">Email <span class="req">*</span></label>
          <div class="input-wrap">
            <span class="input-icon">
              <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7"/></svg>
            </span>
            <input
              v-model="loginForm.email"
              type="email" id="loginEmail"
              placeholder="email@contoh.com"
              autocomplete="email"
              :class="['form-input', errors.loginEmail && 'error']"
            />
          </div>
          <span v-if="errors.loginEmail" class="field-error show">{{ errors.loginEmail }}</span>
        </div>

        <div class="field">
          <label class="field-label" for="loginPw">Password <span class="req">*</span></label>
          <div class="input-wrap">
            <span class="input-icon">
              <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </span>
            <input
              v-model="loginForm.password"
              :type="showLoginPw ? 'text' : 'password'"
              id="loginPw"
              placeholder="Minimal 8 karakter"
              autocomplete="current-password"
              :class="['form-input', errors.loginPassword && 'error']"
            />
            <button type="button" class="toggle-pw" :aria-label="showLoginPw ? 'Sembunyikan' : 'Tampilkan'" @click="showLoginPw = !showLoginPw">
              <svg v-if="!showLoginPw" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg v-else viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
          <span v-if="errors.loginPassword" class="field-error show">{{ errors.loginPassword }}</span>
        </div>

        <button type="submit" class="btn-primary" :disabled="isLoading">
          <span v-if="isLoading" style="display:flex;align-items:center;gap:8px;justify-content:center;">
            <svg style="width:16px;height:16px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="3"/>
              <path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="3" stroke-linecap="round"/>
            </svg>
            Memproses...
          </span>
          <span v-else>Masuk</span>
        </button>
      </form>

      <!-- ── FORM DAFTAR ── -->
      <form v-else novalidate @submit.prevent="handleRegister">

        <div class="field">
          <label class="field-label" for="regNama">Nama Lengkap <span class="req">*</span></label>
          <div class="input-wrap">
            <span class="input-icon">
              <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <input
              v-model="regForm.name"
              type="text" id="regNama"
              placeholder="Masukkan nama lengkap"
              autocomplete="name"
              :class="['form-input', errors.name && 'error']"
            />
          </div>
          <span v-if="errors.name" class="field-error show">{{ errors.name }}</span>
        </div>

        <div class="field">
          <label class="field-label" for="regEmail">Email <span class="req">*</span></label>
          <div class="input-wrap">
            <span class="input-icon">
              <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7"/></svg>
            </span>
            <input
              v-model="regForm.email"
              type="email" id="regEmail"
              placeholder="email@contoh.com"
              autocomplete="email"
              :class="['form-input', errors.email && 'error']"
            />
          </div>
          <span v-if="errors.email" class="field-error show">{{ errors.email }}</span>
        </div>

        <div class="field">
          <label class="field-label" for="regPhone">No. WhatsApp / Telepon <span class="req">*</span></label>
          <div class="phone-wrap">
            <div class="phone-prefix">🇮🇩 +62</div>
            <input
              v-model="regForm.phone_raw"
              type="tel" id="regPhone"
              placeholder="8xxxxxxxxxx"
              autocomplete="tel"
              :class="['form-input', errors.phone_number && 'error']"
              style="padding-left:13px;border-radius:0 var(--radius-sm) var(--radius-sm) 0;"
            />
          </div>
          <span v-if="errors.phone_number" class="field-error show">{{ errors.phone_number }}</span>
        </div>

        <div class="field">
          <label class="field-label" for="regPw">Password <span class="req">*</span></label>
          <div class="input-wrap">
            <span class="input-icon">
              <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </span>
            <input
              v-model="regForm.password"
              :type="showRegPw ? 'text' : 'password'"
              id="regPw"
              placeholder="Minimal 8 karakter"
              autocomplete="new-password"
              :class="['form-input', errors.password && 'error']"
            />
            <button type="button" class="toggle-pw" :aria-label="showRegPw ? 'Sembunyikan' : 'Tampilkan'" @click="showRegPw = !showRegPw">
              <svg v-if="!showRegPw" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg v-else viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
          <span v-if="errors.password" class="field-error show">{{ errors.password }}</span>
        </div>

        <!-- Notice verifikasi UMKM — sesuai AC US-09 -->
        <div v-if="selectedRole === 'pemilik'" class="notice-box">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <p>
            <strong>Menunggu Verifikasi Admin</strong> — Setelah mendaftar, akun toko Anda akan ditinjau oleh admin dalam 1×24 jam. Beberapa fitur baru tersedia setelah toko diverifikasi.
          </p>
        </div>

        <button type="submit" class="btn-primary" :disabled="isLoading">
          <span v-if="isLoading" style="display:flex;align-items:center;gap:8px;justify-content:center;">
            <svg style="width:16px;height:16px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="3"/>
              <path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="3" stroke-linecap="round"/>
            </svg>
            Mendaftarkan...
          </span>
          <span v-else>Buat Akun</span>
        </button>
      </form>

      <!-- Switch row -->
      <div class="switch-row">
        <template v-if="activeTab === 'masuk'">
          Belum punya akun?
          <button class="switch-link" @click="switchTab('daftar')">Daftar sekarang</button>
        </template>
        <template v-else>
          Sudah punya akun?
          <button class="switch-link" @click="switchTab('masuk')">Masuk di sini</button>
        </template>
      </div>

    </main>

    <!-- Toast -->
    <div :class="['toast', toastType, toastVisible && 'show']" role="alert" aria-live="polite">
      {{ toastMsg }}
    </div>

  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router    = useRouter()
const authStore = useAuthStore()

// ── State ──────────────────────────────────────────────────────
const activeTab    = ref('masuk')
const selectedRole = ref('pembeli')
const isLoading    = ref(false)
const showLoginPw  = ref(false)
const showRegPw    = ref(false)
const errors       = reactive({})

const loginForm = reactive({ email: '', password: '' })
const regForm   = reactive({
  name: '', email: '', phone_raw: '', password: '', password_confirmation: '', role: 'buyer',
})

// Toast
const toastMsg     = ref('')
const toastType    = ref('')
const toastVisible = ref(false)
let toastTimer     = null

// ── Feature list untuk brand panel ────────────────────────────
const features = [
  {
    icon: '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>',
    label: 'UMKM Lokal Jebres',
    desc:  'Temukan produk dari pelaku usaha di sekitar Kelurahan Jebres.',
  },
  {
    icon: '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
    label: 'Pencarian Cerdas',
    desc:  'Semantic search berbasis AI — cukup ketik apa yang Anda butuhkan.',
  },
  {
    icon: '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>',
    label: 'Pemesanan Mudah',
    desc:  'Isi form pesanan dalam hitungan menit, langsung ke pemilik UMKM.',
  },
]

// ── Actions ────────────────────────────────────────────────────

function switchTab(tab) {
  activeTab.value = tab
  clearErrors()
}

function selectRole(role) {
  selectedRole.value   = role
  regForm.role         = role === 'pembeli' ? 'buyer' : 'seller'
}

function getRedirectTarget() {
  if (authStore.isSeller) {
    if (!authStore.user?.store && localStorage.getItem('store_onboarding_skipped') !== '1') {
      return { name: 'store.setup' }
    }
    return { name: 'seller.orders' }
  }
  return { name: 'home' }
}

async function handleLogin() {
  clearErrors()
  isLoading.value = true
  showToast('Memproses masuk…')

  try {
    const result = await authStore.login(loginForm)

    // AC US-09: notice jika toko belum terverifikasi
    if (result.data.notice) {
      showToast(result.data.notice, 'success', 4000)
      setTimeout(() => router.push(getRedirectTarget()), 2000)
    } else {
      showToast('Berhasil masuk! Mengalihkan…', 'success')
      setTimeout(() => router.push(getRedirectTarget()), 1200)
    }
  } catch (err) {
    handleApiError(err)
  } finally {
    isLoading.value = false
  }
}

async function handleRegister() {
  clearErrors()
  isLoading.value = true
  showToast('Membuat akun…')

  // Gabungkan +62 dengan nomor yang diinput
  regForm.phone_number          = '0' + regForm.phone_raw.replace(/^0+/, '')
  regForm.password_confirmation = regForm.password

  try {
    await authStore.register(regForm)

    const msg = selectedRole.value === 'pemilik'
      ? 'Akun dibuat! Toko Anda menunggu verifikasi admin (1×24 jam).'
      : 'Akun berhasil dibuat! Selamat datang di Kulaan.id.'
    showToast(msg, 'success', 4000)
    setTimeout(() => router.push(getRedirectTarget()), 1500)

  } catch (err) {
    handleApiError(err)
  } finally {
    isLoading.value = false
  }
}

function handleApiError(err) {
  const res = err.response
  if (res?.status === 422) {
    // Validasi per-field dari Laravel (AC US-01, US-08)
    const fieldErrors = res.data.errors ?? {}
    Object.keys(fieldErrors).forEach(f => {
      errors[f] = fieldErrors[f][0]
    })
    showToast('Periksa kembali isian form.', 'error')
  } else if (res?.status === 401) {
    // Pesan generik — tidak ungkap field mana yang salah (AC US-02)
    errors.loginPassword = res.data.message
    showToast(res.data.message, 'error')
  } else {
    showToast('Terjadi kesalahan. Silakan coba lagi.', 'error')
  }
}

function clearErrors() {
  Object.keys(errors).forEach(k => delete errors[k])
}

function showToast(msg, type = '', duration = 3200) {
  clearTimeout(toastTimer)
  toastMsg.value     = msg
  toastType.value    = type
  toastVisible.value = true
  toastTimer = setTimeout(() => { toastVisible.value = false }, duration)
}
</script>

<style scoped>
/* ── Brand panel ────────────────────────────────────────────── */
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
.feature-item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-bottom: 28px;
}
.feature-icon {
  width: 40px; height: 40px;
  background: rgba(255,255,255,0.1);
  border-radius: var(--radius-sm);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.feature-icon svg {
  width: 20px; height: 20px;
  fill: none;
  stroke: rgba(255,255,255,0.85);
  stroke-width: 1.7;
  stroke-linecap: round;
  stroke-linejoin: round;
}
.feature-text-label { font-size: 14px; font-weight: 600; color: #fff; margin-bottom: 2px; }
.feature-text-desc  { font-size: 13px; color: rgba(255,255,255,0.55); line-height: 1.5; }
.brand-footer {
  border-top: 1px solid rgba(255,255,255,0.12);
  padding-top: 24px;
}
.brand-footer p   { font-size: 12px; color: rgba(255,255,255,0.4); }
.brand-footer strong { color: rgba(255,255,255,0.65); font-weight: 600; }

/* ── Form panel ─────────────────────────────────────────────── */
.form-panel {
  flex: 0 0 520px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 64px 60px;
  background: #fff;
  overflow-y: auto;
}
.form-title    { font-size: 26px; font-weight: 700; color: var(--gray-800); letter-spacing: -0.5px; }
.form-subtitle { font-size: 14px; color: var(--gray-400); line-height: 1.5; }

/* ── Tabs ───────────────────────────────────────────────────── */
.tab-bar {
  display: flex;
  background: var(--gray-100);
  border-radius: var(--radius-sm);
  padding: 4px;
  margin-bottom: 28px;
}
.tab-btn {
  flex: 1; padding: 9px;
  border: none; background: transparent;
  border-radius: 6px;
  font-family: inherit; font-size: 14px; font-weight: 600;
  color: var(--gray-400); cursor: pointer;
  transition: all 0.2s ease;
}
.tab-btn.active {
  background: #fff;
  color: var(--blue-600);
  box-shadow: var(--shadow-sm);
}

/* ── Role cards ─────────────────────────────────────────────── */
.role-label {
  font-size: 12px; font-weight: 600;
  color: var(--gray-600);
  text-transform: uppercase;
  letter-spacing: 0.6px;
  margin-bottom: 10px;
}
.role-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
.role-card {
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-md);
  padding: 16px 12px;
  background: #fff;
  cursor: pointer;
  text-align: center;
  transition: all 0.18s ease;
  font-family: inherit;
}
.role-card:hover { border-color: var(--blue-400); background: var(--blue-50); }
.role-card.selected {
  border-color: var(--blue-500);
  background: var(--blue-50);
  box-shadow: 0 0 0 3px rgba(30,111,197,0.1);
}
.role-icon {
  width: 40px; height: 40px;
  background: var(--gray-100);
  border-radius: var(--radius-sm);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 10px;
  transition: background 0.18s;
}
.role-card.selected .role-icon { background: rgba(30,111,197,0.12); }
.role-icon svg {
  width: 20px; height: 20px;
  fill: none; stroke: var(--gray-600);
  stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round;
}
.role-card.selected .role-icon svg { stroke: var(--blue-600); }
.role-name { font-size: 13px; font-weight: 600; color: var(--gray-800); margin-bottom: 2px; }
.role-desc { font-size: 11px; color: var(--gray-400); }
.role-card.selected .role-name { color: var(--blue-700); }

/* ── Fields ─────────────────────────────────────────────────── */
.field { margin-bottom: 18px; }
.field-label {
  display: block; font-size: 13px; font-weight: 600;
  color: var(--gray-600); margin-bottom: 7px;
}
.req { color: var(--red-400); margin-left: 1px; }
.input-wrap {
  position: relative; display: flex; align-items: center;
}
.input-icon {
  position: absolute; left: 13px;
  display: flex; align-items: center;
  pointer-events: none;
}
.input-icon svg {
  width: 16px; height: 16px;
  fill: none; stroke: var(--gray-400);
  stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round;
}
.form-input {
  width: 100%;
  padding: 10px 40px 10px 40px;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-sm);
  font-family: inherit; font-size: 14px;
  color: var(--gray-800);
  background: var(--gray-50);
  outline: none;
  transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
}
.form-input:focus {
  border-color: var(--blue-400);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(55,138,221,0.12);
}
.form-input.error {
  border-color: var(--red-400);
  background: var(--red-50);
}
.toggle-pw {
  position: absolute; right: 12px;
  background: none; border: none; cursor: pointer; padding: 4px;
  color: var(--gray-400); display: flex; align-items: center;
}
.toggle-pw:hover { color: var(--gray-600); }
.toggle-pw svg {
  width: 17px; height: 17px;
  fill: none; stroke: currentColor;
  stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round;
}
.field-error {
  display: none; margin-top: 6px;
  font-size: 12px; color: var(--red-400);
}
.field-error.show { display: block; }

/* Phone wrap */
.phone-wrap {
  display: flex; align-items: stretch;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius-sm);
  overflow: hidden;
  background: var(--gray-50);
}
.phone-prefix {
  padding: 10px 12px;
  background: var(--gray-100);
  border-right: 1.5px solid var(--gray-200);
  font-size: 13px; font-weight: 600;
  color: var(--gray-600);
  white-space: nowrap;
  display: flex; align-items: center;
}
.phone-wrap .form-input {
  border: none; border-radius: 0;
  background: transparent;
  padding-left: 13px;
}
.phone-wrap .form-input:focus {
  box-shadow: none;
}
.phone-wrap:focus-within {
  border-color: var(--blue-400);
  box-shadow: 0 0 0 3px rgba(55,138,221,0.12);
  background: #fff;
}

/* Notice box (verifikasi UMKM) */
.notice-box {
  display: flex; align-items: flex-start; gap: 10px;
  background: #EFF6FF; border: 1px solid #BFDBFE;
  border-radius: var(--radius-sm);
  padding: 12px 14px; margin-bottom: 20px;
  font-size: 13px; color: #1E40AF; line-height: 1.5;
}
.notice-box svg {
  width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px;
  fill: none; stroke: #1E40AF;
  stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round;
}

/* Primary button */
.btn-primary {
  width: 100%; padding: 12px;
  background: var(--blue-600);
  color: #fff; border: none;
  border-radius: var(--radius-sm);
  font-family: inherit; font-size: 15px; font-weight: 700;
  cursor: pointer; margin-top: 8px;
  transition: background 0.18s, transform 0.1s, box-shadow 0.18s;
  box-shadow: 0 1px 4px rgba(24,95,165,0.3);
}
.btn-primary:hover:not(:disabled) {
  background: var(--blue-700);
  box-shadow: 0 4px 12px rgba(24,95,165,0.35);
}
.btn-primary:active:not(:disabled) { transform: scale(0.99); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

/* Switch row */
.switch-row {
  text-align: center; margin-top: 20px;
  font-size: 13px; color: var(--gray-400);
}
.switch-link {
  background: none; border: none;
  color: var(--blue-600); font-weight: 600;
  font-size: 13px; cursor: pointer; padding: 0;
  font-family: inherit;
}
.switch-link:hover { text-decoration: underline; }

/* Spin keyframe untuk loading button */
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Responsive: sembunyikan brand panel di layar kecil ──────── */
@media (max-width: 768px) {
  .brand-panel { display: none; }
  .form-panel  { flex: 1; padding: 40px 24px; }
}
</style>
