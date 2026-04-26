<template>
  <div class="admin-orders">
    <div class="table-toolbar">
      <div class="filters">
        <select v-model="statusFilter" class="filter-select">
          <option value="all">All Orders</option>
          <option value="pending">Pending</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <input v-model="dateFrom" type="date" class="date-input">
        <input v-model="dateTo" type="date" class="date-input">
      </div>
      <div class="stats">
        <span class="stat">Total: {{ totalOrders }}</span>
        <span class="stat pending">Pending: {{ pendingCount }}</span>
        <span class="stat delivered">Delivered: {{ deliveredCount }}</span>
      </div>
    </div>

    <div class="table-wrapper">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in paginatedOrders" :key="order.id">
            <td>#{{ order.id }}</td>
            <td>{{ order.user?.username || 'N/A' }}</td>
            <td>{{ formatDate(order.date) }}</td>
            <td>{{ order.total }} DT</td>
            <td>
              <span :class="['status-badge', order.status]">
                {{ order.status }}
              </span>
            </td>
            <td class="actions">
              <button title="View" class="action-btn view" @click="viewOrder(order)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <circle cx="12" cy="12" r="3"/>
                  <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7z"/>
                </svg>
              </button>
              <button v-if="order.status === 'pending'" class="action-btn success" title="Confirm" @click="updateStatus(order.id, 'delivered')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
              </button>
              <button v-if="order.status === 'pending'" class="action-btn danger"  title="Cancel" @click="updateStatus(order.id, 'cancelled')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="totalPages > 1" class="pagination" >
      <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">← Prev</button>
      <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
      <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">Next →</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const orders = ref([])
const loading = ref(true)
const statusFilter = ref('all')
const dateFrom = ref('')
const dateTo = ref('')
const currentPage = ref(1)
const itemsPerPage = 5

const fetchOrders = async () => {
  try {
    const response = await axios.get('http://localhost:8000/admin/orders')
    orders.value = response.data.sort((a, b) => new Date(b.date) - new Date(a.date))
  } catch (error) {
    console.error('Error fetching orders:', error)
  } finally {
    loading.value = false
  }
}

const filteredOrders = computed(() => {
  let filtered = [...orders.value]
  
  if (statusFilter.value !== 'all') {
    filtered = filtered.filter(o => o.status === statusFilter.value)
  }
  
  if (dateFrom.value) {
    filtered = filtered.filter(o => new Date(o.date) >= new Date(dateFrom.value))
  }
  
  if (dateTo.value) {
    filtered = filtered.filter(o => new Date(o.date) <= new Date(dateTo.value))
  }
  
  return filtered
})

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredOrders.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => Math.ceil(filteredOrders.value.length / itemsPerPage))
const totalOrders = computed(() => filteredOrders.value.length)
const pendingCount = computed(() => filteredOrders.value.filter(o => o.status === 'pending').length)
const deliveredCount = computed(() => filteredOrders.value.filter(o => o.status === 'delivered').length)

const formatDate = (date) => new Date(date).toLocaleDateString('fr-FR')
const viewOrder = (order) => console.log('View order:', order)

const updateStatus = async (id, status) => {
  try {
    await axios.put(`http://localhost:8000/admin/updateStatus/${id}`, { status })
    await fetchOrders()
  } catch (error) {
    alert('Error updating order status', error)
  }
}

onMounted(fetchOrders)
</script>

<style scoped>
.admin-orders {
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
}

.filter-select, .date-input {
  padding: 0.5rem 1rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 8px;
  background: #fff;
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

.stat.pending { color: #FF9800; }
.stat.delivered { color: #4CAF50; }

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

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-badge.pending {
  background: rgba(255, 152, 0, 0.1);
  color: #FF9800;
}

.status-badge.delivered {
  background: rgba(76, 175, 80, 0.1);
  color: #4CAF50;
}

.status-badge.cancelled {
  background: rgba(244, 67, 54, 0.1);
  color: #f44336;
}

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

.action-btn.view { background: #F8F2E6; color: #2C2117; }
.action-btn.success { background: rgba(76, 175, 80, 0.1); color: #4CAF50; }
.action-btn.danger { background: rgba(244, 67, 54, 0.1); color: #f44336; }

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