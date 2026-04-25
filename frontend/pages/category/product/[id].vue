<template>
  <div>
    <AppHeader />

    <!-- Loader -->
    <div v-if="loading" class="loader-page">
      <div class="spinner"></div>
    </div>

    <main v-else-if="product" class="page-wrapper">

      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <NuxtLink to="/">Accueil</NuxtLink>
        <span class="sep">/</span>
        <NuxtLink :to="`/category/${product.product.category?.id || product.product.category_id}`">
          {{ product.product.category?.name || 'Produits' }}
        </NuxtLink>
        <span class="sep">/</span>
        <span class="current">{{ product.product.nom }}</span>
      </nav>

      <!-- Product Section -->
      <div class="product-section">

        <!-- Gallery -->
        <div class="gallery">
          <div class="main-image">
            <img :src="`/${activeImg}`" :alt="product.product.nom" class="main-img">
            <span v-if="product.product.stock === 0" class="badge-stock">Out of stock</span>
          </div>
          <!-- If you have multiple images, you can add them here -->
          <div v-if="product.product.images?.length > 1" class="thumbs">
            <div
              v-for="(imgSrc, i) in product.product.images"
              :key="i"
              class="thumb"
              :class="{ active: activeImg === imgSrc }"
              @click="activeImg = imgSrc"
            >
              <img :src="`/${imgSrc}`" :alt="`vue ${i+1}`" />
            </div>
          </div>
        </div>

        <!-- Info -->
        <div class="product-info">
          <div class="info-label">{{ product.product.category?.name || 'Produit' }}</div>
          <h1 class="product-name">{{ product.product.nom }}</h1>

          <!-- Stars + avis -->
          <div class="stars-row">
            <div class="stars">
              <span
                v-for="i in 5" :key="i"
                class="star"
                :class="{ 
                  filled: i <= Math.round(product.avgRating || 0), 
                  half: i === Math.ceil(product.avgRating || 0) && (product.avgRating || 0) % 1 >= 0.5 
                }"
              ></span>
            </div>
            <span class="rating-val">{{ (product.avgRating || 0).toFixed(1) }}</span>
            <span class="rating-count">{{ product.reviewCount || 0 }} avis</span>
            <a href="#reviews" class="rating-link">Voir les avis</a>
          </div>

          <!-- Prix -->
          <div class="price-block">
            <span class="price">{{ product.product.prix }} DT</span>
            <span v-if="product.product.unit" class="unit">/ {{ product.product.unit }}</span>
          </div>

          <!-- Description courte -->
          <p class="short-desc">{{ product.product.description }}</p>

          <!-- Stock -->
          <div class="stock-info" :class="product.product.stock > 0 ? 'in-stock' : 'no-stock'">
            <span class="stock-dot"></span>
            {{ product.product.stock > 0 ? `In stock (${product.product.stock} disponibles)` : 'Out of stock' }}
          </div>

          <!-- Quantité + Panier -->
          <div v-if="product.product.stock > 0" class="action-row">
            <div class="qty-ctrl">
              <button @click="qty > 1 && qty--" class="qty-btn">−</button>
              <span class="qty-val">{{ qty }}</span>
              <button class="qty-btn" @click="qty < product.product.stock && qty++" >+</button>
            </div>
            <button class="btn-cart" @click="addToCart">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
              </svg>
              Add to cart
            </button>
            
          </div>
          <div v-else class="out-msg">This product is not available at the moment.</div>

          <!-- Features -->
          <div class="features">
            <div class="feature">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              Fast delivery
            </div>
            <div class="feature">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><polyline points="20 6 9 17 4 12"/></svg>
              Authentic product
            </div>
            <div class="feature">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.6"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
              Support 7j/7
            </div>
          </div>
        </div>
      </div>

      <!-- Reviews -->
      <section id="reviews" class="reviews-section">
        <h2 class="reviews-title">Customer Reviews</h2>

        <!-- Résumé -->
        <div class="reviews-summary">
          <div class="summary-score">
            <div class="big-score">{{ (product.avgRating || 0).toFixed(1) }}</div>
            <div class="stars big-stars">
              <span v-for="i in 5" :key="i" class="star" :class="{ filled: i <= Math.round(product.avgRating || 0) }"></span>
            </div>
            <div class="summary-count">{{ product.reviewCount || 0 }} reviews</div>
          </div>
          <div class="summary-bars">
            <div v-for="n in [5,4,3,2,1]" :key="n" class="bar-row">
              <span class="bar-label">{{ n }}</span>
              <div class="bar-track">
                <div class="bar-fill" :style="{ width: barWidth(n) + '%' }"></div>
              </div>
              <span class="bar-count">{{ countByRating(n) }}</span>
            </div>
          </div>
        </div>

        <!-- Liste d'avis -->
        <div class="reviews-list">
          <div v-if="reviews.length === 0" class="no-reviews">
            No reviews yet. Be the first to review!
          </div>
          <div v-for="r in reviews" :key="r.id" class="review-card">
            <div class="review-header">
              <div class="review-avatar">{{ (r.user || 'U').charAt(0).toUpperCase() }}</div>
              <div>
                <div class="review-author">{{ r.user || 'User' }}</div>
                <div class="review-date">{{ formatDate(r.date) }}</div>
              </div>
              <div class="stars review-stars">
                <span v-for="i in 5" :key="i" class="star" :class="{ filled: i <= (r.rating || 0) }"></span>
              </div>
            </div>
            <p class="review-comment">{{ r.text }}</p>
          </div>
        </div>

        <!-- Formulaire avis -->
        <div v-if="user" class="review-form">
          <h3 class="form-title">Leave a Review</h3>
          <div class="form-stars">
            <span
              v-for="i in 5" :key="i"
              class="star form-star"
              :class="{ filled: i <= newReview.rating }"
              @click="newReview.rating = i"
            ></span>
            <span class="form-rating-label">{{ ratingLabels[newReview.rating - 1] || 'Click to rate' }}</span>
          </div>
          <textarea
            v-model="newReview.comment"
            placeholder="Share your experience with this product..."
            class="review-textarea"
            rows="4"
          ></textarea>
          <button class="btn-cart" :disabled="!newReview.rating" @click="submitReview" >
            Publish my review
          </button>
        </div>
        <div v-else class="review-login-prompt">
          <NuxtLink to="/login">Connectez-vous</NuxtLink> pour laisser un avis.
        </div>
      </section>

      <!-- Produits similaires -->
      <section v-if="related.length > 0" class="related-section">
        <div class="section-label">Vous aimerez aussi</div>
        <h2 class="section-title">Produits similaires</h2>
        <div class="related-grid">
          <Product-card
            v-for="p in related"
            :id="p.id"
            :key="p.id"
            :name="p.nom"
            :img="p.image"
            :description="p.description"
            :prix="p.prix"
            :stock="p.stock"
            :unit="p.unit"
            :rating="p.avgRating ?? 0"
            :review-count="p.review_count ?? 0"
          />
        </div>
      </section>

    </main>

    <div v-else class="not-found">
      <p>Produit introuvable.</p>
      <NuxtLink to="/" class="btn-cart" style="width:fit-content">Retour à l'accueil</NuxtLink>
    </div>

    <AppFooter />
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route   = useRoute()
const id      = route.params.id

const product  = ref(null)
const loading  = ref(false)
const activeImg = ref('')
const qty = ref(1)
const reviews  = ref([])
const related  = ref([])
const user = ref(null)
const token = localStorage.getItem('token')

const newReview = ref({ rating: 0, comment: '' })
const ratingLabels = ['Mauvais', 'Passable', 'Bien', 'Très bien', 'Excellent']

const countByRating = (n) => reviews.value.filter(r => (r.rating || 0) === n).length
const barWidth      = (n) => reviews.value.length ? (countByRating(n) / reviews.value.length) * 100 : 0
const formatDate    = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Date inconnue'

const fetchProduct = async () => {
  try {
    loading.value = true
    const res = await axios.get(`http://localhost:8000/getProduct_${id}`)
    product.value = res.data
    // Set the main image from the product image field
    activeImg.value = product.value.product.image || ''
  } catch (err) {
    console.error('Error fetching product:', err)
    alert('Erreur lors du chargement du produit: ' + (err.response?.data?.message || err.message))
  } finally {
    loading.value = false
  }
}

const fetchReviews = async () => {
  try {
    const res = await axios.get(`http://localhost:8000/api/review/${id}`,{
      headers: { Authorization: `Bearer ${token}` },
  })
    reviews.value = res.data
    
  } catch (err) {
    console.error('Error fetching reviews:', err)
    reviews.value = []
  }
}

const fetchRelated = async () => {
  try {
      const res = await axios.get(`http://localhost:8000/products/suggestions/${product.value.product.id}`)
      // Filter out current product and limit to 4
      related.value = res.data.slice(0, 4)
    
  } catch (err) {
    console.error('Error fetching related products:', err)
    related.value = []
  }
}

const addToCart = async () => {
  if (!token) {
    alert('Veuillez vous connecter pour ajouter au panier')
    return navigateTo('/login')
  }
  try {
    await axios.post('http://localhost:8000/api/addToCart',
      { productId: parseInt(id), quantity: qty.value },
      { headers: { Authorization: `Bearer ${token}` } }
    )
    alert('Produit ajouté au panier !')
  } catch (err) {
    console.error('Error adding to cart:', err)
    alert('Erreur lors de l\'ajout au panier: ' + (err.response?.data?.message || err.message))
  }
}

const submitReview = async () => {
  if (!newReview.value.rating) {
    alert('Veuillez noter le produit')
    return
  }
  if (!token) {
    alert('Veuillez vous connecter pour laisser un avis')
    return navigateTo('/login')
  }
  
  try {
    await axios.post(
      'http://localhost:8000/api/review',
      {
        productId: parseInt(id),
        text: newReview.value.comment,
        rating: newReview.value.rating
      },
      { headers: { Authorization: `Bearer ${token}` } }
    )
    
    // Reset form
    newReview.value = { rating: 0, comment: '' }
    
    // Refresh reviews
    await fetchReviews()
    
    // Also refresh product to update rating and review count
    await fetchProduct()
    
    alert('Votre avis a été envoyé !')
  } catch (err) {
    console.error('Error submitting review:', err)
    alert('Erreur lors de l\'envoi de l\'avis: ' + (err.response?.data?.message || err.message))
  }
}

onMounted(async () => {
  user.value = localStorage.getItem('user_username')
  await fetchProduct()
  await fetchReviews()
  await fetchRelated()
  
})
</script>

<style scoped>
/* Your existing styles remain exactly the same - copy from your new code */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@400;500&display=swap');

* { box-sizing: border-box; }

.page-wrapper { max-width: 1100px; margin: 0 auto; padding: 2rem; font-family: 'DM Sans', sans-serif; }

/* Breadcrumb */
.breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #7a6555; margin-bottom: 2.5rem; flex-wrap: wrap; }
.breadcrumb a { color: #7a6555; text-decoration: none; }
.breadcrumb a:hover { color: #C8854A; }
.sep { color: #c0b4a8; }
.current { color: #2C2117; font-weight: 500; }

/* Product section */
.product-section { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: start; margin-bottom: 5rem; }

/* Gallery */
.gallery { display: flex; flex-direction: column; gap: 12px; }
.main-image { position: relative; border-radius: 14px; overflow: hidden; background: #F8F2E6; aspect-ratio: 1; }
.main-img { width: 100%; height: 100%; object-fit: cover; }
.badge-stock {
  position: absolute; top: 14px; left: 14px;
  background: #fff; color: #7a6555; font-size: 11px; font-weight: 500;
  letter-spacing: 1px; text-transform: uppercase; padding: 5px 12px;
  border-radius: 20px; border: 0.5px solid rgba(44,33,23,0.15);
}
.thumbs { display: flex; gap: 10px; }
.thumb { width: 64px; height: 64px; border-radius: 8px; overflow: hidden; cursor: pointer; border: 1.5px solid transparent; transition: border-color 0.15s; background: #F8F2E6; }
.thumb.active { border-color: #C8854A; }
.thumb img { width: 100%; height: 100%; object-fit: cover; }

/* Info */
.product-info { display: flex; flex-direction: column; gap: 16px; padding-top: 8px; }
.info-label { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #C8854A; }
.product-name { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 500; color: #2C2117; margin: 0; line-height: 1.2; }

/* Stars */
.stars-row { display: flex; align-items: center; gap: 8px; }
.stars { display: flex; gap: 3px; }
.star {
  width: 14px; height: 14px;
  clip-path: polygon(50% 0%,61% 35%,98% 35%,68% 57%,79% 91%,50% 70%,21% 91%,32% 57%,2% 35%,39% 35%);
  background: #e0d5c8; transition: background 0.1s;
}
.star.filled { background: #C8854A; }
.star.half { background: linear-gradient(90deg, #C8854A 50%, #e0d5c8 50%); }
.rating-val   { font-size: 14px; font-weight: 500; color: #2C2117; }
.rating-count { font-size: 13px; color: #7a6555; }
.rating-link  { font-size: 13px; color: #C8854A; text-decoration: none; }
.rating-link:hover { text-decoration: underline; }

/* Price */
.price-block { display: flex; align-items: baseline; gap: 6px; }
.price { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 500; color: #C8854A; }
.unit  { font-size: 13px; color: #7a6555; }

.short-desc { font-size: 14.5px; color: #5a4a3a; line-height: 1.7; margin: 0; }

/* Stock */
.stock-info { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; }
.stock-dot  { width: 8px; height: 8px; border-radius: 50%; }
.in-stock   { color: #3B6D11; }
.in-stock .stock-dot { background: #639922; }
.no-stock   { color: #A32D2D; }
.no-stock .stock-dot { background: #E24B4A; }

/* Actions */
.action-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.qty-ctrl   { display: flex; align-items: center; background: #F8F2E6; border-radius: 8px; border: 0.5px solid rgba(44,33,23,0.15); overflow: hidden; }
.qty-btn    { width: 36px; height: 40px; border: none; background: transparent; font-size: 18px; color: #2C2117; cursor: pointer; }
.qty-btn:hover { background: rgba(200,133,74,0.1); }
.qty-val    { min-width: 32px; text-align: center; font-size: 15px; font-weight: 500; }

.btn-cart {
  flex: 1; display: flex; align-items: center; justify-content: center; gap: 10px;
  background: #2C2117; color: #F8F2E6; font-size: 14px; font-weight: 500;
  padding: 11px 20px; border-radius: 8px; border: none; cursor: pointer;
  font-family: 'DM Sans', sans-serif; transition: background 0.15s; text-decoration: none;
}
.btn-cart:hover:not(:disabled) { background: #C8854A; }
.btn-cart:disabled { opacity: 0.5; cursor: default; }

.btn-wish {
  width: 42px; height: 42px; border-radius: 8px;
  border: 0.5px solid rgba(44,33,23,0.15); background: transparent;
  display: flex; align-items: center; justify-content: center; cursor: pointer;
  color: #7a6555; transition: all 0.15s;
}
.btn-wish:hover, .btn-wish.wished { color: #E24B4A; border-color: #E24B4A; background: #FCEBEB; }

.out-msg { font-size: 14px; color: #A32D2D; background: #FCEBEB; padding: 10px 16px; border-radius: 8px; }

/* Features */
.features { display: flex; flex-direction: column; gap: 8px; padding-top: 4px; border-top: 0.5px solid rgba(44,33,23,0.1); }
.feature  { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #5a4a3a; }

/* Reviews */
.reviews-section { border-top: 0.5px solid rgba(44,33,23,0.1); padding: 4rem 0; margin-bottom: 4rem; }
.reviews-title   { font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 500; color: #2C2117; margin-bottom: 2rem; }

.reviews-summary { display: flex; gap: 3rem; align-items: center; background: #F8F2E6; border-radius: 14px; padding: 2rem; margin-bottom: 2.5rem; }
.summary-score   { text-align: center; flex-shrink: 0; }
.big-score       { font-family: 'Playfair Display', serif; font-size: 3.5rem; font-weight: 500; color: #2C2117; line-height: 1; }
.big-stars       { justify-content: center; margin: 8px 0 4px; }
.big-stars .star { width: 18px; height: 18px; }
.summary-count   { font-size: 12px; color: #7a6555; }
.summary-bars    { flex: 1; display: flex; flex-direction: column; gap: 8px; }
.bar-row         { display: flex; align-items: center; gap: 10px; }
.bar-label       { font-size: 12px; color: #7a6555; min-width: 8px; }
.bar-track       { flex: 1; height: 6px; background: #e8e0d4; border-radius: 3px; overflow: hidden; }
.bar-fill        { height: 100%; background: #C8854A; border-radius: 3px; transition: width 0.4s; }
.bar-count       { font-size: 12px; color: #7a6555; min-width: 20px; text-align: right; }

.reviews-list { display: flex; flex-direction: column; gap: 16px; margin-bottom: 2.5rem; }
.no-reviews   { font-size: 14px; color: #7a6555; text-align: center; padding: 2rem; }

.review-card   { background: #fff; border-radius: 12px; border: 0.5px solid rgba(44,33,23,0.08); padding: 1.2rem 1.4rem; }
.review-header { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
.review-avatar {
  width: 38px; height: 38px; border-radius: 50%; background: #F8F2E6;
  display: flex; align-items: center; justify-content: center;
  font-size: 15px; font-weight: 500; color: #C8854A; flex-shrink: 0;
  border: 0.5px solid rgba(200,133,74,0.3);
}
.review-author { font-size: 14px; font-weight: 500; color: #2C2117; }
.review-date   { font-size: 12px; color: #7a6555; }
.review-stars  { margin-left: auto; }
.review-comment { font-size: 14px; color: #5a4a3a; line-height: 1.7; margin: 0; }

/* Review form */
.review-form     { background: #F8F2E6; border-radius: 14px; padding: 2rem; display: flex; flex-direction: column; gap: 16px; }
.form-title      { font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 500; color: #2C2117; margin: 0; }
.form-stars      { display: flex; align-items: center; gap: 6px; }
.form-star       { width: 24px; height: 24px; cursor: pointer; transition: transform 0.1s; }
.form-star:hover { transform: scale(1.2); }
.form-rating-label { font-size: 13px; color: #7a6555; margin-left: 6px; }
.review-textarea {
  background: #fff; border: 0.5px solid rgba(44,33,23,0.15); border-radius: 10px;
  padding: 12px 14px; font-size: 14px; color: #2C2117; font-family: 'DM Sans', sans-serif;
  resize: none; outline: none; transition: border-color 0.15s;
}
.review-textarea:focus { border-color: #C8854A; }
.review-login-prompt { font-size: 14px; color: #7a6555; text-align: center; padding: 1.5rem; background: #F8F2E6; border-radius: 10px; }
.review-login-prompt a { color: #C8854A; text-decoration: none; font-weight: 500; }

/* Related */
.related-section { padding-bottom: 4rem; }
.section-label   { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #C8854A; margin-bottom: 8px; }
.section-title   { font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 500; color: #2C2117; margin-bottom: 2rem; }
.related-grid    { display: flex; flex-wrap: wrap; gap: 20px; }

/* Loader */
.loader-page { display: flex; justify-content: center; align-items: center; height: 60vh; }
.spinner     { width: 36px; height: 36px; border: 2px solid rgba(200,133,74,0.2); border-top-color: #C8854A; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.not-found { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; height: 50vh; font-size: 16px; color: #7a6555; }

/* Responsive */
@media (max-width: 768px) {
  .product-section { grid-template-columns: 1fr; gap: 2rem; }
  .reviews-summary { flex-direction: column; gap: 1.5rem; }
  .action-row { flex-wrap: wrap; }
  .btn-cart { flex: unset; width: 100%; }
}
</style>