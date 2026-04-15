<template>
  <div class="container py-5">
    <h1 class="mb-4">Finalize Your Order</h1>

    <!-- Résumé du panier -->
    <div class="mb-4" v-if="items.length > 0">
      <h3>Your Cart</h3>
      <ul class="list-group">
        <li v-for="item in items" :key="item.id" class="list-group-item d-flex justify-content-between">
          <span>{{ item.product.nom }} (x{{ item.quantity }})</span>
          <span>{{ item.product.prix }} dt</span>
        </li>
        <li class="list-group-item d-flex justify-content-between fw-bold">
          <span>Total:</span>
          <span>{{ total }} dt</span>
        </li>
      </ul>
    </div>

    <div v-else class="alert alert-info">
      Your cart is empty.
    </div>

    <div class="mb-4" v-if="items.length > 0">
      <h3>Delivery Information</h3>
      <form @submit.prevent="submitOrder">
        <div class="mb-3">
          <label class="form-label">Address :</label>
          <input
              v-model="address"
              type="text"
              class="form-control"
              required
          />
        </div>

        <div class="mb-3">
          <label class="form-label">Delivery Method :</label>
          <select
              v-model="delivery"
              class="form-select"
              required
          >
            <option disabled value="">-- Select delivery option --</option>
            <option value="Standard">Standard Delivery</option>
            <option value="Express">Express Delivery</option>
            <option value="clickAndCollect">Click & Collect</option>
            <option value="Relay">Pick up Point</option>
          </select>
        </div>

        <button type="submit" class="btn btn-success w-100">
          Confirm Order
        </button>
      </form>
    </div>

    <div v-else>
      <button class="btn btn-secondary w-100" disabled>
        Confirm Order
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from '#vue-router'

const props = defineProps({
  page: String,
  query: Object,
})

const router = useRouter()

const items = ref([])
const token = localStorage.getItem('token')

const delivery = ref('')
const address = ref('')

const fetchCart = async () => {
  const response = await axios.get('http://localhost:8000/api/getCart', {
    headers: { Authorization: `Bearer ${token}` },
  })
  items.value = response.data.items
}

const submitOrder = async () => {
  if (!address.value || !delivery.value) {
    alert('Please fill all fields')
    return
  }

  try {
    await axios.post(
        'http://localhost:8000/api/createOrder',
        {
          delivery: delivery.value,
          address: address.value,
        },
        {
          headers: { Authorization: `Bearer ${token}` },
        }
    )

    router.push({
      path: props.page || '/',
      query: props.query || {},
    })
  } catch (error) {
    alert('Erreur lors de la validation.')
    console.error(error)
  }
}

onMounted(fetchCart)

const total = computed(() =>
    items.value.reduce((sum, item) => sum + item.product.prix * item.quantity, 0).toFixed(2)
)
</script>

<style scoped>
.container {
  max-width: 700px;
}
</style>
