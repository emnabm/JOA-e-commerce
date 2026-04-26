<!-- components/admin/AdminReviews.vue -->
<template>
  <div class="admin-reviews">
    <!-- Stats Cards -->
    <div class="stats-summary">
      <div class="stat-summary-card">
        <div class="stat-icon"></div>
        <div class="stat-info">
          <h3>{{ stats.averageRating }}</h3>
          <p>Average Rating</p>
        </div>
      </div>
      <div class="stat-summary-card">
        <div class="stat-icon"></div>
        <div class="stat-info">
          <h3>{{ stats.totalReviews }}</h3>
          <p>Total Reviews</p>
        </div>
      </div>
      <div class="stat-summary-card">
        <div class="stat-icon"></div>
        <div class="stat-info">
          <h3>{{ stats.positiveReviews }}</h3>
          <p>Positive (4-5★)</p>
        </div>
      </div>
      <div class="stat-summary-card">
        <div class="stat-icon"></div>
        <div class="stat-info">
          <h3>{{ stats.negativeReviews }}</h3>
          <p>Negative (1-2★)</p>
        </div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="table-toolbar">
      <div class="filters">
        <select v-model="ratingFilter" class="filter-select">
          <option value="all">All Ratings</option>
          <option value="5">★★★★★ (5)</option>
          <option value="4">★★★★☆ (4)</option>
          <option value="3">★★★☆☆ (3)</option>
          <option value="2">★★☆☆☆ (2)</option>
          <option value="1">★☆☆☆☆ (1)</option>
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
            placeholder="Search by product or user..."
          >
        </div>
      </div>
      <div class="stats">
        <span class="stat">Total: {{ filteredReviews.length }}</span>
      </div>
      <button class="btn-refresh" @click="refreshReviews">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
        Refresh
      </button>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>User</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Date</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="review in paginatedReviews" :key="review.id">
            <td>
              <div class="product-cell" @click="openDetailModal(review)">
                <span class="product-name">{{ review.product?.name || 'N/A' }}</span>
              </div>
            </td>
            <td>{{ review.user?.username || 'Anonymous' }}</td>
            <td>
              <div class="rating-stars">
                <span v-for="i in 5" :key="i" class="star" :class="{ filled: i <= (review.rating || 0) }">★</span>
              </div>
            </td>
            <td>
              <div class="review-text" :title="review.text" @click="openDetailModal(review)">
                {{ truncateText(review.text, 60) }}
              </div>
            </td>
            <td>{{ formatDate(review.date) }}</td>
            <td class="actions">
              <button title="View Details" class="action-btn view" @click="openDetailModal(review)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <circle cx="12" cy="12" r="3"/>
                  <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7z"/>
                </svg>
              </button>
            </td>
          </tr>
          <tr v-if="paginatedReviews.length === 0">
            <td colspan="6" class="empty-row">No reviews found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="pagination">
      <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">← Prev</button>
      <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
      <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">Next →</button>
    </div>

    <!-- Review Detail Modal -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="closeDetailModal">
      <div class="modal-content modal-review">
        <div class="modal-header">
          <h3>Review Details</h3>
          <button class="close-modal" @click="closeDetailModal">✕</button>
        </div>
        <div class="review-detail">
          <div class="detail-row">
            <label>Product:</label>
            <span class="detail-value">{{ selectedReview?.product?.name }}</span>
          </div>
          <div class="detail-row">
            <label>User:</label>
            <span class="detail-value">{{ selectedReview?.user?.username }}</span>
          </div>
          <div class="detail-row">
            <label>Rating:</label>
            <div class="rating-stars large">
              <span v-for="i in 5" :key="i" class="star" :class="{ filled: i <= (selectedReview?.rating || 0) }">★</span>
            </div>
          </div>
          <div class="detail-row">
            <label>Review:</label>
            <p class="full-review">{{ selectedReview?.text }}</p>
          </div>
          <div class="detail-row">
            <label>Date:</label>
            <span class="detail-value">{{ formatDate(selectedReview?.date) }}</span>
          </div>
        </div>
        <div class="modal-actions">
          <button class="btn-close" @click="closeDetailModal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const reviews = ref([])
const ratingFilter = ref('all')
const searchTerm = ref('')
const currentPage = ref(1)
const itemsPerPage = 5
const showDetailModal = ref(false)
const selectedReview = ref(null)

const stats = ref({
  averageRating: 0,
  totalReviews: 0,
  positiveReviews: 0,
  negativeReviews: 0
})

const fetchReviews = async () => {
  try {
    const response = await axios.get('http://localhost:8000/admin/reviews')
    reviews.value = response.data
    
    // Calculate stats
    const total = reviews.value.length
    const avg = total > 0 ? reviews.value.reduce((sum, r) => sum + (r.rating || 0), 0) / total : 0
    const positive = reviews.value.filter(r => (r.rating || 0) >= 4).length
    const negative = reviews.value.filter(r => (r.rating || 0) <= 2).length
    
    stats.value = {
      averageRating: avg.toFixed(1),
      totalReviews: total,
      positiveReviews: positive,
      negativeReviews: negative
    }
  } catch (error) {
    console.error('Error fetching reviews:', error)
  }
}

const truncateText = (text, max) => {
  if (!text) return ''
  if (text.length <= max) return text
  return text.substring(0, max) + '...'
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const filteredReviews = computed(() => {
  let filtered = [...reviews.value]
  
  if (ratingFilter.value !== 'all') {
    filtered = filtered.filter(r => (r.rating || 0) === parseInt(ratingFilter.value))
  }
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(r => 
      r.product?.name?.toLowerCase().includes(term) ||
      r.user?.username?.toLowerCase().includes(term) ||
      r.text?.toLowerCase().includes(term)
    )
  }
  
  return filtered
})

const paginatedReviews = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredReviews.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => Math.ceil(filteredReviews.value.length / itemsPerPage))

const refreshReviews = () => {
  fetchReviews()
}

const openDetailModal = (review) => {
  selectedReview.value = review
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedReview.value = null
}

onMounted(fetchReviews)
</script>

<style scoped>
.admin-reviews {
  padding: 1.5rem;
}

/* Stats Summary */
.stats-summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stat-summary-card {
  background: #F8F2E6;
  border-radius: 12px;
  padding: 1rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.2s;
}

.stat-summary-card:hover {
  transform: translateY(-2px);
}

.stat-icon {
  font-size: 2rem;
}

.stat-info h3 {
  font-size: 1.5rem;
  margin: 0;
  color: #2C2117;
  font-family: 'Playfair Display', serif;
}

.stat-info p {
  font-size: 0.7rem;
  margin: 0;
  color: #7a6555;
}

/* Toolbar */
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

.filter-select {
  padding: 0.5rem 1rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 8px;
  background: #fff;
  font-size: 0.85rem;
  cursor: pointer;
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
  min-width: 200px;
  font-size: 0.85rem;
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

.btn-refresh {
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

.btn-refresh:hover {
  background: #b67038;
}

/* Table */
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

.empty-row {
  text-align: center;
  color: #7a6555;
  padding: 2rem;
}

.product-cell {
  cursor: pointer;
}

.product-name {
  font-weight: 500;
  color: #2C2117;
}

.rating-stars {
  display: flex;
  gap: 2px;
}

.star {
  font-size: 12px;
  color: #ddd;
}

.star.filled {
  color: #FFB800;
}

.rating-stars.large .star {
  font-size: 18px;
}

.review-text {
  max-width: 300px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  cursor: pointer;
  color: #5a4a3a;
}

/* Actions */
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

.action-btn.view {
  background: #F8F2E6;
  color: #2C2117;
}

.action-btn.view:hover {
  background: #C8854A;
  color: #fff;
  transform: scale(1.05);
}

/* Pagination */
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
  transition: all 0.2s;
  font-size: 0.85rem;
}

.page-btn:hover:not(:disabled) {
  background: #C8854A;
  color: #fff;
  border-color: #C8854A;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 0.85rem;
  color: #7a6555;
}

/* Modal */
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
  max-height: 90vh;
  overflow-y: auto;
}

.modal-review {
  max-width: 600px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid rgba(44, 33, 23, 0.1);
}

.modal-header h3 {
  margin: 0;
  color: #2C2117;
  font-family: 'Playfair Display', serif;
}

.close-modal {
  background: none;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  color: #7a6555;
}

.review-detail {
  padding: 1.5rem;
}

.detail-row {
  display: flex;
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(44, 33, 23, 0.08);
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row label {
  width: 100px;
  font-weight: 600;
  color: #2C2117;
}

.detail-value {
  color: #5a4a3a;
}

.full-review {
  flex: 1;
  margin: 0;
  line-height: 1.5;
  background: #F8F2E6;
  padding: 0.75rem;
  border-radius: 8px;
  white-space: pre-wrap;
  color: #2C2117;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid rgba(44, 33, 23, 0.1);
}

.btn-close {
  flex: 1;
  padding: 0.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.2s;
  background: #C8854A;
  border: none;
  color: #fff;
}

.btn-close:hover {
  background: #b67038;
}

/* Responsive */
@media (max-width: 768px) {
  .admin-reviews {
    padding: 1rem;
  }
  
  .table-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filters {
    flex-direction: column;
  }
  
  .stats {
    justify-content: flex-start;
  }
  
  .search-wrapper input {
    min-width: auto;
  }
  
  .detail-row {
    flex-direction: column;
  }
  
  .detail-row label {
    width: 100%;
    margin-bottom: 0.25rem;
  }
}
</style>