<template>
  <div class="page-wrap">

    <!-- ── Header ── -->
    <header class="detail-header">
      <button class="btn-back" @click="$router.back()" aria-label="Kembali">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="header-title">Info Produk</span>
      <button class="btn-share" aria-label="Bagikan">
        <svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
      </button>
    </header>

    <div v-if="product">

      <!-- ── Foto Produk (fullwidth) ── -->
      <div class="product-gallery">
        <div class="gallery-main">
          <div class="gallery-placeholder">
            <span class="gallery-emoji">{{ getCategoryEmoji(product.category) }}</span>
            <p class="gallery-label">Foto Gerai/Produk UMKM<br/>(fullwidth)</p>
          </div>
        </div>
      </div>

      <!-- ── Info Produk ── -->
      <div class="info-card">
        <!-- Thumbnails row (placeholder) -->
        <div class="thumb-row">
          <div v-for="i in 3" :key="i" class="thumb-item">
            <span>{{ getCategoryEmoji(product.category) }}</span>
          </div>
        </div>

        <!-- Nama & toko -->
        <div class="info-top">
          <div>
            <p class="product-store-row">
              <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              {{ product.store_name }}
              <span class="district-badge">{{ product.store_district }}</span>
            </p>
            <h1 class="product-title">{{ product.name }}</h1>
          </div>
          <div class="price-block">
            <span class="price-label">Harga</span>
            <span class="price-val">Rp {{ formatPrice(product.price) }}</span>
            <span class="price-unit">/ {{ product.unit }}</span>
          </div>
        </div>

        <!-- Rating -->
        <div class="rating-row">
          <div class="stars">
            <span v-for="i in 5" :key="i" :class="['star', i <= Math.round(product.rating) && 'star--on']">★</span>
          </div>
          <span class="rating-score">{{ product.rating }}</span>
          <span class="rating-count">({{ product.review_count }} ulasan)</span>
        </div>

        <!-- Stok -->
        <div class="stock-row">
          <span class="stock-label">Stok Tersedia:</span>
          <div class="stock-bar-wrap">
            <div class="stock-bar">
              <div class="stock-fill" :style="{ width: stockPercent + '%' }"></div>
            </div>
            <span class="stock-val">{{ product.stock }} {{ product.unit }}</span>
          </div>
        </div>

        <!-- Min order -->
        <div v-if="product.min_order > 1" class="min-order-badge">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Min. pemesanan: {{ product.min_order }} {{ product.unit }}
        </div>
      </div>

      <!-- ── Deskripsi ── -->
      <div class="section-card">
        <h2 class="section-title">Deskripsi</h2>
        <p class="desc-text" :class="!showFullDesc && 'desc-clamp'">{{ product.description }}</p>
        <button v-if="!showFullDesc" class="btn-readmore" @click="showFullDesc = true">
          Baca selengkapnya ↓
        </button>
      </div>

      <!-- ── Info Toko ── -->
      <div class="section-card">
        <h2 class="section-title">Tentang Toko</h2>
        <div class="store-info-row">
          <div class="store-avatar">
            <span>{{ product.store_name[0] }}</span>
          </div>
          <div>
            <p class="store-info-name">{{ product.store_name }}</p>
            <p class="store-info-sub">📍 {{ product.store_district }}, Jebres</p>
          </div>
          <div class="store-rating-badge">
            ★ {{ product.store_rating }}
          </div>
        </div>
      </div>

      <!-- ── Ulasan ── -->
      <div class="section-card">
        <div class="reviews-header">
          <h2 class="section-title">Ulasan Pembeli</h2>
          <span class="review-total">{{ reviews.length }} ulasan</span>
        </div>
        <div v-for="review in reviews" :key="review.id" class="review-item">
          <div class="review-top">
            <div class="review-avatar">{{ review.user_name[0] }}</div>
            <div>
              <p class="review-name">{{ review.user_name }}</p>
              <div class="review-stars">
                <span v-for="i in 5" :key="i" :class="['rstar', i <= review.rating && 'rstar--on']">★</span>
              </div>
            </div>
            <span class="review-date">{{ formatDate(review.date) }}</span>
          </div>
          <p class="review-comment">{{ review.comment }}</p>
        </div>
        <div v-if="reviews.length === 0" class="no-reviews">
          <span>Belum ada ulasan untuk produk ini.</span>
        </div>
      </div>

      <!-- Spacer for bottom CTA -->
      <div style="height: 88px;"></div>
    </div>

    <!-- ── Bottom CTA ── -->
    <div v-if="product" class="bottom-cta">
      <div class="cta-left">
        <span class="cta-seller-label">Hubungi Penjual</span>
        <a
          :href="`https://wa.me/${product.seller_phone}?text=Halo, saya tertarik dengan produk ${product.name} di Kulaan.id`"
          target="_blank"
          rel="noopener"
          class="btn-wa"
        >
          <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
          WA Penjual
        </a>
      </div>
      <button class="btn-order" @click="goToOrder">
        Pesan Sekarang
      </button>
    </div>

    <!-- Not found -->
    <div v-else class="not-found">
      <p>Produk tidak ditemukan.</p>
      <button @click="$router.back()">Kembali</button>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { mockProducts, mockReviews } from '@/data/mockData'

const router = useRouter()
const route  = useRoute()

const productId = Number(route.params.id)
const product   = mockProducts.find(p => p.id === productId) || null
const reviews   = mockReviews.filter(r => r.product_id === productId)

const showFullDesc = ref(false)

const stockPercent = computed(() => {
  if (!product) return 0
  // Anggap max stok 100 untuk visual
  return Math.min(100, (product.stock / 100) * 100)
})

function formatPrice(n) {
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}
function formatDate(d) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
function getCategoryEmoji(cat) {
  const map = { makanan_minuman:'🍱', fashion_batik:'👗', kerajinan:'🧶', kecantikan:'💄', pertanian:'🌾' }
  return map[cat] || '📦'
}
function goToOrder() {
  if (product) router.push({ name: 'buyer.order', params: { productId: product.id } })
}
</script>

<style scoped>
.page-wrap {
  min-height: 100vh;
  background: #f0f4f8;
  max-width: 480px;
  margin: 0 auto;
  display: flex; flex-direction: column;
}

/* Header */
.detail-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 16px;
  background: linear-gradient(135deg, #1a7fc4 0%, #1565a8 100%);
  position: sticky; top: 0; z-index: 100;
}
.header-title { font-size: 16px; font-weight: 700; color: #fff; }
.btn-back, .btn-share {
  width: 36px; height: 36px;
  background: rgba(255,255,255,0.15);
  border: none; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #fff;
}
.btn-back svg, .btn-share svg {
  width: 20px; height: 20px;
  fill: none; stroke: currentColor;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}

/* Gallery */
.product-gallery { background: #fff; }
.gallery-main {
  aspect-ratio: 1;
  background: #f1f5f9;
  display: flex; align-items: center; justify-content: center;
  flex-direction: column; gap: 8px;
}
.gallery-emoji { font-size: 80px; }
.gallery-label {
  font-size: 11px; color: #9ca3af;
  text-align: center; line-height: 1.5;
}

/* Info card */
.info-card {
  background: #fff;
  padding: 16px;
  margin-bottom: 8px;
}
.thumb-row { display: flex; gap: 8px; margin-bottom: 14px; }
.thumb-item {
  width: 52px; height: 52px;
  background: #f1f5f9; border-radius: 8px;
  border: 2px solid #e5e7eb;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; cursor: pointer;
}
.thumb-item:first-child { border-color: #1565a8; }

.info-top {
  display: flex; justify-content: space-between; align-items: flex-start;
  gap: 12px; margin-bottom: 10px;
}
.product-store-row {
  display: flex; align-items: center; gap: 4px;
  font-size: 12px; color: #6b7280; margin-bottom: 4px;
}
.product-store-row svg {
  width: 12px; height: 12px;
  fill: none; stroke: currentColor;
  stroke-width: 2; stroke-linecap: round;
}
.district-badge {
  background: #dbeafe; color: #1565a8;
  font-size: 10px; font-weight: 600;
  padding: 1px 7px; border-radius: 100px;
}
.product-title {
  font-size: 18px; font-weight: 800; color: #111827;
  line-height: 1.3;
}
.price-block {
  display: flex; flex-direction: column; align-items: flex-end;
  flex-shrink: 0;
}
.price-label { font-size: 10px; color: #9ca3af; }
.price-val {
  font-size: 18px; font-weight: 800; color: #1565a8;
  white-space: nowrap;
}
.price-unit { font-size: 11px; color: #9ca3af; }

/* Rating */
.rating-row {
  display: flex; align-items: center; gap: 6px;
  margin-bottom: 12px;
}
.stars, .review-stars { display: flex; gap: 2px; }
.star { font-size: 16px; color: #d1d5db; }
.star--on { color: #f59e0b; }
.rating-score { font-size: 14px; font-weight: 700; color: #1f2937; }
.rating-count { font-size: 12px; color: #6b7280; }

/* Stock */
.stock-row {
  display: flex; align-items: center; gap: 10px;
  margin-bottom: 10px;
}
.stock-label { font-size: 12px; color: #374151; font-weight: 600; white-space: nowrap; }
.stock-bar-wrap { flex: 1; display: flex; align-items: center; gap: 8px; }
.stock-bar {
  flex: 1; height: 6px;
  background: #e5e7eb; border-radius: 100px; overflow: hidden;
}
.stock-fill {
  height: 100%;
  background: linear-gradient(90deg, #22c55e, #16a34a);
  border-radius: 100px;
  transition: width 0.4s;
}
.stock-val { font-size: 12px; color: #6b7280; white-space: nowrap; }

/* Min order */
.min-order-badge {
  display: flex; align-items: center; gap: 6px;
  padding: 8px 12px;
  background: #fef3c7; border-radius: 8px;
  font-size: 12px; color: #92400e; font-weight: 500;
}
.min-order-badge svg {
  width: 14px; height: 14px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round; flex-shrink: 0;
}

/* Section cards */
.section-card {
  background: #fff; padding: 16px;
  margin-bottom: 8px;
}
.section-title {
  font-size: 14px; font-weight: 700; color: #111827;
  margin-bottom: 10px;
  text-transform: uppercase; letter-spacing: 0.4px;
}
.desc-text {
  font-size: 13px; color: #4b5563; line-height: 1.7;
}
.desc-clamp {
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.btn-readmore {
  background: none; border: none;
  font-size: 13px; font-weight: 600;
  color: #1565a8; cursor: pointer; font-family: inherit;
  margin-top: 4px; padding: 0;
}

/* Store info */
.store-info-row {
  display: flex; align-items: center; gap: 12px;
}
.store-avatar {
  width: 44px; height: 44px;
  background: linear-gradient(135deg, #1a7fc4, #1565a8);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; font-weight: 800; color: #fff;
  flex-shrink: 0;
}
.store-info-name { font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 2px; }
.store-info-sub { font-size: 12px; color: #6b7280; }
.store-rating-badge {
  margin-left: auto;
  background: #fef3c7; color: #92400e;
  font-size: 13px; font-weight: 700;
  padding: 4px 10px; border-radius: 100px;
}

/* Reviews */
.reviews-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.review-total { font-size: 12px; color: #6b7280; }
.review-item { padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
.review-item:last-child { border-bottom: none; }
.review-top { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
.review-avatar {
  width: 30px; height: 30px; border-radius: 50%;
  background: #dbeafe; color: #1565a8;
  font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.review-name { font-size: 12px; font-weight: 600; color: #1f2937; margin-bottom: 2px; }
.rstar { font-size: 12px; color: #d1d5db; }
.rstar--on { color: #f59e0b; }
.review-date { margin-left: auto; font-size: 11px; color: #9ca3af; }
.review-comment { font-size: 13px; color: #4b5563; line-height: 1.5; }
.no-reviews { font-size: 13px; color: #9ca3af; text-align: center; padding: 16px 0; }

/* Bottom CTA */
.bottom-cta {
  position: fixed; bottom: 0; left: 50%;
  transform: translateX(-50%);
  width: 100%; max-width: 480px;
  background: #fff;
  border-top: 1px solid #e5e7eb;
  padding: 12px 16px;
  display: flex; align-items: center; gap: 10px;
  z-index: 200;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.06);
}
.cta-left { display: flex; flex-direction: column; gap: 4px; }
.cta-seller-label { font-size: 10px; color: #6b7280; }
.btn-wa {
  display: flex; align-items: center; gap: 5px;
  padding: 9px 14px;
  background: #25d366; color: #fff;
  border-radius: 10px; text-decoration: none;
  font-size: 13px; font-weight: 700;
  transition: background 0.15s;
}
.btn-wa:hover { background: #16a34a; }
.btn-wa svg {
  width: 16px; height: 16px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round;
}
.btn-order {
  flex: 1; padding: 12px;
  background: linear-gradient(135deg, #1a7fc4, #1565a8);
  color: #fff; border: none; border-radius: 12px;
  font-size: 15px; font-weight: 700; font-family: inherit;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(21,101,168,0.3);
  transition: all 0.15s;
}
.btn-order:hover { box-shadow: 0 4px 12px rgba(21,101,168,0.4); }

.not-found {
  display: flex; flex-direction: column; align-items: center;
  padding: 48px 24px; gap: 12px;
  font-size: 14px; color: #6b7280;
}
.not-found button {
  padding: 10px 24px; background: #1565a8; color: #fff;
  border: none; border-radius: 10px; cursor: pointer;
  font-family: inherit;
}
</style>