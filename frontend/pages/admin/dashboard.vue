<!-- pages/admin/index.vue -->
<template>
  <div class="admin-layout">
    <!-- Sidebar mobile toggle -->
    <button class="mobile-sidebar-toggle" @click="sidebarOpen = !sidebarOpen">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="6" x2="21" y2="6"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>

    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" :class="{ 'sidebar-open': sidebarOpen }">
      <div class="sidebar-header">
        <div class="logo">
          <img src="/logo.png" alt="Logo" height="40">
          <span>Admin Panel</span>
        </div>
        <button class="close-sidebar" @click="sidebarOpen = false">✕</button>
      </div>

      <div class="admin-info">
        <div class="admin-avatar">{{ adminName?.charAt(0) || 'A' }}</div>
        <div class="admin-details">
          <h4>{{ adminName || 'Administrator' }}</h4>
          <p>Super Admin</p>
        </div>
      </div>

      <nav class="sidebar-nav">
        <button 
          v-for="item in menuItems" 
          :key="item.key"
          class="nav-item"
          :class="{ active: currentSection === item.key }"
          @click="changeSection(item.key)"
        >
          <span class="nav-icon" v-html="item.icon"></span>
          <span class="nav-label">{{ item.label }}</span>
          <span v-if="item.badge" class="nav-badge">{{ item.badge }}</span>
        </button>
      </nav>

      <div class="sidebar-footer">
        <button class="nav-item logout" @click="logout">
          <span class="nav-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
          </span>
          <span class="nav-label">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">


      <div class="admin-content">
        <component 
          :is="currentComponent" 
          :global-search="globalSearch"
          @refresh="refreshCurrentSection"
        />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, shallowRef, onMounted } from 'vue'
import { useRouter } from 'vue-router'

// Import components
import AdminDashboard from '~/components/admin/AdminDashboard.vue'
import AdminUsers from '~/components/admin/AdminUsers.vue'
import AdminOrders from '~/components/admin/AdminOrders.vue'
import AdminCategories from '~/components/admin/AdminCategories.vue'
import AdminProducts from '~/components/admin/AdminProducts.vue'
import AdminReviews from '~/components/admin/AdminReviews.vue'
import AdminContact from '~/components/admin/AdminContact.vue'

definePageMeta({
  middleware: ['admin', 'auth']
})

const router = useRouter()
const sidebarOpen = ref(false)
const currentSection = ref('dashboard')
const globalSearch = ref('')
const adminName = ref('')

const menuItems = [
  { key: 'dashboard', label: 'Dashboard', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>' },
  { key: 'users', label: 'Users', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' },
  { key: 'orders', label: 'Orders', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>' },
  { key: 'products', label: 'Products', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>' },
  { key: 'categories', label: 'Categories', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>' },
  { key: 'reviews', label: 'Reviews', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><polyline points="12 9 14 13 18 13 15 15.5 16 18 12 16 8 18 9 15.5 6 13 10 13 12 9"/></svg>', badge: 'New' },
  { key: 'contact', label: 'Messages', icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>' }
]

const currentTitle = computed(() => {
  const titles = {
    dashboard: 'Dashboard',
    users: 'Users Management',
    orders: 'Order Management',
    products: 'Product Management',
    categories: 'Category Management',
    reviews: 'Customer Reviews',
    contact: 'Contact Messages'
  }
  return titles[currentSection.value] || 'Admin Panel'
})

const currentSubtitle = computed(() => {
  const subtitles = {
    dashboard: 'Overview of your store performance',
    users: 'Manage customer and admin accounts',
    orders: 'Track and process customer orders',
    products: 'Add, edit or remove products',
    categories: 'Organize your product categories',
    reviews: 'Manage customer feedback and ratings',
    contact: 'View and respond to contact messages'
  }
  return subtitles[currentSection.value] || ''
})

const currentComponent = shallowRef(AdminDashboard)

const componentMap = {
  dashboard: AdminDashboard,
  users: AdminUsers,
  orders: AdminOrders,
  products: AdminProducts,
  categories: AdminCategories,
  reviews: AdminReviews,
  contact: AdminContact
}

const changeSection = (section) => {
  currentSection.value = section
  currentComponent.value = componentMap[section] || AdminDashboard
  globalSearch.value = ''
  sidebarOpen.value = false
}

const onGlobalSearch = () => {
  // Search logic passed to child components
}

const refreshCurrentSection = () => {
  // Refresh current component data
}

const logout = () => {
  localStorage.clear()
  router.push('/login')
}

onMounted(() => {
  adminName.value = localStorage.getItem('user_username') || 'Admin'
})
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background: #F8F2E6;
}

/* Sidebar */
.admin-sidebar {
  width: 280px;
  background: #2C2117;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  z-index: 100;
  transition: transform 0.3s ease;
}

.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid rgba(248, 242, 230, 0.1);
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.logo span {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  font-weight: 600;
  color: #F8F2E6;
}

.close-sidebar {
  display: none;
  background: none;
  border: none;
  color: #F8F2E6;
  font-size: 1.25rem;
  cursor: pointer;
}

.admin-info {
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  border-bottom: 1px solid rgba(248, 242, 230, 0.1);
}

.admin-avatar {
  width: 48px;
  height: 48px;
  background: #C8854A;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  font-weight: 600;
  color: #fff;
}

.admin-details h4 {
  margin: 0;
  font-size: 0.9rem;
  color: #F8F2E6;
}

.admin-details p {
  margin: 0;
  font-size: 0.7rem;
  color: rgba(248, 242, 230, 0.6);
}

.sidebar-nav {
  flex: 1;
  padding: 1rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  background: none;
  border: none;
  border-radius: 10px;
  color: rgba(248, 242, 230, 0.7);
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
  width: 100%;
  text-align: left;
}

.nav-item:hover {
  background: rgba(200, 133, 74, 0.15);
  color: #C8854A;
}

.nav-item.active {
  background: #C8854A;
  color: #fff;
}

.nav-badge {
  margin-left: auto;
  background: #4CAF50;
  color: #fff;
  font-size: 0.65rem;
  padding: 0.125rem 0.5rem;
  border-radius: 20px;
}

.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid rgba(248, 242, 230, 0.1);
}

.nav-item.logout:hover {
  background: rgba(244, 67, 54, 0.2);
  color: #f44336;
}

/* Mobile */
.mobile-sidebar-toggle {
  display: none;
  position: fixed;
  top: 1rem;
  left: 1rem;
  z-index: 101;
  width: 40px;
  height: 40px;
  background: #C8854A;
  border: none;
  border-radius: 8px;
  color: #fff;
  cursor: pointer;
  align-items: center;
  justify-content: center;
}

.sidebar-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 99;
}

/* Main Content */
.admin-main {
  flex: 1;
  margin-left: 280px;
  padding: 1.5rem;
}

.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.admin-header h1 {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  font-weight: 500;
  color: #2C2117;
  margin: 0;
}

.header-subtitle {
  color: #7a6555;
  margin: 0.25rem 0 0;
  font-size: 0.85rem;
}

.search-box {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #fff;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 10px;
  padding: 0.5rem 1rem;
  min-width: 250px;
}

.search-box input {
  border: none;
  outline: none;
  background: transparent;
  font-size: 0.85rem;
  width: 100%;
}

.admin-content {
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

@media (max-width: 768px) {
  .mobile-sidebar-toggle {
    display: flex;
  }
  
  .sidebar-overlay {
    display: block;
  }
  
  .admin-sidebar {
    transform: translateX(-100%);
  }
  
  .admin-sidebar.sidebar-open {
    transform: translateX(0);
  }
  
  .close-sidebar {
    display: block;
  }
  
  .admin-main {
    margin-left: 0;
    padding: 1rem;
    padding-top: 4rem;
  }

  
  .search-box {
    width: 100%;
  }
}
</style>