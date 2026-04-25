<template>
  <header>
    <nav class="navbar">
      <NuxtLink to="/" class="logo">
        <span class="logo-dot"></span>
        <img src="../public/logo.png" height="44" alt="Logo" >
      </NuxtLink>

      <!-- Bouton menu mobile -->
      <button class="mobile-menu-btn" @click="mobileMenuOpen = !mobileMenuOpen">
        <svg v-if="!mobileMenuOpen" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
        <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>

      <!-- Liens principaux (responsive) -->
      <ul class="nav-links" :class="{ 'mobile-open': mobileMenuOpen }">
        <li><NuxtLink to="/" class="nav-link" active-class="active" @click="mobileMenuOpen = false">Home</NuxtLink></li>

        <li class="dropdown" @click="toggleDropdown">
          <button class="nav-link dropdown-toggle" @click.stop>
            Categories
            <!-- <svg class="chevron" :class="{ rotated: dropdownOpen }" viewBox="0 0 12 12" fill="none">
              <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg> -->
          </button>
          <ul v-if="data.length" class="dropdown-menu" :class="{ 'show': dropdownOpen }" >
            <li v-for="cat in data" :key="cat.id">
              <NuxtLink class="dropdown-item" :to="`/category/${cat.id}`" @click="closeMobileMenu">{{ cat.name }}</NuxtLink>
            </li>
          </ul>
        </li>

        <li><NuxtLink to="#about" class="nav-link" @click="mobileMenuOpen = false">About us</NuxtLink></li>
        <li><NuxtLink to="#contact" class="nav-link" @click="mobileMenuOpen = false">Contact us</NuxtLink></li>

        <!-- Actions mobile (affichées uniquement dans le menu mobile) -->
        <div class="nav-actions-mobile">
          <template v-if="user">
            <NuxtLink to="/cart" class="mobile-action" @click="mobileMenuOpen = false">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
              </svg>
              Cart
              <span v-if="items > 0" class="mobile-badge">{{ items }}</span>
            </NuxtLink>
            <NuxtLink to="/account" class="mobile-action" @click="mobileMenuOpen = false">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              My account
            </NuxtLink>
            <button class="mobile-action logout-mobile" @click="logout">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
              Logout
            </button>
          </template>
          <template v-else>
            <NuxtLink to="/login" class="mobile-action" @click="mobileMenuOpen = false">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                <polyline points="10 17 15 12 10 7"/>
                <line x1="15" y1="12" x2="3" y2="12"/>
              </svg>
              Login
            </NuxtLink>
          </template>
        </div>
      </ul>

      <!-- Actions droite (desktop seulement) -->
      <div class="nav-actions">
        <template v-if="user">
          <NuxtLink to="/cart" class="icon-btn" title="Panier">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
              <line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
            <span v-if="items > 0" class="badge">{{ items }}</span>
          </NuxtLink>

          <NuxtLink to="/account" class="icon-btn" title="Mon compte">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </NuxtLink>

          <div class="divider"></div>
          <button class="logout-btn" @click="logout">Logout</button>
        </template>

        <template v-else>
          <NuxtLink to="/login" class="login-btn">Login</NuxtLink>
        </template>
      </div>
    </nav>

    <!-- Overlay pour fermer le menu mobile -->
    <div v-if="mobileMenuOpen" class="mobile-overlay" @click="mobileMenuOpen = false"></div>
  </header>
</template>

<script setup>
import axios from 'axios'
import { ref, onMounted, watch } from 'vue'

const data = ref([])
const user = ref(null)
const token = ref('')
const items = ref(0)
const mobileMenuOpen = ref(false)
const dropdownOpen = ref(false)

const fetchCategory = async () => {
  try {
    const response = await axios.get('http://localhost:8000/category')
    data.value = response.data
  } catch (err) {
    console.error(err)
  }
}

const fetchCart = async () => {
  if (!token.value) return
  try {
    const response = await axios.get('http://localhost:8000/api/getCart', {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    items.value = response.data.items.length
  } catch (err) {
    console.error(err)
  }
}

const logout = () => {
  ['token', 'user_id', 'user_email', 'user_role', 'user_username'].forEach(k => localStorage.removeItem(k))
  token.value = null
  user.value = null
  items.value = 0
  mobileMenuOpen.value = false
}

const toggleDropdown = () => {
  if (mobileMenuOpen.value) {
    dropdownOpen.value = !dropdownOpen.value
  }
}

const closeMobileMenu = () => {
  mobileMenuOpen.value = false
  dropdownOpen.value = false
}

// Empêcher le scroll quand le menu mobile est ouvert
watch(mobileMenuOpen, (isOpen) => {
  if (isOpen) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(() => {
  token.value = localStorage.getItem('token')
  user.value = localStorage.getItem('user_username')
  fetchCategory()
  fetchCart()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500&display=swap');

* { box-sizing: border-box; }

.navbar {
  font-family: 'DM Sans', sans-serif;
  background: #F8F2E6;
  border-bottom: 1px solid rgba(0,0,0,0.08);
  padding: 0 2rem;
  height: 68px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  position: relative;
  z-index: 1001;
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  flex-shrink: 0;
}

.logo-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #C8854A;
}

/* Bouton menu mobile */
.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  color: #5a4a3a;
  z-index: 1002;
}

/* Nav links desktop */
.nav-links {
  display: flex;
  align-items: center;
  list-style: none;
  gap: 2px;
  margin: 0;
  padding: 0;
}

.nav-link {
  font-size: 14px;
  font-weight: 400;
  color: #5a4a3a;
  text-decoration: none;
  padding: 6px 14px;
  border-radius: 6px;
  background: transparent;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: background 0.15s, color 0.15s;
}

.nav-link:hover, .nav-link.active {
  background: rgba(0,0,0,0.05);
  color: #2C2117;
}

.nav-link.active { 
  font-weight: 500; 
}

/* Dropdown */
.dropdown { 
  position: relative; 
}

.chevron {
  width: 12px;
  height: 12px;
  opacity: 0.5;
  transition: transform 0.15s;
}

.chevron.rotated {
  transform: rotate(180deg);
}

.dropdown-menu {
  display: none;
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  background: #fff;
  border: 0.5px solid rgba(0,0,0,0.12);
  border-radius: 10px;
  padding: 6px;
  min-width: 160px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  list-style: none;
  z-index: 100;
}

.dropdown-menu.show {
  display: block;
}

.dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-item {
  display: block;
  padding: 8px 12px;
  font-size: 13.5px;
  color: #5a4a3a;
  border-radius: 6px;
  text-decoration: none;
}

.dropdown-item:hover { 
  background: #F8F2E6; 
  color: #2C2117; 
}

/* Actions desktop */
.nav-actions {
  display: flex;
  align-items: center;
  gap: 4px;
  flex-shrink: 0;
}

.icon-btn {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  border: none;
  background: transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  color: #5a4a3a;
  text-decoration: none;
  transition: background 0.15s, color 0.15s;
}

.icon-btn:hover { 
  background: rgba(0,0,0,0.06); 
  color: #2C2117; 
}

.badge {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #C8854A;
  color: #fff;
  font-size: 10px;
  font-weight: 500;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1.5px solid #F8F2E6;
}

.divider {
  width: 1px;
  height: 24px;
  background: rgba(0,0,0,0.1);
  margin: 0 8px;
}

.login-btn, .logout-btn {
  font-size: 13.5px;
  font-weight: 500;
  color: #2C2117;
  text-decoration: none;
  padding: 7px 18px;
  border-radius: 8px;
  border: 1px solid rgba(44,33,23,0.25);
  background: transparent;
  cursor: pointer;
  transition: all 0.15s;
}

.login-btn:hover, .logout-btn:hover {
  background: #2C2117;
  color: #F8F2E6;
}

/* Actions mobile (cachées par défaut) */
.nav-actions-mobile {
  display: none;
  flex-direction: column;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(0,0,0,0.08);
}

.mobile-action {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  font-size: 14px;
  color: #5a4a3a;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.15s;
}

.mobile-action:hover {
  background: rgba(0,0,0,0.05);
  color: #2C2117;
}

.mobile-badge {
  background: #C8854A;
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 12px;
  margin-left: auto;
}

.logout-mobile {
  background: none;
  border: none;
  cursor: pointer;
  width: 100%;
  text-align: left;
  font-family: 'DM Sans', sans-serif;
}

.logout-mobile:hover {
  background: rgba(244, 67, 54, 0.1);
  color: #f44336;
}

/* Overlay mobile */
.mobile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
}

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
  .navbar {
    padding: 0 1rem;
  }

  .mobile-menu-btn {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .nav-links {
    position: fixed;
    top: 68px;
    left: -100%;
    width: 280px;
    height: calc(100vh - 68px);
    background: #fff;
    flex-direction: column;
    align-items: stretch;
    padding: 1.5rem;
    transition: left 0.3s ease;
    box-shadow: 2px 0 12px rgba(0, 0, 0, 0.1);
    z-index: 1001;
    overflow-y: auto;
    gap: 4px;
  }

  .nav-links.mobile-open {
    left: 0;
  }

  .nav-link {
    justify-content: space-between;
    padding: 12px 16px;
    font-size: 15px;
  }

  .dropdown {
    width: 100%;
  }

  .dropdown-toggle {
    width: 100%;
    justify-content: space-between;
  }

  .dropdown-menu {
    position: static;
    box-shadow: none;
    background: #F8F2E6;
    margin-top: 4px;
    margin-left: 16px;
    width: calc(100% - 16px);
  }

  .dropdown:hover .dropdown-menu {
    display: none;
  }

  .dropdown-menu.show {
    display: block;
  }

  .nav-actions-mobile {
    display: flex;
  }

  .nav-actions {
    display: none;
  }
}

@media (max-width: 480px) {
  .nav-links {
    width: 85%;
  }
}
</style>