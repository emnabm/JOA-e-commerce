<template>
  <div class="prod-card" :class="{ 'out-of-stock': stock === 0 }">

    <!-- Image -->
    <div class="prod-image">
      <span v-if="stock === 0" class="badge-stock">Rupture de stock</span>
      <img :src="imageUrl" :alt="name" class="prod-img" />
      <div class="prod-overlay">
        <NuxtLink :to="`/category/product/${id}`" class="overlay-btn">
          View Details
        </NuxtLink>
      </div>
    </div>

    <!-- Body -->
    <div class="prod-body">
      <p class="prod-name">{{ name }}</p>
      <p class="prod-desc">{{ description?.length > 55 ? description.substring(0, 55) + '…' : description }}</p>

      <!-- Étoiles -->
      <div class="stars-row">
        <div class="stars">
          <span
            v-for="i in 5"
            :key="i"
            class="star"
            :class="{ filled: i <= Math.round(rating), half: i === Math.ceil(rating) && rating % 1 >= 0.5 }"
          ></span>
        </div>
        <span class="rating-count">({{ reviewCount }})</span>
      </div>

      <!-- Prix + panier -->
      <div class="prod-footer">
        <span class="prod-price">{{ prix }} DT</span>
        <NuxtLink
          :to="`/category/product/${id}`"
          class="cart-btn"
          :class="{ disabled: stock === 0 }"
          :aria-disabled="stock === 0"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 01-8 0"/>
          </svg>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  id:          { type: Number, required: true },
  name:        { type: String, required: true },
  img:         { type: String, default: '' },
  image:       { type: String, default: '' },
  description: { type: String, default: '' },
  prix:        { type: Number, required: true },
  stock:       { type: Number, default: 1 },
  unit:        { type: String, default: '' },
  rating:      { type: Number, default: 0 },
  reviewCount: { type: Number, default: 0 },
})

// Handle both 'img' (from backend) and 'image' field names
const imageUrl = computed(() => {
  const imgPath = props.img || props.image || ''
  return imgPath ? `/${imgPath}` : '/makeup.jpeg'
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500&family=Playfair+Display:wght@500&display=swap');

.prod-card {
  font-family: 'DM Sans', sans-serif;
  background: #fff;
  border-radius: 14px;
  border: 0.5px solid rgba(44,33,23,0.1);
  overflow: hidden;
  width: 240px;
  transition: border-color 0.2s, transform 0.2s;
  position: relative;
}
.prod-card:hover {
  border-color: #C8854A;
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(200,133,74,0.15);
}
.prod-card.out-of-stock { opacity: 0.7; }

/* Image */
.prod-image { position: relative; height: 180px; overflow: hidden; background: #F8F2E6; }
.prod-img   { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.prod-card:hover .prod-img { transform: scale(1.04); }

.badge-stock {
  position: absolute; top: 10px; left: 10px; z-index: 2;
  background: #fff; color: #7a6555;
  font-size: 10px; font-weight: 500; letter-spacing: 1px; text-transform: uppercase;
  padding: 4px 10px; border-radius: 20px;
  border: 0.5px solid rgba(44,33,23,0.15);
}

.prod-overlay {
  position: absolute; inset: 0;
  background: rgba(44,33,23,0.35);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity 0.2s;
}
.prod-card:hover .prod-overlay { opacity: 1; }
.overlay-btn {
  font-size: 12.5px; font-weight: 500; color: #fff;
  border: 1px solid rgba(255,255,255,0.6);
  padding: 8px 18px; border-radius: 8px; text-decoration: none;
  backdrop-filter: blur(4px);
}

/* Body */
.prod-body { padding: 14px 14px 12px; }
.prod-name {
  font-family: 'Playfair Display', serif;
  font-size: 15px; font-weight: 500; color: #2C2117;
  margin: 0 0 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.prod-desc {
  font-size: 12.5px; color: #7a6555; line-height: 1.5; margin: 0 0 10px;
  min-height: 38px;
}

/* Stars */
.stars-row { display: flex; align-items: center; gap: 6px; margin-bottom: 12px; }
.stars     { display: flex; gap: 3px; }
.star {
  width: 13px; height: 13px;
  clip-path: polygon(50% 0%,61% 35%,98% 35%,68% 57%,79% 91%,50% 70%,21% 91%,32% 57%,2% 35%,39% 35%);
  background: #e0d5c8;
  transition: background 0.1s;
}
.star.filled { background: #C8854A; }
.star.half   { background: linear-gradient(90deg, #C8854A 50%, #e0d5c8 50%); }
.rating-count { font-size: 11px; color: #a09080; }

/* Footer */
.prod-footer { display: flex; align-items: center; justify-content: space-between; }
.prod-price  { font-size: 16px; font-weight: 500; color: #C8854A; }
.cart-btn {
  width: 36px; height: 36px; border-radius: 8px;
  background: #2C2117; color: #fff;
  display: flex; align-items: center; justify-content: center;
  text-decoration: none; transition: background 0.15s;
}
.cart-btn:hover   { background: #C8854A; }
.cart-btn.disabled { background: #ccc; pointer-events: none; }
</style>