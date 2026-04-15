<template>
  <div class="d-flex align-items-center justify-content-center vh-100">
    <div class="container" style="max-width: 500px;">
      <h2 class="mb-4 text-center">Login</h2>
      <form @submit.prevent="login">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input
              v-model="email"
              type="email"
              class="form-control"
              placeholder="Votre email"
              required
          />
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input
              v-model="password"
              type="password"
              class="form-control"
              placeholder="Votre mot de passe"
              required
          />
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="loading">
          <span v-if="loading">Connexion...</span>
          <span v-else>Login</span>
        </button>

        <p
            v-if="message"
            :class="success ? 'text-success' : 'text-danger'"
            class="mt-3 text-center"
        >
          {{ message }}
        </p>
      </form>

      <div class="d-flex justify-content-center mt-3">
        <NuxtLink to="register">Don't have an account ? Sign up</NuxtLink>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const email = ref('')
const password = ref('')
const message = ref('')
const success = ref(false)
const loading = ref(false)

const router = useRouter()

const login = async () => {
  loading.value = true
  message.value = ''

  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value
    })

    localStorage.setItem('token', response.data.token)
    localStorage.setItem('user_id', response.data.id)
    localStorage.setItem('user_email', response.data.email)
    localStorage.setItem('user_role', response.data.roles)
    localStorage.setItem('user_username', response.data.username)

    console.log()
    message.value = 'Connexion réussie !'
    success.value = true
    if (localStorage.getItem('user_role').includes("ROLE_ADMIN")) {
      router.push('/admin/dashboard')
    }
    else {
      router.push('/')
    }
  } catch (error) {
    success.value = false
    if (error.response?.data?.error) {
      message.value = error.response.data.error
    } else {
      message.value = 'Erreur lors de la connexion'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.text-success {
  color: green;
}
.text-danger {
  color: red;
}
</style>
