<template>
  <div class="w-50 mx-auto mt-5">
    <Header/>
    <h2>Mes informations</h2>
    <div v-if="loading">Chargement...</div>
    <div v-else-if="error">{{ error }}</div>
    <div v-else>
      <p><strong>ID:</strong> {{ me.id }}</p>
      <p><strong>Email:</strong> {{ me.email }}</p>
      <p><strong>Roles:</strong> {{ me.roles.join(', ') }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
const { $api } = useNuxtApp()

const me = ref({ id: null, email: '', roles: [] })
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const apiToken = localStorage.getItem('token') // ⬅️ 1️⃣ récupère le token

    if (!apiToken) {
      throw new Error('Aucun token trouvé')
    }

    const response = await $api.get('http://localhost:8000/api/me', {
      headers: { Authorization: `Bearer ${apiToken}` }
    })
    me.value = response.data
  } catch (err: any) {
    error.value = err.response?.data?.error || 'Erreur lors de la récupération des infos'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
h2 {
  margin-bottom: 20px;
}
</style>
