<template>
  <div
      class="background hover:scale-105 transition-transform duration-200 cursor-pointer"

  >
    <div class="image-container">
      <span v-if="stock == 0" class="badge">Out Of Stock</span>
      <img :src="`/${img}`" alt="Product Image" class="img mx-auto" />

    </div>
    <p class="fs-4 warning font-bold">{{ name }}</p>
    <p>{{ description.length > 50 ? description.substring(0, 50) + '...' : description }}</p>

    <!-- <p>Unit:{{unit}}</p> -->
    <p>{{ prix }} DT</p>

    <!--  Bouton panier -->
    <div class='add_card_container'>
      <NuxtLink :to="`/category/product/${id}`">
        <img class="add-cart" src="/shopping-basket.png">
      </NuxtLink>

  </div>
  </div>
</template>

<script setup>
// import { useRouter } from 'vue-router'

defineProps({
  id: Number,
  name: String,
  img: String,
  description: String,
  prix: Number,
  stock: Number,
  unit:String,
})

// const router = useRouter()

</script>

<style scoped>
.background {
  text-align: center;
  position: relative;
  height: 250px;
  width: 250px;
  display: flex;
  flex-direction: column;
  justify-content: space-evenly;
  border: 1px solid black;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.background:hover {
  box-shadow: 0 6px 12px rgba(184, 108, 1, 0.6);
  transform: scale(1.05);
  border: 1px solid #e48b08;
  cursor: pointer;
}

.img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: #f2f2f2;
  color: #555;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  text-transform: uppercase;

  opacity: 0;               /* caché par défaut */
  pointer-events: none;     /* n'intercepte pas la souris */
  transition: opacity 0.2s ease-in-out;
}

/* Quand on survole le bloc parent, le badge apparaît */
.background:hover .badge {
  opacity: 1;
}

.add_card_container {
  position: absolute;
  bottom: 20px;
  right: 20px;
  display: flex;
  justify-content: flex-end; /* bouton à droite */
  padding: 0 10px;           /* petit espace à droite/gauche */
  margin-top: auto;          /* pousse le bouton vers le bas si besoin */
}

.add-cart {
  margin: 0 auto;
  color: white;
  border: none;
  border-radius: 20px;
  padding: 8px 16px;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s ease-in-out;
  width: 50px;
  height: 40px;
}

.add-cart:hover {
  background-color: #e69c37;
  border-radius: 20px;
}


</style>
