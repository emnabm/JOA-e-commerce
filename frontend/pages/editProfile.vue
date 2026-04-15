<template>
  <div class="container py-5">
    <h2 class="mb-4 green">Edit Profile</h2>

    <form @submit.prevent="saveProfile" class="card p-4 shadow-sm">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input v-model="form.username" type="text" class="form-control" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input v-model="form.email" type="email" class="form-control" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Address</label>
        <input v-model="form.adress" type="text" class="form-control" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Phone Number </label>
        <input v-model="form.phone" type="text" class="form-control" required />
      </div>


      <button type="submit" class="btn btn-success w-100">Save Changes</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from '#vue-router'
import axios from 'axios'

const router = useRouter()

const token = localStorage.getItem('token')

const form = ref({
  username: '',
  email: '',
  phone:'',
  adress:''
})

// Charger les infos actuelles
onMounted(async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/me', {
      headers: { Authorization: `Bearer ${token}` }
    })
    form.value.username = response.data.username
    form.value.email = response.data.email
    form.value.adress = response.data.address
    form.value.phone = response.data.phone
  } catch (e) {
    console.error('Error loading profile', e)
  }
})

// Sauvegarder
const saveProfile = async () => {
  try {
    await axios.put(
        'http://localhost:8000/api/updateProfile',
        {
          username: form.value.username,
          email: form.value.email,
          adress: form.value.adress,
          phone: form.value.phone
        },
        {
          headers: { Authorization: `Bearer ${token}` }
        }
    )
    alert('Profile updated successfully')
    router.push('/account') // ou ton chemin principal
  } catch (e) {
    console.error('Error saving profile', e)
    alert('Could not save profile')
  }
}
</script>

<style scoped>
.green {
  color: #198754;
}
</style>
