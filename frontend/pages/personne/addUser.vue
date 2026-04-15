<template>
  <div class="container mx-auto p-8 max-w-lg">
    <h2 class="text-2xl font-bold mb-6">Ajouter un utilisateur</h2>

    <form @submit.prevent="addUser" class="space-y-4">

      <div class="d-flex flex-column mb-3">
        <label class="block mb-1 font-medium">First Name:</label>
        <input v-model="firstName" type="text" class="w-full border rounded p-2" required>
      </div>

      <div class="d-flex flex-column mb-3">
        <label class="block mb-1 font-medium">Last Name:</label>
        <input v-model="lastName" type="text" class="w-full border rounded p-2" required>
      </div>

      <div class="d-flex flex-column mb-3">
        <label class="block mb-1 font-medium">Email:</label>
        <input v-model="email" type="email" class="w-full border rounded p-2" required>
      </div>

      <div class="d-flex flex-column mb-3">
      <label class="block mb-1 font-medium">Phone Number:</label>
      <input v-model="phone" type="text" class="w-full border rounded p-2" required>
    </div>

      <div class="d-flex flex-column mb-3">
        <label class="block mb-1 font-medium">Address</label>
        <input v-model="adress" type="text" class="w-full border rounded p-2" required>
      </div>

      <button type="submit" class="btn btn-outline-primary btn-lg bg-blue-600  px-4 py-2 rounded hover:bg-blue-700 transition duration-200">
        Enregistrer
      </button>
    </form>

    <p v-if="message" class="mt-4 text-green-600">{{ message }}</p>
    <p v-if="error" class="mt-4 text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

definePageMeta({
  name: 'personne-addUser'
})

const firstName = ref('')
const lastName = ref('')
const email = ref('')
const phone = ref('')
const adress = ref('')
const message = ref('')
const error = ref('')
const router = useRouter()

const addUser = async () => {
  try {
    const response = await axios.post('http://localhost:8000/users/add', {
      firstName: firstName.value,
      lastName: lastName.value,
      email: email.value,
      phoneNumber: phone.value,
      adress: adress.value
    })

    message.value = 'Utilisateur ajouté avec succès !'
    error.value = ''

    // Rediriger vers la liste après 1s
    setTimeout(() => {
      router.push('/personne')
    }, 2000)

  } catch (err) {
    console.error(err)
    error.value = 'Erreur lors de l\'ajout'
    message.value = ''
  }
}
</script>
