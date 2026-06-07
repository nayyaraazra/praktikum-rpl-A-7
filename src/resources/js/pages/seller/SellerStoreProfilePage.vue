<template>
  <div class="profile-layout">
    <SellerSidebar />

    <main class="profile-main">
      <!-- Loading -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
      </div>

      <template v-else-if="store">
        <!-- Cover -->
        <div class="store-cover">
          <div class="cover-pattern"></div>
          <div class="cover-orb"></div>
        </div>

        <!-- Profile Header -->
        <div class="profile-header">
          <div class="profile-header-inner">
            <div class="store-logo-wrap">
              <div v-if="store.store_logo_url" class="store-logo-img" :style="{ backgroundImage: 'url(' + store.store_logo_url + ')' }"></div>
              <div v-else class="store-logo">{{ storeInitials }}</div>
            </div>
            <div class="store-info">
              <div class="store-name-row">
                <h1 class="store-name">{{ store.store_name }}</h1>
                <span v-if="store.verification_status === 'disetujui'" class="verified-badge">Terverifikasi</span>
                <span v-else-if="store.verification_status === 'menunggu'" class="pending-badge">Menunggu Verifikasi</span>
              </div>
              <p class="store-desc">{{ store.description || 'Belum ada deskripsi toko.' }}</p>
              <div class="store-meta">
                <span class="meta-item">📍 {{ store.district || 'Kelurahan Jebres, Surakarta' }}</span>
                <span class="meta-item">📅 Bergabung {{ joinedDate }}</span>
              </div>
            </div>
            <div class="profile-actions">
              <button class="btn-edit-profile" @click="openEditModal">Edit Profil</button>
            </div>
          </div>
        </div>

        <!-- Body -->
        <div class="store-body">
          <div class="store-body-grid">
            <!-- Left: Products -->
            <div class="products-section">
              <h3 class="section-title">Produk Unggulan</h3>
              <div v-if="products.length === 0" class="empty-products">
                <p>Belum ada produk. Tambahkan produk di halaman Kelola Produk.</p>
              </div>
              <div v-else class="products-grid">
                <div v-for="(p, i) in products" :key="p.id_product" class="product-card-store">
                  <div v-if="p.image_url" class="product-thumb-img" :style="{ backgroundImage: 'url(' + p.image_url + ')' }"></div>
                  <div v-else :class="['product-thumb', thumbBgClass(i)]">
                    <span class="product-thumb-emoji">{{ productEmoji(p.name) }}</span>
                  </div>
                  <div class="product-info">
                    <div class="product-name">{{ p.name }}</div>
                    <div class="product-price">{{ formatRupiah(p.price) }}</div>
                    <div class="product-category">{{ p.category?.name_category || '' }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right: Info & Contact -->
            <div class="info-section">
              <div class="info-card">
                <h4 class="info-card-title">Informasi Toko</h4>
                <div class="info-list">
                  <div class="info-row">
                    <span class="info-label">Jam Buka</span>
                    <span class="info-value">{{ store.operating_hours || '-' }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Kategori</span>
                    <span class="info-value">{{ store.store_category || '-' }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Alamat</span>
                    <span class="info-value">{{ store.address || '-' }}</span>
                  </div>
                </div>
              </div>

              <div class="info-card">
                <h4 class="info-card-title">Kontak & Sosial Media</h4>
                <div class="contact-list">
                  <a v-if="store.whatsapp" :href="'https://wa.me/' + store.whatsapp" target="_blank" class="contact-btn whatsapp">
                    <span>Hubungi via WhatsApp</span>
                  </a>
                  <a v-else class="contact-btn whatsapp disabled">
                    <span>WhatsApp belum diatur</span>
                  </a>
                  <a v-if="store.instagram" :href="'https://instagram.com/' + store.instagram" target="_blank" class="contact-btn instagram">
                    <span>Instagram {{ store.instagram }}</span>
                  </a>
                  <a v-else class="contact-btn instagram disabled">
                    <span>Instagram belum diatur</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>

      <!-- No store -->
      <div v-else class="error-state">
        <p>Toko belum terdaftar. Silakan isi profil toko terlebih dahulu.</p>
        <router-link :to="{ name: 'store.setup' }" class="btn-primary">Isi Profil Toko</router-link>
      </div>
    </main>

    <!-- Edit Modal -->
    <div v-if="showEditModal" class="modal-overlay" @click.self="closeEditModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title">Edit Profil Toko</h2>
          <button class="modal-close" @click="closeEditModal">&times;</button>
        </div>
        <form @submit.prevent="handleSave" class="modal-form" novalidate>
          <div class="field">
            <label class="field-label">Nama Toko</label>
            <input v-model="editForm.store_name" class="form-input" />
            <span v-if="editErrors.store_name" class="field-error">{{ editErrors.store_name }}</span>
          </div>
          <div class="field">
            <label class="field-label">Logo Toko</label>
            <div class="logo-upload-row">
              <label class="image-upload-btn">
                <input type="file" accept="image/*" @change="onLogoChange" />
                <span>Pilih Logo</span>
              </label>
              <div v-if="logoPreview" class="logo-preview-sm">
                <img :src="logoPreview" alt="Logo" />
                <button type="button" class="image-remove" @click="removeLogo">&times;</button>
              </div>
            </div>
            <span v-if="editErrors.store_logo" class="field-error">{{ editErrors.store_logo }}</span>
          </div>
          <div class="field">
            <label class="field-label">Kategori Usaha</label>
            <select v-model="editForm.category" class="form-input">
              <option value="">Pilih kategori</option>
              <option value="makanan_minuman">Makanan &amp; Minuman</option>
              <option value="fashion_batik">Fashion &amp; Batik</option>
              <option value="kerajinan">Kerajinan Tangan</option>
              <option value="elektronik">Elektronik</option>
              <option value="kecantikan">Kecantikan</option>
              <option value="pertanian">Pertanian</option>
              <option value="jasa">Jasa</option>
              <option value="lainnya">Lainnya</option>
            </select>
          </div>
          <div class="field">
            <label class="field-label">Deskripsi Toko</label>
            <textarea v-model="editForm.description" class="form-input" rows="3" maxlength="200"></textarea>
          </div>
          <div class="field">
            <label class="field-label">Kecamatan / Kelurahan</label>
            <select v-model="editForm.district" class="form-input">
              <option value="">Pilih kelurahan</option>
              <option value="Jebres">Jebres</option>
              <option value="Gandekan">Gandekan</option>
              <option value="Jagalan">Jagalan</option>
              <option value="Kepatihan Kulon">Kepatihan Kulon</option>
              <option value="Kepatihan Wetan">Kepatihan Wetan</option>
              <option value="Mojosongo">Mojosongo</option>
              <option value="Pucang Sawit">Pucang Sawit</option>
              <option value="Purwodiningratan">Purwodiningratan</option>
              <option value="Sewu">Sewu</option>
              <option value="Tegalharjo">Tegalharjo</option>
            </select>
          </div>
          <div class="field">
            <label class="field-label">Alamat Lengkap</label>
            <textarea v-model="editForm.address" class="form-input" rows="2"></textarea>
          </div>
          <div class="field">
            <label class="field-label">Jam Operasional</label>
            <input v-model="editForm.operating_hours" class="form-input" placeholder="contoh: 08:00 - 17:00 WIB" />
          </div>
          <div class="grid-2">
            <div class="field">
              <label class="field-label">WhatsApp</label>
              <input v-model="editForm.whatsapp" class="form-input" placeholder="08123456789" />
            </div>
            <div class="field">
              <label class="field-label">Instagram</label>
              <input v-model="editForm.instagram" class="form-input" placeholder="username" />
            </div>
          </div>
          <div v-if="editErrors._" class="error-msg">{{ editErrors._ }}</div>
          <button type="submit" class="btn-submit" :disabled="saving">
            {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </form>
      </div>
    </div>

    <!-- Toast -->
    <div :class="['toast', toastType, toastVisible && 'show']" role="alert">{{ toastMsg }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { sellerApi } from '@/services/api/sellerApi'
import SellerSidebar from '@/components/seller/SellerSidebar.vue'

const store    = ref(null)
const products = ref([])
const loading  = ref(true)
const error    = ref('')

const showEditModal = ref(false)
const saving        = ref(false)
const editForm      = ref({})
const editErrors    = ref({})
const logoFile      = ref(null)
const logoPreview   = ref(null)

// Toast
const toastMsg     = ref('')
const toastType    = ref('')
const toastVisible = ref(false)
let toastTimer     = null

const storeInitials = computed(() => {
  const name = store.value?.store_name || ''
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) || 'TK'
})

const joinedDate = computed(() => {
  if (!store.value?.created_at) return '-'
  const d = new Date(store.value.created_at)
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
  return months[d.getMonth()] + ' ' + d.getFullYear()
})

const thumbColors = ['thumb-bg-1', 'thumb-bg-2', 'thumb-bg-3', 'thumb-bg-4', 'thumb-bg-5']
function thumbBgClass(i) {
  return thumbColors[i % thumbColors.length]
}

function productEmoji(name) {
  const emojis = ['🍱', '🍚', '🧁', '🥘', '🍜', '🥗', '🍛', '🍝', '🥟', '🍩', '🍪', '🥐']
  let hash = 0
  for (const ch of name) hash = ((hash << 5) - hash) + ch.charCodeAt(0)
  return emojis[Math.abs(hash) % emojis.length]
}

function formatRupiah(value) {
  const num = Number(value)
  if (num >= 1000000) return 'Rp' + (num / 1000000).toFixed(1).replace(/\.0$/, '') + 'jt'
  if (num >= 1000) return 'Rp' + (num / 1000).toFixed(1).replace(/\.0$/, '') + 'rb'
  return 'Rp' + num.toLocaleString('id-ID')
}

async function fetchStore() {
  loading.value = true
  error.value   = ''
  try {
    const res = await sellerApi.getStore()
    store.value   = res.data.data
    products.value = res.data.data?.products || []
  } catch (err) {
    if (err.response?.status === 404) {
      store.value = null
    } else {
      error.value = 'Gagal memuat data toko.'
    }
  } finally {
    loading.value = false
  }
}

function showToast(msg, type = '', duration = 3000) {
  clearTimeout(toastTimer)
  toastMsg.value     = msg
  toastType.value    = type
  toastVisible.value = true
  toastTimer = setTimeout(() => { toastVisible.value = false }, duration)
}

function openEditModal() {
  editForm.value = {
    store_name:      store.value?.store_name || '',
    category:        store.value?.store_category || '',
    description:     store.value?.description || '',
    district:        store.value?.district || '',
    address:         store.value?.address || '',
    operating_hours: store.value?.operating_hours || '',
    whatsapp:        store.value?.whatsapp || '',
    instagram:       store.value?.instagram || '',
  }
  logoFile.value    = null
  logoPreview.value = store.value?.store_logo_url || null
  editErrors.value  = {}
  showEditModal.value = true
}

function onLogoChange(e) {
  const file = e.target.files?.[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) {
    editErrors.value.store_logo = 'Maksimal 2MB.'
    return
  }
  logoFile.value = file
  logoPreview.value = URL.createObjectURL(file)
}

function removeLogo() {
  logoFile.value    = null
  logoPreview.value = null
}

function closeEditModal() {
  showEditModal.value = false
}

function buildStoreFormData() {
  const fd = new FormData()
  Object.keys(editForm.value).forEach(k => {
    fd.append(k, editForm.value[k])
  })
  if (logoFile.value) {
    fd.append('store_logo', logoFile.value)
  }
  return fd
}

async function handleSave() {
  saving.value      = true
  editErrors.value  = {}
  try {
    const hasLogo = logoFile.value instanceof File
    const payload = hasLogo ? buildStoreFormData() : editForm.value
    const res = await sellerApi.updateStore(payload)
    store.value = res.data.data
    products.value = res.data.data?.products || []
    showEditModal.value = false
    showToast('Profil toko berhasil diperbarui.', 'success')
  } catch (err) {
    if (err.response?.status === 422) {
      console.error('Store 422 errors:', err.response.data)
      const fieldErrors = err.response.data.errors ?? {}
      const mapped = {}
      Object.keys(fieldErrors).forEach(f => {
        mapped[f] = fieldErrors[f][0]
      })
      editErrors.value = mapped
    } else {
      editErrors.value = { _: 'Gagal menyimpan. Silakan coba lagi.' }
    }
  } finally {
    saving.value = false
  }
}

onMounted(fetchStore)
</script>

<style scoped>
.profile-layout {
  display: flex;
  min-height: 100vh;
}
.profile-main {
  flex: 1;
  background: #F4F3F0;
  overflow-y: auto;
}

/* Loading / Error */
.loading-state, .error-state {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 80px 24px; gap: 12px;
  color: #5C5B54; font-size: 15px;
}
.spinner {
  width: 32px; height: 32px;
  border: 3px solid #E8E7E2;
  border-top-color: rgb(24,95,165);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.error-state .btn-primary {
  padding: 10px 20px;
  border-radius: 8px;
  background: rgb(24,95,165);
  color: #fff;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
}

/* Cover */
.store-cover {
  height: 200px;
  background: linear-gradient(135deg, #124F85 0%, rgb(24,95,165) 60%, #3D7DBD 100%);
  position: relative;
  overflow: hidden;
  flex-shrink: 0;
}
.cover-pattern {
  position: absolute; inset: 0;
  background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px);
  background-size: 24px 24px;
}
.cover-orb {
  position: absolute;
  top: -60px; right: -60px;
  width: 240px; height: 240px;
  background: rgba(255,255,255,0.06);
  border-radius: 50%;
}

/* Profile Header */
.profile-header {
  background: #fff;
  border-bottom: 1px solid #E8E7E2;
  padding: 0 40px 32px;
}
.profile-header-inner {
  display: flex;
  gap: 32px;
  align-items: flex-start;
  margin-top: -64px;
  padding-top: 0;
}
.store-logo-wrap {
  width: 128px; height: 128px;
  border-radius: 20px;
  background: #fff;
  padding: 6px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.1);
  flex-shrink: 0;
  position: relative;
  z-index: 2;
}
.store-logo {
  width: 100%; height: 100%;
  background: linear-gradient(135deg, #FDF3E3, #FFF3D6);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 44px;
  color: #854F0B;
  font-weight: 800;
}
.store-logo-img {
  width: 100%; height: 100%;
  border-radius: 14px;
  background-size: cover;
  background-position: center;
}
.store-info {
  flex: 1;
  padding-top: 72px;
}
.store-name-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}
.store-name {
  font-size: 28px;
  font-weight: 800;
  color: #282724;
  letter-spacing: -0.5px;
}
.verified-badge {
  background: #E8F1FB;
  color: rgb(24,95,165);
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
}
.pending-badge {
  background: #FDF3E3;
  color: #854F0B;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
}
.store-desc {
  color: #5C5B54;
  font-size: 15px;
  margin-bottom: 16px;
  line-height: 1.5;
  max-width: 600px;
}
.store-meta {
  display: flex;
  gap: 24px;
  font-size: 13px;
  flex-wrap: wrap;
}
.meta-item {
  color: #5C5B54;
  display: flex;
  align-items: center;
  gap: 6px;
}
.profile-actions {
  padding-top: 72px;
  flex-shrink: 0;
}
.btn-edit-profile {
  background: #fff;
  border: 1.5px solid #CFCEC7;
  color: #3E3D38;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
  font-family: inherit;
  transition: all 0.2s;
}
.btn-edit-profile:hover {
  border-color: rgb(24,95,165);
  color: rgb(24,95,165);
  background: #E8F1FB;
}

/* Body */
.store-body {
  padding: 28px 40px 40px;
}
.store-body-grid {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 32px;
}
.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #282724;
  margin-bottom: 20px;
}
.empty-products {
  color: #8A8980;
  font-size: 14px;
  padding: 24px;
  text-align: center;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #E8E7E2;
}

/* Product cards */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}
.product-card-store {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #E8E7E2;
  overflow: hidden;
  cursor: default;
  transition: all .22s;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}
.product-card-store:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.10);
}
.product-thumb {
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 52px;
  position: relative;
}
.product-thumb-img {
  height: 140px;
  background-size: cover;
  background-position: center;
  position: relative;
}
.thumb-bg-1 { background: linear-gradient(135deg, #FFF3D6, #FFE8A3); }
.thumb-bg-2 { background: linear-gradient(135deg, #E6F0FF, #C5D8FF); }
.thumb-bg-3 { background: linear-gradient(135deg, #F0FFF4, #C6F6D5); }
.thumb-bg-4 { background: linear-gradient(135deg, #FFF0F0, #FFD6D6); }
.thumb-bg-5 { background: linear-gradient(135deg, #F3F0FF, #DDD6FF); }

.product-info {
  padding: 14px 16px 16px;
}
.product-name {
  font-size: 15px;
  font-weight: 600;
  color: #282724;
  margin-bottom: 8px;
  line-height: 1.3;
}
.product-price {
  font-size: 16px;
  font-weight: 700;
  color: rgb(24,95,165);
  margin-bottom: 4px;
}
.product-category {
  font-size: 12px;
  color: #8A8980;
}

/* Info cards */
.info-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.info-card {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #E8E7E2;
  padding: 20px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}
.info-card-title {
  font-size: 15px;
  font-weight: 700;
  color: #282724;
  margin-bottom: 16px;
}
.info-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.info-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
}
.info-label { color: #8A8980; }
.info-value {
  font-weight: 600;
  color: #3E3D38;
  text-align: right;
  max-width: 60%;
}

/* Contact buttons */
.contact-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.contact-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  text-decoration: none;
  transition: all .15s;
  font-family: inherit;
}
.contact-btn.whatsapp {
  background: #fff;
  color: #25D366;
  border: 1.5px solid #D4E8D5;
}
.contact-btn.whatsapp:hover {
  background: #F0FBF0;
}
.contact-btn.instagram {
  background: #F4F3F0;
  color: #3E3D38;
  border: 1.5px solid #E8E7E2;
}
.contact-btn.instagram:hover {
  background: #E8E7E2;
}
.contact-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Image upload */
.image-upload-btn {
  display: inline-flex;
  padding: 8px 16px;
  background: #F4F3F0;
  border: 1.5px solid #E8E7E2;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #3E3D38;
  cursor: pointer;
  transition: all .15s;
  font-family: inherit;
}
.image-upload-btn:hover {
  background: #E8E7E2;
}
.image-upload-btn input {
  display: none;
}
.logo-upload-row {
  display: flex;
  align-items: center;
  gap: 12px;
}
.logo-preview-sm {
  width: 56px; height: 56px;
  border-radius: 8px;
  overflow: hidden;
  position: relative;
  flex-shrink: 0;
  border: 1.5px solid #E8E7E2;
}
.logo-preview-sm img {
  width: 100%; height: 100%;
  object-fit: cover;
}
.image-preview-sm {
  width: 80px; height: 80px;
  border-radius: 8px;
  overflow: hidden;
  position: relative;
  flex-shrink: 0;
  border: 1.5px solid #E8E7E2;
}
.image-preview-sm img {
  width: 100%; height: 100%;
  object-fit: cover;
}
.image-remove {
  position: absolute;
  top: -6px; right: -6px;
  width: 20px; height: 20px;
  border-radius: 50%;
  background: #E24B4A;
  color: #fff;
  border: none;
  font-size: 14px;
  line-height: 1;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
.image-upload-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* Modal */
.modal-overlay {
  position: fixed; inset: 0;
  z-index: 9999;
  background: rgba(0,0,0,0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}
.modal-content {
  background: #fff;
  border-radius: 16px;
  max-width: 560px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 28px 0;
}
.modal-title {
  font-size: 20px;
  font-weight: 700;
  color: #282724;
}
.modal-close {
  background: none; border: none;
  font-size: 28px; color: #8A8980;
  cursor: pointer; padding: 0 4px;
  line-height: 1;
}
.modal-close:hover { color: #282724; }
.modal-form {
  padding: 20px 28px 28px;
}
.field { margin-bottom: 16px; }
.field-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #5C5B54;
  margin-bottom: 7px;
}
.form-input {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid #E8E7E2;
  border-radius: 8px;
  font-family: 'Plus Jakarta Sans', 'Segoe UI', Arial, sans-serif;
  font-size: 14px;
  color: #282724;
  background: #FAFAF9;
  outline: none;
  transition: all .18s;
}
.form-input:focus {
  border-color: rgb(24,95,165);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(24,95,165,0.1);
}
select.form-input { cursor: pointer; }
textarea.form-input { resize: vertical; min-height: 60px; }
.field-error {
  font-size: 12px;
  color: #E24B4A;
  margin-top: 4px;
  display: block;
}
.error-msg {
  background: #FCEBEB;
  color: #791F1F;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 12px;
}
.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.btn-submit {
  width: 100%;
  padding: 12px;
  background: rgb(24,95,165);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-family: inherit;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all .18s;
  box-shadow: 0 2px 8px rgba(24,95,165,0.25);
}
.btn-submit:hover { background: #175F9E; }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

/* Toast */
.toast {
  position: fixed; bottom: 24px; left: 50%;
  transform: translateX(-50%) translateY(80px);
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 14px; font-weight: 600;
  color: #fff;
  background: #282724;
  opacity: 0;
  transition: all .3s ease;
  pointer-events: none;
  z-index: 99999;
}
.toast.show {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
}
.toast.success { background: #1B7F5C; }
.toast.error { background: #E24B4A; }

@media (max-width: 768px) {
  .profile-header { padding: 0 16px 24px; }
  .profile-header-inner { flex-direction: column; align-items: center; text-align: center; margin-top: -48px; }
  .store-logo-wrap { width: 96px; height: 96px; }
  .store-logo { font-size: 32px; }
  .store-info { padding-top: 16px; }
  .store-name-row { justify-content: center; flex-wrap: wrap; }
  .store-meta { justify-content: center; }
  .profile-actions { padding-top: 12px; }
  .store-body { padding: 20px 16px; }
  .store-body-grid { grid-template-columns: 1fr; }
  .profile-actions { width: 100%; }
  .btn-edit-profile { width: 100%; }
}
</style>
