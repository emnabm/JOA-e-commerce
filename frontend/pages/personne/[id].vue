<template>
  <div class="container mt-5">
    <div v-if="!loading" class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-center mb-8 text-gray-900">Edit profile</h1>

      <div class="grid gap-8 md:grid-cols-2">
        <UserProfilCard
            ref="profilCardRef"
            :fname="data.firstName"
            :lname="data.lastName"
            :email="data.email"
            :phone="data.phoneNumber"
            :address="data.adress"
        />
        <br>
        <button
            @click="modifierUser"
            class="bg-blue-600  px-4 py-2 rounded hover:bg-blue-700"
        >
          Save
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import UserProfilCard from '/components/UserProfilCard.vue'
import { useRouter } from 'vue-router'

const route = useRoute()
const id = route.params.id

const router = useRouter()
const data = ref({})
const loading = ref(false)
const profilCardRef = ref(null)

const fetchUser = async () => {
  loading.value = true
  try {
    const response = await axios.get(`http://localhost:8000/users/${id}`)
    data.value = response.data
  } catch (err) {
    console.error(err)
    alert('Erreur lors du chargement des données')
  } finally {
    loading.value = false
  }
}

const modifierUser = async () => {
  if (!profilCardRef.value) return

  const updatedData = {
    firstName: profilCardRef.value.localFname,
    lastName: profilCardRef.value.localLname,
    email: profilCardRef.value.localEmail,
    phoneNumber: profilCardRef.value.localPhone,
    adress: profilCardRef.value.localAddress
  }

  try {
    await axios.put(`http://localhost:8000/users/${id}/edit`, updatedData)
    alert('Profil mis à jour avec succès !')

    //Rediriger vers la liste après 1s
    setTimeout(() => {
      router.push('/personne')
    }, 1000)

  } catch (err) {
    console.error(err)
    alert('Erreur lors de la mise à jour')
  }
}

onMounted(() => {
  fetchUser()
})
</script>
