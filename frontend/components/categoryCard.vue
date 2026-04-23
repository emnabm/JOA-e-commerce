<template>
  <div class="background hover:scale-105 transition-transform duration-200 cursor-pointer">
    
    <NuxtLink :to="`/category/${id}`" >
    <div class="background hover:scale-105 transition-transform duration-200 cursor-pointer">
    <img :src="`/${img}`" alt="Category Image" class="mx-auto"  />
    <p class="fs-4">{{ name }}</p>
    </div>
    
    </NuxtLink>
  </div>


</template>

<script setup>
import { useRouter } from 'vue-router'
const props=defineProps({
  id:Number,
  name: String,
  img: String,
})



</script>

<style scoped>
.background {
  text-align: center;
  height: 220px;
  width: 220px;
  display: flex;
  flex-direction: column;
  justify-content: space-evenly;
  border: 1px solid black;
  border-radius: 80px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.background:hover {
  box-shadow: 0 6px 12px #FAE5CF;
  transform: scale(1.05);
  cursor: pointer;
}

img {
  width: 100%;
}
</style>
