<template>
  <div class="container my-4">
    <h3>Categories</h3>

    <!-- Barre de recherche -->
    <div class="mb-3">
      <input
          v-model="searchTerm"
          type="text"
          class="form-control"
          placeholder="Search by name"
      >
    </div>

    <table class="table table-striped">
      <thead>
      <tr>
        <th>Name</th>
        <th>Image</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <!--  Utilisation de filteredCategories -->
      <tr v-for="category in filteredCategories" :key="category.id">
        <td>{{ category.name }}</td>
        <td>{{ category.img }}</td>
        <td>
          <button class="btn me-2" @click="editCategory(category)">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="30"
                height="30"
                viewBox="0,0,256,256"
                style="fill:#2C2117;"
            >
              <g fill="#2C2117">
                <g transform="scale(8,8)">
                  <path
                      d="M23.90625,3.96875c-1.04687,0 -2.09375,0.40625 -2.90625,1.21875l-15.8125,15.8125l-0.0625,0.3125l-1.09375,5.5l-0.3125,1.46875l1.46875,-0.3125l5.5,-1.09375l0.3125,-0.0625l15.8125,-15.8125c1.625,-1.625 1.625,-4.1875 0,-5.8125c-0.8125,-0.8125 -1.85937,-1.21875 -2.90625,-1.21875zM23.90625,5.875c0.50391,0 1.01172,0.23047 1.5,0.71875c0.97266,0.97266 0.97266,2.02734 0,3l-0.71875,0.6875l-2.96875,-2.96875l0.6875,-0.71875c0.48828,-0.48828 0.99609,-0.71875 1.5,-0.71875zM20.3125,8.71875l2.96875,2.96875l-12.09375,12.09375c-0.65625,-1.28125 -1.6875,-2.3125 -2.96875,-2.96875zM6.9375,22.4375c1.19922,0.48438 2.14063,1.42578 2.625,2.625l-3.28125,0.65625z"
                  ></path>
                </g>
              </g>
            </svg>
          </button>
          <button class="btn" @click="deleteCategory(category.id)">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="30"
                height="30"
                viewBox="0 0 100 100"
                style="fill:#2C1725;"
            >
              <path
                  d="M 46 13 C 44.35503 13 43 14.35503 43 16 L 43 18 L 32.265625 18 C 30.510922 18 28.879517 18.922811 27.976562 20.427734 L 26.433594 23 L 23 23 C 20.802666 23 19 24.802666 19 27 C 19 29.197334 20.802666 31 23 31 L 24.074219 31 L 27.648438 77.458984 C 27.88773 80.575775 30.504529 83 33.630859 83 L 66.369141 83 C 69.495471 83 72.11227 80.575775 72.351562 77.458984 L 75.925781 31 L 77 31 C 79.197334 31 81 29.197334 81 27 C 81 24.802666 79.197334 23 77 23 L 73.566406 23 L 72.023438 20.427734 C 71.120481 18.922811 69.489078 18 67.734375 18 L 57 18 L 57 16 C 57 14.35503 55.64497 13 54 13 L 46 13 z ..."
              ></path>
            </svg>
          </button>
        </td>
      </tr>
      </tbody>
    </table>

    <div class="card p-3 mt-4">
      <h5>{{ editing ? 'Edit Category' : 'Add Category' }}</h5>
      <form @submit.prevent="saveCategory">
        <div class="mb-2">
          <label>Name:</label>
          <input v-model="name" class="form-control" required >
        </div>
        <div class="mb-2">
          <label>Image URL:</label>
          <input v-model="img" class="form-control" required >
        </div>
        <button class="btn btn-success" type="submit">Save</button>
      </form>
    </div>
  </div>
</template>

<script setup >
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const categories = ref([])
const editing = ref(false)

const id_cat = ref(null)
const name = ref('')
const img = ref('')

const searchTerm = ref('')
const filteredCategories = computed(() =>
    categories.value.filter(category =>
        category.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
)

const fetchCategories = async () => {
  const res = await axios.get('http://localhost:8000/admin/categories')
  categories.value = res.data
}

const editCategory = (category) => {
  id_cat.value = category.id
  name.value = category.name
  img.value = category.img
  editing.value = true
}

const saveCategory = async () => {
  const payload = {
    name: name.value,
    img: img.value
  }

  if (editing.value && id_cat.value !== null) {
    await axios.put(`http://localhost:8000/admin/categories/${id_cat.value}`, payload)
  } else {
    await axios.post(`http://localhost:8000/admin/categories`, payload)
  }

  // Reset
  id_cat.value = null
  name.value = ''
  img.value = ''
  editing.value = false

  fetchCategories()
}

const deleteCategory = async (id) => {
  try {
  await axios.delete(`http://localhost:8000/admin/categories/${id}`)
}catch (e) {
   alert(e)
  }finally {
    fetchCategories()
  }

}

onMounted(fetchCategories)
</script>
