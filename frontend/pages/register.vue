<template>
  <div class="container mt-5" style="max-width: 500px;">
    <h2 class="mb-4">Registration</h2>

    <form @submit.prevent="register">
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
            placeholder="Mot de passe"
            required
        />
      </div>
      <div class="mb-3">
        <label class="form-label">UserName</label>
        <input
            v-model="userName"
            type="text"
            class="form-control"
            placeholder="Votre adresse"
        />
      </div>
      <div class="mb-3">
        <label class="form-label">Address</label>
        <input
            v-model="address"
            type="text"
            class="form-control"
            placeholder="Votre adresse"
        />
      </div>


      <div class="mb-3">
        <label class="form-label">Phone Number</label>
        <input
            v-model="phone"
            type="tel"
            class="form-control"
            placeholder="Numéro de téléphone"
        />
      </div>

      <button type="submit" class="btn btn-primary w-100" :disabled="loading">
        <span v-if="loading">En cours...</span>
        <span v-else>Register</span>
      </button>
      <p
        v-if="message"
        :class="success ? 'text-success' : 'text-danger'"
        class="mt-3"
    >
      {{ message }}
    </p>
    </form>
    <div class="d-flex justify-content-center mt-3">
      <NuxtLink to="login">Already have an account? Log in</NuxtLink>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const userName=ref('')
const address = ref('')
const phone = ref('')
const message = ref('')
const success = ref(false)
const loading = ref(false)

const router = useRouter()

const register = async () => {
  loading.value = true
  message.value = ''
  try {
    const response = await axios.post('http://localhost:8000/api/register', {
      email: email.value,
      password: password.value,
      username:userName.value,
      adress: address.value,
      phone: phone.value
    })

    message.value = response.data.message
    success.value = true
    console.log('Token reçu :', response.data.token)

    // ✅ Si ton backend retourne un token direct, tu peux :
    localStorage.setItem('token', response.data.token)
    await router.push('/')


    email.value = ''
    password.value = ''
    address.value = ''
    phone.value = ''
  } catch (error) {
    console.error(error)
    success.value = false
    message.value = error.response?.data?.error
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
