<template>
  <div>
    <AppHeader/>

    <div class="d-flex m-5 gap-5 me-4">
      <!-- Suggestion Sidebar -->
      <!--  fixe mayet7arekch    -->
      <ProductSuggestions :currentId="id" style="width: 30%;" />

      <!-- Product Detail -->
      <div class="p-3" style="width: 60%;">
        <div v-if="loading">Chargement...</div>

        <div v-else-if="data.product">
          <div class="border rounded mb-3">
            <img :src="`/${data.product.image}`" alt="" class="w-100" />
          </div>

          <h1 class="product-title">{{ data.product.nom }}</h1>
          <p class="text text-muted small">({{ customers }} Customer Reviews)</p>

          <h3 class="text text-success fw-bold">{{ data.product.prix }} dt</h3>

          <p class="text text-description">{{ data.product.description }}</p>

          <p class="text text-muted">Unit: {{ data.product.unit }}</p>

          <p class="text-success">✅ {{ data.product.stock }} in stock</p>
          <hr />

          <div class="d-flex gap-2">
            <div class="input-group" style="width: 120px;">
              <button class="btn btn-outline-secondary" @click="dec()">-</button>
              <input
                  type="text"
                  class="form-control text-center"
                  v-model="quantity"
              />
              <button class="btn btn-outline-secondary" @click="inc()">+</button>
            </div>

            <button
                class="btn btn-warning"
                type="button"
                @click="addToCart"
            >
              <img class="add-cart" src="/shopping-basket.png" />
              Add to cart
            </button>
          </div>

          <hr />

          <!-- Reviews -->
          <div class="border rounded p-3">
            <div class="d-flex justify-content-between align-items-center">
              <h2 class="mb-0">Reviews</h2>
              <button class="btn" @click="getReviews()" >{{ signe }}</button>
            </div>

            <div v-if="reviews">
              <Review ref="reviewComponent" :prod_id="id" />

              <div class="mt-4">
                <form class="border p-3 rounded" @submit.prevent="submitReview">
                  <!-- Rating Stars -->
                  <div class="mb-3">
                    <label class="form-label">Your rating *</label>
                    <div>
                      <span
                          v-for="star in 5"
                          :key="star"
                          class="me-1"
                          style="cursor: pointer; font-size: 1.5rem;"
                          @click="reviewRating = star"
                          
                      >
                        <img
                            src="/star.png"
                            :class="reviewRating >= star ? 'star-filled' : 'star-empty'"
                            style="width: 24px;"
                        >
                      </span>
                    </div>
                  </div>

                  <!-- Textarea -->
                  <div class="mb-3">
                    <label for="reviewText" class="form-label">Your review *</label>
                    <textarea
                        id="reviewText"
                        v-model="reviewText"
                        class="form-control"
                        rows="4"
                        required
                    ></textarea>
                  </div>

                  <button type="submit" class="btn btn-warning">Envoyer</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div v-else>
          Aucun produit trouvé.
        </div>
      </div>
    </div>

    <!-- Cart Sidebar -->
    <CartSidebar :reload="sidebarReload" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from '#vue-router'
import axios from 'axios'
import { Offcanvas } from 'bootstrap'



const route = useRoute()
const id = route.params.id

const data = ref([])
const loading = ref(false)
const customers = ref(0)
const quantity = ref(1)
const signe = ref('+')
const reviews = ref(false)
const reviewText = ref('')
const reviewRating = ref(0)
const reviewComponent = ref(null)

const token = localStorage.getItem('token')

// Sidebar contrôle
const sidebarReload = ref(0)

const fetchProduct = async () => {
  try {
    loading.value = true
    const response = await axios.get(`http://localhost:8000/getProduct_${id}`)
    data.value = response.data
    customers.value = data.value.reviewCount
  } catch (e) {
    alert('Erreur : ' + e.message)
  } finally {
    loading.value = false
  }
}

const inc = () => {
  if (quantity.value < data.value.product.stock) {
    quantity.value++
  }
}
const dec = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const getReviews = () => {
  signe.value = signe.value === '+' ? '-' : '+'
  reviews.value = !reviews.value
}

const submitReview = async () => {
  try {
    await axios.post(
        'http://localhost:8000/api/review',
        {
          productId: id,
          text: reviewText.value,
          rating: reviewRating.value
        },
        { headers: { Authorization: `Bearer ${token}` } }
    )


    // Ajout immédiat côté frontend
    if (reviewComponent.value) {
      reviewComponent.value.addReviewLocally({
        id: Date.now(),
        text: reviewText.value,
        date: new Date().toISOString().split("T")[0],
        user: localStorage.getItem('user_username'),
        rating: Number(reviewRating.value)

      })
    }
    // Nettoyage formulaire
    reviewText.value = ''
    reviewRating.value = 0

    alert('Votre avis a été envoyé !')
  } catch (e) {
    alert("Erreur lors de l'envoi de l'avis."+e)
  }
}

const addToCart = async () => {
  try {
    await axios.post(
        'http://localhost:8000/api/addToCart',
        { productId: id, quantity: quantity.value },
        { headers: { Authorization: `Bearer ${token}` } }
    )
    sidebarReload.value++
    const myOffcanvas = new Offcanvas('#offcanvasCart')
    myOffcanvas.show()

    await fetchProduct()
  } catch (e) {
    alert("Erreur lors de l'ajout au panier."+e)
  }
}

onMounted(fetchProduct)
</script>

<style scoped>
.product-title {
  font-weight: 700;
}

.text-description {
  color: #6c757d;
  line-height: 1.5;
}

.text-success {
  color: #198754;
}

.add-cart {
  width: 20px;
  margin-right: 5px;
}

.star-filled {
  filter: none;
}

.star-empty {
  filter: grayscale(100%);
  opacity: 0.4;
}
</style>
