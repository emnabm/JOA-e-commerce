<template>
  <div>
    <nav class="navbar navbar-expand-lg bg-body-tertiary pe-5 ps-4">
      <div v-if="!loading" class="container-fluid" >
        <NuxtLink to="/">
          <img src="../public/Adobe%20Express%20-%20file.png" width="120" height="60" />
        </NuxtLink>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <NuxtLink class="nav-link active" aria-current="page" to="/">Home</NuxtLink>
            </li>

            <li class="nav-item dropdown">
              <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  role="button"
                  data-bs-toggle="dropdown"
              >
                Categories
              </a>
              <ul class="dropdown-menu">
                <li v-for="cat in data" :key="cat.id">
                  <NuxtLink class="dropdown-item" :to="`/category/${cat.id}`">
                    {{ cat.name }}
                  </NuxtLink>
                </li>
              </ul>
            </li>

            <li class="nav-item">
              <NuxtLink class="nav-link" to="#">About Us</NuxtLink>
            </li>
          </ul>

          <!-- user part -->
          <ul v-if="user" class="navbar-nav mb-2 mb-lg-0 gap-5">
            <li class="nav-item position-relative">
              <NuxtLink to="/cart" class="nav-link p-0">
                <img src="../public/panier.png" alt="Panier" width="40" class="img-fluid " />
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ items }}</span>
              </NuxtLink>
            </li>

            <li  class="nav-item position-relative">
              <NuxtLink to="/account">
              <img
                  src="../public/user_icon.png"
                  width="40"
                  class="img-fluid ">
              </NuxtLink></li>
              <li class="nav-item">
              <NuxtLink @click.prevent="logout">
                <p>logout</p>

              </NuxtLink>
                </li>
              </ul>
            <ul v-else class="nav-item text-center">
              <NuxtLink class="nav-link" to="/login">Login</NuxtLink>
            </ul>
        </div>
      </div>
    </nav>
  </div>
</template>

<script setup >
import axios from 'axios'
import { ref, onMounted } from 'vue'


const data = ref([])
const loading = ref(false)
const user = ref(null)
const token = ref('')
const items=ref(0)

user.value=localStorage.getItem('user_username')
const fetchCategory = async () => {
  try {
    loading.value = true
    const response = await axios.get('http://localhost:8000/category')
    data.value = response.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}
const fetchCart = async () => {
  try {
    loading.value = true
    const response = await axios.get('http://localhost:8000/api/getCart', {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    items.value = response.data.items.length
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}
const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user_id')

  localStorage.removeItem('user_email')
  localStorage.removeItem('user_role')
  localStorage.removeItem('user_username')


  token.value = null
  user.value = null
}

onMounted(() => {
  token.value = localStorage.getItem('token')
  console.log(token.value)
  fetchCategory()
  user.value=localStorage.getItem('user_username')
  fetchCart()

})
</script>
