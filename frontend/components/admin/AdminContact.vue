<!-- components/admin/AdminContact.vue -->
<template>
  <div class="admin-contact">
    <!-- Stats Cards -->
    <div class="stats-summary">
      <div class="stat-summary-card">
        <div class="stat-icon"></div>
        <div class="stat-info">
          <h3>{{ stats.totalMessages }}</h3>
          <p>Total Messages</p>
        </div>
      </div>
      <div class="stat-summary-card">
        <div class="stat-icon"></div>
        <div class="stat-info">
          <h3>{{ stats.unreadMessages }}</h3>
          <p>Unread</p>
        </div>
      </div>
      <div class="stat-summary-card">
        <div class="stat-icon"></div>
        <div class="stat-info">
          <h3>{{ stats.readMessages }}</h3>
          <p>Read</p>
        </div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="table-toolbar">
      <div class="filters">
        <select v-model="statusFilter" class="filter-select">
          <option value="all">All Messages</option>
          <option value="unread">Unread</option>
          <option value="read">Read</option>
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
            placeholder="Search by name, email or message..."
          >
        </div>
      </div>
      <div class="stats">
        <span class="stat">Total: {{ filteredMessages.length }}</span>
      </div>
      <button class="btn-refresh" @click="refreshMessages">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
        Refresh
      </button>
    </div>

    <!-- Messages List -->
    <div class="messages-list">
      <div 
        v-for="message in paginatedMessages" 
        :key="message.id" 
        class="message-card"
        :class="{ unread: !message.isRead }"
        @click="openMessageDetail(message)"
      >
        <div class="message-avatar">
          {{ getInitials(message.firstName, message.lastName) }}
        </div>
        <div class="message-content">
          <div class="message-header">
            <div class="sender-info">
              <h4>{{ message.firstName }} {{ message.lastName }}</h4>
              <span class="sender-email">{{ message.email }}</span>
            </div>
            <div class="message-meta">
              <span class="message-date">{{ formatDate(message.createdAt) }}</span>
              <span :class="['message-status', message.isRead ? 'read' : 'unread']">
                {{ message.isRead ? 'Read' : 'Unread' }}
              </span>
            </div>
          </div>
          <p class="message-preview">{{ truncateText(message.text, 100) }}</p>
        </div>
      </div>
      
      <div v-if="filteredMessages.length === 0" class="empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <p>No messages found</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="pagination">
      <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">← Prev</button>
      <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
      <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">Next →</button>
    </div>

    <!-- Message Detail Modal -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="closeDetailModal">
      <div class="modal-content modal-message">
        <div class="modal-header">
          <h3>Message from {{ selectedMessage?.firstName }} {{ selectedMessage?.lastName }}</h3>
          <button class="close-modal" @click="closeDetailModal">✕</button>
        </div>
        
        <div class="message-detail">
          <div class="detail-section">
            <div class="detail-row">
              <label>From:</label>
              <span><strong>{{ selectedMessage?.firstName }} {{ selectedMessage?.lastName }}</strong> ({{ selectedMessage?.email }})</span>
            </div>
            <div class="detail-row">
              <label>Date:</label>
              <span>{{ formatDate(selectedMessage?.createdAt) }}</span>
            </div>
            <div class="detail-row">
              <label>Status:</label>
              <span :class="['status-badge', selectedMessage?.isRead ? 'read' : 'unread']">
                {{ selectedMessage?.isRead ? 'Read' : 'Unread' }}
              </span>
            </div>
          </div>
          
          <div class="detail-section">
            <label>Message:</label>
            <div class="message-body">{{ selectedMessage?.text }}</div>
          </div>
        </div>
        
        <div class="modal-actions">
          <button class="btn-cancel" @click="closeDetailModal">Close</button>
          <button class="btn-save" @click="markAsRead" v-if="selectedMessage && !selectedMessage.isRead">
            Mark as Read
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const messages = ref([])
const statusFilter = ref('all')
const searchTerm = ref('')
const currentPage = ref(1)
const itemsPerPage = 8
const showDetailModal = ref(false)
const selectedMessage = ref(null)

const stats = ref({
  totalMessages: 0,
  unreadMessages: 0,
  readMessages: 0
})

const fetchMessages = async () => {
  try {
    const response = await axios.get('http://localhost:8000/admin/messages')
    messages.value = response.data
    
    // Calculate stats
    const total = messages.value.length
    const unread = messages.value.filter(m => !m.isRead).length
    const read = messages.value.filter(m => m.isRead).length
    
    stats.value = {
      totalMessages: total,
      unreadMessages: unread,
      readMessages: read
    }
  } catch (error) {
    console.error('Error fetching messages:', error)
  }
}

const getInitials = (firstName, lastName) => {
  if (!firstName && !lastName) return '?'
  const first = firstName ? firstName.charAt(0) : ''
  const last = lastName ? lastName.charAt(0) : ''
  return (first + last).toUpperCase().slice(0, 2)
}

const truncateText = (text, max) => {
  if (!text) return ''
  if (text.length <= max) return text
  return text.substring(0, max) + '...'
}

const formatDate = (date) => {
  if (!date) return '-'
  const d = new Date(date)
  const now = new Date()
  const diff = now - d
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  
  if (days === 0) return 'Today'
  if (days === 1) return 'Yesterday'
  if (days < 7) return `${days} days ago`
  return d.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const filteredMessages = computed(() => {
  let filtered = [...messages.value]
  
  if (statusFilter.value !== 'all') {
    const isUnread = statusFilter.value === 'unread'
    filtered = filtered.filter(m => m.isRead === !isUnread)
  }
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(m => 
      m.firstName?.toLowerCase().includes(term) ||
      m.lastName?.toLowerCase().includes(term) ||
      m.email?.toLowerCase().includes(term) ||
      m.text?.toLowerCase().includes(term)
    )
  }
  
  return filtered
})

const paginatedMessages = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredMessages.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => Math.ceil(filteredMessages.value.length / itemsPerPage))

const refreshMessages = () => {
  fetchMessages()
}

const openMessageDetail = async (message) => {
  selectedMessage.value = message
  showDetailModal.value = true
}

const markAsRead = async () => {
  const id = selectedMessage.value?.id
  if (!id) return
  
  try {
    await axios.patch(`http://localhost:8000/admin/messages/${id}/read`)
    await fetchMessages()
    if (selectedMessage.value) {
      selectedMessage.value.isRead = true
    }
  } catch (error) {
    console.error('Error marking as read:', error)
  }
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedMessage.value = null
}

onMounted(fetchMessages)
</script>

<style scoped>
.admin-contact {
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

/* Messages List */
.messages-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.message-card {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  background: #fff;
  border: 1px solid rgba(44, 33, 23, 0.08);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s;
}

.message-card:hover {
  transform: translateX(4px);
  border-color: #C8854A;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.message-card.unread {
  background: #FFF5EC;
  border-left: 3px solid #C8854A;
}

.message-avatar {
  width: 48px;
  height: 48px;
  background: #C8854A;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: #fff;
  flex-shrink: 0;
}

.message-content {
  flex: 1;
}

.message-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.sender-info h4 {
  margin: 0;
  font-size: 1rem;
  color: #2C2117;
}

.sender-email {
  font-size: 0.7rem;
  color: #7a6555;
  margin-left: 0.5rem;
}

.message-meta {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.message-date {
  font-size: 0.7rem;
  color: #7a6555;
}

.message-status {
  font-size: 0.65rem;
  padding: 0.125rem 0.5rem;
  border-radius: 20px;
}

.message-status.unread {
  background: #C8854A;
  color: #fff;
}

.message-status.read {
  background: #F8F2E6;
  color: #7a6555;
}

.message-preview {
  font-size: 0.85rem;
  color: #7a6555;
  margin: 0.25rem 0;
  line-height: 1.4;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  background: #fff;
  border-radius: 12px;
  color: #7a6555;
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

.modal-message {
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

.message-detail {
  padding: 1.5rem;
}

.detail-section {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(44, 33, 23, 0.08);
}

.detail-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.detail-section label {
  display: block;
  font-weight: 600;
  color: #2C2117;
  margin-bottom: 0.5rem;
}

.detail-row {
  display: flex;
  margin-bottom: 0.75rem;
}

.detail-row label {
  width: 80px;
  margin-bottom: 0;
}

.message-body {
  background: #F8F2E6;
  padding: 1rem;
  border-radius: 8px;
  line-height: 1.6;
  white-space: pre-wrap;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.7rem;
  font-weight: 500;
}

.status-badge.read {
  background: rgba(76, 175, 80, 0.1);
  color: #4CAF50;
}

.status-badge.unread {
  background: rgba(200, 133, 74, 0.1);
  color: #C8854A;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid rgba(44, 33, 23, 0.1);
}

.btn-cancel, .btn-save {
  flex: 1;
  padding: 0.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.2s;
}

.btn-cancel {
  background: none;
  border: 1px solid rgba(44, 33, 23, 0.2);
  color: #7a6555;
}

.btn-cancel:hover {
  border-color: #C8854A;
  color: #C8854A;
}

.btn-save {
  background: #C8854A;
  border: none;
  color: #fff;
}

.btn-save:hover {
  background: #b67038;
}

/* Responsive */
@media (max-width: 768px) {
  .admin-contact {
    padding: 1rem;
  }
  
  .table-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filters {
    flex-direction: column;
  }
  
  .search-wrapper input {
    min-width: auto;
  }
  
  .message-header {
    flex-direction: column;
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