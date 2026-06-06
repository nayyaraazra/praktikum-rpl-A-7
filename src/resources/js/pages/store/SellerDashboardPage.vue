<template>
  <div class="min-h-screen flex bg-slate-50 font-sans">
    <!-- ══════════════════════════════════════════════════
         1. SIDEBAR NAVIGATION (LEFT)
    ══════════════════════════════════════════════════ -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col flex-shrink-0 shadow-xl">
      <!-- Sidebar Header -->
      <div class="p-6 border-b border-slate-800">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center font-bold text-lg text-white">K</div>
          <div>
            <h1 class="font-extrabold text-base tracking-tight text-white">KULAAN.id</h1>
            <span class="text-[10px] text-blue-400 font-semibold tracking-wider uppercase">Seller Hub</span>
          </div>
        </div>
      </div>

      <!-- Seller Profile Box -->
      <div class="p-4 mx-4 my-4 bg-slate-800/50 rounded-xl border border-slate-700/50 flex flex-col gap-2">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-sm font-bold text-slate-200">
            {{ userInitial }}
          </div>
          <div class="overflow-hidden">
            <h2 class="text-xs font-semibold text-slate-200 truncate">{{ authStore.user?.name }}</h2>
            <p class="text-[10px] text-slate-400 truncate">{{ authStore.user?.email }}</p>
          </div>
        </div>
        <div class="border-t border-slate-700/60 pt-2 flex items-center">
          <span class="text-[10px] bg-blue-950 text-blue-300 font-semibold px-2 py-0.5 rounded-full border border-blue-800/40">
            {{ storeName || 'Toko Baru' }}
          </span>
        </div>
      </div>

      <!-- Nav Links -->
      <nav class="flex-1 px-4 space-y-1">
        <button
          v-for="item in navItems"
          :key="item.value"
          @click="activeTab = item.value"
          :class="[
            'w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-all duration-150',
            activeTab === item.value
              ? 'bg-blue-600 text-white shadow-md shadow-blue-950/40'
              : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200'
          ]"
        >
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="item.icon"></svg>
          {{ item.label }}
        </button>
      </nav>

      <!-- Bottom actions -->
      <div class="p-4 border-t border-slate-800 space-y-2">
        <RouterLink
          to="/home"
          class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-xs font-semibold text-slate-400 hover:bg-slate-800/40 hover:text-slate-200 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          Kembali ke Beranda
        </RouterLink>
        <button
          @click="handleLogout"
          class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-xs font-semibold text-red-400 hover:bg-red-950/30 hover:text-red-300 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Keluar Sesi
        </button>
      </div>
    </aside>

    <!-- ══════════════════════════════════════════════════
         2. MAIN CONTENT AREA (RIGHT)
    ══════════════════════════════════════════════════ -->
    <main class="flex-1 flex flex-col overflow-y-auto max-h-screen">
      <!-- Navbar / Top Header -->
      <header class="bg-white border-b border-slate-200 px-8 py-5 flex items-center justify-between sticky top-0 z-30">
        <div>
          <h2 class="text-xl font-bold text-slate-800 capitalize">{{ pageTitle }}</h2>
          <p class="text-xs text-slate-400">Selamat datang kembali di panel Kulaan.id</p>
        </div>
        <div class="flex items-center gap-4">
          <div class="text-right">
            <span class="text-xs font-semibold text-slate-500 block">Status Akun Toko</span>
            <span :class="verificationBadgeClass" class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border">
              {{ formatStoreStatus(storeStatus) }}
            </span>
          </div>
        </div>
      </header>

      <!-- Global Loading State -->
      <div v-if="loadingStore" class="flex-1 flex flex-col justify-center items-center py-20">
        <svg class="w-10 h-10 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="text-xs text-slate-500 mt-3 font-semibold">Mengambil data toko...</span>
      </div>

      <!-- Main Section -->
      <div v-else class="flex-1 p-8 space-y-6">
        <!-- Verification Banner Warning -->
        <div v-if="storeStatus !== 'disetujui'" class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3 items-start">
          <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 font-bold text-lg flex-shrink-0">⚠️</div>
          <div>
            <h3 class="text-sm font-bold text-amber-800">Toko Anda Sedang Menunggu Verifikasi Admin</h3>
            <p class="text-xs text-amber-700/90 leading-relaxed mt-0.5">
              Akun toko Anda saat ini belum disetujui oleh admin. Beberapa fitur seperti menambahkan produk baru atau memproses transaksi hanya akan berfungsi penuh setelah toko Anda disetujui. Harap tunggu 1x24 jam untuk peninjauan.
            </p>
          </div>
        </div>

        <!-- ───────────────────────────────────────────────
             TAB 1: DASHBOARD
        ──────────────────────────────────────────────── -->
        <section v-if="activeTab === 'dashboard'" class="space-y-6">
          <!-- Stats Summary Cards -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-6 flex items-center justify-between hover:shadow-md transition-all duration-150">
              <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pesanan</h3>
                <p class="text-3xl font-extrabold text-slate-800 mt-2">{{ dashboardStats.total_orders }}</p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-xl">📦</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-6 flex items-center justify-between hover:shadow-md transition-all duration-150">
              <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Menunggu</h3>
                <p class="text-3xl font-extrabold text-yellow-600 mt-2">{{ dashboardStats.waiting_orders }}</p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 border border-yellow-100 flex items-center justify-center text-xl">⏳</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-6 flex items-center justify-between hover:shadow-md transition-all duration-150">
              <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Diproses</h3>
                <p class="text-3xl font-extrabold text-blue-600 mt-2">{{ dashboardStats.processed_orders }}</p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-xl">⚙️</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-6 flex items-center justify-between hover:shadow-md transition-all duration-150">
              <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Selesai</h3>
                <p class="text-3xl font-extrabold text-green-600 mt-2">{{ dashboardStats.completed_orders }}</p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 border border-green-100 flex items-center justify-center text-xl">✅</div>
            </div>
          </div>

          <!-- Incoming Orders Card -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
              <div>
                <h3 class="font-bold text-slate-800 text-base">Pesanan Masuk</h3>
                <p class="text-xs text-slate-400 mt-0.5">Kelola pesanan pembeli yang masuk ke toko Anda</p>
              </div>
              <!-- Order Filter Badges -->
              <div class="flex gap-2 bg-slate-100 p-1 rounded-lg border border-slate-200">
                <button
                  v-for="f in orderFilters"
                  :key="f.value"
                  @click="activeOrderFilter = f.value"
                  :class="[
                    'px-3 py-1 rounded-md text-xs font-semibold transition-colors duration-150',
                    activeOrderFilter === f.value
                      ? 'bg-blue-600 text-white shadow-sm'
                      : 'text-slate-500 hover:text-slate-800'
                  ]"
                >
                  {{ f.label }}
                </button>
              </div>
            </div>

            <!-- Orders Table / List -->
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-slate-50/70 border-b border-slate-200 text-xs uppercase text-slate-400 font-bold tracking-wider">
                    <th class="py-4 px-6">No. Pesanan</th>
                    <th class="py-4 px-6">Pelanggan</th>
                    <th class="py-4 px-6">Detail Produk</th>
                    <th class="py-4 px-6">Metode</th>
                    <th class="py-4 px-6">Total Pembayaran</th>
                    <th class="py-4 px-6">Status</th>
                    <th class="py-4 px-6 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-if="filteredOrders.length === 0" class="text-center">
                    <td colspan="7" class="py-12 text-slate-400 text-sm">
                      <div class="text-3xl mb-2">📋</div>
                      Tidak ada pesanan masuk dalam kategori ini.
                    </td>
                  </tr>
                  <tr 
                    v-else 
                    v-for="order in filteredOrders" 
                    :key="order.id_order" 
                    class="hover:bg-slate-50/40 transition-colors"
                  >
                    <td class="py-4 px-6 text-sm font-semibold text-slate-700">
                      #{{ String(order.id_order).padStart(3, '0') }}
                    </td>
                    <td class="py-4 px-6 text-sm font-semibold text-slate-800">
                      {{ order.user?.name }}
                    </td>
                    <td class="py-4 px-6 text-slate-600 text-xs max-w-xs truncate" :title="getOrderProductsText(order)">
                      {{ getOrderProductsText(order) }}
                    </td>
                    <td class="py-4 px-6">
                      <span :class="order.payment_method === 'cod' ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : 'bg-teal-50 text-teal-700 border-teal-100'" class="px-2 py-0.5 rounded-md text-[10px] font-bold border uppercase">
                        {{ order.payment_method }}
                      </span>
                    </td>
                    <td class="py-4 px-6 text-sm font-bold text-slate-800">
                      Rp {{ formatNumber(order.total_order) }}
                    </td>
                    <td class="py-4 px-6">
                      <span :class="orderStatusClass(order.status)" class="px-2.5 py-1 rounded-full text-xs font-semibold border">
                        {{ formatStatusText(order.status) }}
                      </span>
                    </td>
                    <td class="py-4 px-6 text-right space-x-2">
                      <button 
                        v-if="order.status === 'menunggu'"
                        @click="updateStatus(order.id_order, 'diproses')"
                        class="px-2.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg text-xs font-bold transition-all duration-150 inline-flex items-center gap-1"
                      >
                        Proses
                      </button>
                      <button 
                        v-if="order.status === 'diproses'"
                        @click="updateStatus(order.id_order, 'selesai')"
                        class="px-2.5 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 rounded-lg text-xs font-bold transition-all duration-150 inline-flex items-center gap-1"
                      >
                        Selesaikan
                      </button>
                      <button 
                        @click="openOrderDetail(order)"
                        class="px-2.5 py-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg text-xs font-semibold transition-all"
                      >
                        Detail
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- ───────────────────────────────────────────────
             TAB 2: TOKO SAYA
        ──────────────────────────────────────────────── -->
        <section v-if="activeTab === 'store'">
          <!-- Product Catalog List (Full width) -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
              <div>
                <h3 class="font-bold text-slate-800 text-base">Katalog Produk</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar produk yang Anda jual di Kulaan.id</p>
              </div>
              <button
                @click="openAddProductModal"
                :disabled="storeStatus !== 'disetujui'"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold rounded-lg transition-all shadow-md shadow-blue-900/10 flex items-center gap-1.5"
              >
                <span>➕</span> Tambah Produk
              </button>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-slate-50/70 border-b border-slate-200 text-xs uppercase text-slate-400 font-bold tracking-wider">
                    <th class="py-4 px-6">Foto</th>
                    <th class="py-4 px-6">Nama Produk</th>
                    <th class="py-4 px-6">Kategori</th>
                    <th class="py-4 px-6">Harga</th>
                    <th class="py-4 px-6">Stok</th>
                    <th class="py-4 px-6">Satuan</th>
                    <th class="py-4 px-6">Min Order</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                  <tr v-if="products.length === 0" class="text-center">
                    <td colspan="7" class="py-12 text-slate-400">
                      <div class="text-3xl mb-2">🛍️</div>
                      Belum ada produk di toko Anda. Klik "Tambah Produk" untuk mulai menjual.
                    </td>
                  </tr>
                  <tr v-else v-for="prod in products" :key="prod.id_product" class="hover:bg-slate-50/40 transition-colors">
                    <td class="py-4 px-6">
                      <img 
                        v-if="prod.image_product" 
                        :src="prod.image_product" 
                        class="w-10 h-10 object-cover rounded-lg border border-slate-200 shadow-sm"
                        alt="Foto produk"
                      />
                      <div v-else class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-sm">
                        📦
                      </div>
                    </td>
                    <td class="py-4 px-6 font-semibold text-slate-800">
                      {{ prod.name }}
                    </td>
                    <td class="py-4 px-6 text-slate-500 text-xs">
                      {{ formatCategoryText(prod.category?.name_category) }}
                    </td>
                    <td class="py-4 px-6 font-bold text-slate-800">
                      Rp {{ formatNumber(prod.price) }}
                    </td>
                    <td class="py-4 px-6">
                      <span :class="prod.stock > 10 ? 'text-slate-700' : 'text-red-600 font-bold'">
                        {{ prod.stock }}
                      </span>
                    </td>
                    <td class="py-4 px-6 text-slate-500">
                      {{ prod.unit }}
                    </td>
                    <td class="py-4 px-6 text-slate-500">
                      {{ prod.min_order }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- ───────────────────────────────────────────────
             TAB 3: PENGATURAN
        ──────────────────────────────────────────────── -->
        <section v-if="activeTab === 'settings'" class="max-w-4xl mx-auto w-full">
          <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-6 space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
              <div>
                <h3 class="font-bold text-slate-800 text-base">Pengaturan Profil</h3>
                <p class="text-xs text-slate-400 mt-0.5">Kelola informasi akun seller dan profil toko Anda</p>
              </div>
              <button
                @click="openEditProfileModal"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-md shadow-blue-900/10 flex items-center gap-1.5"
              >
                <span>✏️</span> Ubah Profil
              </button>
            </div>

            <!-- Content Grid: Two sections (Account & Store details) side by side -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
              <!-- Left side: Account Info (1 column) -->
              <div class="space-y-4 border-b md:border-b-0 md:border-r border-slate-100 pb-6 md:pb-0 md:pr-8 text-sm">
                <h4 class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2 text-blue-600">I. Informasi Akun</h4>
                <div class="space-y-3">
                  <div>
                    <span class="block text-xs text-slate-400 font-semibold">Nama Lengkap</span>
                    <p class="font-bold text-slate-800 mt-0.5">{{ authStore.user?.name }}</p>
                  </div>
                  <div>
                    <span class="block text-xs text-slate-400 font-semibold">Alamat Email</span>
                    <p class="font-bold text-slate-800 mt-0.5">{{ authStore.user?.email }}</p>
                  </div>
                  <div>
                    <span class="block text-xs text-slate-400 font-semibold">No. Telepon / WhatsApp</span>
                    <p class="font-bold text-slate-800 mt-0.5">{{ authStore.user?.phone_number || '-' }}</p>
                  </div>
                  <div>
                    <span class="block text-xs text-slate-400 font-semibold">Peran Pengguna</span>
                    <div class="mt-1">
                      <span class="inline-block bg-green-50 text-green-700 border border-green-200 text-xs font-semibold px-2.5 py-0.5 rounded-full capitalize">
                        {{ authStore.user?.roles?.join(', ') }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right side: Store Profile (2 columns) -->
              <div class="md:col-span-2 space-y-4">
                <h4 class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2 text-blue-600">II. Profil Toko</h4>
                
                <div class="flex flex-col sm:flex-row gap-6 items-start">
                  <!-- Logo column -->
                  <div class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-xl border border-slate-100 flex-shrink-0 w-full sm:w-auto">
                    <img 
                      v-if="store?.logo"
                      :src="store.logo"
                      class="w-24 h-24 rounded-2xl object-cover border border-slate-200 shadow-sm"
                      alt="Logo Toko"
                    />
                    <div 
                      v-else
                      class="w-24 h-24 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-4xl shadow-inner font-bold text-blue-700"
                    >
                      🏪
                    </div>
                    <span class="text-[10px] text-slate-400 font-semibold mt-3 uppercase tracking-wider">Logo Toko</span>
                  </div>

                  <!-- Details column (takes remaining space) -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs flex-1 w-full">
                    <div>
                      <span class="block text-slate-400 font-semibold">Nama Toko</span>
                      <p class="font-bold text-slate-800 text-sm mt-0.5">{{ store?.store_name }}</p>
                    </div>

                    <div>
                      <span class="block text-slate-400 font-semibold">Kategori Usaha</span>
                      <p class="font-bold text-slate-800 text-sm mt-0.5">{{ formatCategoryText(store?.store_category) }}</p>
                    </div>

                    <div class="sm:col-span-2">
                      <span class="block text-slate-400 font-semibold">Deskripsi</span>
                      <p class="text-slate-700 leading-relaxed mt-0.5">{{ store?.description || 'Tidak ada deskripsi.' }}</p>
                    </div>

                    <div>
                      <span class="block text-slate-400 font-semibold">Jam Operasional</span>
                      <p class="font-bold text-slate-800 text-sm mt-0.5">{{ store?.operating_hours }}</p>
                    </div>

                    <div>
                      <span class="block text-slate-400 font-semibold">Alamat Lengkap</span>
                      <p class="text-slate-700 leading-relaxed mt-0.5">{{ store?.address || '-' }}, {{ formatDistrictText(store?.district) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>

    <!-- ══════════════════════════════════════════════════
         3. ORDER DETAIL MODAL
    ══════════════════════════════════════════════════ -->
    <div v-if="selectedOrder" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeOrderDetail"></div>

      <!-- Modal Card -->
      <div class="relative bg-white rounded-2xl shadow-xl max-w-xl w-full max-h-[90vh] flex flex-col transform transition-all overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-150 flex justify-between items-center bg-slate-50">
          <div>
            <h3 class="text-base font-bold text-slate-800">Detail Transaksi #{{ String(selectedOrder.id_order).padStart(3, '0') }}</h3>
            <span class="text-xs text-slate-400 mt-0.5 block">Diorder pada: {{ formatDate(selectedOrder.order_date) }}</span>
          </div>
          <button @click="closeOrderDetail" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Scrollable content -->
        <div class="p-6 overflow-y-auto space-y-5 flex-1">
          <!-- Buyer Info Grid -->
          <div class="bg-slate-50 rounded-xl p-4 border border-slate-150 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
              <span class="block text-slate-400 font-semibold mb-0.5">Nama Pembeli</span>
              <p class="font-bold text-slate-800 text-sm">{{ selectedOrder.user?.name }}</p>
            </div>
            <div>
              <span class="block text-slate-400 font-semibold mb-0.5">Nomor WhatsApp</span>
              <p class="font-bold text-slate-800 text-sm">{{ selectedOrder.user?.phone_number || '-' }}</p>
            </div>
            <div class="md:col-span-2">
              <span class="block text-slate-400 font-semibold mb-0.5">Alamat Pengiriman</span>
              <p class="text-slate-700 leading-relaxed">{{ selectedOrder.shipping_address }}</p>
            </div>
            <div class="md:col-span-2" v-if="selectedOrder.note">
              <span class="block text-slate-400 font-semibold mb-0.5">Catatan Pembeli</span>
              <p class="text-slate-700 italic">"{{ selectedOrder.note }}"</p>
            </div>
          </div>

          <!-- Items list -->
          <div>
            <h4 class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2">Item yang Dipesan</h4>
            <div class="border border-slate-200 rounded-xl overflow-hidden text-xs">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold">
                    <th class="p-3">Nama Produk</th>
                    <th class="p-3 text-center">Qty</th>
                    <th class="p-3 text-right">Harga</th>
                    <th class="p-3 text-right">Subtotal</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                  <tr v-for="item in selectedOrder.items" :key="item.id_order_detail">
                    <td class="p-3 font-semibold text-slate-800">{{ item.product?.name || 'Produk' }}</td>
                    <td class="p-3 text-center font-bold text-slate-700">{{ item.quantity }}</td>
                    <td class="p-3 text-right text-slate-600">Rp {{ formatNumber(item.price_at_purchase) }}</td>
                    <td class="p-3 text-right font-bold text-slate-800">Rp {{ formatNumber(item.quantity * item.price_at_purchase) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Payment Info -->
          <div class="flex items-center justify-between border-t border-slate-150 pt-4 text-sm">
            <div>
              <span class="text-xs text-slate-400 font-semibold">Metode Pembayaran</span>
              <span class="block mt-0.5 text-slate-800 font-bold uppercase text-xs">{{ selectedOrder.payment_method }}</span>
            </div>
            <div class="text-right">
              <span class="text-xs text-slate-400 font-semibold">Total Pembayaran</span>
              <span class="block text-base font-extrabold text-blue-600">Rp {{ formatNumber(selectedOrder.total_order) }}</span>
            </div>
          </div>
        </div>

        <!-- Action Footer -->
        <div class="px-6 py-4 border-t border-slate-150 bg-slate-50 flex justify-end gap-3">
          <button 
            v-if="selectedOrder.status === 'menunggu'"
            @click="updateStatusFromModal(selectedOrder.id_order, 'diproses')"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shadow"
          >
            Proses Pesanan
          </button>
          <button 
            v-if="selectedOrder.status === 'diproses'"
            @click="updateStatusFromModal(selectedOrder.id_order, 'selesai')"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold transition-all shadow"
          >
            Selesaikan Pesanan
          </button>
          <button 
            @click="closeOrderDetail" 
            class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         4. ADD PRODUCT MODAL
    ══════════════════════════════════════════════════ -->
    <div v-if="showAddProductModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeAddProductModal"></div>

      <!-- Modal Card -->
      <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] flex flex-col transform transition-all overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-150 flex justify-between items-center bg-slate-50">
          <div>
            <h3 class="text-base font-bold text-slate-800">Tambah Produk Baru</h3>
            <p class="text-xs text-slate-400 mt-0.5">Daftarkan produk baru Anda di toko</p>
          </div>
          <button @click="closeAddProductModal" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Scrollable Form -->
        <form @submit.prevent="submitProduct" class="flex flex-col flex-1 overflow-hidden">
          <div class="p-6 overflow-y-auto space-y-4 flex-1 text-sm">
            <!-- Name -->
            <div>
              <label class="block text-xs font-semibold text-slate-500 mb-1" for="prodName">Nama Produk <span class="text-red-500">*</span></label>
              <input
                v-model="productForm.name"
                type="text" id="prodName"
                placeholder="mis. Tempe Orek Lezat"
                :class="['w-full px-3.5 py-2 border rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium', errors.name ? 'border-red-400 bg-red-50' : 'border-slate-200']"
              />
              <span v-if="errors.name" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.name }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <!-- Category -->
              <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1" for="prodCat">Kategori <span class="text-red-500">*</span></label>
                <select
                  v-model="productForm.id_category"
                  id="prodCat"
                  :class="['w-full px-3.5 py-2 border rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium', errors.id_category ? 'border-red-400 bg-red-50' : 'border-slate-200']"
                >
                  <option value="" disabled>Pilih kategori...</option>
                  <option v-for="cat in categories" :key="cat.id_category" :value="cat.id_category">
                    {{ formatCategoryText(cat.name_category) }}
                  </option>
                </select>
                <span v-if="errors.id_category" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.id_category }}</span>
              </div>

              <!-- Unit -->
              <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1" for="prodUnit">Satuan <span class="text-red-500">*</span></label>
                <select
                  v-model="productForm.unit"
                  id="prodUnit"
                  :class="['w-full px-3.5 py-2 border rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium', errors.unit ? 'border-red-400 bg-red-50' : 'border-slate-200']"
                >
                  <option value="pcs">pcs</option>
                  <option value="bungkus">bungkus</option>
                  <option value="botol">botol</option>
                  <option value="porsi">porsi</option>
                  <option value="lembar">lembar</option>
                  <option value="buah">buah</option>
                  <option value="kg">kg</option>
                  <option value="pack">pack</option>
                </select>
                <span v-if="errors.unit" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.unit }}</span>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <!-- Price -->
              <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1" for="prodPrice">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                <div class="relative flex items-center">
                  <span class="absolute left-3 text-slate-400 text-xs font-bold">Rp</span>
                  <input
                    v-model="productForm.price"
                    type="number" id="prodPrice"
                    placeholder="20000"
                    :class="['w-full pl-9 pr-3.5 py-2 border rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium', errors.price ? 'border-red-400 bg-red-50' : 'border-slate-200']"
                  />
                </div>
                <span v-if="errors.price" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.price }}</span>
              </div>

              <!-- Stock & Min Order -->
              <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1" for="prodStock">Stok awal <span class="text-red-500">*</span></label>
                <input
                  v-model="productForm.stock"
                  type="number" id="prodStock"
                  placeholder="50"
                  :class="['w-full px-3.5 py-2 border rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium', errors.stock ? 'border-red-400 bg-red-50' : 'border-slate-200']"
                />
                <span v-if="errors.stock" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.stock }}</span>
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-500 mb-1" for="prodMinOrder">Minimal Order</label>
              <input
                v-model="productForm.min_order"
                type="number" id="prodMinOrder"
                placeholder="1"
                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium"
              />
            </div>

            <!-- Description -->
            <div>
              <label class="block text-xs font-semibold text-slate-500 mb-1" for="prodDesc">Deskripsi Produk</label>
              <textarea
                v-model="productForm.description"
                id="prodDesc"
                rows="2"
                placeholder="Deskripsikan keistimewaan produk Anda secara mendetail..."
                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs leading-relaxed"
              ></textarea>
            </div>

            <!-- Image Upload -->
            <div>
              <label class="block text-xs font-semibold text-slate-500 mb-1">Foto Produk</label>
              <div 
                class="border-2 border-dashed border-slate-200 hover:border-blue-500 bg-slate-50/50 rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer transition-colors text-xs font-semibold text-slate-500"
                @click="$refs.imageInput.click()"
              >
                <input ref="imageInput" type="file" accept="image/*" style="display:none;" @change="handleImageChange" />
                
                <div v-if="!imagePreview" class="text-center py-2">
                  <div class="text-2xl mb-1">📸</div>
                  <p class="text-slate-600">Klik untuk upload foto produk</p>
                  <p class="text-[10px] text-slate-400 font-normal mt-0.5">Format JPG, PNG, WEBP. Maks 2 MB</p>
                </div>
                
                <div v-else class="relative w-full flex justify-center py-2">
                  <img :src="imagePreview" class="h-28 object-contain rounded-lg border border-slate-100 shadow-sm" />
                  <button 
                    type="button" 
                    @click.stop="removeImage" 
                    class="absolute top-0 right-2 w-6 h-6 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs hover:bg-slate-700 shadow"
                  >
                    ×
                  </button>
                </div>
              </div>
              <span v-if="errors.image_product" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.image_product }}</span>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 border-t border-slate-150 bg-slate-50 flex justify-end gap-3">
            <button 
              type="button" 
              @click="closeAddProductModal" 
              class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="savingProduct"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shadow flex items-center gap-1.5"
            >
              <span v-if="savingProduct" class="w-3.5 h-3.5 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
              {{ savingProduct ? 'Menyimpan...' : 'Simpan Produk' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Toast Notification -->
    <div :class="['toast', toastType, toastVisible && 'show']" role="alert" aria-live="polite">
      {{ toastMsg }}
    </div>

    <!-- ══════════════════════════════════════════════════
         5. EDIT PROFILE MODAL
    ══════════════════════════════════════════════════ -->
    <div v-if="showEditProfileModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeEditProfileModal"></div>

      <!-- Modal Card -->
      <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col transform transition-all overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-150 flex justify-between items-center bg-slate-50">
          <div>
            <h3 class="text-base font-bold text-slate-800">Ubah Profil Akun & Toko</h3>
            <p class="text-xs text-slate-400 mt-0.5">Perbarui informasi akun login dan profil usaha Anda</p>
          </div>
          <button @click="closeEditProfileModal" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Scrollable Form -->
        <form @submit.prevent="submitProfile" class="flex flex-col flex-1 overflow-hidden">
          <div class="p-6 overflow-y-auto space-y-5 flex-1 text-sm">
            <!-- SECTION A: Akun Seller -->
            <div class="border-b border-slate-100 pb-3">
              <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-3 text-blue-600">I. Informasi Akun</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-500 mb-1" for="profName">Nama Lengkap <span class="text-red-500">*</span></label>
                  <input
                    v-model="profileForm.name"
                    type="text" id="profName"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium"
                  />
                  <span v-if="errors.name" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.name }}</span>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-500 mb-1" for="profPhone">No. WhatsApp <span class="text-red-500">*</span></label>
                  <input
                    v-model="profileForm.phone_number"
                    type="text" id="profPhone"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium"
                  />
                  <span v-if="errors.phone_number" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.phone_number }}</span>
                </div>
              </div>
            </div>

            <!-- SECTION B: Profil Toko -->
            <div>
              <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-3 text-blue-600">II. Profil Toko</h4>
              <div class="space-y-4">
                <!-- Logo upload -->
                <div>
                  <label class="block text-xs font-semibold text-slate-500 mb-1">Logo / Foto Toko</label>
                  <div 
                    class="border-2 border-dashed border-slate-200 hover:border-blue-500 bg-slate-50/50 rounded-xl p-3 flex flex-col items-center justify-center cursor-pointer transition-colors text-xs font-semibold text-slate-500"
                    @click="$refs.profileLogoInput.click()"
                  >
                    <input ref="profileLogoInput" type="file" accept="image/*" style="display:none;" @change="handleProfileLogoChange" />
                    <div v-if="!profileLogoPreview" class="text-center py-2">
                      <div class="text-2xl mb-1">📸</div>
                      <p class="text-slate-600">Klik untuk upload logo toko baru</p>
                    </div>
                    <div v-else class="relative w-full flex justify-center py-1">
                      <img :src="profileLogoPreview" class="h-20 object-contain rounded-lg border border-slate-100 shadow-sm" />
                      <button 
                        type="button" 
                        @click.stop="removeProfileLogo" 
                        class="absolute top-0 right-2 w-5 h-5 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs hover:bg-slate-700 shadow"
                      >
                        ×
                      </button>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Store Name -->
                  <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1" for="profStoreName">Nama Toko <span class="text-red-500">*</span></label>
                    <input
                      v-model="profileForm.store_name"
                      type="text" id="profStoreName"
                      class="w-full px-3 py-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium"
                    />
                    <span v-if="errors.store_name" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.store_name }}</span>
                  </div>

                  <!-- Store Category -->
                  <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1" for="profStoreCat">Kategori Toko <span class="text-red-500">*</span></label>
                    <select
                      v-model="profileForm.category"
                      id="profStoreCat"
                      class="w-full px-3 py-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium"
                    >
                      <option value="" disabled>Pilih kategori...</option>
                      <option v-for="cat in categories" :key="cat.id_category" :value="cat.name_category">
                        {{ formatCategoryText(cat.name_category) }}
                      </option>
                    </select>
                    <span v-if="errors.category" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.category }}</span>
                  </div>
                </div>

                <!-- Description -->
                <div>
                  <label class="block text-xs font-semibold text-slate-500 mb-1" for="profStoreDesc">Deskripsi Singkat</label>
                  <textarea
                    v-model="profileForm.description"
                    id="profStoreDesc"
                    rows="2"
                    placeholder="Ceritakan tentang toko Anda..."
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs leading-relaxed"
                  ></textarea>
                  <span v-if="errors.description" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.description }}</span>
                </div>

                <!-- Operating hours -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-3">
                  <span class="block text-xs font-bold text-slate-700">Jam Operasional</span>
                  <div class="flex items-center justify-between">
                    <label class="text-xs font-semibold text-slate-600" for="profOpenEveryday">Buka setiap hari</label>
                    <button
                      type="button"
                      role="switch"
                      :aria-checked="profileForm.open_every_day"
                      id="profOpenEveryday"
                      :class="['w-10 h-5 flex items-center rounded-full p-1 cursor-pointer transition-colors duration-200 focus:outline-none', profileForm.open_every_day ? 'bg-blue-600' : 'bg-slate-300']"
                      @click="profileForm.open_every_day = !profileForm.open_every_day"
                    >
                      <span :class="['bg-white w-3 h-3 rounded-full shadow-md transform transition-transform duration-200', profileForm.open_every_day ? 'translate-x-5' : 'translate-x-0']"></span>
                    </button>
                  </div>

                  <!-- Weekdays checklist if not open everyday -->
                  <div v-if="!profileForm.open_every_day" class="space-y-1.5">
                    <label class="block text-[11px] font-semibold text-slate-500">Pilih Hari Buka</label>
                    <div class="flex flex-wrap gap-1.5">
                      <button
                        v-for="day in weekdays"
                        :key="day.value"
                        type="button"
                        :class="[
                          'px-2.5 py-1 rounded text-xs font-bold border transition-colors',
                          profileForm.open_days.includes(day.value)
                            ? 'bg-blue-50 text-blue-700 border-blue-200'
                            : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50'
                        ]"
                        @click="toggleProfileDay(day.value)"
                      >
                        {{ day.label }}
                      </button>
                    </div>
                  </div>

                  <!-- Time Picker Row -->
                  <div class="flex gap-4 items-center">
                    <div class="flex-1">
                      <label class="block text-[11px] font-semibold text-slate-500 mb-1" for="profOpenTime">Jam Buka</label>
                      <input
                        v-model="profileForm.open_time"
                        type="time" id="profOpenTime"
                        class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs outline-none bg-white focus:border-blue-500"
                      />
                    </div>
                    <div class="text-slate-400 font-bold self-end mb-2">→</div>
                    <div class="flex-1">
                      <label class="block text-[11px] font-semibold text-slate-500 mb-1" for="profCloseTime">Jam Tutup</label>
                      <input
                        v-model="profileForm.close_time"
                        type="time" id="profCloseTime"
                        class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs outline-none bg-white focus:border-blue-500"
                      />
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- District -->
                  <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1" for="profDistrict">Kelurahan <span class="text-red-500">*</span></label>
                    <select
                      v-model="profileForm.district"
                      id="profDistrict"
                      class="w-full px-3 py-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium"
                    >
                      <option value="" disabled>Pilih Kelurahan...</option>
                      <option v-for="d in districts" :key="d.value" :value="d.value">{{ d.label }}</option>
                    </select>
                    <span v-if="errors.district" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.district }}</span>
                  </div>

                  <!-- Address -->
                  <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1" for="profAddress">Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea
                      v-model="profileForm.address"
                      id="profAddress"
                      rows="1"
                      placeholder="Nama jalan, nomor rumah, RT/RW..."
                      class="w-full px-3 py-2 border border-slate-200 rounded-lg outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs leading-relaxed"
                    ></textarea>
                    <span v-if="errors.address" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.address }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 border-t border-slate-150 bg-slate-50 flex justify-end gap-3">
            <button 
              type="button" 
              @click="closeEditProfileModal" 
              class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="savingProfile"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shadow flex items-center gap-1.5"
            >
              <span v-if="savingProfile" class="w-3.5 h-3.5 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
              {{ savingProfile ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { sellerService } from '@/services/sellerService'

const router = useRouter()
const authStore = useAuthStore()

// ── State variables ──────────────────────────────────────────
const activeTab = ref('dashboard')
const loadingStore = ref(true)
const store = ref(null)
const products = ref([])
const categories = ref([])
const dashboardStats = ref({
  total_orders: 0,
  waiting_orders: 0,
  processed_orders: 0,
  completed_orders: 0
})
const allOrders = ref([])

// Filtering orders
const orderFilters = [
  { label: 'Semua', value: 'semua' },
  { label: 'Menunggu', value: 'menunggu' },
  { label: 'Diproses', value: 'diproses' }
]
const activeOrderFilter = ref('semua')

// Selection for details modal
const selectedOrder = ref(null)

// Add product modal state
const showAddProductModal = ref(false)
const savingProduct = ref(false)
const imagePreview = ref(null)
const imageInput = ref(null)

const productForm = reactive({
  name: '',
  id_category: '',
  price: '',
  stock: '',
  unit: 'pcs',
  min_order: 1,
  description: '',
  image_product: null
})

const errors = reactive({})

// Edit Profile modal state
const showEditProfileModal = ref(false)
const savingProfile = ref(false)
const profileLogoPreview = ref(null)
const profileLogoInput = ref(null)

const profileForm = reactive({
  name: '',
  phone_number: '',
  store_name: '',
  category: '',
  description: '',
  open_every_day: false,
  open_days: [],
  open_time: '08:00',
  close_time: '17:00',
  logo: null
})

// Static choices
const weekdays = [
  { value: 'sen', label: 'Sen' },
  { value: 'sel', label: 'Sel' },
  { value: 'rab', label: 'Rab' },
  { value: 'kam', label: 'Kam' },
  { value: 'jum', label: "Jum'" },
  { value: 'sab', label: 'Sab' },
  { value: 'min', label: 'Min' }
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
  { value: 'mojosongo',      label: 'Mojosongo' }
]

// Toast notification state
const toastMsg = ref('')
const toastType = ref('')
const toastVisible = ref(false)
let toastTimer = null

// Navigation Items
const navItems = [
  {
    label: 'Dashboard Toko',
    value: 'dashboard',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />'
  },
  {
    label: 'Toko Saya',
    value: 'store',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />'
  },
  {
    label: 'Pengaturan Akun',
    value: 'settings',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><circle cx="12" cy="12" r="3" />'
  }
]

// ── Computed properties ───────────────────────────────────────
const pageTitle = computed(() => {
  const item = navItems.find(x => x.value === activeTab.value)
  return item ? item.label : 'Dashboard'
})

const storeName = computed(() => store.value?.store_name || '')
const storeStatus = computed(() => store.value?.verification_status || 'menunggu')

const userInitial = computed(() => {
  const name = authStore.user?.name || ''
  return name.charAt(0).toUpperCase()
})

const verificationBadgeClass = computed(() => {
  const status = storeStatus.value
  if (status === 'disetujui') return 'bg-green-100 text-green-800 border-green-200'
  if (status === 'dibatalkan') return 'bg-red-100 text-red-800 border-red-200'
  return 'bg-yellow-100 text-yellow-800 border-yellow-200'
})

const filteredOrders = computed(() => {
  if (activeOrderFilter.value === 'semua') {
    return allOrders.value
  }
  return allOrders.value.filter(o => o.status === activeOrderFilter.value)
})

// ── Methods ───────────────────────────────────────────────────

const loadDashboardData = async () => {
  try {
    const res = await sellerService.getDashboard()
    if (res.success) {
      dashboardStats.value = res.data.metrics
      allOrders.value = res.data.orders
    }
  } catch (error) {
    console.error('Failed loading stats', error)
  }
}

const loadStoreData = async () => {
  try {
    loadingStore.value = true
    const res = await sellerService.getStore()
    if (res.success) {
      store.value = res.data.store
      products.value = res.data.products
    }
  } catch (error) {
    console.error('Failed loading store data', error)
    if (error.response?.status === 404) {
      // If store setup missing, send to setup onboarding
      showToast('Profil toko belum lengkap. Silakan buat profil toko.', 'error')
      setTimeout(() => router.push({ name: 'store.setup' }), 2000)
    }
  } finally {
    loadingStore.value = false
  }
}

const loadCategories = async () => {
  try {
    const res = await sellerService.getCategories()
    if (res.success) {
      categories.value = res.data
    }
  } catch (error) {
    console.error('Failed loading categories', error)
  }
}

// Order helpers
const getOrderProductsText = (order) => {
  if (!order.items || order.items.length === 0) return 'Tidak ada produk'
  return order.items.map(i => `${i.product?.name || 'Produk'} (${i.quantity} ${i.product?.unit || 'pcs'})`).join(', ')
}

const orderStatusClass = (status) => {
  if (status === 'menunggu') return 'bg-yellow-50 text-yellow-700 border-yellow-200'
  if (status === 'diproses') return 'bg-blue-50 text-blue-700 border-blue-200'
  if (status === 'selesai') return 'bg-green-50 text-green-700 border-green-200'
  return 'bg-red-50 text-red-700 border-red-200'
}

const formatStatusText = (status) => {
  if (status === 'menunggu') return 'Menunggu'
  if (status === 'diproses') return 'Diproses'
  if (status === 'selesai') return 'Selesai'
  return 'Dibatalkan'
}

const formatStoreStatus = (status) => {
  if (status === 'menunggu') return 'Menunggu Verifikasi'
  if (status === 'disetujui') return 'Toko Aktif'
  return 'Ditolak'
}

const formatCategoryText = (cat) => {
  if (!cat) return '-'
  const mapping = {
    'makanan_minuman': '🍱 Makanan & Minuman',
    'fashion_batik': '👗 Fashion & Batik',
    'kerajinan': '🧶 Kerajinan Tangan',
    'elektronik': '📱 Elektronik & Aksesori',
    'kecantikan': '💄 Kecantikan & Perawatan',
    'pertanian': '🌾 Pertanian & Hasil Bumi',
    'jasa': '🛠️ Jasa & Layanan',
    'lainnya': '📦 Lainnya'
  }
  return mapping[cat] || cat
}

const formatDistrictText = (dist) => {
  if (!dist) return '-'
  return dist.charAt(0).toUpperCase() + dist.slice(1)
}

// Status Updates
const updateStatus = async (orderId, newStatus) => {
  const actionText = newStatus === 'diproses' ? 'memproses' : 'menyelesaikan'
  if (!confirm(`Apakah Anda yakin ingin ${actionText} pesanan ini?`)) return

  try {
    const res = await sellerService.updateOrderStatus(orderId, newStatus)
    if (res.success) {
      showToast('Status pesanan berhasil diperbarui.', 'success')
      await loadDashboardData()
    }
  } catch (error) {
    showToast('Gagal memperbarui status pesanan.', 'error')
  }
}

const updateStatusFromModal = async (orderId, newStatus) => {
  const actionText = newStatus === 'diproses' ? 'memproses' : 'menyelesaikan'
  if (!confirm(`Apakah Anda yakin ingin ${actionText} pesanan ini?`)) return

  try {
    const res = await sellerService.updateOrderStatus(orderId, newStatus)
    if (res.success) {
      showToast('Status pesanan berhasil diperbarui.', 'success')
      // Update local modal data
      selectedOrder.value.status = newStatus
      await loadDashboardData()
    }
  } catch (error) {
    showToast('Gagal memperbarui status pesanan.', 'error')
  }
}

// Detail Modal Actions
const openOrderDetail = (order) => {
  selectedOrder.value = order
}

const closeOrderDetail = () => {
  selectedOrder.value = null
}

// Edit Profile Actions
const openEditProfileModal = () => {
  clearErrors()
  
  // Account details
  profileForm.name = authStore.user?.name || ''
  profileForm.phone_number = authStore.user?.phone_number || ''
  
  // Store details
  profileForm.store_name = store.value?.store_name || ''
  profileForm.category = store.value?.store_category || ''
  profileForm.description = store.value?.description || ''
  profileForm.district = store.value?.district || ''
  profileForm.address = store.value?.address || ''
  profileForm.logo = null
  profileLogoPreview.value = store.value?.logo || null
  
  // Operating hours parsing
  const opHours = store.value?.operating_hours || ''
  if (opHours.includes(',')) {
    const parts = opHours.split(',')
    const daysStr = parts.slice(0, -1).join(',').trim()
    const timesStr = parts[parts.length - 1].trim()
    
    if (daysStr.toLowerCase() === 'setiap hari') {
      profileForm.open_every_day = true
      profileForm.open_days = []
    } else {
      profileForm.open_every_day = false
      const rawDays = daysStr.split(',').map(d => d.trim().toLowerCase())
      profileForm.open_days = weekdays
        .filter(w => rawDays.includes(w.label.toLowerCase()))
        .map(w => w.value)
    }
    
    const timeParts = timesStr.split(/[-–]/).map(t => t.trim())
    if (timeParts.length === 2) {
      profileForm.open_time = timeParts[0]
      profileForm.close_time = timeParts[1]
    } else {
      profileForm.open_time = '08:00'
      profileForm.close_time = '17:00'
    }
  } else {
    // defaults
    profileForm.open_every_day = false
    profileForm.open_days = []
    profileForm.open_time = '08:00'
    profileForm.close_time = '17:00'
  }
  
  showEditProfileModal.value = true
}

const closeEditProfileModal = () => {
  showEditProfileModal.value = false
  clearErrors()
}

const toggleProfileDay = (day) => {
  const idx = profileForm.open_days.indexOf(day)
  if (idx === -1) {
    profileForm.open_days.push(day)
  } else {
    profileForm.open_days.splice(idx, 1)
  }
}

const handleProfileLogoChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      errors.logo = 'Ukuran logo maksimal 2 MB.'
      return
    }
    delete errors.logo
    profileForm.logo = file
    const reader = new FileReader()
    reader.onload = (e) => {
      profileLogoPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeProfileLogo = () => {
  profileForm.logo = null
  profileLogoPreview.value = null
  if (profileLogoInput.value) profileLogoInput.value.value = ''
}

const validateProfile = () => {
  clearErrors()
  let valid = true

  if (!profileForm.name.trim()) {
    errors.name = 'Nama lengkap wajib diisi.'
    valid = false
  }
  if (!profileForm.phone_number.trim()) {
    errors.phone_number = 'No. WhatsApp wajib diisi.'
    valid = false
  }
  if (!profileForm.store_name.trim()) {
    errors.store_name = 'Nama toko wajib diisi.'
    valid = false
  }
  if (!profileForm.category) {
    errors.category = 'Pilih kategori toko.'
    valid = false
  }
  if (!profileForm.district) {
    errors.district = 'Pilih kelurahan.'
    valid = false
  }
  if (!profileForm.address.trim()) {
    errors.address = 'Alamat lengkap wajib diisi.'
    valid = false
  }
  if (!profileForm.open_time) {
    errors.open_time = 'Jam buka wajib diisi.'
    valid = false
  }
  if (!profileForm.close_time) {
    errors.close_time = 'Jam tutup wajib diisi.'
    valid = false
  }
  if (profileForm.open_time && profileForm.close_time && profileForm.open_time >= profileForm.close_time) {
    errors.close_time = 'Jam tutup harus lebih akhir dari jam buka.'
    valid = false
  }
  if (!profileForm.open_every_day && profileForm.open_days.length === 0) {
    errors.open_days = 'Pilih minimal satu hari buka.'
    valid = false
  }

  return valid
}

const submitProfile = async () => {
  if (!validateProfile()) {
    showToast('Mohon isi field wajib.', 'error')
    return
  }

  savingProfile.value = true

  try {
    const dayText = profileForm.open_every_day
      ? 'Setiap hari'
      : profileForm.open_days.map(d => weekdays.find(w => w.value === d)?.label).join(', ')
    const operating_hours = `${dayText}, ${profileForm.open_time} – ${profileForm.close_time}`

    const payload = new FormData()
    payload.append('name', profileForm.name)
    payload.append('phone_number', profileForm.phone_number)
    payload.append('store_name', profileForm.store_name)
    payload.append('category', profileForm.category)
    payload.append('district', profileForm.district)
    payload.append('address', profileForm.address)
    payload.append('description', profileForm.description || '')
    payload.append('operating_hours', operating_hours)
    if (profileForm.logo) {
      payload.append('logo', profileForm.logo)
    }

    const res = await sellerService.updateProfile(payload)
    if (res.success) {
      showToast('Profil berhasil diperbarui.', 'success')
      closeEditProfileModal()
      await authStore.fetchCurrentUser()
      await loadStoreData()
    }
  } catch (err) {
    console.error('Failed updating profile', err)
    const res = err.response
    if (res?.status === 422) {
      const fieldErrors = res.data.errors ?? {}
      Object.keys(fieldErrors).forEach(f => {
        errors[f] = fieldErrors[f][0]
      })
      showToast('Periksa kembali isian form.', 'error')
    } else {
      showToast(res?.data?.message || 'Gagal menyimpan profil.', 'error')
    }
  } finally {
    savingProfile.value = false
  }
}

// Add Product Actions
const openAddProductModal = () => {
  clearProductForm()
  clearErrors()
  showAddProductModal.value = true
}

const closeAddProductModal = () => {
  showAddProductModal.value = false
}

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      errors.image_product = 'Ukuran foto maksimal 2 MB.'
      return
    }
    delete errors.image_product
    productForm.image_product = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeImage = () => {
  productForm.image_product = null
  imagePreview.value = null
  if (imageInput.value) imageInput.value.value = ''
}

const clearProductForm = () => {
  productForm.name = ''
  productForm.id_category = ''
  productForm.price = ''
  productForm.stock = ''
  productForm.unit = 'pcs'
  productForm.min_order = 1
  productForm.description = ''
  productForm.image_product = null
  imagePreview.value = null
}

const validateProduct = () => {
  clearErrors()
  let valid = true

  if (!productForm.name.trim()) {
    errors.name = 'Nama produk wajib diisi.'
    valid = false
  }
  if (!productForm.id_category) {
    errors.id_category = 'Pilih kategori produk.'
    valid = false
  }
  if (!productForm.price || productForm.price < 0) {
    errors.price = 'Harga produk harus bernilai positif.'
    valid = false
  }
  if (!productForm.stock || productForm.stock < 0) {
    errors.stock = 'Stok produk harus bernilai 0 atau positif.'
    valid = false
  }
  if (!productForm.unit) {
    errors.unit = 'Pilih satuan produk.'
    valid = false
  }

  return valid
}

const submitProduct = async () => {
  if (!validateProduct()) {
    showToast('Mohon isi field wajib.', 'error')
    return
  }

  savingProduct.value = true

  try {
    const payload = new FormData()
    payload.append('name', productForm.name)
    payload.append('id_category', productForm.id_category)
    payload.append('price', productForm.price)
    payload.append('stock', productForm.stock)
    payload.append('unit', productForm.unit)
    payload.append('min_order', productForm.min_order)
    payload.append('description', productForm.description)
    if (productForm.image_product) {
      payload.append('image_product', productForm.image_product)
    }

    const res = await sellerService.addProduct(payload)
    if (res.success) {
      showToast('Produk berhasil ditambahkan.', 'success')
      closeAddProductModal()
      // Refresh products and stats
      await loadStoreData()
      await loadDashboardData()
    }
  } catch (err) {
    console.error('Failed adding product', err)
    const res = err.response
    if (res?.status === 422) {
      const fieldErrors = res.data.errors ?? {}
      Object.keys(fieldErrors).forEach(f => {
        errors[f] = fieldErrors[f][0]
      })
      showToast('Periksa kembali isian form.', 'error')
    } else {
      showToast(res?.data?.message || 'Gagal menyimpan produk.', 'error')
    }
  } finally {
    savingProduct.value = false
  }
}

// General helpers
const formatNumber = (num) => {
  if (!num) return '0'
  return parseFloat(num).toLocaleString('id-ID')
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Logout
const handleLogout = async () => {
  await authStore.logout()
  router.push({ name: 'login' })
}

// Error handling helpers
const clearErrors = () => {
  Object.keys(errors).forEach(k => delete errors[k])
}

const showToast = (msg, type = '', duration = 3200) => {
  clearTimeout(toastTimer)
  toastMsg.value = msg
  toastType.value = type
  toastVisible.value = true
  toastTimer = setTimeout(() => { toastVisible.value = false }, duration)
}

// ── Lifecycle Hooks ───────────────────────────────────────────
onMounted(async () => {
  // Load store and products
  await loadStoreData()
  // Load stats and orders list
  await loadDashboardData()
  // Load product categories options
  await loadCategories()
})
</script>

<style scoped>
/* Custom scoped animations and Toast styling overrides */
.toast {
  position: fixed;
  bottom: 28px;
  left: 50%;
  transform: translateX(-50%) translateY(12px);
  background: var(--gray-800, #2D3547);
  color: #fff;
  font-size: 13px;
  font-weight: 600;
  padding: 10px 22px;
  border-radius: 100px;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s ease, transform 0.25s ease;
  z-index: 99999;
  white-space: nowrap;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}
.toast.show {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
}
.toast.success { background: #15803d; }
.toast.error   { background: #b91c1c; }
</style>
