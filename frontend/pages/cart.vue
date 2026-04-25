<template>
  <div>
    <AppHeader />

    <main class="cart-page">
      <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
          <NuxtLink to="/">Home</NuxtLink>
          <span class="sep">/</span>
          <span class="current">Cart</span>
        </nav>

        <h1 class="cart-title">Your shopping Cart</h1>

        <div v-if="loading" class="loader-page">
          <div class="spinner"></div>
        </div>

        <div v-else>
          <div v-if="items.length === 0" class="empty-cart">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#C8854A" stroke-width="1.2">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
              <line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
            <h2>You cart is empty</h2>
            <p>Discover our products and add them to your cart</p>
            <NuxtLink to="/category" class="btn-primary">Continue shopping</NuxtLink>
          </div>

          <div v-else class="cart-content">
            <!-- Cart Items -->
            <div class="cart-items">
              <div class="cart-header">
                <div class="header-product">Product</div>
                <div class="header-price">Unit Price</div>
                <div class="header-quantity">Quantity</div>
                <div class="header-total">Total</div>
                <div class="header-action"></div>
              </div>

              <div
                v-for="item in items"
                :key="item.id"
                class="cart-item"
              >
                <div class="item-product">
                  <img :src="`/${item.product.image}`" :alt="item.product.nom" class="item-image" />
                  <div class="item-info">
                    <h3 class="item-name">{{ item.product.nom }}</h3>
                    <p class="item-unit" v-if="item.product.unit">/ {{ item.product.unit }}</p>
                  </div>
                </div>
                
                <div class="item-price">{{ item.product.prix }} DT</div>
                
                <div class="item-quantity">
                  <div class="qty-ctrl">
                    <button @click="updateQuantity(item, -1)" class="qty-btn" :disabled="updating">−</button>
                    <span class="qty-val">{{ item.quantity }}</span>
                    <button @click="updateQuantity(item, 1)" class="qty-btn" :disabled="updating">+</button>
                  </div>
                </div>
                
                <div class="item-total">{{ (item.product.prix * item.quantity).toFixed(2) }} DT</div>
                
                <div class="item-action">
                  <button  class="remove-btn" :disabled="updating" @click="removeItem(item.id)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                      <line x1="18" y1="6" x2="6" y2="18"/>
                      <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
              <h3 class="summary-title">Order Summary</h3>
              <div class="summary-row">
                <span>Sub Total</span>
                <span>{{ total }} DT</span>
              </div>
              <div class="summary-row">
                <span>Shipping</span>
                <span>calculated at the next step</span>
              </div>
              <div class="summary-divider"></div>
              <div class="summary-row total">
                <span>Total</span>
                <span>{{ total }} DT</span>
              </div>
              <button class="btn-checkout" @click="checkout" >
                Proceed to Checkout
              </button>
              <NuxtLink to="/category" class="continue-shopping">
                ← Continue Shopping
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </main>

    <AppFooter />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(true)
const updating = ref(false)
const items = ref([])
const token = ref(null)

const fetchCart = async () => {
  loading.value = true
  try {
    const response = await axios.get('http://localhost:8000/api/getCart', {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    items.value = response.data.items || []
  } catch (e) {
    console.error('Error fetching cart:', e)
    if (e.response?.status === 401) {
      alert('Veuillez vous connecter pour voir votre panier')
      router.push('/login')
    } else {
      items.value = []
    }
  } finally {
    loading.value = false
  }
}

const updateQuantity = async (item, change) => {
  const newQuantity = item.quantity + change
  if (newQuantity < 1) return
  
  updating.value = true
  try {
    // First update quantity in the cart
    await axios.put(
      `http://localhost:8000/api/updateCart/${item.id}`,
      { quantity: newQuantity },
      { headers: { Authorization: `Bearer ${token.value}` } }
    )
    
    // Update local state
    item.quantity = newQuantity
  } catch (e) {
    console.error('Error updating quantity:', e)
    alert('Erreur lors de la mise à jour de la quantité')
  } finally {
    updating.value = false
  }
}

const removeItem = async (id) => {
  if (!confirm('Voulez-vous vraiment supprimer cet article ?')) return
  
  updating.value = true
  try {
    await axios.delete(`http://localhost:8000/api/removeItem/${id}`, {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    items.value = items.value.filter((item) => item.id !== id)
  } catch (e) {
    console.error('Error removing item:', e)
    alert('Erreur lors de la suppression : ' + (e.response?.data?.message || e.message))
  } finally {
    updating.value = false
  }
}

const checkout = () => {
  router.push('/category/product/checkout')
}

const total = computed(() => {
  const sum = items.value.reduce((sum, item) => sum + (item.product.prix * item.quantity), 0)
  return sum.toFixed(2)
})

onMounted(() => {
  token.value = localStorage.getItem('token')
  if (!token.value) {
    router.push('/login')
  } else {
    fetchCart()
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@400;500&display=swap');

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Breadcrumb */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #7a6555;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.breadcrumb a {
  color: #7a6555;
  text-decoration: none;
}

.breadcrumb a:hover {
  color: #C8854A;
}

.sep {
  color: #c0b4a8;
}

.current {
  color: #2C2117;
  font-weight: 500;
}

/* Cart Page */
.cart-page {
  padding: 2rem 0 4rem;
  background: #fff;
  min-height: 60vh;
}

.cart-title {
  font-family: 'Playfair Display', serif;
  font-size: 2.5rem;
  font-weight: 500;
  color: #2C2117;
  margin-bottom: 2rem;
  text-align: center;
}

/* Loader */
.loader-page {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 40vh;
}

.spinner {
  width: 36px;
  height: 36px;
  border: 2px solid rgba(200,133,74,0.2);
  border-top-color: #C8854A;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Empty Cart */
.empty-cart {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-cart svg {
  margin-bottom: 1.5rem;
  color: #C8854A;
}

.empty-cart h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  color: #2C2117;
  margin-bottom: 1rem;
}

.empty-cart p {
  color: #7a6555;
  margin-bottom: 2rem;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #C8854A;
  color: #fff;
  font-size: 14px;
  font-weight: 500;
  padding: 12px 24px;
  border-radius: 8px;
  text-decoration: none;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: background 0.15s;
}

.btn-primary:hover {
  background: #2C2117;
}

/* Cart Content */
.cart-content {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 2rem;
}

/* Cart Items */
.cart-items {
  background: #fff;
  border: 0.5px solid rgba(44,33,23,0.1);
  border-radius: 12px;
  overflow: hidden;
}

.cart-header {
  display: grid;
  grid-template-columns: 3fr 1fr 1.5fr 1fr 0.5fr;
  background: #F8F2E6;
  padding: 1rem 1.5rem;
  font-size: 13px;
  font-weight: 500;
  color: #2C2117;
  border-bottom: 0.5px solid rgba(44,33,23,0.1);
}

.cart-item {
  display: grid;
  grid-template-columns: 3fr 1fr 1.5fr 1fr 0.5fr;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 0.5px solid rgba(44,33,23,0.08);
  transition: background 0.15s;
}

.cart-item:hover {
  background: #FCFAF7;
}

.item-product {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.item-image {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 8px;
  background: #F8F2E6;
}

.item-info {
  flex: 1;
}

.item-name {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  font-weight: 500;
  color: #2C2117;
  margin: 0 0 4px 0;
}

.item-unit {
  font-size: 12px;
  color: #7a6555;
  margin: 0;
}

.item-price,
.item-total {
  font-size: 14px;
  font-weight: 500;
  color: #2C2117;
}

.item-quantity {
  display: flex;
  justify-content: flex-start;
}

.qty-ctrl {
  display: inline-flex;
  align-items: center;
  background: #F8F2E6;
  border-radius: 8px;
  border: 0.5px solid rgba(44,33,23,0.15);
  overflow: hidden;
}

.qty-btn {
  width: 32px;
  height: 36px;
  border: none;
  background: transparent;
  font-size: 18px;
  color: #2C2117;
  cursor: pointer;
  transition: background 0.15s;
}

.qty-btn:hover:not(:disabled) {
  background: rgba(200,133,74,0.1);
}

.qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.qty-val {
  min-width: 32px;
  text-align: center;
  font-size: 14px;
  font-weight: 500;
}

.item-action {
  display: flex;
  justify-content: flex-end;
}

.remove-btn {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 0.5px solid rgba(44,33,23,0.15);
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #7a6555;
  transition: all 0.15s;
}

.remove-btn:hover:not(:disabled) {
  color: #E24B4A;
  border-color: #E24B4A;
  background: #FCEBEB;
}

.remove-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Cart Summary */
.cart-summary {
  background: #F8F2E6;
  border-radius: 12px;
  padding: 1.5rem;
  height: fit-content;
  position: sticky;
  top: 20px;
}

.summary-title {
  font-family: 'Playfair Display', serif;
  font-size: 1.2rem;
  font-weight: 500;
  color: #2C2117;
  margin: 0 0 1.5rem 0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  font-size: 14px;
  color: #5a4a3a;
}

.summary-divider {
  height: 1px;
  background: rgba(44,33,23,0.1);
  margin: 1rem 0;
}

.summary-row.total {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2C2117;
  margin-bottom: 1.5rem;
}

.btn-checkout {
  width: 100%;
  background: #C8854A;
  color: #fff;
  font-size: 14px;
  font-weight: 500;
  padding: 12px 20px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: background 0.15s;
  margin-bottom: 1rem;
}

.btn-checkout:hover {
  background: #2C2117;
}

.continue-shopping {
  display: inline-block;
  text-align: center;
  width: 100%;
  font-size: 13px;
  color: #7a6555;
  text-decoration: none;
  transition: color 0.15s;
}

.continue-shopping:hover {
  color: #C8854A;
}

/* Responsive */
@media (max-width: 968px) {
  .cart-content {
    grid-template-columns: 1fr;
  }
  
  .cart-summary {
    position: static;
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
  
  .cart-header {
    display: none;
  }
  
  .cart-item {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .item-product {
    flex-direction: column;
    text-align: center;
  }
  
  .item-price,
  .item-total,
  .item-quantity,
  .item-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .item-price::before {
    content: "Prix unitaire:";
    font-size: 12px;
    color: #7a6555;
  }
  
  .item-total::before {
    content: "Total:";
    font-size: 12px;
    color: #7a6555;
  }
  
  .item-quantity::before {
    content: "Quantité:";
    font-size: 12px;
    color: #7a6555;
  }
  
  .cart-title {
    font-size: 2rem;
  }
}
</style>