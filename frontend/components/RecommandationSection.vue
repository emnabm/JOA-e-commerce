<!-- components/RecommendationSection.vue -->
<template>
  <div class="recommendation-section">
    <div class="container">
      
      <!-- Section Recommandations personnalisées -->
      <div class="rec-block">
        <div class="rec-header">
          <div class="rec-icon rec-icon-ai">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M12 2a10 10 0 0 1 10 10c0 5.5-4.5 10-10 10S2 17.5 2 12 6.5 2 12 2z"/>
              <path d="M12 6v6l4 2"/>
            </svg>
          </div>
          <div>
            <span class="rec-label">RECOMMENDED FOR YOU</span>
            <h3 class="rec-title">You might also like</h3>
            <p v-if="user" class="rec-subtitle" >Based on your purchase history</p>
          </div>
        </div>

        <div v-if="loading" class="rec-loading">
          <div class="spinner"></div>
          <span>Loading recommendations...</span>
        </div>

        <div v-else-if="recommendations.length > 0" class="rec-carousel-wrapper">
          <!-- Pagination Controls -->
          <div class="carousel-controls">
            <button 
              :disabled="currentPage === 1"
              class="carousel-btn prev" 
              @click="prevPage"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
              </svg>
            </button>
            
            <div class="page-indicator">
              <span class="current-page">{{ currentPage }}</span>
              <span> / </span>
              <span class="total-pages">{{ totalPages }}</span>
            </div>
            
            <button 
              :disabled="currentPage === totalPages"
              class="carousel-btn next" 
              @click="nextPage"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
              </svg>
            </button>
          </div>

          <!-- Carousel Row -->
          <div class="rec-carousel">
            <div class="rec-track" :style="trackStyle">
              <div 
                v-for="product in paginatedRecommendations" 
                :key="'rec-'+product.id" 
                class="product-card"
                @click="goToProduct(product.id)"
              >
                <div class="product-image">
                  <img :src="getImageUrl(product.image)" :alt="product.nom" loading="lazy">
                  <span v-if="product.stock === 0" class="stock-badge out">Out of Stock</span>
                  <span v-else-if="product.stock < 5" class="stock-badge low">Low Stock</span>
                </div>
                <div class="product-info">
                  <div class="product-category">{{ product.category || 'Product' }}</div>
                  <h4 class="product-name">{{ truncateText(product.nom, 30) }}</h4>
                  <div class="product-rating" v-if="product.avgRating > 0">
                    <div class="stars">
                      <span v-for="i in 5" :key="i" class="star" :class="{ filled: i <= Math.round(product.avgRating) }">★</span>
                    </div>
                    <span class="rating-count">({{ product.reviewCount || 0 }})</span>
                  </div>
                  <div class="product-footer">
                    <div class="product-price">{{ product.prix }} DT</div>
                    <span v-if="product.unit" class="product-unit">/ {{ product.unit }}</span>
                    <button
                      :disabled="product.stock === 0"
                      :title="product.stock === 0 ? 'Out of stock' : 'Add to cart'" 
                      class="add-cart-btn" 
                      @click.stop="addToCart(product)"
                    >
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="rec-empty">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <p>No recommendations available at the moment</p>
          <p class="rec-empty-sub">Discover our products to get personalized suggestions</p>
          <NuxtLink to="/category" class="explore-link">Explore products →</NuxtLink>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const props = defineProps({
  currentProductId: {
    type: Number,
    required: true
  }
})

const router = useRouter()
const token = localStorage.getItem('token')
const user = ref(null)
const loading = ref(false)
const recommendations = ref([])
const currentPage = ref(1)
const itemsPerPage = 4 // Number of items per page

// Get history from localStorage
const getHistory = () => {
  try {
    return JSON.parse(localStorage.getItem('browsing_history') || '[]')
  } catch {
    return []
  }
}

const getImageUrl = (image) => {
  if (!image) return '/default-product.jpg'
  if (image.startsWith('http')) return image
  if (image.startsWith('/')) return image
  return `/${image}`
}

const truncateText = (text, maxLength) => {
  if (!text) return ''
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}

const goToProduct = (id) => {
  router.push(`/product/${id}`)
}

const addToCart = async (product) => {
  if (!token) {
    router.push('/login')
    return
  }
  
  try {
    await axios.post('http://localhost:8000/api/addToCart', {
      productId: product.id,
      quantity: 1
    }, {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    window.dispatchEvent(new CustomEvent('cart-updated'))
    alert(`"${product.nom}" added to cart!`)
  } catch (error) {
    console.error('Error adding to cart:', error)
    alert('Error adding to cart')
  }
}

// Pagination
const totalPages = computed(() => {
  return Math.ceil(recommendations.value.length / itemsPerPage)
})

const paginatedRecommendations = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return recommendations.value.slice(start, end)
})

const trackStyle = computed(() => {
  // Pour l'affichage horizontal avec pagination, on utilise grid
  return {}
})

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

// Load recommendations
const loadRecommendations = async () => {
  loading.value = true
  
  try {
    const config = token ? {
      headers: { Authorization: `Bearer ${token}` }
    } : {}
    
    const { data } = await axios.post('http://localhost:8000/api/recommendations/purchases', {
      currentId: props.currentProductId
    }, config)
    
    recommendations.value = data
    currentPage.value = 1
  } catch (error) {
    console.error('Error loading recommendations:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  user.value = localStorage.getItem('user_username')
  loadRecommendations()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@400;500&display=swap');

.recommendation-section {
  background: #fff;
  padding: 3rem 0;
  margin-top: 2rem;
  border-top: 1px solid rgba(44, 33, 23, 0.08);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Block */
.rec-block {
  margin-bottom: 3rem;
}

.rec-block:last-child {
  margin-bottom: 0;
}

/* Header */
.rec-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.rec-icon {
  width: 48px;
  height: 48px;
  background: #F8F2E6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #C8854A;
}

.rec-icon-ai {
  background: linear-gradient(135deg, #C8854A 0%, #2C2117 100%);
  color: #fff;
}

.rec-label {
  font-size: 0.7rem;
  font-weight: 500;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: #C8854A;
}

.rec-title {
  font-family: 'Playfair Display', serif;
  font-size: 1.3rem;
  font-weight: 500;
  margin: 0;
  color: #2C2117;
}

.rec-subtitle {
  font-size: 0.75rem;
  color: #7a6555;
  margin: 0.25rem 0 0;
}

/* Carousel Wrapper */
.rec-carousel-wrapper {
  position: relative;
}

/* Pagination Controls */
.carousel-controls {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.carousel-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #F8F2E6;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2C2117;
  transition: all 0.2s;
}

.carousel-btn:hover:not(:disabled) {
  background: #C8854A;
  color: #fff;
}

.carousel-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-indicator {
  font-size: 0.8rem;
  color: #7a6555;
  font-weight: 500;
}

.current-page {
  color: #C8854A;
  font-weight: 600;
}

/* Carousel Grid */
.rec-carousel {
  overflow-x: auto;
  overflow-y: hidden;
  scroll-behavior: smooth;
}

.rec-carousel::-webkit-scrollbar {
  height: 4px;
}

.rec-carousel::-webkit-scrollbar-track {
  background: rgba(44, 33, 23, 0.08);
  border-radius: 2px;
}

.rec-carousel::-webkit-scrollbar-thumb {
  background: #C8854A;
  border-radius: 2px;
}

.rec-track {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

/* Product Card */
.product-card {
  background: #fff;
  border: 1px solid rgba(44, 33, 23, 0.08);
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
  border-color: #C8854A;
}

.product-image {
  position: relative;
  height: 200px;
  background: #F8F2E6;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.stock-badge {
  position: absolute;
  top: 0.75rem;
  left: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.7rem;
  font-weight: 500;
}

.stock-badge.out {
  background: #2C2117;
  color: #fff;
}

.stock-badge.low {
  background: #f44336;
  color: #fff;
}

.product-info {
  padding: 1rem;
}

.product-category {
  font-size: 0.7rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #C8854A;
  margin-bottom: 0.5rem;
}

.product-name {
  font-family: 'Playfair Display', serif;
  font-size: 0.9rem;
  font-weight: 500;
  margin: 0 0 0.5rem 0;
  color: #2C2117;
  line-height: 1.4;
  min-height: 2.5rem;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.stars {
  display: flex;
  gap: 2px;
}

.star {
  font-size: 11px;
  color: #ddd;
}

.star.filled {
  color: #FFB800;
}

.rating-count {
  font-size: 0.7rem;
  color: #7a6555;
}

.product-footer {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.25rem;
}

.product-price {
  font-size: 1.1rem;
  font-weight: 700;
  color: #C8854A;
}

.product-unit {
  font-size: 0.7rem;
  color: #7a6555;
}

.add-cart-btn {
  margin-left: auto;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #C8854A;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  transition: all 0.2s;
}

.add-cart-btn:hover:not(:disabled) {
  background: #b67038;
  transform: scale(1.05);
}

.add-cart-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* Loading */
.rec-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  padding: 4rem;
  color: #7a6555;
}

.spinner {
  width: 24px;
  height: 24px;
  border: 2px solid #F8F2E6;
  border-top-color: #C8854A;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Empty state */
.rec-empty {
  text-align: center;
  padding: 4rem;
  background: #F8F2E6;
  border-radius: 20px;
}

.rec-empty svg {
  margin-bottom: 1rem;
  color: #C8854A;
}

.rec-empty p {
  color: #7a6555;
  margin-bottom: 0.5rem;
}

.rec-empty-sub {
  font-size: 0.8rem;
  margin-top: 0.5rem;
}

.explore-link {
  display: inline-block;
  margin-top: 1.5rem;
  padding: 0.75rem 1.5rem;
  background: #C8854A;
  color: #fff;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s;
}

.explore-link:hover {
  background: #b67038;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 1200px) {
  .rec-track {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 992px) {
  .rec-track {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
  
  .rec-track {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  
  .product-image {
    height: 150px;
  }
  
  .product-name {
    font-size: 0.8rem;
    min-height: 2rem;
  }
  
  .rec-title {
    font-size: 1.1rem;
  }
  
  .carousel-controls {
    margin-bottom: 1rem;
  }
}

@media (max-width: 480px) {
  .rec-track {
    grid-template-columns: 1fr;
  }
  
  .rec-empty {
    padding: 2rem;
  }
}
</style>