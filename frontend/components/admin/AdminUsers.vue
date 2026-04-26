<!-- components/admin/AdminUsers.vue -->
<template>
  <div class="admin-users">
    <div class="table-toolbar">
      <div class="filters">
        <select v-model="roleFilter" class="filter-select">
          <option value="all">All Users</option>
          <option value="ROLE_USER">Customers</option>
          <option value="ROLE_ADMIN">Admins</option>
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
            placeholder="Search by name or email..."
          >
        </div>
      </div>
      <div class="stats">
        <span class="stat">Total: {{ filteredUsers.length }}</span>
        <span class="stat customers">Customers: {{ customerCount }}</span>
        <span class="stat admins">Admins: {{ adminCount }}</span>
      </div>
      <button class="btn-add" @click="openUserModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add User
      </button>
    </div>

    <div class="table-wrapper">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Orders</th>
            <th>Joined</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in paginatedUsers" :key="user.id">
            <td>#{{ user.id }}</td>
            <td>
              <div class="user-cell">
                <div class="user-avatar">{{ user.username?.charAt(0) || 'U' }}</div>
                <span>{{ user.username }}</span>
              </div>
            </td>
            <td>{{ user.email }}</td>
            <td>{{ user.phone || '-' }}</td>
            <td>
              <span :class="['status-badge', user.roles.includes('ROLE_ADMIN') ? 'admin' : 'user']">
                {{ user.roles.includes('ROLE_ADMIN') ? 'Admin' : 'Customer' }}
              </span>
            </td>
            <td>{{ user.orderCount || 0 }}</td>
            <td>{{ formatDate(user.createAt) }}</td>
            <td class="actions">
              <button title="Edit" class="action-btn edit" @click="openUserModal(user)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M17 3l4 4-7 7H10v-4l7-7z"/>
                  <path d="M4 20h16"/>
                </svg>
              </button>
              <button title="Delete" class="action-btn delete" @click="deleteUser(user.id)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </td>
          </tr>
          <tr v-if="paginatedUsers.length === 0">
            <td colspan="8" class="empty-row">No users found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="totalPages > 1" class="pagination">
      <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">← Prev</button>
      <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
      <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">Next →</button>
    </div>

    <!-- User Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ editingUser ? 'Edit User' : 'Add User' }}</h3>
          <button class="close-modal" @click="closeModal">✕</button>
        </div>
        <form @submit.prevent="saveUser">
          <div class="form-group">
            <label>Username</label>
            <input v-model="form.username" type="text" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input v-model="form.email" type="email" required>
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input v-model="form.phone" type="tel">
          </div>
          <div class="form-group">
            <label>Address</label>
            <input v-model="form.adress" type="text">
          </div>
          <div class="form-group">
            <label>Role</label>
            <select v-model="form.role">
              <option value="ROLE_USER">Customer</option>
              <option value="ROLE_ADMIN">Admin</option>
            </select>
          </div>
          <div v-if="!editingUser" class="form-group">
            <label>Password</label>
            <input v-model="form.password" type="password" required>
          </div>
          <div class="modal-actions">
            <button type="button" class="btn-cancel" @click="closeModal">Cancel</button>
            <button type="submit" class="btn-save">Save User</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const users = ref([])
const roleFilter = ref('all')
const searchTerm = ref('')
const currentPage = ref(1)
const itemsPerPage = 10
const showModal = ref(false)
const editingUser = ref(null)

const form = ref({
  username: '',
  email: '',
  phone: '',
  adress: '',
  role: 'ROLE_USER',
  password: ''
})

const fetchUsers = async () => {
  try {
    const response = await axios.get('http://localhost:8000/admin/users')
    users.value = response.data
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

const filteredUsers = computed(() => {
  let filtered = [...users.value]
  
  if (roleFilter.value !== 'all') {
    filtered = filtered.filter(u => u.roles.includes(roleFilter.value))
  }
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(u => 
      u.username?.toLowerCase().includes(term) ||
      u.email?.toLowerCase().includes(term)
    )
  }
  
  return filtered
})

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredUsers.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / itemsPerPage))
const customerCount = computed(() => users.value.filter(u => u.roles.includes('ROLE_USER') && !u.roles.includes('ROLE_ADMIN')).length)
const adminCount = computed(() => users.value.filter(u => u.roles.includes('ROLE_ADMIN')).length)

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('fr-FR')
}

const openUserModal = (user = null) => {
  editingUser.value = user
  if (user) {
    form.value = {
      username: user.username,
      email: user.email,
      phone: user.phone || '',
      adress: user.adress || '',
      role: user.roles.includes('ROLE_ADMIN') ? 'ROLE_ADMIN' : 'ROLE_USER',
      password: ''
    }
  } else {
    form.value = {
      username: '',
      email: '',
      phone: '',
      adress: '',
      role: 'ROLE_USER',
      password: ''
    }
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingUser.value = null
}

const saveUser = async () => {
  try {
    if (editingUser.value) {
      await axios.put(`http://localhost:8000/admin/users/${editingUser.value.id}`, form.value)
    } else {
      await axios.post('http://localhost:8000/admin/users', form.value)
    }
    await fetchUsers()
    closeModal()
  } catch (error) {
    alert('Error saving user: ' + (error.response?.data?.message || error.message))
  }
}

const deleteUser = async (id) => {
  if (confirm('Are you sure you want to delete this user?')) {
    try {
      await axios.delete(`http://localhost:8000/admin/users/${id}`)
      await fetchUsers()
    } catch (error) {
      alert('Error deleting user', error)
    }
  }
}

onMounted(fetchUsers)
</script>

<style scoped>
.admin-users {
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

.filter-select {
  padding: 0.5rem 1rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 8px;
  background: #fff;
  min-width: 130px;
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

.stat.customers { color: #2196F3; }
.stat.admins { color: #9C27B0; }

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

.empty-row {
  text-align: center;
  color: #7a6555;
  padding: 2rem;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #C8854A;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.7rem;
  font-weight: 500;
}

.status-badge.admin {
  background: rgba(156, 39, 176, 0.1);
  color: #9C27B0;
}

.status-badge.user {
  background: rgba(33, 150, 243, 0.1);
  color: #2196F3;
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

/* Modal Styles */
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

.modal-content form {
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

.form-group input,
.form-group select {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 8px;
  font-size: 0.9rem;
  transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #C8854A;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
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

@media (max-width: 768px) {
  .admin-users {
    padding: 1rem;
  }
  
  .table-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  
  .stats {
    justify-content: space-around;
  }
  
  .filters {
    flex-direction: column;
  }
  
  .search-wrapper input {
    min-width: auto;
  }
}
</style>