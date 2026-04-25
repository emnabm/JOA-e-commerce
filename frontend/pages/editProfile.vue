<!-- pages/editProfile.vue -->
<template>
  <div class="edit-profile-page">
    <Header />
    
    <div class="edit-container">
      <div class="edit-card">
        <div class="card-header">
          <div class="header-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <h1>Edit Profile</h1>
          <p>Update your personal information</p>
        </div>

        <form @submit.prevent="saveProfile" class="edit-form">
          <div class="form-group">
            <label>Full Name</label>
            <div class="input-wrapper">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              <input
                v-model="form.username"
                type="text"
                placeholder="Your full name"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label>Email Address</label>
            <div class="input-wrapper">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              <input
                v-model="form.email"
                type="email"
                placeholder="you@example.com"
                required
              >
            </div>
          </div>

          <div class="form-group">
            <label>Phone Number</label>
            <div class="input-wrapper">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              <input
                v-model="form.phone"
                type="tel"
                placeholder="+216 00 000 000"
                required
              >
            </div>
          </div>

          <div class="form-group">
            <label>Address</label>
            <div class="input-wrapper">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              <input
                v-model="form.adress"
                type="text"
                placeholder="Your complete address"
                required
              >
            </div>
          </div>

          <div class="form-actions">
            <NuxtLink to="/account" class="cancel-btn">Cancel</NuxtLink>
            <button type="submit" class="save-btn" :disabled="loading">
              <span v-if="loading">Saving...</span>
              <span v-else>Save Changes</span>
            </button>
          </div>
        </form>

        <!-- Success/Error Message -->
        <div v-if="message" :class="['alert-message', success ? 'success' : 'error']">
          <svg v-if="success" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ message }}
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from '#vue-router'
import axios from 'axios'

const router = useRouter()
const token = localStorage.getItem('token')
const loading = ref(false)
const message = ref('')
const success = ref(false)

const form = ref({
  username: '',
  email: '',
  phone: '',
  adress: ''
})

// Load current info
onMounted(async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/me', {
      headers: { Authorization: `Bearer ${token}` }
    })
    form.value.username = response.data.username || ''
    form.value.email = response.data.email || ''
    form.value.adress = response.data.address || ''
    form.value.phone = response.data.phone || ''
  } catch (e) {
    console.error('Error loading profile', e)
    message.value = 'Failed to load profile data'
    success.value = false
  }
})

// Save profile
const saveProfile = async () => {
  loading.value = true
  message.value = ''

  try {
    await axios.put(
      'http://localhost:8000/api/updateProfile',
      {
        username: form.value.username,
        email: form.value.email,
        adress: form.value.adress,
        phone: form.value.phone
      },
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    )
    
    // Update localStorage
    localStorage.setItem('user_username', form.value.username)
    localStorage.setItem('user_email', form.value.email)
    
    message.value = 'Profile updated successfully!'
    success.value = true
    
    setTimeout(() => {
      router.push('/account')
    }, 1500)
  } catch (e) {
    console.error('Error saving profile', e)
    message.value = e.response?.data?.message || 'Could not save profile'
    success.value = false
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.edit-profile-page {
  min-height: 9%;
  background: #F8F2E6;
  display: flex;
  flex-direction: column;
}

.edit-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
}

.edit-card {
  max-width: 550px;
  width: 100%;
  background: #fff;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.card-header {
  text-align: center;
  margin-bottom: 1rem;
}

.header-icon {
  width: 60px;
  height: 60px;
  background: rgba(200, 133, 74, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  color: #C8854A;
}

.card-header h1 {
  font-family: 'Playfair Display', serif;
  font-size: 1.75rem;
  color: #2C2117;
  margin-bottom: 0.5rem;
}

.card-header p {
  color: #7a6555;
  font-size: 0.9rem;
}

.edit-form {
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.85rem;
  font-weight: 500;
  color: #2C2117;
}

.input-wrapper {
  position: relative;
}

.input-wrapper svg {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #7a6555;
}

.input-wrapper input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 10px;
  font-size: 0.9rem;
  transition: all 0.2s;
}

.input-wrapper input:focus {
  outline: none;
  border-color: #C8854A;
  box-shadow: 0 0 0 3px rgba(200, 133, 74, 0.1);
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 0.5rem;
}

.cancel-btn {
  flex: 1;
  text-align: center;
  padding: 0.75rem;
  background: none;
  border: 1px solid rgba(44, 33, 23, 0.2);
  border-radius: 10px;
  color: #7a6555;
  text-decoration: none;
  transition: all 0.2s;
}

.cancel-btn:hover {
  border-color: #C8854A;
  color: #C8854A;
}

.save-btn {
  flex: 1;
  padding: 0.75rem;
  background: #C8854A;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.save-btn:hover:not(:disabled) {
  background: #b67038;
  transform: translateY(-2px);
}

.save-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.alert-message {
  margin-top: 1rem;
  padding: 0.75rem;
  border-radius: 10px;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.alert-message.success {
  background: rgba(76, 175, 80, 0.1);
  color: #4CAF50;
}

.alert-message.error {
  background: rgba(244, 67, 54, 0.1);
  color: #f44336;
}

@media (max-width: 768px) {
  .edit-container {
    padding: 2rem 1rem;
  }
  
  .edit-card {
    padding: 1.5rem;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
</style>