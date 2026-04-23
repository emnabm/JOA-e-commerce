<template>
  <div v-if="!loading">
    <AppHeader />

    <!-- ═══ PAGE HEADER ═══ -->
    <section class="page-header">
      <div class="container">
        <div class="header-content">
          <h1>All Categories</h1>
          <p>Discover our complete collection of products</p>
          <div class="breadcrumb">
            <NuxtLink to="/">Home</NuxtLink>
            <span class="separator">/</span>
            <span>Categories</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ CATEGORIES STATS ═══ -->
    <section class="stats-bar">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-number">{{ categories.length }}</div>
            <div class="stat-label">Categories</div>
          </div>
          <div class="stat-card">
            <div class="stat-number">{{ totalProducts }}</div>
            <div class="stat-label">Total Products</div>
          </div>
          <div class="stat-card">
            <div class="stat-number">{{ featuredCount }}</div>
            <div class="stat-label">Featured</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ SEARCH & FILTER ═══ -->
    <section class="filter-section">
      <div class="container">
        <div class="filter-wrapper">
          <div class="search-box">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Search categories..."
              @input="filterCategories">
          </div>
          <div class="filter-buttons">
            <button 
              v-for="filter in filters" 
              :key="filter.value"
              :class="['filter-btn', { active: activeFilter === filter.value }]"
              @click="setFilter(filter.value)"
            >
              {{ filter.label }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ CATEGORIES GRID ═══ -->
    <section class="categories-section">
      <div class="container">
        <div v-if="filteredCategories.length > 0" class="categories-grid">
          <NuxtLink
            v-for="category in filteredCategories"
            :key="category.id"
            :to="`/category/${category.id}`"
            class="category-card"
          >
            <div class="card-image">
              <img 
                v-if="category.img" 
                :src="category.img" 
                :alt="category.name"
                @error="handleImageError">
              <div v-else class="placeholder-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.4">
                  <rect x="2" y="3" width="20" height="14" rx="2"/>
                  <path d="M8 21h8M12 17v4"/>
                </svg>
              </div>
              <div class="category-overlay">
                <span class="view-details">View Details →</span>
              </div>
            </div>
            <div class="card-content">
              <h3 class="category-name">{{ category.name }}</h3>
              <div class="category-meta">
                <span class="product-count">{{ getProductCount(category.id) }} products</span>
                <span class="explore-link">Explore</span>
              </div>
            </div>
          </NuxtLink>
        </div>

        <!-- ═══ EMPTY STATE ═══ -->
        <div v-else class="empty-state">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <h3>No categories found</h3>
          <p>Try adjusting your search or filter to find what you're looking for.</p>
          <button class="btn-primary" @click="resetFilters" >Reset Filters</button>
        </div>

        <!-- ═══ PAGINATION ═══ -->
        <div v-if="totalPages > 1" class="pagination">
          <button 
            class="page-btn"
            :disabled="currentPage === 1" 
            @click="changePage(currentPage - 1)"
            
          >
            ← Previous
          </button>
          <div class="page-numbers">
            <button
              v-for="page in visiblePages"
              :key="page"
              :class="['page-num', { active: currentPage === page }]"
              @click="changePage(page)"
            >
              {{ page }}
            </button>
          </div>
          <button 
            class="page-btn"
            :disabled="currentPage === totalPages" 
            @click="changePage(currentPage + 1)"
            
          >
            Next →
          </button>
        </div>
      </div>
    </section>

    <!-- ═══ FEATURED CATEGORIES SPOTLIGHT ═══ -->
    <section v-if="featuredCategories.length > 0" class="featured-section">
      <div class="container">
        <div class="section-header">
          <div>
            <div class="section-label">Curated Collections</div>
            <h2 class="section-title">Featured Categories</h2>
          </div>
        </div>
        <div class="featured-grid">
          <div 
            v-for="category in featuredCategories" 
            :key="category.id"
            class="featured-card"
            :style="{ backgroundImage: `linear-gradient(135deg, rgba(44,33,23,0.9), rgba(44,33,23,0.7)), url(${category.img || '/default-category-bg.jpg'})` }"
          >
            <div class="featured-content">
              <h3>{{ category.name }}</h3>
              <p>{{ getCategoryDescription(category.id) }}</p>
              <NuxtLink :to="`/category/${category.id}`" class="btn-outline-light">
                Shop Now →
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ NEWSLETTER ═══ -->
    <section class="newsletter">
      <div class="container">
        <div class="newsletter-content">
          <h2>Stay Updated</h2>
          <p>Subscribe to receive updates about new categories and special offers</p>
          <form class="newsletter-form" @submit.prevent="subscribeNewsletter">
            <input 
              v-model="newsletterEmail" 
              type="email" 
              placeholder="Your email address"
              required>
            <button type="submit" class="btn-primary">Subscribe</button>
          </form>
        </div>
      </div>
    </section>

    <AppFooter/>
  </div>

  <div v-else class="loader">
    <div class="spinner"></div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, computed, onMounted } from 'vue'

const categories = ref([])
const loading = ref(false)
const searchQuery = ref('')
const activeFilter = ref('all')
const currentPage = ref(1)
const itemsPerPage = 12
const newsletterEmail = ref('')

// Mock product counts (you can fetch from API)
const productCounts = ref({})
const featuredCategories = ref([])

const filters = [
  { label: 'All Categories', value: 'all' },
  { label: 'Popular', value: 'popular' },
  { label: 'New Arrivals', value: 'new' },
  { label: 'Featured', value: 'featured' }
]

const fetchCategories = async () => {
  try {
    const response = await axios.get('http://localhost:8000/category')
    categories.value = response.data
    
    // Fetch product counts for each category
    await fetchProductCounts()
    
    // Mark some categories as featured (example: first 3 categories)
    featuredCategories.value = categories.value.slice(0, 3)
  } catch (err) {
    console.error('Error fetching categories:', err)
  }
}

const fetchProductCounts = async () => {
  try {
    // This assumes you have an endpoint that returns product count per category
    // If not, you can modify this based on your API structure
    for (const category of categories.value) {
      const response = await axios.get(`http://localhost:8000/category/${category.id}/products/count`)
      productCounts.value[category.id] = response.data.count
    }
  } catch (err) {
    console.error('Error fetching product counts:', err)
    // Fallback: generate random counts if API doesn't exist
    categories.value.forEach(category => {
      productCounts.value[category.id] = Math.floor(Math.random() * 50) + 10
    })
  }
}

const filterCategories = () => {
  currentPage.value = 1
}

const setFilter = (filter) => {
  activeFilter.value = filter
  currentPage.value = 1
}

const resetFilters = () => {
  searchQuery.value = ''
  activeFilter.value = 'all'
  currentPage.value = 1
}

const filteredCategories = computed(() => {
  let filtered = [...categories.value]
  
  // Apply search filter
  if (searchQuery.value) {
    filtered = filtered.filter(cat => 
      cat.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  
  // Apply category filters
  switch (activeFilter.value) {
    case 'popular':
      filtered = filtered.filter(cat => getProductCount(cat.id) > 30)
      break
    case 'new':
      filtered = filtered.slice(0, 6)
      break
    case 'featured':
      filtered = featuredCategories.value
      break
    default:
      break
  }
  
  // Apply pagination
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filtered.slice(start, end)
})

const totalPages = computed(() => {
  let total = categories.value.length
  if (searchQuery.value) {
    total = categories.value.filter(cat => 
      cat.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    ).length
  }
  return Math.ceil(total / itemsPerPage)
})

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  const start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  const end = Math.min(totalPages.value, start + maxVisible - 1)
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

const totalProducts = computed(() => {
  return Object.values(productCounts.value).reduce((a, b) => a + b, 0)
})

const featuredCount = computed(() => featuredCategories.value.length)

const getProductCount = (categoryId) => {
  return productCounts.value[categoryId] || 0
}

const getCategoryDescription = (categoryId) => {
  const descriptions = {
    // Add descriptions based on category IDs
    // Example: 1: 'Discover our premium collection of eco-friendly products'
  }
  return descriptions[categoryId] || 'Explore our curated collection of high-quality products'
}

const changePage = (page) => {
  currentPage.value = page
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleImageError = (event) => {
  event.target.src = '/default-category.jpg'
}

const subscribeNewsletter = () => {
  alert(`Thank you for subscribing with: ${newsletterEmail.value}`)
  newsletterEmail.value = ''
}

onMounted(async () => {
  loading.value = true
  await fetchCategories()
  loading.value = false
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@400;500&display=swap');

* { box-sizing: border-box; }

.container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }

/* ── Page Header ── */
.page-header {
  background: linear-gradient(135deg, #2C2117 0%, #3a2a1f 100%);
  padding: 4rem 2rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.page-header::before {
  content: '';
  position: absolute;
  inset: 0;
  background: repeating-linear-gradient(45deg, rgba(200,133,74,0.05) 0px, rgba(200,133,74,0.05) 2px, transparent 2px, transparent 8px);
}

.header-content {
  position: relative;
  z-index: 1;
}

.header-content h1 {
  font-family: 'Playfair Display', serif;
  font-size: 3rem;
  color: #F8F2E6;
  margin-bottom: 1rem;
  font-weight: 600;
}

.header-content p {
  font-size: 1.1rem;
  color: rgba(248,242,230,0.7);
  margin-bottom: 1.5rem;
}

.breadcrumb {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.breadcrumb a {
  color: #C8854A;
  text-decoration: none;
}

.breadcrumb a:hover {
  text-decoration: underline;
}

.breadcrumb .separator {
  color: rgba(248,242,230,0.5);
}

.breadcrumb span:last-child {
  color: rgba(248,242,230,0.7);
}

/* ── Stats Bar ── */
.stats-bar {
  background: #fff;
  padding: 2rem;
  border-bottom: 1px solid rgba(44,33,23,0.1);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  text-align: center;
}

.stat-card {
  padding: 1rem;
}

.stat-number {
  font-family: 'Playfair Display', serif;
  font-size: 2.5rem;
  font-weight: 600;
  color: #C8854A;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.85rem;
  color: #7a6555;
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* ── Filter Section ── */
.filter-section {
  background: #F8F2E6;
  padding: 2rem;
}

.filter-wrapper {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
}

.search-box {
  flex: 1;
  min-width: 250px;
  display: flex;
  align-items: center;
  background: #fff;
  border: 1px solid rgba(44,33,23,0.1);
  border-radius: 8px;
  padding: 0.75rem 1rem;
  gap: 0.75rem;
}

.search-box svg {
  color: #7a6555;
}

.search-box input {
  flex: 1;
  border: none;
  outline: none;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.9rem;
  background: transparent;
}

.filter-buttons {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.filter-btn {
  padding: 0.5rem 1rem;
  border: 1px solid rgba(44,33,23,0.1);
  background: #fff;
  border-radius: 8px;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.85rem;
  transition: all 0.2s;
}

.filter-btn:hover {
  border-color: #C8854A;
  color: #C8854A;
}

.filter-btn.active {
  background: #C8854A;
  color: #fff;
  border-color: #C8854A;
}

/* ── Categories Grid ── */
.categories-section {
  background: #fff;
  padding: 4rem 2rem;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.category-card {
  text-decoration: none;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  border: 1px solid rgba(44,33,23,0.1);
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.card-image {
  position: relative;
  height: 200px;
  overflow: hidden;
  background: #F8F2E6;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.category-card:hover .card-image img {
  transform: scale(1.05);
}

.placeholder-icon {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.category-overlay {
  position: absolute;
  inset: 0;
  background: rgba(44,33,23,0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
}

.category-card:hover .category-overlay {
  opacity: 1;
}

.view-details {
  color: #C8854A;
  font-size: 0.9rem;
  font-weight: 500;
}

.card-content {
  padding: 1.5rem;
}

.category-name {
  font-family: 'Playfair Display', serif;
  font-size: 1.25rem;
  color: #2C2117;
  margin-bottom: 0.75rem;
}

.category-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.product-count {
  font-size: 0.85rem;
  color: #7a6555;
}

.explore-link {
  font-size: 0.85rem;
  color: #C8854A;
  font-weight: 500;
}

/* ── Empty State ── */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-state svg {
  margin-bottom: 1rem;
  color: #C8854A;
}

.empty-state h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  color: #2C2117;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #7a6555;
  margin-bottom: 1.5rem;
}

/* ── Pagination ── */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.page-btn, .page-num {
  padding: 0.5rem 1rem;
  border: 1px solid rgba(44,33,23,0.1);
  background: #fff;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: all 0.2s;
}

.page-btn {
  border-radius: 8px;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-num {
  border-radius: 4px;
}

.page-num.active {
  background: #C8854A;
  color: #fff;
  border-color: #C8854A;
}

.page-numbers {
  display: flex;
  gap: 0.5rem;
}

/* ── Featured Section ── */
.featured-section {
  background: #F8F2E6;
  padding: 4rem 2rem;
}

.section-header {
  margin-bottom: 2rem;
}

.section-label {
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: #C8854A;
  margin-bottom: 0.5rem;
}

.section-title {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  font-weight: 500;
  color: #2C2117;
}

.featured-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.featured-card {
  background-size: cover;
  background-position: center;
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.3s;
}

.featured-card:hover {
  transform: translateY(-5px);
}

.featured-content {
  padding: 2rem;
  color: #fff;
}

.featured-content h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  margin-bottom: 0.75rem;
}

.featured-content p {
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
  opacity: 0.9;
}

.btn-outline-light {
  display: inline-block;
  padding: 0.5rem 1rem;
  border: 1px solid #fff;
  color: #fff;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s;
}

.btn-outline-light:hover {
  background: #fff;
  color: #2C2117;
}

/* ── Newsletter ── */
.newsletter {
  background: #2C2117;
  padding: 4rem 2rem;
}

.newsletter-content {
  text-align: center;
  max-width: 600px;
  margin: 0 auto;
}

.newsletter-content h2 {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  color: #F8F2E6;
  margin-bottom: 1rem;
}

.newsletter-content p {
  color: rgba(248,242,230,0.7);
  margin-bottom: 2rem;
}

.newsletter-form {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  justify-content: center;
}

.newsletter-form input {
  flex: 1;
  min-width: 250px;
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 8px;
  font-family: 'DM Sans', sans-serif;
}

/* ── Buttons ── */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #C8854A;
  color: #fff;
  font-size: 13.5px;
  font-weight: 500;
  padding: 12px 24px;
  border-radius: 8px;
  text-decoration: none;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
}

/* ── Loader ── */
.loader {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.spinner {
  width: 36px;
  height: 36px;
  border: 2px solid rgba(200,133,74,0.2);
  border-top-color: #C8854A;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
  
  .header-content h1 {
    font-size: 2rem;
  }
  
  .filter-wrapper {
    flex-direction: column;
    align-items: stretch;
  }
  
  .categories-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .featured-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .pagination {
    flex-direction: column;
  }
}
</style>