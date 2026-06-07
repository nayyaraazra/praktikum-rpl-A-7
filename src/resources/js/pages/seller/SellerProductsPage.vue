<template>
  <div class="app-layout">
    <SellerSidebar />
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1 class="page-title">Kelola Produk</h1>
          <p class="page-sub">{{ products.length }} produk terdaftar</p>
        </div>
        <button class="btn-primary" @click="openAddModal">+ Tambah Produk</button>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Memuat produk…</p>
      </div>

      <template v-else-if="products.length === 0">
        <div class="empty-state">
          <div class="empty-icon">📦</div>
          <p class="empty-title">Belum ada produk</p>
          <p class="empty-sub">Tambahkan produk pertama Anda untuk mulai berjualan.</p>
          <button class="btn-primary" style="margin-top:16px;width:auto;padding:10px 24px;" @click="openAddModal">+ Tambah Produk</button>
        </div>
      </template>

      <template v-else>
        <div class="products-table">
          <div class="table-header">
            <div class="table-header-cell">Produk</div>
            <div class="table-header-cell">Kategori</div>
            <div class="table-header-cell">Harga</div>
            <div class="table-header-cell">Stok</div>
            <div class="table-header-cell">Min. Pesan</div>
            <div class="table-header-cell">Status</div>
            <div class="table-header-cell">Aksi</div>
          </div>
          <div v-for="product in products" :key="product.id_product" class="table-row">
            <div class="table-cell">
              <div class="table-cell-main">{{ product.name }}</div>
              <div class="table-cell-sub" v-if="product.description">{{ product.description.slice(0, 60) }}</div>
            </div>
            <div class="table-cell">{{ product.category?.name_category || '—' }}</div>
            <div class="table-cell">{{ formatRupiah(product.price) }}</div>
            <div class="table-cell">
              <span :class="['stock-badge', product.stock < 5 ? 'low' : 'ok']">
                {{ product.stock }} {{ product.unit }}
              </span>
            </div>
            <div class="table-cell">{{ product.min_order }} {{ product.unit }}</div>
            <div class="table-cell">
              <span :class="['status-badge', product.is_active ? 'active' : 'inactive']">
                {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </div>
            <div class="table-cell">
              <div class="action-btns">
                <button class="icon-btn edit" title="Edit" @click="openEditModal(product)">✏️</button>
                <button class="icon-btn delete" title="Hapus" @click="confirmDelete(product)">🗑️</button>
              </div>
            </div>
          </div>
        </div>
      </template>

      <!-- Modal Form -->
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">{{ editingProduct ? 'Edit Produk' : 'Tambah Produk' }}</h2>
            <button class="modal-close" @click="closeModal">✕</button>
          </div>
          <form @submit.prevent="handleSave" class="modal-form">
            <div class="field">
              <label class="field-label" for="prodName">Nama Produk <span class="req">*</span></label>
              <input v-model="form.name" id="prodName" class="form-input" placeholder="Nama produk" />
              <span v-if="errors.name" class="field-error-show">{{ errors.name }}</span>
            </div>

            <div class="field">
              <label class="field-label" for="prodCategory">Kategori <span class="req">*</span></label>
              <input v-model="form.category_name" id="prodCategory" class="form-input" placeholder="mis. Makanan & Minuman" />
              <span v-if="errors.category_name" class="field-error-show">{{ errors.category_name }}</span>
            </div>

            <div class="field-row">
              <div class="field" style="flex:1">
                <label class="field-label" for="prodPrice">Harga <span class="req">*</span></label>
                <input v-model.number="form.price" id="prodPrice" type="number" min="0" class="form-input" placeholder="0" />
                <span v-if="errors.price" class="field-error-show">{{ errors.price }}</span>
              </div>
              <div class="field" style="flex:1">
                <label class="field-label" for="prodStock">Stok <span class="req">*</span></label>
                <input v-model.number="form.stock" id="prodStock" type="number" min="0" class="form-input" placeholder="0" />
                <span v-if="errors.stock" class="field-error-show">{{ errors.stock }}</span>
              </div>
            </div>

            <div class="field-row">
              <div class="field" style="flex:1">
                <label class="field-label" for="prodUnit">Satuan <span class="req">*</span></label>
                <select v-model="form.unit" id="prodUnit" class="form-input form-select">
                  <option value="" disabled>Pilih satuan</option>
                  <option value="pcs">pcs</option>
                  <option value="kg">kg</option>
                  <option value="gram">gram</option>
                  <option value="liter">liter</option>
                  <option value="ml">ml</option>
                  <option value="pack">pack</option>
                  <option value="lusin">lusin</option>
                  <option value="ikat">ikat</option>
                  <option value="buah">buah</option>
                </select>
                <span v-if="errors.unit" class="field-error-show">{{ errors.unit }}</span>
              </div>
              <div class="field" style="flex:1">
                <label class="field-label" for="prodMinOrder">Min. Pesan <span class="req">*</span></label>
                <input v-model.number="form.min_order" id="prodMinOrder" type="number" min="1" class="form-input" placeholder="1" />
                <span v-if="errors.min_order" class="field-error-show">{{ errors.min_order }}</span>
              </div>
            </div>

            <div class="field">
              <label class="field-label" for="prodDesc">Deskripsi</label>
              <textarea v-model="form.description" id="prodDesc" class="form-input form-textarea" rows="3" placeholder="Deskripsi produk (opsional)"></textarea>
              <span v-if="errors.description" class="field-error-show">{{ errors.description }}</span>
            </div>

            <div class="toggle-row">
              <span class="toggle-label">Produk Aktif</span>
              <button type="button" role="switch" :aria-checked="form.is_active" :class="['toggle-switch', form.is_active && 'active']" @click="form.is_active = !form.is_active">
                <span class="toggle-knob"></span>
              </button>
            </div>

            <div v-if="globalError" class="error-box">{{ globalError }}</div>

            <div class="modal-actions">
              <button type="button" class="btn-secondary" @click="closeModal">Batal</button>
              <button type="submit" class="btn-primary" :disabled="saving" style="flex:1">
                <span v-if="saving">Menyimpan…</span>
                <span v-else>{{ editingProduct ? 'Simpan Perubahan' : 'Tambah Produk' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Konfirmasi Hapus -->
      <div v-if="showDeleteConfirm" class="modal-overlay" @click.self="showDeleteConfirm = false">
        <div class="modal-content" style="max-width:400px;">
          <div class="modal-header">
            <h2 class="modal-title">Hapus Produk</h2>
            <button class="modal-close" @click="showDeleteConfirm = false">✕</button>
          </div>
          <p style="margin:16px 0;font-size:14px;color:#3E3D38;">
            Yakin ingin menghapus <strong>{{ deletingProduct?.name }}</strong>? Tindakan ini tidak dapat dibatalkan.
          </p>
          <div class="modal-actions">
            <button class="btn-secondary" @click="showDeleteConfirm = false">Batal</button>
            <button class="btn-danger" @click="handleDelete">Hapus</button>
          </div>
        </div>
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import SellerSidebar from '@/components/seller/SellerSidebar.vue'
import { sellerApi } from '@/services/api/sellerApi'

const products        = ref([])
const loading         = ref(true)
const saving          = ref(false)
const showModal       = ref(false)
const editingProduct  = ref(null)
const showDeleteConfirm = ref(false)
const deletingProduct = ref(null)
const errors          = reactive({})
const globalError     = ref('')

const emptyForm = () => ({
  name: '',
  category_name: '',
  price: 0,
  stock: 0,
  unit: '',
  min_order: 1,
  description: '',
  is_active: true,
})

const form = reactive(emptyForm())

onMounted(fetchProducts)

async function fetchProducts() {
  loading.value = true
  try {
    const { data } = await sellerApi.getProducts()
    if (data.success) products.value = data.data
  } catch {
    products.value = []
  } finally {
    loading.value = false
  }
}

function openAddModal() {
  editingProduct.value = null
  Object.assign(form, emptyForm())
  clearErrors()
  showModal.value = true
}

function openEditModal(product) {
  editingProduct.value = product
  form.name          = product.name
  form.category_name = product.category?.name_category || ''
  form.price         = product.price
  form.stock         = product.stock
  form.unit          = product.unit
  form.min_order     = product.min_order
  form.description   = product.description || ''
  form.is_active     = !!product.is_active
  clearErrors()
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingProduct.value = null
}

function confirmDelete(product) {
  deletingProduct.value = product
  showDeleteConfirm.value = true
}

function clearErrors() {
  Object.keys(errors).forEach(k => delete errors[k])
  globalError.value = ''
}

function validate() {
  clearErrors()
  let valid = true
  if (!form.name.trim())              { errors.name = 'Nama produk wajib diisi.'; valid = false }
  if (!form.category_name.trim())     { errors.category_name = 'Kategori wajib diisi.'; valid = false }
  if (!form.price || form.price < 0)  { errors.price = 'Harga harus diisi (min 0).'; valid = false }
  if (form.stock === '' || form.stock < 0) { errors.stock = 'Stok harus diisi (min 0).'; valid = false }
  if (!form.unit)                     { errors.unit = 'Pilih satuan.'; valid = false }
  if (!form.min_order || form.min_order < 1) { errors.min_order = 'Min. pesan minimal 1.'; valid = false }
  return valid
}

async function handleSave() {
  if (!validate()) return
  saving.value = true
  try {
    if (editingProduct.value) {
      await sellerApi.updateProduct(editingProduct.value.id_product, form)
    } else {
      await sellerApi.createProduct(form)
    }
    closeModal()
    await fetchProducts()
  } catch (err) {
    const res = err.response
    if (res?.status === 422) {
      const fieldErrors = res.data.errors ?? {}
      Object.keys(fieldErrors).forEach(f => { errors[f] = fieldErrors[f][0] })
    } else {
      globalError.value = 'Gagal menyimpan produk. Coba lagi.'
    }
  } finally {
    saving.value = false
  }
}

async function handleDelete() {
  if (!deletingProduct.value) return
  try {
    await sellerApi.deleteProduct(deletingProduct.value.id_product)
    showDeleteConfirm.value = false
    deletingProduct.value = null
    await fetchProducts()
  } catch {
    globalError.value = 'Gagal menghapus produk.'
  }
}

function formatRupiah(value) {
  const num = parseFloat(value) || 0
  if (num >= 1_000_000) return `Rp ${(num / 1_000_000).toFixed(1)}jt`
  if (num >= 1_000)     return `Rp ${(num / 1_000).toFixed(0)}K`
  return `Rp ${num.toFixed(0)}`
}
</script>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  background: #F4F3F0;
}
.main-content {
  flex: 1;
  min-width: 0;
  padding: 32px;
}
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}
.page-title   { font-size: 22px; font-weight: 700; color: #161514; letter-spacing: -.5px; }
.page-sub     { font-size: 14px; color: #8A8980; margin-top: 4px; }

.loading-state {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; gap: 16px;
  padding: 80px 0; color: #8A8980;
}
.spinner {
  width: 32px; height: 32px;
  border: 3px solid #E8E7E2;
  border-top-color: #175F9E;
  border-radius: 50%;
  animation: spin .8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.btn-primary {
  padding: 10px 20px;
  background: #175F9E;
  color: #fff; border: none;
  border-radius: 8px;
  font-family: inherit; font-size: 14px; font-weight: 700;
  cursor: pointer; white-space: nowrap;
  transition: background .18s;
}
.btn-primary:hover { background: #124880; }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }

.btn-secondary {
  padding: 10px 20px;
  background: #F4F3F0;
  color: #3E3D38; border: 1px solid #E8E7E2;
  border-radius: 8px;
  font-family: inherit; font-size: 14px; font-weight: 600;
  cursor: pointer;
}
.btn-secondary:hover { background: #E8E7E2; }

.btn-danger {
  padding: 10px 20px;
  background: #E24B4A;
  color: #fff; border: none;
  border-radius: 8px;
  font-family: inherit; font-size: 14px; font-weight: 700;
  cursor: pointer;
}
.btn-danger:hover { background: #C73D3C; }

.products-table {
  background: #fff;
  border-radius: 16px; border: 1px solid #E8E7E2;
  overflow: hidden;
  box-shadow: 0 1px 2px rgba(0,0,0,.06);
}
.table-header {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 80px;
  padding: 12px 20px;
  background: #F4F3F0;
  border-bottom: 1px solid #E8E7E2;
}
.table-header-cell {
  font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .5px; color: #8A8980;
}
.table-row {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 80px;
  padding: 14px 20px; align-items: center;
  border-bottom: 1px solid #F4F3F0;
  transition: background .15s;
}
.table-row:hover  { background: #F4F3F0; }
.table-row:last-child { border-bottom: none; }
.table-cell      { font-size: 14px; color: #3E3D38; }
.table-cell-main { font-weight: 600; color: #161514; }
.table-cell-sub  { font-size: 11px; color: #8A8980; margin-top: 2px; }

.stock-badge {
  padding: 3px 8px; border-radius: 9999px;
  font-size: 12px; font-weight: 600;
}
.stock-badge.ok  { background: #EAF3DE; color: #3B6D11; }
.stock-badge.low { background: #FDF3E3; color: #854F0B; }

.status-badge {
  padding: 3px 8px; border-radius: 9999px;
  font-size: 12px; font-weight: 600;
}
.status-badge.active   { background: #EAF3DE; color: #3B6D11; }
.status-badge.inactive { background: #FCEBEB; color: #791F1F; }

.action-btns { display: flex; gap: 4px; }
.icon-btn {
  width: 32px; height: 32px;
  border: none; border-radius: 6px;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  font-size: 15px; background: transparent; transition: background .15s;
}
.icon-btn:hover    { background: #E8E7E2; }
.icon-btn.delete:hover { background: #FCEBEB; }

.empty-state {
  text-align: center; padding: 60px 20px;
  background: #fff; border-radius: 16px;
  border: 1px solid #E8E7E2;
}
.empty-icon  { font-size: 40px; margin-bottom: 12px; }
.empty-title { font-size: 16px; font-weight: 600; color: #3E3D38; }
.empty-sub   { font-size: 13px; color: #8A8980; margin-top: 4px; }

/* Modal */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.4);
  display: flex; align-items: center; justify-content: center;
  z-index: 1000; padding: 20px;
}
.modal-content {
  background: #fff;
  border-radius: 16px;
  width: 100%; max-width: 520px;
  max-height: 90vh; overflow-y: auto;
  box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}
.modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 24px 0;
}
.modal-title { font-size: 18px; font-weight: 700; color: #161514; }
.modal-close {
  width: 32px; height: 32px; border: none; border-radius: 8px;
  background: #F4F3F0; cursor: pointer;
  font-size: 14px; color: #8A8980;
  display: flex; align-items: center; justify-content: center;
}
.modal-close:hover { background: #E8E7E2; color: #3E3D38; }
.modal-form  { padding: 20px 24px 24px; }
.modal-actions {
  display: flex; gap: 10px; margin-top: 20px;
}

.field { margin-bottom: 14px; }
.field-row { display: flex; gap: 12px; }
.field-label {
  display: block; font-size: 13px; font-weight: 600;
  color: #5F6A7D; margin-bottom: 6px;
}
.req { color: #E24B4A; margin-left: 1px; }
.form-input {
  width: 100%; padding: 10px 12px;
  border: 1.5px solid #DDE1E9; border-radius: 8px;
  font-family: inherit; font-size: 14px;
  color: #2D3547; background: #F8F9FB;
  outline: none; box-sizing: border-box;
  transition: border-color .18s, box-shadow .18s;
}
.form-input:focus {
  border-color: #378ADD; background: #fff;
  box-shadow: 0 0 0 3px rgba(55,138,221,0.12);
}
.form-select { cursor: pointer; }
.form-textarea { resize: vertical; min-height: 72px; line-height: 1.5; }
.field-error-show {
  display: block; margin-top: 4px;
  font-size: 12px; color: #E24B4A;
}

.toggle-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 14px;
  background: #F8F9FB; border: 1.5px solid #EFF1F5;
  border-radius: 8px; margin-bottom: 14px;
}
.toggle-label { font-size: 13px; font-weight: 500; color: #5F6A7D; }
.toggle-switch {
  width: 44px; height: 24px; border-radius: 100px;
  background: #DDE1E9; border: none; cursor: pointer;
  position: relative; flex-shrink: 0;
  transition: background .2s;
}
.toggle-switch.active { background: #1E6FC5; }
.toggle-knob {
  position: absolute; top: 3px; left: 3px;
  width: 18px; height: 18px;
  background: #fff; border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
  transition: transform .2s;
}
.toggle-switch.active .toggle-knob { transform: translateX(20px); }

.error-box {
  padding: 10px 14px;
  background: #FFF0F0; border: 1px solid #FECACA;
  border-radius: 8px; font-size: 13px; color: #E24B4A;
  margin-bottom: 12px;
}
</style>
