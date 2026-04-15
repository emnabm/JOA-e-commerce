<template>
  <div class="d-flex min-vh-100">
    <!-- Sidebar -->
    <aside class="sidebar position-sticky d-flex flex-column gap-5 ps-4 pt-4">
      <h2 class="mb-4 white">Account</h2>
      <ul class="list-unstyled d-flex flex-column gap-5">
        <li>
          <button
              class="btn text-start rounded-start-pill"
              :class="{ active: section === 'profile' }"
              @click="section = 'profile'"
          >Profile</button>
        </li>
        <li>
          <button
              class="btn w-100 text-start rounded-start-pill"
              :class="{ active: section === 'orders' }"
              @click="section = 'orders'"
          >Order History</button>
        </li>
        <li>
          <button
              class="btn w-100 text-start rounded-start-pill"
              :class="{ active: section === 'checkout' }"
              @click="section = 'checkout'"
          >Checkout</button>
        </li>
        <li>
          <button
              class="btn w-100 text-start rounded-start-pill"
              :class="{ active: section === 'password' }"
              @click="updatePassword"
          >Update Password</button>
        </li>
        <li>
          <button class="btn w-100 text-start rounded-start-pill" @click="logout">Logout</button>
        </li>
      </ul>
    </aside>

    <!-- Content -->
    <main class="flex-grow-1 p-5 bg-light">
      <!-- Profile -->
      <div v-if="section === 'profile'" class="card p-5 shadow-sm gap-4">
        <h3 class="green">Profile</h3>
        <div class="d-flex flex-column gap-2">
          <p><strong>Name:</strong> {{ username }}</p>
          <p><strong>Email:</strong> {{ email }}</p>
          <p><strong>Total Orders:</strong> {{ info.totalOrders }}</p>
          <p><strong>Total Spent:</strong> {{ info.totalSpent }} dt</p>
          <p><strong>Last Order Status:</strong> {{ info.lastOrderStatus }}</p>
          <p><strong>Member Since:</strong> {{ info.memberSince }}</p>

          <button class="btn green edit" @click="edit">Edit Profile</button>
        </div>
      </div>

      <!-- Orders -->
      <div v-else-if="section === 'orders'" class="card p-4 shadow-sm">
        <h3 class="mb-4 green">Order History</h3>

        <!-- Onglets filtre -->
        <ul class="nav nav-tabs mb-3">
          <li class="nav-item" v-for="tab in tabs" :key="tab.key">
            <button
                class="nav-link"
                :class="{ active: activeTab === tab.key }"
                @click="setTab(tab.key)"
            >
              {{ tab.label }}
            </button>
          </li>
        </ul>

        <!-- Filtres date -->
        <div class="d-flex gap-3 mb-3 flex-wrap">
          <div class="flex-fill">
            <input type="date" v-model="fromDate" class="form-control" />
          </div>
          <div class="flex-fill">
            <input type="date" v-model="toDate" class="form-control" />
          </div>
        </div>

        <!-- Tableau -->
        <table class="table table-hover">
          <thead>
          <tr class="green">
            <th>Date</th>
            <th>Address</th>
            <th>Status</th>
            <th>Delivery</th>
            <th>Price</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="order in filteredOrders" :key="order.id">
            <td :class="{ 'table-active': order.status === 'delivered' }">{{ order.date }}</td>
            <td :class="{ 'table-active': order.status === 'delivered' }">{{ order.address }}</td>
            <td :class="{ 'table-active': order.status === 'delivered' }">{{ order.status }}</td>
            <td :class="{ 'table-active': order.status === 'delivered' }">{{ order.delivery }}</td>
            <td :class="{ 'table-active': order.status === 'delivered' }">{{ order.price.toFixed(3) }}</td>
          </tr>
          </tbody>
        </table>
      </div>

      <!-- Checkout -->
      <div v-else-if="section === 'checkout'">
        <Checkout
            page="/account"
            :query="{ section: 'orders' }"
        />
      </div>

    </main>
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

const section = ref('profile')

const email = localStorage.getItem('user_email')
const username = localStorage.getItem('user_username')
const token = localStorage.getItem('token')

const orders = ref([])
const activeTab = ref('all')
const fromDate = ref('')
const toDate = ref('')

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

const fetchProfile = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/getInfos', {
      headers: { Authorization: `Bearer ${token}` },
    })
    info.value = response.data
  } catch (e) {
    console.error('Error loading profile info', e)
  }
}

const fetchOrders = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/my_orders', {
      headers: { Authorization: `Bearer ${token}` },
    })
    orders.value = response.data.sort(
        (a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()
    )
  } catch (e) {
    console.error('Error loading orders', e)
  }
}

const setTab = (tab: string) => {
  activeTab.value = tab
}

const filteredOrders = computed(() => {
  return orders.value.filter((order) => {
    if (activeTab.value === 'delivered' && order.status !== 'delivered') return false
    if (activeTab.value === 'cancelled' && order.status !== 'cancelled') return false
    if (activeTab.value === 'pending' && order.status !== 'pending') return false

    if (fromDate.value && new Date(order.date) < new Date(fromDate.value)) return false
    if (toDate.value && new Date(order.date) > new Date(toDate.value)) return false

    return true
  })
})

watch(section, async (newSection) => {
  if (newSection === 'orders') {
    await fetchOrders()
  } else if (newSection === 'profile') {
    await fetchProfile()
  }
})

watch(
    () => route.query.section,
    (newSection) => {
      if (newSection && typeof newSection === 'string') {
        section.value = newSection
      }
    },
    { immediate: true }
)

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

onMounted(fetchProfile)
</script>

<style>
aside{
  top: 0;
  height: 100vh;
}

.sidebar {
  width: 350px;
  background: #198754;
  border-right: 1px solid #ddd;
}

.sidebar .btn {
  width: 100%;
  border: none;
  color: #fff;
  padding: 0.75rem;
  text-align: left;
  font-size: 1rem;
  transition: background-color 0.2s, color 0.2s;
}

.sidebar .btn:hover {
  background-color: #198553;
}

.sidebar .btn.active {
  background-color: #fff;
  color: #198754;
}

.white {
  color: #fff;
}

.green {
  color: #198754;
}

.card {
  max-width: 900px;
}

.edit:hover {
  background-color: #198754;
  color: #fff;
}
</style>
