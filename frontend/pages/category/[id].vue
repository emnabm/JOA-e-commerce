<template>
  <div v-if="!loading">
    <AppHeader />

    <!-- ═══ CATEGORY HEADER ═══ -->
    <section class="category-header" :style="{ backgroundImage: `linear-gradient(135deg, rgba(44,33,23,0.95), rgba(44,33,23,0.85)), url(${categoryBanner})` }">
      <div class="container">
        <div class="header-content">
          <div class="breadcrumb">
            <NuxtLink to="/">Home</NuxtLink>
            <span class="separator">/</span>
            <NuxtLink to="/category">Categories</NuxtLink>
            <span class="separator">/</span>
            <span>{{ category?.name || 'Category' }}</span>
          </div>
          <h1>{{ category?.name }}</h1>
          <p v-if="category?.description">{{ category.description }}</p>
          <div class="category-stats">
            <div class="stat">
              <span class="stat-number">{{ products.length }}</span>
              <span class="stat-label">Products</span>
            </div>
            <div class="stat">
              <span class="stat-number">{{ inStockCount }}</span>
              <span class="stat-label">In Stock</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FILTERS & SIDEBAR  -->
    <section class="products-section">
      <div class="container">
        <div class="products-layout">
          <!-- Sidebar Filters -->
          <aside class="filters-sidebar">
            <div class="filter-group">
              <h3>Search</h3>
              <div class="search-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <circle cx="11" cy="11" r="8"/>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input 
                  v-model="searchQuery" 
                  type="text" 
                  placeholder="Search products..."
                  @input="filterProducts">
              </div>
            </div>

            <div class="filter-group">
              <h3>Price Range</h3>
              <div class="price-range">
                <input 
                  v-model="priceRange" 
                  type="range" 
                  :min="minPrice" 
                  :max="maxPrice"
                  step="1"
                  class="price-slider">
                <div class="price-inputs">
                  <input  v-model.number="priceMin" type="number" :min="minPrice" :max="maxPrice" >
                  <span>—</span>
                  <input v-model.number="priceMax" type="number" :min="minPrice" :max="maxPrice" >
                </div>
              </div>
            </div>

            <div class="filter-group">
              <h3>Availability</h3>
              <label class="checkbox-label">
                <input v-model="showInStockOnly"  type="checkbox">
                <span>In Stock Only</span>
              </label>
            </div>

            <button v-if="hasActiveFilters" class="clear-filters" @click="clearFilters" >
              Clear All Filters
            </button>
          </aside>

          <!-- Products Main Content -->
          <div class="products-main">
            <!-- Sort and View Options -->
            <div class="products-toolbar">
              <div class="results-count">
                Showing {{ filteredProducts.length }} of {{ products.length }} products
              </div>
              <div class="sort-options">
                <label>Sort by:</label>
                <select v-model="sortBy" @change="sortProducts">
                  <option value="default">Default</option>
                  <option value="price-asc">Price: Low to High</option>
                  <option value="price-desc">Price: High to Low</option>
                  <option value="name-asc">Name: A to Z</option>
                  <option value="name-desc">Name: Z to A</option>
                  <option value="rating">Best Rating</option>
                </select>
              </div>
              <div class="view-options">
                <button :class="{ active: viewMode === 'grid' }" @click="viewMode = 'grid'" >
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                  </svg>
                </button>
                <button :class="{ active: viewMode === 'list' }" @click="viewMode = 'list'">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Products Grid/List -->
            <div v-if="paginatedProducts.length > 0" :class="['products-container', viewMode]">
              <product-card
                v-for="product in paginatedProducts"
                :id="product.id"
                :key="product.id"
                :name="product.nom"
                :img="product.img"
                :description="product.description"
                :prix="product.prix"
                :stock="product.stock"
                :unit="product.unit"
                :view-mode="viewMode"
                :rating="product.rating || 0"
              />
            </div>

            <!-- Empty State -->
            <div v-else class="empty-state">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
              </svg>
              <h3>No products found</h3>
              <p>Try adjusting your filters or search criteria</p>
              <button class="btn-primary" @click="clearFilters">Clear Filters</button>
            </div>

            <!-- Pagination -->
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
        </div>
      </div>
    </section>

    <!-- ═══ RELATED CATEGORIES ═══ -->
    <section v-if="relatedCategories.length > 0" class="related-section">
      <div class="container">
        <div class="section-header">
          <div class="section-label">You might also like</div>
          <h2 class="section-title">Related Categories</h2>
        </div>
        <div class="related-grid">
          <NuxtLink
            v-for="cat in relatedCategories"
            :key="cat.id"
            :to="`/category/${cat.id}`"
            class="related-card"
          >
            <div class="related-icon">
              <img v-if="cat.img" :src="cat.img" :alt="cat.name" >
              <svg v-else width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.4">
                <rect x="2" y="3" width="20" height="14" rx="2"/>
                <path d="M8 21h8M12 17v4"/>
              </svg>
            </div>
            <h3>{{ cat.name }}</h3>
            <span>Explore →</span>
          </NuxtLink>
        </div>
      </div>
    </section>

    <AppFooter />
  </div>

  <div v-else class="loader">
    <div class="spinner"></div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const categoryId = computed(() => route.params.id)

const category = ref(null)
const products = ref([])
const allCategories = ref([])
const loading = ref(false)

// Filter states
const searchQuery = ref('')
const priceRange = ref(1000)
const priceMin = ref(0)
const priceMax = ref(1000)
const showInStockOnly = ref(false)
const sortBy = ref('default')
const viewMode = ref('grid')
const currentPage = ref(1)
const itemsPerPage = 11

let minPriceGlobal = 0
let maxPriceGlobal = 1000
const rating = ref(0)

const fetchCategory = async () => {
  try {
    const response = await axios.get(`http://localhost:8000/category/${categoryId.value}`)
    category.value = response.data
  } catch (err) {
    console.error('Error fetching category:', err)
  }
}

const fetchProducts = async () => {
  try {
    const response = await axios.get(`http://localhost:8000/products_${categoryId.value}`)
    products.value = response.data
    
    // Calculate price range
    if (products.value.length > 0) {
      const prices = products.value.map(p => p.prix)
      minPriceGlobal = Math.min(...prices)
      maxPriceGlobal = Math.max(...prices)
      priceMin.value = minPriceGlobal
      priceMax.value = maxPriceGlobal
      priceRange.value = maxPriceGlobal
    }
  } catch (err) {
    console.error('Error fetching products:', err)
  }
}
const getReview =async() => {
  try {
    const response = await axios(`http://localhost:8000/api/reviewCount/${id}`);
    rating.value = response.data.count;
  } catch (error) {
    console.error('Error fetching reviews:', error);
    return null;
  }
}

const fetchAllCategories = async () => {
  try {
    const response = await axios.get('http://localhost:8000/category')
    allCategories.value = response.data
  } catch (err) {
    console.error('Error fetching categories:', err)
  }
}

const filteredProducts = computed(() => {
  let filtered = [...products.value]
  
  // Search filter
  if (searchQuery.value) {
    filtered = filtered.filter(p => 
      p.nom?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      p.description?.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  
  // Price filter
  filtered = filtered.filter(p => 
    p.prix >= priceMin.value && p.prix <= priceMax.value
  )
  
  // Stock filter
  if (showInStockOnly.value) {
    filtered = filtered.filter(p => p.stock > 0)
  }
  
  return filtered
})

const sortedProducts = computed(() => {
  const productsToSort = [...filteredProducts.value]
  
  switch (sortBy.value) {
    case 'price-asc':
      return productsToSort.sort((a, b) => a.prix - b.prix)
    case 'price-desc':
      return productsToSort.sort((a, b) => b.prix - a.prix)
    case 'name-asc':
      return productsToSort.sort((a, b) => a.nom?.localeCompare(b.nom))
    case 'name-desc':
      return productsToSort.sort((a, b) => b.nom?.localeCompare(a.nom))
    case 'rating':
      return productsToSort.sort((a, b) => (b.rating || 0) - (a.rating || 0))
    default:
      return productsToSort
  }
})

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return sortedProducts.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(sortedProducts.value.length / itemsPerPage)
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

const inStockCount = computed(() => {
  return products.value.filter(p => p.stock > 0).length
})

const hasActiveFilters = computed(() => {
  return searchQuery.value !== '' || 
         priceMin.value !== minPriceGlobal || 
         priceMax.value !== maxPriceGlobal || 
         showInStockOnly.value === true
})

const relatedCategories = computed(() => {
  // Show 4 random categories excluding current one
  return allCategories.value
    .filter(c => c.id !== category.value?.id)
    .slice(0, 4)
})

const categoryBanner = computed(() => {
  return category.value?.banner || category.value?.img || '/default-category-bg.jpg'
})

const filterProducts = () => {
  currentPage.value = 1
}

const sortProducts = () => {
  currentPage.value = 1
}

const clearFilters = () => {
  searchQuery.value = ''
  priceMin.value = minPriceGlobal
  priceMax.value = maxPriceGlobal
  showInStockOnly.value = false
  sortBy.value = 'default'
  currentPage.value = 1
}

const changePage = (page) => {
  currentPage.value = page
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Watch for price range changes
watch(priceRange, (newValue) => {
  priceMax.value = newValue
  currentPage.value = 1
})

watch([priceMin, priceMax], () => {
  currentPage.value = 1
})

onMounted(async () => {
  loading.value = true
  await Promise.all([
    getReview(),
    fetchCategory(),
    fetchProducts(),
    fetchAllCategories()
  ])
  loading.value = false
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@400;500&display=swap');

* { box-sizing: border-box; }

.container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; }

/* ── Category Header ── */
.category-header {
  background-size: cover;
  background-position: center;
  padding: 4rem 2rem;
  position: relative;
}

.header-content {
  position: relative;
  z-index: 1;
  color: #F8F2E6;
}

.breadcrumb {
  display: flex;
  gap: 0.5rem;
  font-size: 0.85rem;
  margin-bottom: 2rem;
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

.category-header h1 {
  font-family: 'Playfair Display', serif;
  font-size: 3rem;
  margin-bottom: 1rem;
}

.category-header p {
  font-size: 1rem;
  max-width: 600px;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.category-stats {
  display: flex;
  gap: 2rem;
}

.stat {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  font-weight: 600;
  color: #C8854A;
}

.stat-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  opacity: 0.7;
}

/* ── Products Layout ── */
.products-section {
  padding: 4rem 0;
  background: #fff;
}

.products-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 2rem;
}

/* Sidebar Filters */
.filters-sidebar {
  background: #F8F2E6;
  padding: 1.5rem;
  border-radius: 12px;
  height: fit-content;
  position: sticky;
  top: 20px;
}

.filter-group {
  margin-bottom: 2rem;
}

.filter-group h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  margin-bottom: 1rem;
  color: #2C2117;
}

.search-box {
  display: flex;
  align-items: center;
  background: #fff;
  border: 1px solid rgba(44,33,23,0.1);
  border-radius: 8px;
  padding: 0.5rem 0.75rem;
  gap: 0.5rem;
}

.search-box input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  font-family: 'DM Sans', sans-serif;
}

.price-range {
  margin-top: 1rem;
}

.price-slider {
  width: 100%;
  margin-bottom: 1rem;
}

.price-inputs {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.price-inputs input {
  width: 80px;
  padding: 0.25rem 0.5rem;
  border: 1px solid rgba(44,33,23,0.1);
  border-radius: 4px;
  font-family: 'DM Sans', sans-serif;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.clear-filters {
  width: 100%;
  padding: 0.5rem;
  background: #C8854A;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.85rem;
}

/* Products Main */
.products-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(44,33,23,0.1);
  flex-wrap: wrap;
  gap: 1rem;
}

.results-count {
  font-size: 0.85rem;
  color: #7a6555;
}

.sort-options {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.sort-options select {
  padding: 0.25rem 0.5rem;
  border: 1px solid rgba(44,33,23,0.1);
  border-radius: 4px;
  font-family: 'DM Sans', sans-serif;
}

.view-options {
  display: flex;
  gap: 0.5rem;
}

.view-options button {
  padding: 0.25rem;
  background: none;
  border: 1px solid rgba(44,33,23,0.1);
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.view-options button.active {
  background: #C8854A;
  color: #fff;
  border-color: #C8854A;
}

/* Products Container */
.products-container.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.products-container.list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 3rem;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-state svg {
  margin-bottom: 1rem;
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

/* Pagination */
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

/* Related Categories */
.related-section {
  background: #F8F2E6;
  padding: 4rem 2rem;
}

.section-header {
  margin-bottom: 2rem;
  text-align: center;
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

.related-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.related-card {
  background: #fff;
  padding: 1.5rem;
  text-align: center;
  text-decoration: none;
  border-radius: 12px;
  transition: transform 0.3s;
}

.related-card:hover {
  transform: translateY(-5px);
}

.related-icon {
  width: 60px;
  height: 60px;
  margin: 0 auto 1rem;
  background: #F8F2E6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.related-card h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  color: #2C2117;
  margin-bottom: 0.5rem;
}

.related-card span {
  font-size: 0.75rem;
  color: #C8854A;
}

/* Buttons */
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

/* Loader */
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

/* Responsive */
@media (max-width: 968px) {
  .products-layout {
    grid-template-columns: 1fr;
  }
  
  .filters-sidebar {
    position: static;
  }
  
  .products-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  
  .related-grid {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
  
  .category-header h1 {
    font-size: 2rem;
  }
  
  .products-container.grid {
    grid-template-columns: 1fr;
  }
}
</style>