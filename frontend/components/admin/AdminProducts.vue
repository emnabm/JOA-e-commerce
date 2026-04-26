<template>
  <div class="admin-products">
    <div class="table-toolbar">
      <div class="filters">
        <select v-model="stockFilter" class="filter-select">
          <option value="all">All Products</option>
          <option value="in_stock">In Stock</option>
          <option value="low_stock">Low Stock (≤20)</option>
          <option value="out_of_stock">Out of Stock</option>
        </select>
        <select v-model="categoryFilter" class="filter-select">
          <option value="all">All Categories</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <div class="search-wrapper">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input 
            v-model="searchTerm" 
            type="text" 
            class="search-input" 
            placeholder="Search products..."
          >
        </div>
      </div>
      <div class="stats">
        <span class="stat">Total: {{ filteredProducts.length }}</span>
        <span class="stat in-stock">In Stock: {{ inStockCount }}</span>
        <span class="stat low-stock">Low Stock: {{ lowStockCount }}</span>
      </div>
      <button class="btn-add" @click="openProductModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Product
      </button>
    </div>

    <div class="table-wrapper">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Unit</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in paginatedProducts" :key="product.id">
            <td>
              <img :src="getImageUrl(product.image)" :alt="product.nom" class="product-thumb">
            </td>
            <td>
              <div class="product-name" :title="product.nom">
                {{ truncateText(product.nom, 30) }}
              </div>
            </td>
            <td>{{ product.category?.name || '-' }}</td>
            <td class="price">{{ product.prix }} DT</td>
            <td class="stock" :class="{ 'stock-low': product.stock <= 20 && product.stock > 0, 'stock-out': product.stock === 0 }">
              {{ product.stock }}
            </td>
            <td>{{ product.unit || '-' }}</td>
            <td>
              <span :class="['status-badge', getStockStatus(product.stock)]">
                {{ getStockLabel(product.stock) }}
              </span>
            </td>
            <td class="actions">
              <button title="Edit" class="action-btn edit" @click="openProductModal(product)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M17 3l4 4-7 7H10v-4l7-7z"/>
                  <path d="M4 20h16"/>
                </svg>
              </button>
              <button title="Delete" class="action-btn delete" @click="deleteProduct(product.id)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="totalPages > 1" class="pagination">
      <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">← Prev</button>
      <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
      <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">Next →</button>
    </div>

    <!-- Product Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content modal-large">
        <div class="modal-header">
          <h3>{{ editingProduct ? 'Edit Product' : 'Add Product' }}</h3>
          <button class="close-modal" @click="closeModal">✕</button>
        </div>
        <form @submit.prevent="saveProduct">
          <div class="form-row">
            <div class="form-group">
              <label>Name *</label>
              <input v-model="form.nom" type="text" required>
            </div>
            <div class="form-group">
              <label>Category *</label>
              <select v-model="form.category" required>
                <option value="">Select category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label>Description</label>
            <textarea v-model="form.description" rows="3" placeholder="Product description..."></textarea>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Price (DT) *</label>
              <input v-model.number="form.prix" type="number" step="0.001" required>
            </div>
            <div class="form-group">
              <label>Stock *</label>
              <input v-model.number="form.stock" type="number" min="0" required>
            </div>
            <div class="form-group">
              <label>Unit</label>
              <input v-model="form.unit" type="text" placeholder="kg, L, pièce...">
            </div>
          </div>
          
          <div class="form-group">
            <label>Image URL</label>
            <input v-model="form.image" type="text" placeholder="/images/product.jpg">
          </div>
          
          <div class="modal-actions">
            <button type="button" class="btn-cancel" @click="closeModal">Cancel</button>
            <button type="submit" class="btn-save">Save Product</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])
const categories = ref([])
const stockFilter = ref('all')
const categoryFilter = ref('all')
const searchTerm = ref('')
const currentPage = ref(1)
const itemsPerPage = 5
const showModal = ref(false)
const editingProduct = ref(null)

const form = ref({
  nom: '',
  description: '',
  prix: 0,
  stock: 0,
  unit: '',
  image: '',
  category: ''
})

const fetchCategories = async () => {
  const res = await axios.get('http://localhost:8000/admin/categories')
  categories.value = res.data
}

const fetchProducts = async () => {
  try {
    const response = await axios.get('http://localhost:8000/admin/products')
    products.value = response.data
  } catch (error) {
    console.error('Error fetching products:', error)
  }
}

const getImageUrl = (image) => {
  if (!image) return '/default-product.jpg'
  if (image.startsWith('http')) return image
  return `/${image}`
}

const truncateText = (text, max) => text?.length > max ? text.substring(0, max) + '...' : text || '-'

const getStockStatus = (stock) => {
  if (stock === 0) return 'out'
  if (stock <= 20) return 'low'
  return 'in'
}

const getStockLabel = (stock) => {
  if (stock === 0) return 'Out of Stock'
  if (stock <= 20) return 'Low Stock'
  return 'In Stock'
}

const filteredProducts = computed(() => {
  let filtered = [...products.value]
  
  if (stockFilter.value === 'in_stock') {
    filtered = filtered.filter(p => p.stock > 0)
  } else if (stockFilter.value === 'low_stock') {
    filtered = filtered.filter(p => p.stock > 0 && p.stock <= 20)
  } else if (stockFilter.value === 'out_of_stock') {
    filtered = filtered.filter(p => p.stock === 0)
  }
  
  if (categoryFilter.value !== 'all') {
    filtered = filtered.filter(p => p.category?.id === parseInt(categoryFilter.value))
  }
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(p => 
      p.nom?.toLowerCase().includes(term) ||
      p.description?.toLowerCase().includes(term)
    )
  }
  
  return filtered
})

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredProducts.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage))
const inStockCount = computed(() => products.value.filter(p => p.stock > 0).length)
const lowStockCount = computed(() => products.value.filter(p => p.stock > 0 && p.stock <= 20).length)

const openProductModal = (product = null) => {
  editingProduct.value = product
  if (product) {
    form.value = {
      nom: product.nom,
      description: product.description || '',
      prix: product.prix,
      stock: product.stock,
      unit: product.unit || '',
      image: product.image || '',
      category: product.category?.id || ''
    }
  } else {
    form.value = {
      nom: '',
      description: '',
      prix: 0,
      stock: 0,
      unit: '',
      image: '',
      category: ''
    }
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingProduct.value = null
}

const saveProduct = async () => {
  try {
    const payload = {
      nom: form.value.nom,
      description: form.value.description,
      prix: form.value.prix,
      stock: form.value.stock,
      unit: form.value.unit,
      image: form.value.image,
      category: form.value.category
    }
    
    if (editingProduct.value) {
      await axios.put(`http://localhost:8000/admin/products/${editingProduct.value.id}`, payload)
    } else {
      await axios.post('http://localhost:8000/admin/addProduct', payload)
    }
    
    await fetchProducts()
    closeModal()
  } catch (error) {
    alert('Error saving product: ' + (error.response?.data?.message || error.message))
  }
}

const deleteProduct = async (id) => {
  if (confirm('Are you sure you want to delete this product?')) {
    try {
      await axios.delete(`http://localhost:8000/api/products/${id}`)
      await fetchProducts()
    } catch (error) {
      alert('Error deleting product', error)
    }
  }
}

onMounted(() => {
  fetchCategories()
  fetchProducts()
})
</script>

<style scoped>
.admin-products {
  padding: 1.5rem;
}

.table-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.filters {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  align-items: center;
}

.filter-select, .search-input {
  padding: 0.5rem 1rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 8px;
  background: #fff;
}

.search-wrapper {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 8px;
  background: #fff;
}

.search-wrapper input {
  border: none;
  outline: none;
  background: transparent;
}

.stats {
  display: flex;
  gap: 1rem;
}

.stat {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  background: #F8F2E6;
}

.stat.in-stock { color: #4CAF50; }
.stat.low-stock { color: #FF9800; }

.btn-add {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #C8854A;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-add:hover {
  background: #b67038;
}

.table-wrapper {
  overflow-x: auto;
}

.admin-table {
  width: 100%;
  border-collapse: collapse;
}

.admin-table th,
.admin-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid rgba(44, 33, 23, 0.08);
}

.admin-table th {
  background: #F8F2E6;
  font-weight: 600;
  color: #2C2117;
}

.product-thumb {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 8px;
}

.product-name {
  font-weight: 500;
  color: #2C2117;
}

.stock-low {
  color: #FF9800;
  font-weight: 500;
}

.stock-out {
  color: #f44336;
  font-weight: 500;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.7rem;
  font-weight: 500;
}

.status-badge.in { background: rgba(76, 175, 80, 0.1); color: #4CAF50; }
.status-badge.low { background: rgba(255, 152, 0, 0.1); color: #FF9800; }
.status-badge.out { background: rgba(244, 67, 54, 0.1); color: #f44336; }

.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.action-btn.edit { background: #F8F2E6; color: #2C2117; }
.action-btn.delete { background: rgba(244, 67, 54, 0.1); color: #f44336; }

.action-btn:hover {
  transform: scale(1.05);
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(44, 33, 23, 0.08);
}

.page-btn {
  padding: 0.5rem 1rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 8px;
  background: #fff;
  cursor: pointer;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: #fff;
  border-radius: 16px;
  width: 90%;
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid rgba(44, 33, 23, 0.1);
}

.close-modal {
  background: none;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
}

form {
  padding: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.25rem;
  font-size: 0.85rem;
  font-weight: 500;
  color: #2C2117;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 8px;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.btn-cancel, .btn-save {
  flex: 1;
  padding: 0.5rem;
  border-radius: 8px;
  cursor: pointer;
}

.btn-cancel {
  background: none;
  border: 1px solid rgba(44, 33, 23, 0.2);
}

.btn-save {
  background: #C8854A;
  border: none;
  color: #fff;
}

@media (max-width: 768px) {
  .table-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  
  .stats {
    justify-content: space-around;
  }
}
</style>