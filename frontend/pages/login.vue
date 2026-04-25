<!-- pages/login.vue -->
<template>
  <div class="auth-page">
    
    <div class="auth-container">
      <div class="auth-card">
        <!-- Left Side - Decorative -->
        <div class="auth-image">
          <div class="auth-overlay">
            <h2>Welcome Back!</h2>
            <p>Sign in to continue your shopping journey</p>
            <div class="auth-features">
              <div class="feature">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Fast checkout</span>
              </div>
              <div class="feature">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Track your orders</span>
              </div>
              <div class="feature">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Exclusive offers</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Side - Form -->
        <div class="auth-form">
          <div class="form-header">
            <h1>Sign In</h1>
            <p>Access your account to continue</p>
          </div>

          <form @submit.prevent="login">
            <!-- Email Field -->
            <div class="form-group">
              <label class="form-label">Email Address</label>
              <div class="input-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
                <input
                  v-model="email"
                  type="email"
                  placeholder="you@example.com"
                  required
                  :class="{ 'error': emailError }"
                >
              </div>
              <span v-if="emailError" class="error-message">{{ emailError }}</span>
            </div>

            <!-- Password Field -->
            <div class="form-group">
              <label class="form-label">Password</label>
              <div class="input-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input
                  v-model="password"
                    :type="showPassword ? 'text' : 'password'"
                  placeholder="Enter your password"
                  required
                  :class="{ 'error': passwordError }"
                >
                <button 
                  class="toggle-password"
                  type="button" 
                  @click="showPassword = !showPassword" 
                  
                >
                  <svg v-if="showPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
              </div>
              <span v-if="passwordError" class="error-message">{{ passwordError }}</span>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="form-options">
              <label class="checkbox-label">
                <input v-model="rememberMe" type="checkbox" >
                <span class="checkbox-custom"></span>
                <span>Remember me</span>
              </label>
              <NuxtLink to="/forgot-password" class="forgot-link">
                Forgot password?
              </NuxtLink>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary" :disabled="loading">
              <span v-if="loading">Signing in...</span>
              <span v-else>Sign In</span>
            </button>

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
          </form>

          <!-- Divider -->
          <div class="divider">
            <span>or continue with</span>
          </div>

          <!-- Social Login -->
          <div class="social-login">
            <button class="social-btn google" @click="socialLogin('google')">
              <svg width="18" height="18" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
              </svg>
              Google
            </button>
            <button class="social-btn facebook" @click="socialLogin('facebook')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
              </svg>
              Facebook
            </button>
          </div>

          <!-- Register Link -->
          <p class="auth-footer">
            Don't have an account?
            <NuxtLink to="/register" class="auth-link">Sign up →</NuxtLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const rememberMe = ref(false)
const message = ref('')
const success = ref(false)
const loading = ref(false)
const emailError = ref('')
const passwordError = ref('')

const router = useRouter()

// Validation
const validateForm = () => {
  let isValid = true
  emailError.value = ''
  passwordError.value = ''
  
  if (!email.value) {
    emailError.value = 'Email is required'
    isValid = false
  } else if (!/\S+@\S+\.\S+/.test(email.value)) {
    emailError.value = 'Please enter a valid email address'
    isValid = false
  }
  
  if (!password.value) {
    passwordError.value = 'Password is required'
    isValid = false
  } 
  
  return isValid
}

const login = async () => {
  if (!validateForm()) return
  
  loading.value = true
  message.value = ''

  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value
    })

    // Store token and user data
    localStorage.setItem('token', response.data.token)
    localStorage.setItem('user_id', response.data.id)
    localStorage.setItem('user_email', response.data.email)
    localStorage.setItem('user_role', response.data.roles)
    localStorage.setItem('user_username', response.data.username)
    
    // Store remember me preference
    if (rememberMe.value) {
      localStorage.setItem('remember_me', 'true')
    }

    message.value = 'Login successful! Redirecting...'
    success.value = true
    
    setTimeout(() => {
      if (localStorage.getItem('user_role').includes("ROLE_ADMIN")) {
        router.push('/admin/dashboard')
      } else {
        router.push('/')
      }
    }, 1000)
    
  } catch (error) {
    success.value = false
    if (error.response?.data?.error) {
      message.value = error.response.data.error
    } else if (error.response?.data?.message) {
      message.value = error.response.data.message
    } else {
      message.value = 'Connection error. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

const socialLogin = (provider) => {
  // Implement social login logic
  console.log(`Login with ${provider}`)
  message.value = `${provider} login coming soon!`
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #F8F2E6;
}

.auth-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
}

.auth-card {
  display: grid;
  grid-template-columns: 1fr 1fr;
  max-width: 1100px;
  width: 100%;
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Left Side */
.auth-image {
  background: linear-gradient(135deg, #2C2117 0%, #3a2a1f 100%);
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
}

.auth-image::before {
  content: '';
  position: absolute;
  inset: 0;
  background: repeating-linear-gradient(45deg, rgba(200,133,74,0.08) 0px, rgba(200,133,74,0.08) 2px, transparent 2px, transparent 8px);
}

.auth-overlay {
  position: relative;
  z-index: 1;
  color: #F8F2E6;
  text-align: center;
}

.auth-overlay h2 {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  margin-bottom: 1rem;
}

.auth-overlay p {
  margin-bottom: 2rem;
  opacity: 0.9;
}

.auth-features {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.feature {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.9rem;
}

.feature svg {
  color: #C8854A;
}

/* Right Side */
.auth-form {
  padding: 3rem;
}

.form-header {
  text-align: center;
  margin-bottom: 2rem;
}

.form-header h1 {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  color: #2C2117;
  margin-bottom: 0.5rem;
}

.form-header p {
  color: #7a6555;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.85rem;
  font-weight: 500;
  color: #2C2117;
  margin-bottom: 0.5rem;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-wrapper svg:first-child {
  position: absolute;
  left: 1rem;
  color: #7a6555;
}

.input-wrapper input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 10px;
  font-size: 0.9rem;
  transition: all 0.2s;
  background: #fff;
}

.input-wrapper input:focus {
  outline: none;
  border-color: #C8854A;
  box-shadow: 0 0 0 3px rgba(200, 133, 74, 0.1);
}

.input-wrapper input.error {
  border-color: #f44336;
}

.toggle-password {
  position: absolute;
  right: 1rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #7a6555;
  display: flex;
  align-items: center;
}

.error-message {
  font-size: 0.75rem;
  color: #f44336;
  margin-top: 0.25rem;
  display: block;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.85rem;
  color: #7a6555;
  position: relative;
  padding-left: 1.5rem;
}

.checkbox-label input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.checkbox-custom {
  position: absolute;
  left: 0;
  width: 18px;
  height: 18px;
  background: #fff;
  border: 1px solid rgba(44, 33, 23, 0.2);
  border-radius: 4px;
  transition: all 0.2s;
}

.checkbox-label input:checked + .checkbox-custom {
  background: #C8854A;
  border-color: #C8854A;
}

.checkbox-label input:checked + .checkbox-custom::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #fff;
  font-size: 10px;
}

.forgot-link {
  font-size: 0.85rem;
  color: #C8854A;
  text-decoration: none;
}

.forgot-link:hover {
  text-decoration: underline;
}

.btn-primary {
  width: 100%;
  padding: 0.875rem;
  background: #C8854A;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary:hover:not(:disabled) {
  background: #b67038;
  transform: translateY(-2px);
}

.btn-primary:disabled {
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
  border: 1px solid rgba(76, 175, 80, 0.2);
}

.alert-message.error {
  background: rgba(244, 67, 54, 0.1);
  color: #f44336;
  border: 1px solid rgba(244, 67, 54, 0.2);
}

.divider {
  text-align: center;
  margin: 2rem 0;
  position: relative;
}

.divider::before,
.divider::after {
  content: '';
  position: absolute;
  top: 50%;
  width: calc(50% - 60px);
  height: 1px;
  background: rgba(44, 33, 23, 0.1);
}

.divider::before {
  left: 0;
}

.divider::after {
  right: 0;
}

.divider span {
  background: #fff;
  padding: 0 1rem;
  color: #7a6555;
  font-size: 0.8rem;
}

.social-login {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.social-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 10px;
  background: #fff;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.85rem;
}

.social-btn:hover {
  background: #F8F2E6;
}

.auth-footer {
  text-align: center;
  font-size: 0.85rem;
  color: #7a6555;
  margin-top: 2rem;
}

.auth-link {
  color: #C8854A;
  text-decoration: none;
  font-weight: 500;
}

.auth-link:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
  .auth-card {
    grid-template-columns: 1fr;
  }
  
  .auth-image {
    display: none;
  }
  
  .auth-form {
    padding: 2rem;
  }
  
  .social-login {
    flex-direction: column;
  }
}
</style>