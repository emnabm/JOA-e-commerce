<!-- pages/checkout.vue -->
<template>
  <div class="checkout-page">
    <AppHeader />
    
    <div class="checkout-container">
      <!-- Progress Steps -->
      <div class="progress-steps">
        <div class="step completed">
          <div class="step-number">1</div>
          <div class="step-label">Cart</div>
        </div>
        <div class="step active">
          <div class="step-number">2</div>
          <div class="step-label">Delivery</div>
        </div>
        <div class="step">
          <div class="step-number">3</div>
          <div class="step-label">Payment</div>
        </div>
      </div>

      <div class="checkout-grid">
        <!-- Left Side - Order Summary -->
        <div class="order-summary-wrapper">
          <div class="order-summary">
            <h2>Order Summary</h2>
            
            <div v-if="items.length > 0" class="summary-items">
              <div v-for="item in items" :key="item.id" class="summary-item">
                <div class="item-image">
                  <img :src="getProductImage(item)" :alt="item.product.nom">
                </div>
                <div class="item-details">
                  <h4>{{ item.product.nom }}</h4>
                  <p>Quantity: {{ item.quantity }}</p>
                  <p class="item-price">{{ formatPrice(item.product.prix * item.quantity) }}</p>
                </div>
              </div>
            </div>

            <div v-else class="empty-cart">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
              </svg>
              <p>Your cart is empty</p>
            </div>

            <div v-if="items.length > 0" class="summary-totals">
              <div class="total-row">
                <span>Subtotal</span>
                <span>{{ formatPrice(subtotal) }}</span>
              </div>
              <div class="total-row">
                <span>Delivery Fee</span>
                <span v-if="deliveryFee === 0" class="free-tag">Free</span>
                <span v-else>{{ formatPrice(deliveryFee) }}</span>
              </div>
              <div class="total-row grand-total">
                <span>Total</span>
                <span>{{ formatPrice(total) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Side - Delivery Information -->
        <div class="delivery-info-wrapper">
          <div class="delivery-info">
            <h2>Delivery Information</h2>
            
            <form class="delivery-form" @submit.prevent="goToPayment">
              <div class="form-group">
                <label>Full Name</label>
                <div class="input-wrapper">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                  <input
                    v-model="deliveryInfo.fullName"
                    type="text"
                    placeholder="Your full name"
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
                    v-model="deliveryInfo.phone"
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
                    v-model="deliveryInfo.address"
                    type="text"
                    placeholder="Street address"
                    required
                  >
                </div>
              </div>

              <div class="fields-row">
                <div class="form-group">
                  <label>City</label>
                  <div class="input-wrapper">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <rect x="2" y="4" width="20" height="16" rx="2"/>
                      <line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                    <input
                      v-model="deliveryInfo.city"
                      type="text"
                      placeholder="City"
                      required
                    >
                  </div>
                </div>
                <div class="form-group">
                  <label>Postal Code</label>
                  <div class="input-wrapper">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <rect x="3" y="3" width="18" height="18" rx="2"/>
                      <line x1="3" y1="9" x2="21" y2="9"/>
                    </svg>
                    <input
                      v-model="deliveryInfo.postalCode"
                      type="text"
                      placeholder="Postal code"
                      required
                    >
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>Delivery Method</label>
                <div class="delivery-options">
                  <label 
                    v-for="option in deliveryOptions" 
                    :key="option.value"
                    class="delivery-option"
                    :class="{ selected: deliveryInfo.delivery === option.value }"
                  >
                    <input
                      v-model="deliveryInfo.delivery"
                      type="radio"
                      :value="option.value"
                      required
                    >
                    <div class="option-content">
                      <div class="option-header">
                        <span class="option-name">{{ option.label }}</span>
                        <span class="option-price">{{ option.cost === 0 ? 'Free' : formatPrice(option.cost) }}</span>
                      </div>
                      <div class="option-desc">{{ option.description }}</div>
                    </div>
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label>Order Notes (Optional)</label>
                <textarea
                  v-model="deliveryInfo.note"
                  rows="3"
                  placeholder="Special delivery instructions..."
                ></textarea>
              </div>

              <div class="form-actions">
                <NuxtLink to="/cart" class="back-btn">← Back to Cart</NuxtLink>
                <button type="submit" class="continue-btn" :disabled="items.length === 0 || loading">
                  <span v-if="loading">Processing...</span>
                  <span v-else>Continue to Payment →</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const token = localStorage.getItem('token')
const items = ref([])
const loading = ref(false)

const deliveryInfo = ref({
  fullName: '',
  phone: '',
  address: '',
  city: '',
  postalCode: '',
  delivery: 'Standard',
  note: ''
})

const deliveryOptions = [
  { value: 'Standard', label: 'Standard Delivery', cost: 7, description: 'Delivery in 3-5 business days' },
  { value: 'Express', label: 'Express Delivery', cost: 15, description: 'Delivery in 1-2 business days' },
  { value: 'clickAndCollect', label: 'Click & Collect', cost: 0, description: 'Pick up from our store' },
  { value: 'Relay', label: 'Pick up Point', cost: 4, description: 'Delivery to your nearest pick up point' },
]

const fetchCart = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/getCart', {
      headers: { Authorization: `Bearer ${token}` },
    })
    items.value = response.data.items || []
  } catch (error) {
    console.error('Error fetching cart:', error)
  }
}

const subtotal = computed(() => 
  items.value.reduce((sum, item) => sum + item.product.prix * item.quantity, 0)
)

const deliveryFee = computed(() => {
  const option = deliveryOptions.find(opt => opt.value === deliveryInfo.value.delivery)
  return option ? option.cost : 0
})

const total = computed(() => subtotal.value + deliveryFee.value)

const formatPrice = (price) => {
  return new Intl.NumberFormat('tn-TN', {
    style: 'currency',
    currency: 'TND',
    minimumFractionDigits: 3,
    maximumFractionDigits: 3
  }).format(price)
}

const getProductImage = (item) => {
  const imagePath = item?.product?.image
  if (!imagePath) return '/default-product.jpg'

  // Handle both relative backend paths and absolute URLs.
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }

  return imagePath.startsWith('/') ? imagePath : `/${imagePath}`
}

const goToPayment = () => {
  // Validate form
  if (!deliveryInfo.value.fullName || !deliveryInfo.value.phone || 
      !deliveryInfo.value.address || !deliveryInfo.value.city || 
      !deliveryInfo.value.postalCode || !deliveryInfo.value.delivery) {
    alert('Please fill all required fields')
    return
  }

  // Store delivery info in sessionStorage to use in payment page
  sessionStorage.setItem('deliveryInfo', JSON.stringify({
    ...deliveryInfo.value,
    subtotal: subtotal.value,
    deliveryFee: deliveryFee.value,
    total: total.value
  }))

  router.push('/payment')
}

onMounted(() => {
  fetchCart()
  
  // Load saved delivery info if exists
  const savedInfo = sessionStorage.getItem('deliveryInfo')
  if (savedInfo) {
    const parsed = JSON.parse(savedInfo)
    deliveryInfo.value = { ...deliveryInfo.value, ...parsed }
  }
})
</script>

<style scoped>
.checkout-page {
  background: #F8F2E6;
  min-height: 100vh;
}

.checkout-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

/* Progress Steps */
.progress-steps {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-bottom: 2rem;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.step-number {
  width: 40px;
  height: 40px;
  background: #fff;
  border: 2px solid rgba(44, 33, 23, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: #7a6555;
}

.step.completed .step-number {
  background: #4CAF50;
  border-color: #4CAF50;
  color: #fff;
}

.step.active .step-number {
  border-color: #C8854A;
  color: #C8854A;
}

.step-label {
  font-size: 0.75rem;
  color: #7a6555;
}

/* Grid Layout */
.checkout-grid {
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  gap: 2rem;
}

/* Left Side - Order Summary */
.order-summary-wrapper {
  height: calc(100vh - 200px);
  display: flex;
  flex-direction: column;
}

.order-summary {
  background: #fff;
  border-radius: 20px;
  padding: 1.5rem;
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.order-summary h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.25rem;
  color: #2C2117;
  margin-bottom: 1rem;
  flex-shrink: 0;
}

.summary-items {
  flex: 1;
  overflow-y: auto;
  margin-bottom: 1rem;
  padding-right: 0.5rem;
}

.summary-items::-webkit-scrollbar {
  width: 6px;
}

.summary-items::-webkit-scrollbar-track {
  background: rgba(44, 33, 23, 0.05);
  border-radius: 3px;
}

.summary-items::-webkit-scrollbar-thumb {
  background: #C8854A;
  border-radius: 3px;
}

.summary-item {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 1px solid rgba(44, 33, 23, 0.08);
}

.item-image {
  width: 70px;
  height: 70px;
  background: #F8F2E6;
  border-radius: 10px;
  overflow: hidden;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  flex: 1;
}

.item-details h4 {
  font-size: 0.9rem;
  font-weight: 600;
  color: #2C2117;
  margin-bottom: 0.25rem;
}

.item-details p {
  font-size: 0.75rem;
  color: #7a6555;
  margin: 0;
}

.item-price {
  font-weight: 600;
  color: #C8854A !important;
  margin-top: 0.25rem !important;
}

.empty-cart {
  text-align: center;
  padding: 2rem;
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.summary-totals {
  flex-shrink: 0;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(44, 33, 23, 0.1);
}

.total-row {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
}

.grand-total {
  font-weight: 700;
  font-size: 1.1rem;
  color: #2C2117;
  border-top: 1px solid rgba(44, 33, 23, 0.1);
  margin-top: 0.5rem;
  padding-top: 0.5rem;
}

.free-tag {
  color: #4CAF50;
  font-weight: 500;
}

/* Right Side - Delivery Info */
.delivery-info-wrapper {
  height: calc(100vh - 200px);
  display: flex;
  flex-direction: column;
}

.delivery-info {
  background: #fff;
  border-radius: 20px;
  padding: 1.5rem;
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.delivery-info h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.25rem;
  color: #2C2117;
  margin-bottom: 1rem;
  flex-shrink: 0;
}

.delivery-form {
  flex: 1;
  overflow-y: auto;
  padding-right: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.delivery-form::-webkit-scrollbar {
  width: 6px;
}

.delivery-form::-webkit-scrollbar-track {
  background: rgba(44, 33, 23, 0.05);
  border-radius: 3px;
}

.delivery-form::-webkit-scrollbar-thumb {
  background: #C8854A;
  border-radius: 3px;
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

.input-wrapper input,
.form-group textarea {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 10px;
  font-size: 0.9rem;
  font-family: inherit;
  transition: all 0.2s;
}

.input-wrapper input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #C8854A;
  box-shadow: 0 0 0 3px rgba(200, 133, 74, 0.1);
}

.form-group textarea {
  padding: 0.75rem 1rem;
  resize: vertical;
}

.fields-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.delivery-options {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.delivery-option {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid rgba(44, 33, 23, 0.15);
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s;
}

.delivery-option:hover {
  border-color: #C8854A;
  background: rgba(200, 133, 74, 0.02);
}

.delivery-option.selected {
  border-color: #C8854A;
  background: rgba(200, 133, 74, 0.05);
}

.option-content {
  flex: 1;
}

.option-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.25rem;
}

.option-name {
  font-weight: 500;
  color: #2C2117;
}

.option-price {
  font-weight: 600;
  color: #C8854A;
}

.option-desc {
  font-size: 0.7rem;
  color: #7a6555;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 0.5rem;
}

.back-btn, .continue-btn {
  flex: 1;
  padding: 0.875rem;
  text-align: center;
  border-radius: 10px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  font-size: 0.9rem;
}

.back-btn {
  background: none;
  border: 1px solid rgba(44, 33, 23, 0.2);
  color: #7a6555;
}

.back-btn:hover {
  border-color: #C8854A;
  color: #C8854A;
}

.continue-btn {
  background: #C8854A;
  border: none;
  color: #fff;
}

.continue-btn:hover:not(:disabled) {
  background: #b67038;
  transform: translateY(-2px);
}

.continue-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 968px) {
  .checkout-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .order-summary-wrapper,
  .delivery-info-wrapper {
    height: auto;
    max-height: 500px;
  }
  
  .checkout-container {
    padding: 1rem;
  }
  
  .progress-steps {
    gap: 1rem;
  }
}

@media (max-width: 768px) {
  .fields-row {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .step-label {
    font-size: 0.7rem;
  }
}
</style>