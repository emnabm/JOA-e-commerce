<template>
  <div v-if="!loading" class="mt-3">
    <div v-if="data ==='No reviews found'">
      No reviews found
    </div>
    <div
        v-else
        v-for="review in data"
        :key="review.id"
        class="d-flex gap-3 border-bottom pb-3 mb-3"
    >
      <img
          src="/user.png"
          alt="User avatar"
          class="rounded-circle"
          style="width: 50px; height: 50px; object-fit: cover;"
      />

      <div class="flex-grow-1">
        <div class="d-flex justify-content-between">
          <strong class="text-dark">{{ review.user }}</strong>
          <small class="text-muted">{{ review.date }}</small>
        </div>

        <!--  Étoiles  -->
        <div class="mb-1">
          <span
              v-for="star in 5"
              :key="star"
              style="font-size: 1.2rem;"
          >
            <img
                src="/star.png"
                :class="review.rating >= star ? 'star-filled' : 'star-empty'"
                style="width: 24px;"
            />
          </span>
        </div>

        <p class="mb-0 text-secondary">{{ review.text }}</p>
      </div>
    </div>

    <div v-if="data.length === 0" class="text-muted">
      Aucun avis pour ce produit.
    </div>
  </div>

  <div v-else class="text-center">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Chargement...</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  prod_id: Number,
})


defineExpose({
  addReviewLocally: (review) => {
    data.value.unshift(review) // ajoute en haut de la liste
  }
})

const loading = ref(false)
const data = ref([])

const fetchReviews = async () => {
  try {
    loading.value = true
    const response = await axios.get(
        `http://localhost:8000/api/review/${props.prod_id}`,
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        }
    )
    data.value = response.data
  } catch (e) {
    alert('Erreur de chargement des avis: ' + e.message)
  } finally {
    loading.value = false
  }
}

onMounted(fetchReviews)
</script>

<style scoped>
.star-filled {
  filter: none;
}

.star-empty {
  filter: grayscale(100%);
  opacity: 0.4;
}
</style>
