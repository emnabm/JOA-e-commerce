<template>
  <div v-if="!loading">
    <Header/>
    <img src="../public/Nourriture saine et fraîche.png" alt="Ecolive Slogan" style="width: 100%; height: auto;" />
    <h1 class="text-2xl font-bold mt-5 position-absolute  start-50 translate-middle">Categories</h1><br>
    <div class="cards " >
      <category-card
          v-for="cat in data"
          :key="cat.id"
          :id="cat.id"
          :name="cat.name"
          :img="cat.img"
      />
    </div>
<!--    Top Rate-->
    <h2 class="text-center mt-5">Top Rate</h2>
    <div class="cards">
      <product-card
          v-for="product in topRate"
          :key="product.id"
          :id="product.id"
          :name="product.nom"
          :img="product.img"
          :description="product.description"
          :prix="product.prix"
          :stock="product.stock"
          :unit="product.unit"
      />

    </div>



  </div>
</template>


<script setup>

import axios from "axios";
const data=ref([]);
const loading=ref(false)
const topRate=ref([])

const fetchCategory= async ()=>
{
  try {
    loading.value=true;
    const response = await axios.get(`http://localhost:8000/category`)
    data.value = response.data
  } catch (err) {
    console.error(err)
    alert('Erreur lors du chargement des données')
  }finally {
    loading.value=false
  }
}

const fetchTopRate=async ()=>{
  try {
    loading.value=true;
    const response = await axios.get(`http://localhost:8000/topRate`)
    topRate.value = response.data
  } catch (err) {
    console.error(err)
    alert('Erreur lors du chargement des données')
  }finally {
    loading.value=false
  }
}

onMounted(()=> {
  fetchCategory();
  fetchTopRate();
})

</script>

<style>

.cards{
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 30px;
  margin-top: 80px;
  margin-bottom: 80px;
}

</style>