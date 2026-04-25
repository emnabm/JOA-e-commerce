                <!-- pages/register.vue -->
<template>
  <div class="auth-page">
    <Header />
    
    <div class="auth-container">
      <div class="auth-card">
        <!-- Left Side - Decorative -->
        <div class="auth-image">
          <div class="auth-overlay">
            <h2>Join Us Today!</h2>
            <p>Create an account to enjoy exclusive benefits</p>
            <div class="auth-features">
              <div class="feature">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Exclusive discounts</span>
              </div>
              <div class="feature">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Loyalty points</span>
              </div>
              <div class="feature">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Priority support</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Side - Form -->
        <div class="auth-form">
          <div class="form-header">
            <h1>Create Account</h1>
            <p>Fill in your details to get started</p>
          </div>

          <form @submit.prevent="register">
            <!-- Full Name -->
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <div class="input-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
                <input
                  v-model="userName"
                  type="text"
                  placeholder="John Doe"
                  required
                  :class="{ 'error': userNameError }"
                />
              </div>
              <span v-if="userNameError" class="error-message">{{ userNameError }}</span>
            </div>

            <!-- Email -->
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
                />
              </div>
              <span v-if="emailError" class="error-message">{{ emailError }}</span>
            </div>

            <!-- Phone -->
            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <div class="input-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <input
                  v-model="phone"
                  type="tel"
                  placeholder="+216 00 000 000"
                  :class="{ 'error': phoneError }"
                />
              </div>
              <span v-if="phoneError" class="error-message">{{ phoneError }}</span>
            </div>

            <!-- Address -->
            <div class="form-group">
              <label class="form-label">Address</label>
              <div class="input-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
                <input
                  v-model="address"
                  type="text"
                  placeholder="Your address"
                  :class="{ 'error': addressError }"
                />
              </div>
              <span v-if="addressError" class="error-message">{{ addressError }}</span>
            </div>

            <!-- Password -->
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
                  placeholder="Create a password"
                  required
                  :class="{ 'error': passwordError }"
                />
                <button 
                  type="button" 
                  @click="showPassword = !showPassword" 
                  class="toggle-password"
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

            <!-- Confirm Password -->
            <div class="form-group">
              <label class="form-label">Confirm Password</label>
              <div class="input-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input
                  v-model="confirmPassword"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  placeholder="Confirm your password"
                  required
                  :class="{ 'error': confirmPasswordError }"
                />
                <button 
                  type="button" 
                  @click="showConfirmPassword = !showConfirmPassword" 
                  class="toggle-password"
                >
                  <svg v-if="showConfirmPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
              </div>
              <span v-if="confirmPasswordError" class="error-message">{{ confirmPasswordError }}</span>
            </div>

            <!-- Terms & Conditions -->
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="acceptTerms">
                <span class="checkbox-custom"></span>
                <span>I agree to the <NuxtLink to="/terms" class="terms-link">Terms & Conditions</NuxtLink></span>
              </label>
              <span v-if="termsError" class="error-message">{{ termsError }}</span>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary" :disabled="loading">
              <span v-if="loading">Creating account...</span>
              <span v-else>Create Account</span>
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
            <span>or sign up with</span>
          </div>

          <!-- Social Register -->
          <div class="social-login">
            <button class="social-btn google" @click="socialRegister('google')">
              <svg width="18" height="18" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
              </svg>
              Google
            </button>
            <button class="social-btn facebook" @click="socialRegister('facebook')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
              </svg>
              Facebook
            </button>
          </div>

          <!-- Login Link -->
          <p class="auth-footer">
            Already have an account?
            <NuxtLink to="/login" class="auth-link">Sign in →</NuxtLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

// Form fields
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const userName = ref('')
const address = ref('')
const phone = ref('')
const acceptTerms = ref(false)

// UI states
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const message = ref('')
const success = ref(false)
const loading = ref(false)

// Error states
const emailError = ref('')
const passwordError = ref('')
const confirmPasswordError = ref('')
const userNameError = ref('')
const addressError = ref('')
const phoneError = ref('')
const termsError = ref('')

const router = useRouter()

// Validations
const validateEmail = () => {
  if (!email.value) {
    emailError.value = 'Email is required'
    return false
  } else if (!/\S+@\S+\.\S+/.test(email.value)) {
    emailError.value = 'Please enter a valid email address'
    return false
  }
  emailError.value = ''
  return true
}

const validateUserName = () => {
  if (!userName.value) {
    userNameError.value = 'Full name is required'
    return false
  } else if (userName.value.length < 2) {
    userNameError.value = 'Name must be at least 2 characters'
    return false
  }
  userNameError.value = ''
  return true
}

const validatePhone = () => {
  if (phone.value && !/^[+\d\s]{8,}$/.test(phone.value.replace(/\s/g, ''))) {
    phoneError.value = 'Please enter a valid phone number'
    return false
  }
  phoneError.value = ''
  return true
}

const validateAddress = () => {
  if (address.value && address.value.length < 5) {
    addressError.value = 'Please enter a complete address'
    return false
  }
  addressError.value = ''
  return true
}

const validatePassword = () => {
  if (!password.value) {
    passwordError.value = 'Password is required'
    return false
  } else if (password.value.length < 6) {
    passwordError.value = 'Password must be at least 6 characters'
    return false
  } else if (!/(?=.*[A-Z])/.test(password.value)) {
    passwordError.value = 'Password must contain at least one uppercase letter'
    return false
  } else if (!/(?=.*[0-9])/.test(password.value)) {
    passwordError.value = 'Password must contain at least one number'
    return false
  }
  passwordError.value = ''
  return true
}

const validateConfirmPassword = () => {
  if (!confirmPassword.value) {
    confirmPasswordError.value = 'Please confirm your password'
    return false
  } else if (confirmPassword.value !== password.value) {
    confirmPasswordError.value = 'Passwords do not match'
    return false
  }
  confirmPasswordError.value = ''
  return true
}

const validateTerms = () => {
  if (!acceptTerms.value) {
    termsError.value = 'You must accept the Terms & Conditions'
    return false
  }
  termsError.value = ''
  return true
}

const validateForm = () => {
  const isEmailValid = validateEmail()
  const isUserNameValid = validateUserName()
  const isPhoneValid = validatePhone()
  const isAddressValid = validateAddress()
  const isPasswordValid = validatePassword()
  const isConfirmPasswordValid = validateConfirmPassword()
  const isTermsValid = validateTerms()
  
  return isEmailValid && isUserNameValid && isPhoneValid && isAddressValid && 
         isPasswordValid && isConfirmPasswordValid && isTermsValid
}

// Real-time validation
watch(email, () => validateEmail())
watch(userName, () => validateUserName())
watch(phone, () => validatePhone())
watch(address, () => validateAddress())
watch(password, () => {
  validatePassword()
  if (confirmPassword.value) validateConfirmPassword()
})
watch(confirmPassword, () => validateConfirmPassword())
watch(acceptTerms, () => validateTerms())

const register = async () => {
  if (!validateForm()) return
  
  loading.value = true
  message.value = ''

  try {
    const response = await axios.post('http://localhost:8000/api/register', {
      email: email.value,
      password: password.value,
      username: userName.value,
      adress: address.value,
      phone: phone.value
    })

    message.value = response.data.message || 'Registration successful! Redirecting to login...'
    success.value = true
    
    // Store token if returned
    if (response.data.token) {
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user_email', email.value)
      localStorage.setItem('user_username', userName.value)
      
      // Auto login after registration
      setTimeout(() => {
        router.push('/')
      }, 1500)
    } else {
      // Redirect to login page after 2 seconds
      setTimeout(() => {
        router.push('/login')
      }, 2000)
    }
    
    // Clear form
    email.value = ''
    password.value = ''
    confirmPassword.value = ''
    userName.value = ''
    address.value = ''
    phone.value = ''
    acceptTerms.value = false
    
  } catch (error) {
    console.error(error)
    success.value = false
    
    if (error.response?.data?.error) {
      message.value = error.response.data.error
    } else if (error.response?.data?.message) {
      message.value = error.response.data.message
    } else if (error.response?.data?.errors) {
      // Handle validation errors from backend
      const errors = error.response.data.errors
      if (errors.email) emailError.value = errors.email[0]
      if (errors.password) passwordError.value = errors.password[0]
      if (errors.username) userNameError.value = errors.username[0]
      message.value = 'Please correct the errors above'
    } else {
      message.value = 'Registration failed. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

const socialRegister = (provider) => {
  console.log(`Register with ${provider}`)
  message.value = `${provider} registration coming soon!`
}
</script>

<style scoped>
/* Same styles as login page plus additional styles */

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
  max-height: 80vh;
  overflow-y: auto;
}

.auth-form::-webkit-scrollbar {
  width: 6px;
}

.auth-form::-webkit-scrollbar-track {
  background: #F8F2E6;
  border-radius: 3px;
}

.auth-form::-webkit-scrollbar-thumb {
  background: #C8854A;
  border-radius: 3px;
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
  margin-bottom: 1.25rem;
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

.terms-link {
  color: #C8854A;
  text-decoration: none;
}

.terms-link:hover {
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
  margin-top: 0.5rem;
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
  transform: translateY(-2px);
}

.social-btn.google:hover {
  border-color: #DB4437;
  color: #DB4437;
}

.social-btn.facebook:hover {
  border-color: #4267B2;
  color: #4267B2;
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

/* Password strength indicator */
.password-strength {
  margin-top: 0.5rem;
}

.strength-bar {
  height: 4px;
  background: rgba(44, 33, 23, 0.1);
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 0.25rem;
}

.strength-fill {
  height: 100%;
  transition: width 0.3s, background 0.3s;
}

.strength-text {
  font-size: 0.7rem;
  color: #7a6555;
}

/* Responsive */
@media (max-width: 968px) {
  .auth-card {
    grid-template-columns: 1fr;
  }
  
  .auth-image {
    display: none;
  }
  
  .auth-form {
    padding: 2rem;
    max-height: none;
  }
  
  .social-login {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .auth-container {
    padding: 2rem 1rem;
  }
  
  .auth-form {
    padding: 1.5rem;
  }
  
  .form-header h1 {
    font-size: 1.5rem;
  }
}
</style>