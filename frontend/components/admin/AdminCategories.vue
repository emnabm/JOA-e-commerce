<!-- components/admin/AdminCategories.vue -->
<template>
  <div class="admin-categories">
    <div class="table-toolbar">
      <div class="filters">
        <div class="search-wrapper">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input 
            v-model="searchTerm" 
            type="text" 
            class="search-input" 
            placeholder="Search categories..."
          >
        </div>
      </div>
      <div class="stats">
        <span class="stat">Total: {{ filteredCategories.length }}</span>
      </div>
      <button class="btn-add" @click="openCategoryModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Category
      </button>
    </div>

    <div class="categories-grid">
      <div v-for="category in paginatedCategories" :key="category.id" class="category-card">
        <div class="category-image">
          <img :src="getImageUrl(category.img)" :alt="category.name">
          <div class="category-actions">
            <button title="Edit" class="action-btn edit" @click="openCategoryModal(category)">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M17 3l4 4-7 7H10v-4l7-7z"/>
                <path d="M4 20h16"/>
              </svg>
            </button>
            <button title="Delete" class="action-btn delete" @click="deleteCategory(category.id)">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="category-info">
          <h3>{{ category.name }}</h3>
          <p>{{ getProductCount(category.id) }} products</p>
        </div>
      </div>
    </div>

    <div v-if="totalPages > 1" class="pagination">
      <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">← Prev</button>
      <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
      <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">Next →</button>
    </div>

    <!-- Category Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ editingCategory ? 'Edit Category' : 'Add Category' }}</h3>
          <button class="close-modal" @click="closeModal">✕</button>
        </div>
        <form @submit.prevent="saveCategory">
          <div class="form-group">
            <label>Category Name *</label>
            <input v-model="form.name" type="text" required>
          </div>
          <div class="form-group">
            <label>Image URL</label>
            <input v-model="form.img" type="text" placeholder="/images/category.jpg">
          </div>
          <div v-if="form.img" class="image-preview">
            <img :src="getImageUrl(form.img)" alt="Preview" class="preview-img">
          </div>
          <div class="modal-actions">
            <button type="button" class="btn-cancel" @click="closeModal">Cancel</button>
            <button type="submit" class="btn-save">Save Category</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const categories = ref([])
const products = ref([])
const loading = ref(true)
const searchTerm = ref('')
const currentPage = ref(1)
const itemsPerPage = 12
const showModal = ref(false)
const editingCategory = ref(null)

const form = ref({
  name: '',
  img: ''
})

const fetchCategories = async () => {
  try {
    const response = await axios.get('http://localhost:8000/admin/categories')
    categories.value = response.data
  } catch (error) {
    console.error('Error fetching categories:', error)
  } finally {
    loading.value = false
  }
}

const fetchProducts = async () => {
  try {
    const response = await axios.get('http://localhost:8000/admin/products')
    products.value = response.data
  } catch (error) {
    console.error('Error fetching products for category counts:', error)
  }
}

const getImageUrl = (image) => {
  if (!image) return '/default-category.jpg'
  if (image.startsWith('http')) return image
  return `/${image}`
}

const filteredCategories = computed(() => {
  let filtered = [...categories.value]
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(c => c.name.toLowerCase().includes(term))
  }
  
  return filtered
})

const productCountsByCategory = computed(() => {
  return products.value.reduce((acc, product) => {
    const categoryId = product?.category?.id
    if (!categoryId) return acc
    acc[categoryId] = (acc[categoryId] || 0) + 1
    return acc
  }, {})
})

const getProductCount = (categoryId) => productCountsByCategory.value[categoryId] || 0

const paginatedCategories = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredCategories.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => Math.ceil(filteredCategories.value.length / itemsPerPage))

const openCategoryModal = (category = null) => {
  editingCategory.value = category
  if (category) {
    form.value = {
      name: category.name,
      img: category.img || ''
    }
  } else {
    form.value = {
      name: '',
      img: ''
    }
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingCategory.value = null
}

const saveCategory = async () => {
  try {
    if (editingCategory.value) {
      await axios.put(`http://localhost:8000/admin/categories/${editingCategory.value.id}`, form.value)
    } else {
      await axios.post('http://localhost:8000/admin/categories', form.value)
    }
    await Promise.all([fetchCategories(), fetchProducts()])
    closeModal()
  } catch (error) {
    alert('Error saving category: ' + (error.response?.data?.message || error.message))
  }
}

const deleteCategory = async (id) => {
  if (confirm('Are you sure you want to delete this category? Products in this category will be affected.')) {
    try {
      await axios.delete(`http://localhost:8000/admin/categories/${id}`)
      await Promise.all([fetchCategories(), fetchProducts()])
    } catch (error) {
      alert('Error deleting category',error)
    }
  }
}

onMounted(async () => {
  await Promise.all([fetchCategories(), fetchProducts()])
})
</script>

<style scoped>
.admin-categories {
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
  min-width: 250px;
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
  color: #2C2117;
}

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

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.category-card {
  background: #fff;
  border: 1px solid rgba(44, 33, 23, 0.08);
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.category-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.category-image {
  position: relative;
  height: 140px;
  overflow: hidden;
}

.category-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.category-card:hover .category-image img {
  transform: scale(1.05);
}

.category-actions {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  display: flex;
  gap: 0.25rem;
  opacity: 0;
  transition: opacity 0.2s;
}

.category-card:hover .category-actions {
  opacity: 1;
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

.category-info {
  padding: 1rem;
  text-align: center;
}

.category-info h3 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
  color: #2C2117;
}

.category-info p {
  font-size: 0.7rem;
  color: #7a6555;
  margin: 0;
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
  max-width: 500px;
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

.form-group input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 8px;
}

.image-preview {
  margin-top: 1rem;
  text-align: center;
}

.preview-img {
  max-width: 100%;
  max-height: 100px;
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
  
  .categories-grid {
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  }
}
</style>