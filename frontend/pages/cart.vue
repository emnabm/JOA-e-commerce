<template>
  <div class="container py-5">
    <h2 class="mb-4">Your Shopping Cart</h2>

    <div v-if="loading" class="text-center">
      <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else>
      <div v-if="items.length === 0" class="text-center text-muted">
        Your cart is empty.
      </div>

      <div v-else>
        <div
            v-for="item in items"
            :key="item.id"
            class="cart-item d-flex mb-4 align-items-center border-bottom pb-3"
        >
          <img
              :src="`/${item.product.image}`"
              alt="Product"
              style="width: 80px; height: 80px; object-fit: cover;"
              class="me-4 rounded"
          />
          <div class="flex-grow-1">
            <h5 class="mb-1">{{ item.product.name }}</h5>
            <p class="mb-1 text-muted">Quantity: {{ item.quantity }}</p>
            <p class="mb-0 text-muted">Price: {{ item.product.prix }} dt</p>
          </div>
          <button
              class="btn btn-sm btn-outline-danger"
              @click="removeItem(item.id)"
          >
            Remove
          </button>
        </div>

        <div class="border-top pt-4 d-flex justify-content-between align-items-center">
          <h4>Total: {{ total }} dt</h4>
          <button @click="checkout" class="btn btn-success btn-lg">
            Proceed to Checkout
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(true)
const items = ref([])
const token = localStorage.getItem('token')

const fetchCart = async () => {
  loading.value = true
  try {
    const response = await axios.get('http://localhost:8000/api/getCart', {
      headers: { Authorization: `Bearer ${token}` }
    })
    items.value = response.data.items
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const removeItem = async (id) => {
  loading.value=true
  try {
    await axios.delete(`http://localhost:8000/api/removeItem/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    items.value = items.value.filter((item) => item.id !== id);
  } catch (e) {
    alert("Erreur lors de la suppression : " + e.message);
  }
  finally {
    loading.value=false
  }
};

const checkout = () => {
  router.push('/category/product/checkout')
}

const total = computed(() =>
    items.value.reduce((sum, item) => sum + item.product.prix * item.quantity, 0).toFixed(2)
)

onMounted(() => {
  fetchCart()
})
</script>

<style scoped>
.cart-item img {
  border: 1px solid #dee2e6;
}
</style>
