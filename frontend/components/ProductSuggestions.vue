<template>
  <div class="suggestion-box p-3 rounded shadow-sm">
    <h4 class="mb-3 text-warning fw-bold">Produits similaires</h4>

    <!-- Loading -->
    <div v-if="loading" class="text-muted fst-italic text-center">
      Chargement des suggestions...
    </div>

    <!-- Suggestions List -->
    <div v-else-if="suggestions.length > 0" class="suggestion-list">
      <div
          v-for="product in suggestions"
          :key="product.id"
          class="suggestion-card d-flex align-items-center justify-content-between"
      >
        <div class="d-flex align-items-center">
          <img
              :src="`/${product.image}`"
              alt="product image"
              class="product-img me-3"
          />
          <div>
            <router-link
                :to="`/product/${product.id}`"
                class="fw-bold text-dark text-decoration-none product-name"
            >
              {{ product.nom }}
            </router-link>
            <p class="mb-0">
              <span class="badge bg-success px-2">{{ product.prix }} dt</span>
            </p>
          </div>
        </div>

        <router-link
            :to="`/product/${product.id}`"
            class="btn btn-sm btn-outline-warning rounded-pill px-3"
        >
          Voir
        </router-link>
      </div>
    </div>

    <!-- No suggestions -->
    <div v-else class="text-muted fst-italic text-center">
      Aucun produit suggéré.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"

const props = defineProps({
  currentId: {
    type: Number,
    required: true
  }
})

const suggestions = ref([])
const loading = ref(false)

const fetchSuggestions = async () => {
  try {
    loading.value = true
    const res = await axios.get(`http://localhost:8000/products/suggestions/${props.currentId}`)
    suggestions.value = res.data
  } catch (err) {
    console.error("Erreur lors du chargement des suggestions", err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchSuggestions)
</script>

<style scoped>
.suggestion-box {
  background: #fff;
  border: 1px solid #f1f1f1;
  min-height: 450px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding: 1rem;
}

.suggestion-list {
  display: flex;
  flex-direction: column;
  gap: 3rem; /* espace entre chaque produit */
}

.suggestion-card {
  border-radius: 10px;
  padding: 0.5rem 1rem;
  text-align: left;
  background: #fff;
  transition: all 0.3s ease-in-out;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.suggestion-card:hover {
  background: #f8f9fa;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
}

.product-img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.product-name {
  font-size: 0.95rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
