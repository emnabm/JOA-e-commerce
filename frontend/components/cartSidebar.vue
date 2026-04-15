<template>
  <div
      id="offcanvasCart"
      class="offcanvas offcanvas-end"
      tabindex="-1"
      aria-labelledby="offcanvasCartLabel"
      data-bs-scroll="true"
  >
    <div class="offcanvas-header">
      <h5 id="offcanvasCartLabel" class="offcanvas-title">Your Shopping Cart</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column">
      <div v-if="items.length === 0" class="text-center text-muted">
        Your cart is empty.
      </div>

      <div
          v-for="item in items"
          :key="item.id"
          class="cart-item d-flex mb-3 align-items-center border-bottom pb-3"
      >
        <img
            :src="`/${item.product.image}`"
            alt="Product"
            style="width: 60px; height: 60px; object-fit: cover;"
            class="me-3"
        />
        <div class="flex-grow-1">
          <h6 class="mb-1">{{ item.product.name }}</h6>
          <small class="text-muted">Quantity: {{ item.quantity }}</small
          >
          <br>
          <small class="text-muted">Price: ${{ item.product.prix }}</small>
        </div>
        <button
            class="btn btn-sm btn-outline-danger ms-2"
            @click="removeItem(item.id)"
        >
          Remove
        </button>
      </div>
      </div>

      <div v-if="items.length > 0" class="mt-auto">
        <div class="border-top pt-3">
          <button @click="checkout" class="btn btn-success btn-lg w-100 d-flex justify-content-between align-items-center">
            <span>Proceed to Checkout</span>
            <strong>{{ total }} dt</strong>
          </button>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  reload: Number
})
const router=useRouter()
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
};
const checkout = () => {
  router.push('/category/product/checkout')

}

watch(() => props.reload, () => fetchCart(), { immediate: true })

const total = computed(() =>
    items.value.reduce((sum, item) => sum + item.product.prix * item.quantity, 0).toFixed(2)
)
</script>
