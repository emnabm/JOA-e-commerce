<template>
  <div v-if="!loading" class="card shadow-sm p-4 m-4">
    <h5 class="mb-4 text-success fw-bold">Order History</h5>

    <!-- Onglets filtre -->
    <ul class="nav nav-tabs mb-3">
      <li class="nav-item" v-for="tab in tabs" :key="tab.key">
        <button
            class="nav-link"
            :class="{ active: activeTab === tab.key }"
            @click="setTab(tab.key)"
        >
          {{ tab.label }}
        </button>
      </li>
    </ul>

    <!-- Filtres date -->
    <div class="d-flex gap-3 mb-3 flex-wrap">
      <div class="flex-fill">
        <input type="date" v-model="fromDate" class="form-control" />
      </div>
      <div class="flex-fill">
        <input type="date" v-model="toDate" class="form-control" />
      </div>
    </div>

    <!-- Tableau scrollable -->
    <div class="table-responsive order-table-scroll">
      <table class="table table-borderless mb-0">
        <thead class="table-light">
        <tr>
          <th>Order ID</th>
          <th>Address</th>
          <th>Date</th>
          <th>Delivery</th>
          <th>Total</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="order in filteredOrders" :key="order.date">
          <td>{{ order.id }}</td>
          <td>{{ order.adressOrder.substring(0,11) }}</td>
          <td>{{ order.date.split('T')[0] }}</td>
          <td>{{ order.delivery }}</td>
          <td>{{ order.total }}</td>
          <td>
              <span
                  :class="order.status === 'delivered' ? 'text-success fw-semibold' : 'text-danger fw-semibold'"
              >
                {{ order.status }}
              </span>
          </td>

          <td class="d-flex">
            <div :class="{ 'disabled-icon': order.status !== 'pending'  }"
                 @click="order.status !== 'delivered' && order.status !== 'cancelled'&& cancelOrder(order.id)"
            :title="cancel">

              <svg   xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0,0,256,256">
                <g fill="#12b886" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(10.66667,10.66667)"><path transform="translate(-4.97056,12.0003) rotate(-45.001)" d="M11,3.2h2v17.5h-2z"></path><path d="M12,22c-5.5,0 -10,-4.5 -10,-10c0,-5.5 4.5,-10 10,-10c5.5,0 10,4.5 10,10c0,5.5 -4.5,10 -10,10zM12,4c-4.4,0 -8,3.6 -8,8c0,4.4 3.6,8 8,8c4.4,0 8,-3.6 8,-8c0,-4.4 -3.6,-8 -8,-8z"></path></g></g>
              </svg>

            </div>

            <div :class="{ 'disabled-icon': order.status === 'cancelled' }"
                 @click="order.status !== 'cancelled' && verifOrder(order.id)"
            :title="verif">

              <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0,0,256,256"
                   style="fill:#12B886;">
                <g fill="#12b886" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(5.12,5.12)"><path d="M42.875,8.625c-0.03125,0.00781 -0.0625,0.01953 -0.09375,0.03125c-0.26172,0.06641 -0.48828,0.23438 -0.625,0.46875l-20.4375,31.6875l-14.0625,-12.6875c-0.24609,-0.3125 -0.65625,-0.44922 -1.04297,-0.34766c-0.38672,0.10156 -0.67187,0.42578 -0.73047,0.82031c-0.05859,0.39453 0.12109,0.78516 0.46094,0.99609l14.90625,13.5c0.21875,0.19141 0.51172,0.27734 0.80078,0.23438c0.28906,-0.04297 0.54297,-0.20703 0.69922,-0.45312l21.09375,-32.6875c0.23047,-0.32812 0.24219,-0.76172 0.03125,-1.10156c-0.21094,-0.33984 -0.60547,-0.51953 -1,-0.46094z"></path></g></g>
              </svg>

            </div>



          </td>

        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from "axios"


// Onglets
const tabs = [
  { key: 'all', label: 'All Orders' },
  { key: 'completed', label: 'Completed' },
  { key: 'pending', label: 'Pending' },
  { key: 'cancelled', label: 'Cancelled' },
]

const activeTab = ref('all')
const fromDate = ref('')
const toDate = ref('')
const loading = ref(true)
const orders = ref([])
const cancel=ref('')

const fetchOrders = async () => {
  try {
    loading.value = true
    const response = await axios.get('http://localhost:8000/admin/orders')
    orders.value = response.data.sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
  } catch (e) {
    alert("Erreur de chargement des commandes !")
  } finally {
    loading.value = false
  }
}

const setTab = (tab: string) => {
  activeTab.value = tab
}

const filteredOrders = computed(() => {
  return orders.value.filter(order => {
    if (activeTab.value === 'completed' && order.status !== 'delivered') return false
    if (activeTab.value === 'cancelled' && order.status !== 'cancelled') return false
    if (activeTab.value === 'pending' && order.status !== 'pending') return false

    if (fromDate.value && new Date(order.date) < new Date(fromDate.value)) return false
    if (toDate.value && new Date(order.date) > new Date(toDate.value)) return false

    return true
  })
})
const cancelOrder=async (id)=>{
  try {
    loading.value = true
    const response = await axios.put(`http://localhost:8000/admin/updateStatus/${id}`,
        {'status':'cancelled'})
    cancel.value = response.data
    fetchOrders()
  } catch (e) {
    alert("Erreur de blocage de la commande!")
  } finally {
    loading.value = false
  }
}
const verifOrder=async (id)=>{
  try {
    loading.value = true
    const response = await axios.put(`http://localhost:8000/admin/updateStatus/${id}`,
        {'status':'delivered'})
    cancel.value = response.data
    fetchOrders()
  } catch (e) {
    alert("Erreur de blocage de la commande!")
  } finally {
    loading.value = false
  }
}

onMounted(fetchOrders)
</script>

<style scoped>
.card {
  border: 1px solid #e48b08;
  max-width: 100%;
}

.nav-tabs .nav-link.active {
  color: #198754;
  border-color: #198754 #198754 #fff;
}

.table thead {
  background-color: #fff8e1;
}

.table th {
  color: #198754;
  font-weight: 600;
}

.order-table-scroll {
  max-height: 280px;
  overflow-y: auto;
}

.table td {
  vertical-align: middle;
}

.text-success {
  color: #198754 !important;
}

.text-danger {
  color: #e48b08 !important;
}
.disabled-icon {
  opacity: 0.4;     /* rend l'icône plus pâle */
  pointer-events: none; /* désactive le clic */
  cursor: not-allowed;  /* curseur interdit */
}
</style>
