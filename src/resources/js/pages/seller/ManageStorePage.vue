<template>
  <div class="page-wrap">

    <!-- ── Header ── -->
    <header class="detail-header">
      <button class="btn-back" @click="$router.back()" aria-label="Kembali">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="header-title">Kelola Profil Toko</span>
      <button
        v-if="!isEditing"
        class="btn-edit-header"
        @click="startEdit"
        aria-label="Edit"
      >
        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit
      </button>
      <button
        v-else
        class="btn-cancel-header"
        @click="cancelEdit"
      >
        Batal
      </button>
    </header>

    <!-- ── Toast ── -->
    <transition name="toast-fade">
      <div v-if="toast.visible" :class="['toast-msg', toast.type]">
        {{ toast.message }}
      </div>
    </transition>

    <div class="page-content">

      <!-- ── Status Banner ── -->
      <div :class="['status-banner', store.verification_status]">
        <div class="status-icon">
          <svg v-if="store.verification_status === 'disetujui'" viewBox="0 0 24 24">
            <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          <svg v-else-if="store.verification_status === 'menunggu'" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
          </svg>
          <svg v-else viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </div>
        <div class="status-text">
          <strong>{{ statusLabels[store.verification_status]?.title }}</strong>
          <p>{{ statusLabels[store.verification_status]?.desc }}</p>
        </div>
      </div>

      <!-- ── Logo Section ── -->
      <div class="section-card">
        <div class="logo-section">
          <div class="store-logo-wrap">
            <div v-if="!logoPreview" class="logo-placeholder">
              <span>{{ (form.store_name || store.store_name || 'T')[0].toUpperCase() }}</span>
            </div>
            <img v-else :src="logoPreview" alt="Logo toko" class="logo-img" />
            <button
              v-if="isEditing"
              class="btn-change-logo"
              @click="$refs.logoFileInput.click()"
              aria-label="Ganti foto"
            >
              <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </button>
          </div>
          <input ref="logoFileInput" type="file" accept="image/*" class="hidden-input" @change="handleLogoChange" />
          <div class="logo-meta">
            <h2 class="store-name-display">{{ store.store_name }}</h2>
            <p class="store-cat-display">{{ getCategoryLabel(store.store_category) }}</p>
            <div class="store-stats-row">
              <span class="stat-chip">⭐ {{ store.rating }}</span>
              <span class="stat-chip">📦 {{ store.orders_count }} Pesanan</span>
              <span class="stat-chip">🛍️ {{ store.products_count }} Produk</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Form / Info Sections ── -->
      <form @submit.prevent="handleSave" novalidate>

        <!-- ── Identitas Toko ── -->
        <div class="section-card">
          <div class="section-head">
            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <h3 class="section-title">Identitas Toko</h3>
          </div>

          <!-- Nama Toko -->
          <div class="form-field">
            <label class="field-label">Nama Toko <span class="req">*</span></label>
            <div v-if="isEditing" class="input-wrap">
              <input
                v-model="form.store_name"
                type="text"
                placeholder="Nama toko Anda"
                :class="['form-input', errors.store_name && 'error']"
                maxlength="100"
              />
              <span class="char-hint">{{ form.store_name.length }}/100</span>
            </div>
            <p v-else class="field-value">{{ store.store_name }}</p>
            <p v-if="errors.store_name" class="field-error">{{ errors.store_name }}</p>
          </div>

          <!-- Kategori -->
          <div class="form-field">
            <label class="field-label">Kategori Usaha <span class="req">*</span></label>
            <div v-if="isEditing" class="select-wrap">
              <select
                v-model="form.store_category"
                :class="['form-input', 'form-select', errors.store_category && 'error']"
              >
                <option value="" disabled>Pilih kategori...</option>
                <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                  {{ cat.icon }} {{ cat.label }}
                </option>
              </select>
              <span class="select-arrow">
                <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
              </span>
            </div>
            <p v-else class="field-value">{{ getCategoryLabel(store.store_category) }}</p>
            <p v-if="errors.store_category" class="field-error">{{ errors.store_category }}</p>
          </div>

          <!-- Deskripsi -->
          <div class="form-field">
            <label class="field-label">Deskripsi Toko</label>
            <div v-if="isEditing">
              <textarea
                v-model="form.description"
                rows="3"
                maxlength="200"
                placeholder="Ceritakan keunggulan toko Anda..."
                :class="['form-input', 'form-textarea', errors.description && 'error']"
              ></textarea>
              <span class="char-hint">{{ (form.description || '').length }}/200</span>
            </div>
            <p v-else class="field-value">{{ store.description || '—' }}</p>
          </div>
        </div>

        <!-- ── Lokasi & Kontak ── -->
        <div class="section-card">
          <div class="section-head">
            <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <h3 class="section-title">Lokasi & Kontak</h3>
          </div>

          <!-- Kelurahan/Kecamatan -->
          <div class="form-field">
            <label class="field-label">Kecamatan / Kelurahan <span class="req">*</span></label>
            <div v-if="isEditing" class="select-wrap">
              <select
                v-model="form.district"
                :class="['form-input', 'form-select', errors.district && 'error']"
              >
                <option value="" disabled>Pilih kelurahan...</option>
                <option v-for="d in districts" :key="d.value" :value="d.value">{{ d.label }}</option>
              </select>
              <span class="select-arrow">
                <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
              </span>
            </div>
            <p v-else class="field-value capitalize">{{ store.district || '—' }}</p>
            <p v-if="errors.district" class="field-error">{{ errors.district }}</p>
          </div>

          <!-- Alamat -->
          <div class="form-field">
            <label class="field-label">Alamat Lengkap <span class="req">*</span></label>
            <textarea
              v-if="isEditing"
              v-model="form.address"
              rows="2"
              placeholder="Jl. Contoh No. 12, RT/RW, Kelurahan..."
              :class="['form-input', 'form-textarea', errors.address && 'error']"
            ></textarea>
            <p v-else class="field-value">{{ store.address || '—' }}</p>
            <p v-if="errors.address" class="field-error">{{ errors.address }}</p>
          </div>

          <!-- Kontak WA (read-only) -->
          <div class="form-field">
            <label class="field-label">No. WhatsApp</label>
            <div class="field-value-with-icon">
              <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
              <p class="field-value">{{ formatPhone(store.seller_phone) }}</p>
            </div>
            <p class="field-hint">Nomor WA tidak dapat diubah di sini.</p>
          </div>
        </div>

        <!-- ── Jam Operasional ── -->
        <div class="section-card">
          <div class="section-head">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <h3 class="section-title">Jam Operasional</h3>
          </div>

          <div class="form-field">
            <label class="field-label">Jadwal Buka <span class="req">*</span></label>
            <input
              v-if="isEditing"
              v-model="form.operating_hours"
              type="text"
              placeholder="mis. Senin–Sabtu, 07:00 – 20:00"
              :class="['form-input', errors.operating_hours && 'error']"
            />
            <p v-else class="field-value">{{ store.operating_hours || '—' }}</p>
            <p v-if="errors.operating_hours" class="field-error">{{ errors.operating_hours }}</p>
          </div>
        </div>

        <!-- ── Produk Toko ── -->
        <div class="section-card">
          <div class="section-head-between">
            <div class="section-head">
              <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
              <h3 class="section-title">Produk Toko</h3>
            </div>
            <button type="button" class="btn-text-link" @click="$router.push({ name: 'seller.products' })">
              Lihat semua →
            </button>
          </div>

          <div class="product-quick-list">
            <div v-for="product in storeProducts" :key="product.id" class="product-quick-item">
              <div class="product-quick-thumb">
                <span>{{ getCategoryEmoji(product.category) }}</span>
              </div>
              <div class="product-quick-info">
                <p class="product-quick-name">{{ product.name }}</p>
                <p class="product-quick-price">Rp {{ formatPrice(product.price) }} / {{ product.unit }}</p>
              </div>
              <div class="product-quick-meta">
                <span :class="['stock-chip', product.stock < 5 ? 'low' : 'ok']">
                  {{ product.stock }} {{ product.unit }}
                </span>
                <span :class="['active-chip', product.is_active ? 'on' : 'off']">
                  {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </div>
            </div>

            <div v-if="storeProducts.length === 0" class="empty-products">
              <span>🛍️</span>
              <p>Belum ada produk ditambahkan.</p>
            </div>
          </div>

          <button
            type="button"
            class="btn-add-product"
            @click="$router.push({ name: 'seller.products.add' })"
          >
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Produk Baru
          </button>
        </div>

        <!-- ── Save Button ── -->
        <div v-if="isEditing" class="save-bar">
          <button type="submit" class="btn-save" :disabled="isSaving">
            <span v-if="isSaving" class="saving-spinner">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3"/></svg>
              Menyimpan...
            </span>
            <span v-else>
              <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
              Simpan Perubahan
            </span>
          </button>
        </div>

      </form>

      <!-- Spacer for nav -->
      <div style="height: 88px;"></div>
    </div>

    <!-- ── Bottom Nav ── -->
    <nav class="bottom-nav">
      <button class="nav-btn" @click="$router.push({ name: 'seller.dashboard' })">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Pesanan
      </button>
      <button class="nav-btn nav-btn--active">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        Toko
      </button>
      <button class="nav-btn">
        <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
        Notifikasi
      </button>
      <button class="nav-btn">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profil
      </button>
    </nav>

  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { mockStore, mockProducts } from '@/data/mockData'

// ── State ─────────────────────────────────────────────────────
const isEditing = ref(false)
const isSaving  = ref(false)
const logoPreview = ref(null)

const store = reactive({ ...mockStore })

const form = reactive({
  store_name:      store.store_name,
  store_category:  store.store_category,
  description:     store.description || '',
  district:        store.district,
  address:         store.address,
  operating_hours: store.operating_hours,
})

const errors = reactive({})

const toast = reactive({ visible: false, message: '', type: '' })

// ── Computed ──────────────────────────────────────────────────
const storeProducts = computed(() =>
  mockProducts.filter(p => p.store_id === store.id_store).slice(0, 4)
)

// ── Data ──────────────────────────────────────────────────────
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
  { value: 'jebres',          label: 'Jebres' },
  { value: 'tegalharjo',      label: 'Tegalharjo' },
  { value: 'kepatihan_kulon', label: 'Kepatihan Kulon' },
  { value: 'kepatihan_wetan', label: 'Kepatihan Wetan' },
  { value: 'pucang_sawit',    label: 'Pucang Sawit' },
  { value: 'gandekan',        label: 'Gandekan' },
  { value: 'sewu',            label: 'Sewu' },
  { value: 'jagalan',         label: 'Jagalan' },
  { value: 'mojosongo',       label: 'Mojosongo' },
]

const statusLabels = {
  disetujui:  { title: 'Toko Terverifikasi', desc: 'Toko Anda telah diverifikasi dan aktif di katalog.' },
  menunggu:   { title: 'Menunggu Verifikasi', desc: 'Admin sedang meninjau profil toko Anda (1×24 jam).' },
  dibatalkan: { title: 'Verifikasi Ditolak', desc: 'Hubungi admin untuk informasi lebih lanjut.' },
}

// ── Methods ───────────────────────────────────────────────────
function startEdit() {
  // Sync form ke data store terkini
  form.store_name      = store.store_name
  form.store_category  = store.store_category
  form.description     = store.description || ''
  form.district        = store.district
  form.address         = store.address
  form.operating_hours = store.operating_hours
  clearErrors()
  isEditing.value = true
}

function cancelEdit() {
  clearErrors()
  logoPreview.value = null
  isEditing.value = false
}

function validate() {
  clearErrors()
  let valid = true
  if (!form.store_name.trim()) {
    errors.store_name = 'Nama toko wajib diisi.'
    valid = false
  }
  if (!form.store_category) {
    errors.store_category = 'Pilih kategori usaha.'
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
  if (!form.operating_hours.trim()) {
    errors.operating_hours = 'Jam operasional wajib diisi.'
    valid = false
  }
  return valid
}

function handleSave() {
  if (!validate()) {
    showToast('Periksa kembali isian form.', 'error')
    return
  }

  isSaving.value = true

  // Simulasi API call — nanti ganti dengan apiClient.post('/store/setup', ...)
  setTimeout(() => {
    // Update local store state
    store.store_name      = form.store_name
    store.store_category  = form.store_category
    store.description     = form.description
    store.district        = form.district
    store.address         = form.address
    store.operating_hours = form.operating_hours

    isSaving.value  = false
    isEditing.value = false
    showToast('Profil toko berhasil diperbarui!', 'success')
  }, 900)
}

function handleLogoChange(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) {
    showToast('Ukuran foto maksimal 2 MB.', 'error')
    return
  }
  const reader = new FileReader()
  reader.onload = ev => { logoPreview.value = ev.target.result }
  reader.readAsDataURL(file)
}

function getCategoryLabel(value) {
  return categories.find(c => c.value === value)?.label || value || '—'
}

function getCategoryEmoji(value) {
  const map = {
    makanan_minuman: '🍱',
    fashion_batik:   '👗',
    kerajinan:       '🧶',
    kecantikan:      '💄',
    pertanian:       '🌾',
    elektronik:      '📱',
    jasa:            '🛠️',
    lainnya:         '📦',
  }
  return map[value] || '📦'
}

function formatPrice(v) {
  return Number(v).toLocaleString('id-ID')
}

function formatPhone(phone) {
  if (!phone) return '—'
  // Format: +62 8xx-xxxx-xxxx
  const raw = String(phone).replace(/\D/g, '')
  if (raw.startsWith('62')) return '+' + raw.replace(/(\d{2})(\d{3})(\d{4})(\d+)/, '$1 $2-$3-$4')
  return phone
}

function clearErrors() {
  Object.keys(errors).forEach(k => delete errors[k])
}

let toastTimer = null
function showToast(message, type = '') {
  clearTimeout(toastTimer)
  toast.message = message
  toast.type    = type
  toast.visible = true
  toastTimer = setTimeout(() => { toast.visible = false }, 3000)
}
</script>

<style scoped>
/* ── Base ─────────────────────────────────────────────────────── */
* { box-sizing: border-box; }

.page-wrap {
  min-height: 100vh;
  background: #f0f4f8;
  font-family: 'Plus Jakarta Sans', sans-serif;
  max-width: 480px;
  margin: 0 auto;
  position: relative;
}

.hidden-input { display: none; }

/* ── Header ──────────────────────────────────────────────────── */
.detail-header {
  position: sticky; top: 0; z-index: 100;
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 16px;
  background: linear-gradient(135deg, #1a7fc4 0%, #1565a8 100%);
  box-shadow: 0 2px 8px rgba(21,101,168,0.25);
}
.btn-back {
  width: 36px; height: 36px;
  background: rgba(255,255,255,0.18); border: none; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #fff;
}
.btn-back svg {
  width: 20px; height: 20px;
  fill: none; stroke: currentColor;
  stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round;
}
.header-title { font-size: 16px; font-weight: 700; color: #fff; }
.btn-edit-header {
  display: flex; align-items: center; gap: 5px;
  padding: 6px 14px;
  background: rgba(255,255,255,0.2);
  border: 1.5px solid rgba(255,255,255,0.5);
  border-radius: 100px;
  font-size: 13px; font-weight: 700; color: #fff;
  cursor: pointer; font-family: inherit;
  transition: background 0.15s;
}
.btn-edit-header:hover { background: rgba(255,255,255,0.3); }
.btn-edit-header svg {
  width: 14px; height: 14px;
  fill: none; stroke: currentColor;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}
.btn-cancel-header {
  padding: 6px 14px;
  background: rgba(255,255,255,0.15);
  border: 1.5px solid rgba(255,255,255,0.4);
  border-radius: 100px;
  font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.9);
  cursor: pointer; font-family: inherit;
}

/* ── Toast ────────────────────────────────────────────────────── */
.toast-msg {
  position: fixed; bottom: 88px; left: 50%;
  transform: translateX(-50%);
  padding: 10px 20px;
  background: #1f2937; color: #fff;
  font-size: 13px; font-weight: 600;
  border-radius: 100px;
  z-index: 999; white-space: nowrap;
  box-shadow: 0 4px 16px rgba(0,0,0,0.2);
}
.toast-msg.success { background: #166534; }
.toast-msg.error   { background: #991b1b; }
.toast-fade-enter-active,
.toast-fade-leave-active { transition: opacity 0.25s ease, transform 0.25s ease; }
.toast-fade-enter-from,
.toast-fade-leave-to { opacity: 0; transform: translateX(-50%) translateY(8px); }

/* ── Page Content ─────────────────────────────────────────────── */
.page-content { padding: 12px 12px 0; }

/* ── Status Banner ────────────────────────────────────────────── */
.status-banner {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 14px; border-radius: 12px;
  margin-bottom: 10px;
}
.status-banner.disetujui  { background: #dcfce7; border: 1px solid #bbf7d0; }
.status-banner.menunggu   { background: #fef3c7; border: 1px solid #fde68a; }
.status-banner.dibatalkan { background: #fee2e2; border: 1px solid #fecaca; }
.status-icon {
  width: 36px; height: 36px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.status-banner.disetujui  .status-icon { background: #bbf7d0; }
.status-banner.menunggu   .status-icon { background: #fde68a; }
.status-banner.dibatalkan .status-icon { background: #fecaca; }
.status-icon svg {
  width: 18px; height: 18px;
  fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}
.status-banner.disetujui  .status-icon svg { stroke: #166534; }
.status-banner.menunggu   .status-icon svg { stroke: #92400e; }
.status-banner.dibatalkan .status-icon svg { stroke: #991b1b; }
.status-text strong { font-size: 13px; font-weight: 700; color: #1f2937; }
.status-text p { font-size: 12px; color: #6b7280; margin: 2px 0 0; }

/* ── Section Card ─────────────────────────────────────────────── */
.section-card {
  background: #fff;
  border-radius: 14px;
  padding: 16px;
  margin-bottom: 10px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}

.section-head {
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 14px;
}
.section-head svg {
  width: 17px; height: 17px;
  fill: none; stroke: #1565a8;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
  flex-shrink: 0;
}
.section-head-between {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 14px;
}
.section-head-between .section-head { margin-bottom: 0; }
.section-title { font-size: 14px; font-weight: 700; color: #111827; }

/* ── Logo Section ─────────────────────────────────────────────── */
.logo-section {
  display: flex; align-items: center; gap: 16px;
}
.store-logo-wrap {
  position: relative; flex-shrink: 0;
}
.logo-placeholder {
  width: 72px; height: 72px; border-radius: 16px;
  background: linear-gradient(135deg, #1a7fc4, #1565a8);
  display: flex; align-items: center; justify-content: center;
  font-size: 28px; font-weight: 800; color: #fff;
}
.logo-img {
  width: 72px; height: 72px; border-radius: 16px;
  object-fit: cover;
}
.btn-change-logo {
  position: absolute; bottom: -6px; right: -6px;
  width: 26px; height: 26px; border-radius: 50%;
  background: #1565a8; border: 2px solid #fff;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #fff;
}
.btn-change-logo svg {
  width: 13px; height: 13px;
  fill: none; stroke: currentColor;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}
.logo-meta { flex: 1; min-width: 0; }
.store-name-display { font-size: 16px; font-weight: 800; color: #111827; margin-bottom: 2px; }
.store-cat-display  { font-size: 12px; color: #6b7280; margin-bottom: 8px; }
.store-stats-row { display: flex; gap: 6px; flex-wrap: wrap; }
.stat-chip {
  font-size: 11px; font-weight: 600;
  padding: 3px 8px; border-radius: 100px;
  background: #f0f4f8; color: #374151;
}

/* ── Form Fields ──────────────────────────────────────────────── */
.form-field { margin-bottom: 14px; }
.form-field:last-child { margin-bottom: 0; }
.field-label {
  display: flex; align-items: center; justify-content: space-between;
  font-size: 12px; font-weight: 600;
  color: #4b5563; margin-bottom: 6px;
  text-transform: uppercase; letter-spacing: 0.3px;
}
.req { color: #e24b4a; font-size: 13px; }
.char-hint { font-size: 11px; font-weight: 400; color: #9ca3af; margin-left: auto; }
.field-value {
  font-size: 14px; color: #1f2937;
  line-height: 1.5; padding: 2px 0;
}
.field-hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }
.field-value-with-icon {
  display: flex; align-items: center; gap: 6px;
}
.field-value-with-icon svg {
  width: 14px; height: 14px; flex-shrink: 0;
  fill: none; stroke: #25d366;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}
.capitalize { text-transform: capitalize; }
.field-error { font-size: 12px; color: #e24b4a; margin-top: 4px; }

.input-wrap { position: relative; }
.form-input {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  font-size: 14px; font-family: inherit;
  color: #1f2937; background: #f9fafb;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
  appearance: none;
}
.form-input:focus {
  border-color: #1565a8;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(21,101,168,0.1);
}
.form-input.error { border-color: #e24b4a; background: #fff0f0; }
.form-textarea { resize: vertical; min-height: 64px; line-height: 1.5; }
.select-wrap { position: relative; }
.form-select { cursor: pointer; padding-right: 36px; }
.select-arrow {
  position: absolute; right: 11px; top: 50%; transform: translateY(-50%);
  pointer-events: none; display: flex; align-items: center;
  color: #9ca3af;
}
.select-arrow svg {
  width: 15px; height: 15px;
  fill: none; stroke: currentColor;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}

/* ── Product Quick List ───────────────────────────────────────── */
.product-quick-list { margin-bottom: 12px; }
.product-quick-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 0;
  border-bottom: 1px solid #f3f4f6;
}
.product-quick-item:last-child { border-bottom: none; }
.product-quick-thumb {
  width: 38px; height: 38px; border-radius: 10px;
  background: #f0f4f8;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; flex-shrink: 0;
}
.product-quick-info { flex: 1; min-width: 0; }
.product-quick-name  { font-size: 13px; font-weight: 600; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.product-quick-price { font-size: 11px; color: #6b7280; margin-top: 1px; }
.product-quick-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 3px; }
.stock-chip, .active-chip {
  font-size: 10px; font-weight: 700;
  padding: 2px 7px; border-radius: 100px;
  white-space: nowrap;
}
.stock-chip.ok  { background: #dcfce7; color: #166534; }
.stock-chip.low { background: #fef3c7; color: #92400e; }
.active-chip.on  { background: #dbeafe; color: #1e40af; }
.active-chip.off { background: #f3f4f6; color: #9ca3af; }

.empty-products {
  display: flex; flex-direction: column; align-items: center;
  padding: 28px; gap: 6px; color: #9ca3af; font-size: 13px;
}
.empty-products span { font-size: 32px; }

.btn-add-product {
  width: 100%; padding: 11px;
  display: flex; align-items: center; justify-content: center; gap: 7px;
  border: 1.5px dashed #93c5fd;
  border-radius: 10px;
  background: #eff6ff; color: #1565a8;
  font-size: 13px; font-weight: 700; font-family: inherit;
  cursor: pointer; transition: background 0.15s, border-color 0.15s;
}
.btn-add-product:hover { background: #dbeafe; border-color: #60a5fa; }
.btn-add-product svg {
  width: 16px; height: 16px;
  fill: none; stroke: currentColor;
  stroke-width: 2.2; stroke-linecap: round;
}

.btn-text-link {
  font-size: 12px; font-weight: 700; color: #1565a8;
  background: none; border: none; cursor: pointer;
  font-family: inherit; padding: 0;
}
.btn-text-link:hover { text-decoration: underline; }

/* ── Save Bar ─────────────────────────────────────────────────── */
.save-bar {
  position: sticky; bottom: 72px; z-index: 50;
  padding: 10px 0 4px;
}
.btn-save {
  width: 100%; padding: 14px;
  background: linear-gradient(135deg, #1a7fc4, #1565a8);
  color: #fff; border: none; border-radius: 14px;
  font-size: 15px; font-weight: 700; font-family: inherit;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  box-shadow: 0 4px 14px rgba(21,101,168,0.35);
  transition: opacity 0.15s, transform 0.1s;
}
.btn-save:hover:not(:disabled) { opacity: 0.92; transform: translateY(-1px); }
.btn-save:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-save svg {
  width: 18px; height: 18px;
  fill: none; stroke: currentColor;
  stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
}
.saving-spinner {
  display: flex; align-items: center; gap: 8px;
}
.saving-spinner svg {
  width: 16px; height: 16px;
  fill: none; stroke: rgba(255,255,255,0.6);
  stroke-width: 3;
  animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Bottom Nav ───────────────────────────────────────────────── */
.bottom-nav {
  position: fixed; bottom: 0; left: 50%;
  transform: translateX(-50%);
  width: 100%; max-width: 480px;
  background: #fff; border-top: 1px solid #e5e7eb;
  display: flex; z-index: 200;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.06);
}
.nav-btn {
  flex: 1; padding: 8px 4px 10px;
  background: none; border: none; cursor: pointer;
  display: flex; flex-direction: column; align-items: center; gap: 3px;
  font-size: 10px; font-weight: 500; font-family: inherit;
  color: #9ca3af; transition: color 0.15s;
}
.nav-btn svg {
  width: 20px; height: 20px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}
.nav-btn--active { color: #1565a8; }
</style>