<template>
  <div class="card shadow-sm p-4 m-4">
    <h5 class="mb-4 text-success fw-bold">Product Management</h5>

    <!-- Onglets filtre identiques -->
    <ul class="nav nav-tabs mb-3">
      <li class="nav-item text-secondary" v-for="tab in tabs" :key="tab.key">
        <button
            class="nav-link"
            :class="{ active: activeTab === tab.key }"
            @click="setTab(tab.key)"
        >
          {{ tab.label }}
        </button>
      </li>
    </ul>

    <!-- Barre de recherche -->
    <div class="mb-3">
      <input
          v-model="searchTerm"
          type="text"
          class="form-control"
          placeholder="Search"
      />
    </div>

    <!-- Tableau -->
    <div class="table-responsive order-table-scroll">
      <table class="table table-borderless mb-0">
        <thead class="table-light">
        <tr>
          <th>Nom</th>
          <th>Description</th>
          <th>Prix</th>
          <th>Stock</th>
          <th>Unit</th>
          <th>État</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="product in filteredProducts" :key="product.id">
          <td :title="product.nom">
            {{ product.nom.length > 15 ? product.nom.substring(0, 15) + '...' : product.nom }}
          </td>
          <td :title="product.description">
            {{
              product.description.length > 25
                  ? product.description.substring(0, 25) + '...'
                  : product.description
            }}
          </td>
          <td>{{ product.prix }}</td>
          <td>{{ product.stock }}</td>
          <td>{{ product.unit }}</td>
          <td>
            <span v-if="product.stock === 0" class="text-danger fw-semibold">Out Of Stock</span>
            <span
                v-else-if="product.stock <= 20"
                class="text-warning fw-semibold"
            >Low Stock</span
            >
            <span v-else class="text-success fw-semibold">Available</span>
          </td>
          <td>
            <button class="btn  me-2" @click="editProduct(product)">
              <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0,0,256,256" style="fill:#12B886;">
                <g fill="#12b886" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(8,8)"><path d="M23.90625,3.96875c-1.04687,0 -2.09375,0.40625 -2.90625,1.21875l-15.8125,15.8125l-0.0625,0.3125l-1.09375,5.5l-0.3125,1.46875l1.46875,-0.3125l5.5,-1.09375l0.3125,-0.0625l15.8125,-15.8125c1.625,-1.625 1.625,-4.1875 0,-5.8125c-0.8125,-0.8125 -1.85937,-1.21875 -2.90625,-1.21875zM23.90625,5.875c0.50391,0 1.01172,0.23047 1.5,0.71875c0.97266,0.97266 0.97266,2.02734 0,3l-0.71875,0.6875l-2.96875,-2.96875l0.6875,-0.71875c0.48828,-0.48828 0.99609,-0.71875 1.5,-0.71875zM20.3125,8.71875l2.96875,2.96875l-12.09375,12.09375c-0.65625,-1.28125 -1.6875,-2.3125 -2.96875,-2.96875zM6.9375,22.4375c1.19922,0.48438 2.14063,1.42578 2.625,2.625l-3.28125,0.65625z"></path></g></g>
              </svg>
            </button>
            <button class="btn " @click="deleteProduct(product.id)">
              <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0 0 100 100" style="fill:#12B886;">
                <path d="M 46 13 C 44.35503 13 43 14.35503 43 16 L 43 18 L 32.265625 18 C 30.510922 18 28.879517 18.922811 27.976562 20.427734 L 26.433594 23 L 23 23 C 20.802666 23 19 24.802666 19 27 C 19 29.197334 20.802666 31 23 31 L 24.074219 31 L 27.648438 77.458984 C 27.88773 80.575775 30.504529 83 33.630859 83 L 66.369141 83 C 69.495471 83 72.11227 80.575775 72.351562 77.458984 L 75.925781 31 L 77 31 C 79.197334 31 81 29.197334 81 27 C 81 24.802666 79.197334 23 77 23 L 73.566406 23 L 72.023438 20.427734 C 71.120481 18.922811 69.489078 18 67.734375 18 L 57 18 L 57 16 C 57 14.35503 55.64497 13 54 13 L 46 13 z M 46 15 L 54 15 C 54.56503 15 55 15.43497 55 16 L 55 18 L 45 18 L 45 16 C 45 15.43497 45.43497 15 46 15 z M 32.265625 20 L 43.832031 20 A 1.0001 1.0001 0 0 0 44.158203 20 L 55.832031 20 A 1.0001 1.0001 0 0 0 56.158203 20 L 67.734375 20 C 68.789672 20 69.763595 20.551955 70.306641 21.457031 L 71.833984 24 L 68.5 24 A 0.50005 0.50005 0 1 0 68.5 25 L 73.5 25 L 77 25 C 78.116666 25 79 25.883334 79 27 C 79 28.116666 78.116666 29 77 29 L 23 29 C 21.883334 29 21 28.116666 21 27 C 21 25.883334 21.883334 25 23 25 L 27 25 L 61.5 25 A 0.50005 0.50005 0 1 0 61.5 24 L 28.166016 24 L 29.693359 21.457031 C 30.236405 20.551955 31.210328 20 32.265625 20 z M 64.5 24 A 0.50005 0.50005 0 1 0 64.5 25 L 66.5 25 A 0.50005 0.50005 0 1 0 66.5 24 L 64.5 24 z M 26.078125 31 L 73.921875 31 L 70.357422 77.306641 C 70.196715 79.39985 68.46881 81 66.369141 81 L 33.630859 81 C 31.53119 81 29.803285 79.39985 29.642578 77.306641 L 26.078125 31 z M 38 35 C 36.348906 35 35 36.348906 35 38 L 35 73 C 35 74.651094 36.348906 76 38 76 C 39.651094 76 41 74.651094 41 73 L 41 38 C 41 36.348906 39.651094 35 38 35 z M 50 35 C 48.348906 35 47 36.348906 47 38 L 47 73 C 47 74.651094 48.348906 76 50 76 C 51.651094 76 53 74.651094 53 73 L 53 69.5 A 0.50005 0.50005 0 1 0 52 69.5 L 52 73 C 52 74.110906 51.110906 75 50 75 C 48.889094 75 48 74.110906 48 73 L 48 38 C 48 36.889094 48.889094 36 50 36 C 51.110906 36 52 36.889094 52 38 L 52 63.5 A 0.50005 0.50005 0 1 0 53 63.5 L 53 38 C 53 36.348906 51.651094 35 50 35 z M 62 35 C 60.348906 35 59 36.348906 59 38 L 59 39.5 A 0.50005 0.50005 0 1 0 60 39.5 L 60 38 C 60 36.889094 60.889094 36 62 36 C 63.110906 36 64 36.889094 64 38 L 64 73 C 64 74.110906 63.110906 75 62 75 C 60.889094 75 60 74.110906 60 73 L 60 47.5 A 0.50005 0.50005 0 1 0 59 47.5 L 59 73 C 59 74.651094 60.348906 76 62 76 C 63.651094 76 65 74.651094 65 73 L 65 38 C 65 36.348906 63.651094 35 62 35 z M 38 36 C 39.110906 36 40 36.889094 40 38 L 40 73 C 40 74.110906 39.110906 75 38 75 C 36.889094 75 36 74.110906 36 73 L 36 38 C 36 36.889094 36.889094 36 38 36 z M 59.492188 41.992188 A 0.50005 0.50005 0 0 0 59 42.5 L 59 44.5 A 0.50005 0.50005 0 1 0 60 44.5 L 60 42.5 A 0.50005 0.50005 0 0 0 59.492188 41.992188 z"></path>
              </svg></button>
          </td>
        </tr>
        <tr v-if="filteredProducts.length === 0">
          <td colspan="7" class="text-center">Aucun produit trouvé.</td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- Formulaire -->
    <div class="card p-3 mt-4 shadow-sm">
      <h5>{{ editing ? ' Edit Product' : ' Add Product' }}</h5>
      <form @submit.prevent="saveProduct">
        <div class="row">
          <div class="col-md-6 mb-2">
            <label>Nom:</label>
            <input v-model="nom" class="form-control" required />
          </div>
          <div class="col-md-6 mb-2">
            <label>Description:</label>
            <input v-model="description" class="form-control" required />
          </div>
          <div class="col-md-4 mb-2">
            <label>Prix:</label>
            <input v-model="prix"  class="form-control" required />
          </div>
          <div class="col-md-4 mb-2">
            <label>Stock:</label>
            <input v-model="stock"  class="form-control" required />
          </div>
          <div class="col-md-4 mb-2">
            <label>Unit:</label>
            <input v-model="unit" class="form-control" required />
          </div>

          <div class="col-md-6 mb-2">
            <label>Image URL:</label>
            <input v-model="image" class="form-control" required />
          </div>

          <div class="col-md-6 mb-2">
            <label>Category ID:</label>
            <select v-model="category" class="form-control" required>
              <option value="" disabled>-- Sélectionnez une catégorie --</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>

        </div>
        <button class="btn btn-success" type="submit">Save</button>
        <button
            v-if="editing"
            type="button"
            class="btn btn-secondary ms-2"
            @click="cancelEdit"
        >
          Annuler
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// Onglets
const tabs = [
  { key: 'all', label: 'All' },
  { key: 'available', label: 'Available' },
  { key: 'low_stock', label: 'Low Stock' },
  { key: 'out_of_stock', label: 'Out Of Stock' },
]

const activeTab = ref('all')
const searchTerm = ref('')

const products = ref<any[]>([])
const categories=ref([])
// ✅ Refs unitaires

const id_prod = ref<number | null>(null)
const nom = ref('')
const description = ref('')
const prix = ref(0)
const stock = ref(0)
const unit = ref('')
const image = ref('') // ➜ Nouveau champ
const category = ref('') // ➜ Nouveau champ
const editing = ref(false)

const fetchCategories = async () => {
  const res = await axios.get('http://localhost:8000/admin/categories')
  categories.value = res.data
}

const fetchProducts = async () => {
  const res = await axios.get('http://localhost:8000/admin/products')
  products.value = res.data
}

const setTab = (tab: string) => {
  activeTab.value = tab
}

const filteredProducts = computed(() => {
  return products.value.filter((product) => {
    const matchesSearch = product.nom.toLowerCase().includes(searchTerm.value.toLowerCase())
    let matchesFilter = true

    if (activeTab.value === 'out_of_stock') {
      matchesFilter = product.stock === 0
    } else if (activeTab.value === 'low_stock') {
      matchesFilter = product.stock > 0 && product.stock <= 20
    } else if (activeTab.value === 'available') {
      matchesFilter = product.stock > 0
    }

    return matchesSearch && matchesFilter
  })
})

const editProduct = (product: any) => {
  id_prod.value = product.id
  nom.value = product.nom
  description.value = product.description
  prix.value = product.prix
  stock.value = product.stock
  unit.value = product.unit
  image.value = product.image || ''
  category.value = product.category?.id || ''
  editing.value = true
}

const saveProduct = async () => {
  const payload = {
    nom: nom.value,
    description: description.value,
    prix: prix.value,
    stock: stock.value,
    unit: unit.value,
    image: image.value,
    category: category.value,
  }

// Ne mets `product` que si tu édites
  if (editing.value && id_prod.value !== null) {
    await axios.put(`http://localhost:8000/admin/products/${id_prod.value}`, payload)
  } else {
    console.log(payload)
    await axios.post(`http://localhost:8000/admin/addProduct`, payload)
  }

  resetForm()
  fetchProducts()
}

const deleteProduct = async (id: number) => {
  await axios.delete(`http://localhost:8000/api/products/${id}`)
  fetchProducts()
}

const resetForm = () => {
  id_prod.value = null
  nom.value = ''
  description.value = ''
  prix.value = 0
  stock.value = 0
  unit.value = ''
  image.value = ''
  category.value = ''
  editing.value = false
}

const cancelEdit = () => {
  resetForm()
}

onMounted(()=>
{fetchProducts()
  fetchCategories()
})
</script>

<style scoped>
.card{
  max-width: 100%;
}
.order-table-scroll {
  max-height: 280px;
  overflow-y: auto;
}

.nav-tabs .nav-link {
  color: #6c757d;
}

.nav-tabs .nav-link.active {
  color: #198754;
  border-color: #198754 #198754 #fff;
}

.table thead {
  background-color: #fff8e1;
}

.text-success {
  color: #198754 !important;
}

.text-danger {
  color: #e48b08 !important;
}

.text-warning {
  color: #ffc107 !important;
}
</style>
