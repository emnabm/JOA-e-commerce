<template>
  <div class="account-page">
    <Header />
    
    <div class="account-container">
      <!-- Sidebar -->
      <aside class="account-sidebar" :class="{ 'mobile-open': mobileMenuOpen }">
        <div class="sidebar-header">
          <h2>My Account</h2>
          <button class="close-sidebar" @click="mobileMenuOpen = false">✕</button>
        </div>
        
        <div class="user-info">
          <div class="user-avatar">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="user-details">
            <h3>{{ username || 'Guest' }}</h3>
            <p>{{ email }}</p>
          </div>
        </div>

        <nav class="sidebar-nav">
          <button
            class="nav-item"
            :class="{ active: section === 'profile' }"
            @click="switchSection('profile')"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            <span>Profile</span>
          </button>

          <button
            class="nav-item"
            :class="{ active: section === 'orders' }"
            @click="switchSection('orders')"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
              <line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <span>Order History</span>
          </button>

          <button
            class="nav-item"
            :class="{ active: section === 'checkout' }"
            @click="switchSection('checkout')"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <circle cx="9" cy="21" r="1"/>
              <circle cx="20" cy="21" r="1"/>
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <span>Checkout</span>
          </button>

          <button
            class="nav-item"
            @click="updatePassword"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span>Update Password</span>
          </button>

          <button
            class="nav-item logout"
            @click="logout"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Logout</span>
          </button>
        </nav>
      </aside>

      <!-- Mobile menu button -->
      <button class="mobile-menu-toggle" @click="mobileMenuOpen = true">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>

      <!-- Overlay -->
      <div v-if="mobileMenuOpen" class="sidebar-overlay" @click="mobileMenuOpen = false"></div>

      <!-- Main Content -->
      <main class="account-content">
        <!-- Profile Section -->
        <div v-if="section === 'profile'" class="content-card">
          <div class="card-header">
            <h2>Profile Information</h2>
            <button class="edit-btn" @click="edit">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M17 3l4 4-7 7H10v-4l7-7z"/>
                <path d="M4 20h16"/>
              </svg>
              Edit Profile
            </button>
          </div>
          
          <div class="profile-info">
            <div class="info-row">
              <div class="info-label">Full Name</div>
              <div class="info-value">{{ username || 'Not provided' }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Email Address</div>
              <div class="info-value">{{ email }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Member Since</div>
              <div class="info-value">{{ info.memberSince || 'N/A' }}</div>
            </div>
          </div>

          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-number">{{ info.totalOrders || 0 }}</div>
              <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
              <div class="stat-number">{{ formatPrice(info.totalSpent || 0) }}</div>
              <div class="stat-label">Total Spent</div>
            </div>
            <div class="stat-card">
              <div class="stat-number">{{ info.lastOrderStatus || '-' }}</div>
              <div class="stat-label">Last Order Status</div>
            </div>
          </div>
        </div>

        <!-- Orders Section -->
        <div v-else-if="section === 'orders'" class="content-card">
          <div class="card-header">
            <h2>Order History</h2>
          </div>

          <!-- Filters -->
          <div class="filters-bar">
            <div class="tabs">
              <button
                v-for="tab in tabs"
                :key="tab.key"
                class="tab-btn"
                :class="{ active: activeTab === tab.key }"
                @click="setTab(tab.key)"
              >
                {{ tab.label }}
              </button>
            </div>

            <div class="date-filters">
              <div class="date-input">
                <label>From</label>
                <input type="date" v-model="fromDate" class="date-picker" />
              </div>
              <div class="date-input">
                <label>To</label>
                <input type="date" v-model="toDate" class="date-picker" />
              </div>
            </div>
          </div>

          <!-- Orders Table -->
          <div v-if="filteredOrders.length > 0" class="orders-table">
            <table>
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Date</th>
                  <th>Address</th>
                  <th>Status</th>
                  <th>Delivery</th>
                  <th>Total</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in paginatedOrders" :key="order.id">
                  <td data-label="Order ID">#{{ order.id }}</td>
                  <td data-label="Date">{{ formatDate(order.date) }}</td>
                  <td data-label="Address">{{ order.address }}</td>
                  <td data-label="Status">
                    <span :class="['status-badge', getStatusClass(order.status)]">
                      {{ order.status }}
                    </span>
                  </td>
                  <td data-label="Delivery">{{ order.delivery || 'Standard' }}</td>
                  <td data-label="Total">{{ formatPrice(order.price) }}</td>
                  <td data-label="Action">
                    <button class="view-btn" @click="viewOrder(order.id)">View</button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="pagination">
              <button 
                class="page-btn"
                :disabled="currentPage === 1" 
                @click="currentPage--"
                
              >
                ← Previous
              </button>
              <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
              <button 
                class="page-btn"
                :disabled="currentPage === totalPages" 
                @click="currentPage++"
                
              >
                Next →
              </button>
            </div>
          </div>

          <div v-else class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <h3>No orders found</h3>
            <p>You haven't placed any orders yet</p>
            <NuxtLink to="/category" class="shop-now-btn">Shop Now</NuxtLink>
          </div>
        </div>

        <!-- Checkout Section -->
        <div v-else-if="section === 'checkout'" class="content-card">
          <Checkout
            page="/account"
            :query="{ section: 'orders' }"
          />
        </div>
      </main>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue'
import { useRouter, useRoute } from '#vue-router'
import axios from 'axios'
import Checkout from '~/pages/category/product/checkout.vue'

definePageMeta({
  middleware: 'auth',
})

const router = useRouter()
const route = useRoute()

// State
const section = ref('profile')
const mobileMenuOpen = ref(false)
const email = ref('')
const username = ref('')
const token = ref('')
const orders = ref([])
const activeTab = ref('all')
const fromDate = ref('')
const toDate = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

const tabs = [
  { key: 'all', label: 'All Orders' },
  { key: 'delivered', label: 'Delivered' },
  { key: 'pending', label: 'Pending' },
  { key: 'cancelled', label: 'Cancelled' },
]

const info = ref({
  totalOrders: 0,
  totalSpent: 0,
  lastOrderStatus: '',
  memberSince: '',
})

// Fetch data
const fetchProfile = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/getInfos', {
      headers: { Authorization: `Bearer ${token.value}` },
    })
    info.value = response.data
  } catch (e) {
    console.error('Error loading profile info', e)
  }
}

const fetchOrders = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/my_orders', {
      headers: { Authorization: `Bearer ${token.value}` },
    })
    orders.value = response.data.sort(
      (a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()
    )
    currentPage.value = 1
  } catch (e) {
    console.error('Error loading orders', e)
  }
}

// Computed
const filteredOrders = computed(() => {
  let filtered = orders.value
  
  if (activeTab.value !== 'all') {
    filtered = filtered.filter(order => order.status === activeTab.value)
  }
  
  if (fromDate.value) {
    filtered = filtered.filter(order => new Date(order.date) >= new Date(fromDate.value))
  }
  if (toDate.value) {
    filtered = filtered.filter(order => new Date(order.date) <= new Date(toDate.value))
  }
  
  return filtered
})

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredOrders.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredOrders.value.length / itemsPerPage)
})

// Methods
const switchSection = (newSection: string) => {
  section.value = newSection
  mobileMenuOpen.value = false
  router.push({ query: { section: newSection } })
}

const setTab = (tab: string) => {
  activeTab.value = tab
  currentPage.value = 1
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tn-TN', {
    style: 'currency',
    currency: 'TND',
    minimumFractionDigits: 3,
    maximumFractionDigits: 3
  }).format(price)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const getStatusClass = (status: string) => {
  switch (status) {
    case 'delivered': return 'delivered'
    case 'pending': return 'pending'
    case 'cancelled': return 'cancelled'
    default: return ''
  }
}

const viewOrder = (orderId: number) => {
  router.push(`/order/${orderId}`)
}

const updatePassword = () => {
  router.push('/password')
}

const logout = () => {
  localStorage.clear()
  router.push('/login')
}

const edit = () => {
  router.push('/editProfile')
}

// Watchers
watch(section, async (newSection) => {
  if (newSection === 'orders') {
    await fetchOrders()
  } else if (newSection === 'profile') {
    await fetchProfile()
  }
})

watch([activeTab, fromDate, toDate], () => {
  currentPage.value = 1
})

// Initialization
onMounted(() => {
  token.value = localStorage.getItem('token') || ''
  email.value = localStorage.getItem('user_email') || ''
  username.value = localStorage.getItem('user_username') || ''
  
  const querySection = route.query.section
  if (querySection && typeof querySection === 'string') {
    section.value = querySection
  }
  
  fetchProfile()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@400;500&display=swap');

.account-page {
  min-height: 100vh;
  background: #F8F2E6;
  display: flex;
  flex-direction: column;
}

.account-container {
  flex: 1;
  display: flex;
  position: relative;
  max-width: 1400px;
  margin: 0 auto;
  width: 100%;
  padding: 2rem;
  gap: 2rem;
}

/* Sidebar */
.account-sidebar {
  width: 300px;
  background: #2C2117;
  border-radius: 16px;
  padding: 1.5rem;
  position: sticky;
  top: 20px;
  height: fit-content;
  transition: transform 0.3s ease;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.sidebar-header h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  color: #F8F2E6;
  margin: 0;
}

.close-sidebar {
  display: none;
  background: none;
  border: none;
  color: #F8F2E6;
  font-size: 1.5rem;
  cursor: pointer;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 0;
  border-bottom: 1px solid rgba(248, 242, 230, 0.1);
  margin-bottom: 1.5rem;
}

.user-avatar {
  width: 56px;
  height: 56px;
  background: rgba(200, 133, 74, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #C8854A;
}

.user-details h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  color: #F8F2E6;
  margin: 0 0 4px 0;
}

.user-details p {
  font-size: 0.75rem;
  color: rgba(248, 242, 230, 0.6);
  margin: 0;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: none;
  border: none;
  border-radius: 10px;
  color: rgba(248, 242, 230, 0.7);
  font-family: 'DM Sans', sans-serif;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
  width: 100%;
  text-align: left;
}

.nav-item svg {
  flex-shrink: 0;
}

.nav-item:hover {
  background: rgba(200, 133, 74, 0.15);
  color: #C8854A;
}

.nav-item.active {
  background: #C8854A;
  color: #fff;
}

.nav-item.logout {
  margin-top: 1rem;
  border-top: 1px solid rgba(248, 242, 230, 0.1);
  border-radius: 0;
  padding-top: 1rem;
}

.nav-item.logout:hover {
  background: rgba(244, 67, 54, 0.2);
  color: #f44336;
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
  display: none;
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  width: 48px;
  height: 48px;
  background: #C8854A;
  border: none;
  border-radius: 50%;
  color: #fff;
  cursor: pointer;
  z-index: 100;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.sidebar-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 98;
}

/* Main Content */
.account-content {
  flex: 1;
  min-width: 0;
}

.content-card {
  background: #fff;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.card-header h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  color: #2C2117;
  margin: 0;
}

.edit-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 20px;
  background: none;
  border: 1px solid #C8854A;
  border-radius: 8px;
  color: #C8854A;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  transition: all 0.2s;
}

.edit-btn:hover {
  background: #C8854A;
  color: #fff;
}

/* Profile Info */
.profile-info {
  margin-bottom: 2rem;
}

.info-row {
  display: flex;
  padding: 1rem 0;
  border-bottom: 1px solid rgba(44, 33, 23, 0.1);
}

.info-label {
  width: 140px;
  font-weight: 500;
  color: #7a6555;
}

.info-value {
  flex: 1;
  color: #2C2117;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-top: 2rem;
}

.stat-card {
  background: #F8F2E6;
  padding: 1.5rem;
  border-radius: 12px;
  text-align: center;
}

.stat-number {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  font-weight: 600;
  color: #C8854A;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.8rem;
  color: #7a6555;
}

/* Filters */
.filters-bar {
  margin-bottom: 2rem;
}

.tabs {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.tab-btn {
  padding: 8px 20px;
  background: none;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 30px;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: all 0.2s;
}

.tab-btn:hover {
  border-color: #C8854A;
  color: #C8854A;
}

.tab-btn.active {
  background: #C8854A;
  border-color: #C8854A;
  color: #fff;
}

.date-filters {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.date-input {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.date-input label {
  font-size: 0.8rem;
  color: #7a6555;
}

.date-picker {
  padding: 6px 10px;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 6px;
  font-family: 'DM Sans', sans-serif;
}

/* Orders Table */
.orders-table {
  overflow-x: auto;
}

.orders-table table {
  width: 100%;
  border-collapse: collapse;
}

.orders-table th {
  text-align: left;
  padding: 1rem 0.75rem;
  background: #F8F2E6;
  font-weight: 500;
  color: #2C2117;
}

.orders-table td {
  padding: 1rem 0.75rem;
  border-bottom: 1px solid rgba(44, 33, 23, 0.08);
  color: #5a4a3a;
}

.status-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-badge.delivered {
  background: rgba(76, 175, 80, 0.1);
  color: #4CAF50;
}

.status-badge.pending {
  background: rgba(255, 152, 0, 0.1);
  color: #FF9800;
}

.status-badge.cancelled {
  background: rgba(244, 67, 54, 0.1);
  color: #f44336;
}

.view-btn {
  padding: 4px 12px;
  background: none;
  border: 1px solid #C8854A;
  border-radius: 6px;
  color: #C8854A;
  cursor: pointer;
  font-size: 0.75rem;
  transition: all 0.2s;
}

.view-btn:hover {
  background: #C8854A;
  color: #fff;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(44, 33, 23, 0.1);
}

.page-btn {
  padding: 6px 16px;
  background: none;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  background: #C8854A;
  border-color: #C8854A;
  color: #fff;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 0.85rem;
  color: #7a6555;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem;
}

.empty-state svg {
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.25rem;
  color: #2C2117;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #7a6555;
  margin-bottom: 1.5rem;
}

.shop-now-btn {
  display: inline-block;
  padding: 10px 24px;
  background: #C8854A;
  color: #fff;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s;
}

.shop-now-btn:hover {
  background: #b67038;
}

/* Responsive */
@media (max-width: 968px) {
  .account-container {
    padding: 1rem;
  }

  .account-sidebar {
    position: fixed;
    top: 0;
    left: -100%;
    width: 280px;
    height: 100vh;
    z-index: 99;
    border-radius: 0;
    padding: 1.5rem;
  }

  .account-sidebar.mobile-open {
    left: 0;
  }

  .close-sidebar {
    display: block;
  }

  .mobile-menu-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .sidebar-overlay {
    display: block;
  }

  .content-card {
    padding: 1.5rem;
  }

  .info-row {
    flex-direction: column;
    gap: 0.25rem;
  }

  .info-label {
    width: auto;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .orders-table th {
    display: none;
  }

  .orders-table td {
    display: block;
    padding: 0.75rem;
    text-align: right;
    position: relative;
    border-bottom: none;
  }

  .orders-table td::before {
    content: attr(data-label);
    position: absolute;
    left: 0.75rem;
    font-weight: 500;
    color: #2C2117;
  }

  .orders-table tr {
    display: block;
    margin-bottom: 1rem;
    border: 1px solid rgba(44, 33, 23, 0.1);
    border-radius: 8px;
    overflow: hidden;
  }

  .tabs {
    overflow-x: auto;
    flex-wrap: nowrap;
    padding-bottom: 0.5rem;
  }

  .date-filters {
    flex-direction: column;
  }

  .date-input {
    justify-content: space-between;
  }
}

@media (max-width: 480px) {
  .account-container {
    padding: 0.75rem;
  }

  .content-card {
    padding: 1rem;
  }

  .card-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>