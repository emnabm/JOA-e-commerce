<template>
  <header>
    <nav class="navbar">
      <NuxtLink to="/" class="logo">
        <span class="logo-dot"></span>
        <img src="../public/logo.png" height="44" alt="Logo" />
      </NuxtLink>

      <!-- Liens principaux -->
      <ul class="nav-links">
        <li><NuxtLink to="/" class="nav-link" active-class="active">Home</NuxtLink></li>

        <li class="dropdown">
          <button class="nav-link dropdown-toggle">
            Categories
            <svg class="chevron" viewBox="0 0 12 12" fill="none">
              <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <ul class="dropdown-menu" v-if="data.length">
            <li v-for="cat in data" :key="cat.id">
              <NuxtLink class="dropdown-item" :to="`/category/${cat.id}`">{{ cat.name }}</NuxtLink>
            </li>
          </ul>
        </li>

        <li><NuxtLink to="/about" class="nav-link">About us</NuxtLink></li>
        <li><NuxtLink to="/contact" class="nav-link">Contact us</NuxtLink></li>
      </ul>

      <!-- Actions droite -->
      <div class="nav-actions">
        <template v-if="user">
          <NuxtLink to="/cart" class="icon-btn" title="Panier">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
              <line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
            <span v-if="items > 0" class="badge">{{ items }}</span>
          </NuxtLink>

          <NuxtLink to="/account" class="icon-btn" title="Mon compte">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
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
  </header>
</template>

<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'

const data = ref([])
// const loading = ref(false)
const user = ref(null)
const token = ref('')
const items = ref(0)

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
}

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
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #C8854A;
}

/* Nav links */
.nav-links {
  display: flex;
  align-items: center;
  list-style: none;
  gap: 2px;
  margin: 0; padding: 0;
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
.nav-link.active { font-weight: 500; }

/* Dropdown */
.dropdown { position: relative; }
.chevron {
  width: 12px; height: 12px;
  opacity: 0.5;
  transition: transform 0.15s;
}
.dropdown:hover .chevron { transform: rotate(180deg); }
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
.dropdown:hover .dropdown-menu { display: block; }
.dropdown-item {
  display: block;
  padding: 8px 12px;
  font-size: 13.5px;
  color: #5a4a3a;
  border-radius: 6px;
  text-decoration: none;
}
.dropdown-item:hover { background: #F8F2E6; color: #2C2117; }

/* Actions */
.nav-actions {
  display: flex;
  align-items: center;
  gap: 4px;
  flex-shrink: 0;
}
.icon-btn {
  width: 38px; height: 38px;
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
.icon-btn:hover { background: rgba(0,0,0,0.06); color: #2C2117; }
.badge {
  position: absolute;
  top: 4px; right: 4px;
  width: 16px; height: 16px;
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
  width: 1px; height: 24px;
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
</style>