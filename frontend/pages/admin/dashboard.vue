<template>
  <div class="d-flex min-vh-100">
    <aside class="sidebar position-sticky d-flex flex-column gap-5 ps-4 pt-4">
      <h2 class="mb-4 white">Account</h2>
      <ul class="list-unstyled d-flex flex-column gap-4">
        <li>
          <button
              class="btn text-start rounded-start-pill"
              :class="{ active: section === 'dashboard' }"
              @click="section = 'dashboard'"
          > Dashboard </button>
        </li>
        <li>
          <button
              class="btn w-100 text-start rounded-start-pill"
              :class="{ active: section === 'inventory' }"
              @click="section = 'inventory'"
          > Users </button>
        </li>
        <li>
          <button
              class="btn w-100 text-start rounded-start-pill"
              :class="{ active: section === 'orders' }"
              @click="section = 'orders'"
          > Orders </button>
        </li>
        <li>
          <button
              class="btn w-100 text-start rounded-start-pill"
              :class="{ active: section === 'categories' }"
              @click="section = 'categories'"
          > Categories </button>
        </li>
        <li>
          <button
              class="btn w-100 text-start rounded-start-pill"
              :class="{ active: section === 'products' }"
              @click="section = 'products'"
          > Products </button>
        </li>
        <li>
          <button
              class="btn w-100 text-start rounded-start-pill"
              @click="logout"
          > Logout </button>
        </li>
      </ul>
    </aside>

    <!-- Composant dynamique -->
    <component :is="currentComponent" class="flex-fill" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
// Importe tes composants


import Acceuil from '~/components/acceuil.vue'
import Inventory from '~/components/inventory.vue'
import Orders from '~/components/orders.vue'
import Categories from '~/components/categories.vue'
import Products from '~/components/products.vue'
import {useRouter} from "#vue-router";
definePageMeta(
    {
      middleware: ['admin','auth']
    }
)


const router = useRouter()
const section = ref('dashboard')

// Calcul du composant à afficher
const currentComponent = computed(() => {
  switch (section.value) {
    case 'dashboard':
      return Acceuil
    case 'inventory':
      return Inventory
    case 'orders':
      return Orders
    case 'categories':
      return Categories
    case 'products':
      return Products
    default:
      return Acceuil
  }
})

const logout = () => {
  localStorage.clear()
  router.push('/login')
}

</script>



<style >
.sidebar {
  top: 0;
  height: 100vh;
  width: 300px;
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
.white{
  color: #fff;
}
.card {
  max-width: 700px;
}
.edit:hover{
  background-color: #198754;
  color: #fff;
}



.card {
  max-width: 900px;
}

</style>