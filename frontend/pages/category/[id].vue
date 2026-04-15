<template>
  <div>
    <Header/>
    <div class="d-flex justify-content-end mt-4 me-5">
      <div class="dropdown">
        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <span v-if="currentSort === 'default'">Default sorting</span>
          <span v-else-if="currentSort === 'lowToHigh'">Price: Low to High </span>
          <span v-else-if="currentSort === 'highToLow'">Price: High to Low </span>
          <span v-else-if="currentSort === 'alphabetical'">Alphabetical A–Z </span>
        </button>

        <ul class="dropdown-menu">
          <li><button class="dropdown-item" @click="sortLowToHigh">Sort by price: low to high</button></li>
          <li><button class="dropdown-item" @click="sortHighToLow">Sort by price: high to low</button></li>
          <li><button class="dropdown-item" @click="sortAlphabetical">Sort A–Z</button></li>
          <li><button class="dropdown-item" @click="resetSorting">Reset sorting</button></li>
        </ul>
      </div>
    </div>

    <div v-if="!loading" class="cards">
      <product-card
          v-for="prod in data"
          :key="prod.id"
          :id="prod.id"
          :name="prod.nom"
          :img="prod.image"
          :description="prod.description"
          :prix="prod.prix"
          :stock="prod.stock"
          :unit="prod.unit"
      />
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { useRoute } from '#vue-router'

const route = useRoute()
const id = route.params.id

const data = ref([])
const originalData = ref([])
const loading = ref(false)

const currentSort = ref('default')
const getProducts = async () => {
  try {
    loading.value = true
    const response = await axios.get(`http://localhost:8000/products_${id}`)
    data.value = response.data
    originalData.value = [...response.data] // copie pour reset
  } catch (err) {
    console.error(err)
    alert('Failed to get products')
  } finally {
    loading.value = false
  }
}
const sortLowToHigh = () => {
  data.value.sort((a, b) => a.prix - b.prix)
  currentSort.value = 'lowToHigh'
}

const sortHighToLow = () => {
  data.value.sort((a, b) => b.prix - a.prix)
  currentSort.value = 'highToLow'
}

const sortAlphabetical = () => {
  data.value.sort((a, b) => {
    const nameA = a.nom.toUpperCase()
    const nameB = b.nom.toUpperCase()
    if (nameA < nameB) return -1
    if (nameA > nameB) return 1
    return 0
  })
  currentSort.value = 'alphabetical'
}

const resetSorting = () => {
  data.value = [...originalData.value]
  currentSort.value = 'default'
}

onMounted(getProducts)
</script>

<style >
.cards {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 30px;
  margin-top: 80px;
}
</style>
