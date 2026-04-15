<template>
  <div class="container-fluid p-3 min-vh-100">
    <!-- Over View -->
    <div class="card shadow-sm p-3 mb-4">
      <h2 class="mb-3 text-success">Over View</h2>
      <div class="row g-3">
        <div v-for="(item, index) in overview" :key="index" class="col-6 col-md-3">
          <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-3">
              <p class="card-title fw-semibold text-secondary mb-1">{{ item.title }}</p>
              <p class="card-text h3 fw-bold text-dark">{{ item.value }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex flex-wrap gap-3 justify-content-between">
      <div class="d-flex flex-column gap-4 flex-grow-1" style="min-width: 280px; max-width: 65%;">
        <div class="d-flex gap-4 flex-wrap">
          <!-- No of Users -->
          <div class="flex-fill min-w-200">
            <div class="card p-3 shadow-sm text-center h-100">
              <h5 class="text-secondary">No of Users</h5>
              <h2 class="text-success display-6 fw-bold mb-2">{{ data.users }}</h2>
              <p class="text-muted">Total Customers</p>
            </div>
          </div>

          <div class="flex-fill min-w-200">
            <div class="card p-3 shadow-sm text-center h-100">
              <h5 class="text-secondary mb-3">Inventory Values</h5>
              <canvas id="inventoryChart" width="300" height="100"></canvas>
            </div>
          </div>
        </div>

        <!-- Expense vs Profit -->
        <div class="card p-3 shadow-sm">
          <div class="d-flex justify-content-between mb-3">
            <h5 class="text-secondary">Expense vs Profit</h5>
            <span class="text-muted">Last 6 months</span>
          </div>
          <canvas id="revenueChart" height="100"></canvas>
        </div>
      </div>

      <!-- Top Products Table -->
      <div class="card p-3 shadow-sm " style="width: 350px; min-width: 300px;">
        <h5 class="text-secondary mb-3">Top Products</h5>
        <table class="table table-borderless mb-0">
          <tbody>
          <tr v-for="(product, index) in topProducts" :key="index">
            <td class="fw-light"
                style="min-width: 110px;"
                :title="product.name"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
            >
              {{ product.name.length > 11 ? product.name.substring(0, 11) + '...' : product.name }}
            </td>
            <td style="width: 100%;">
              <div class="progress" style="height: 12px;">
                <div
                    class="progress-bar bg-success"
                    role="progressbar"
                    :style="{ width: product.popularity + '%' }"
                    :aria-valuenow="product.popularity"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
              </div>
            </td>
            <td class="text-end fw-bold" style="min-width: 40px;">{{ product.sales }}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { onMounted, ref } from 'vue'
import Chart from 'chart.js/auto'

interface OverviewItem {
  title: string
  value: number
}

interface Product {
  name: string
  sales: number
  popularity: number
}

const data = ref({
  products: 0,
  orders: 0,
  users: 0,
  totalStock: 0,
  stockOutOf: 0,
  soldUnit: 0,
})

const overview = ref<OverviewItem[]>([])

const topProducts = ref<Product[]>([])

const fetchInfos = async () => {
  try {
    const response = await axios.get('http://localhost:8000/admin/dashboard')
    data.value = response.data

    overview.value = [
      { title: 'Total Products', value: data.value.products },
      { title: 'Orders', value: data.value.orders },
      { title: 'Total Stock', value: data.value.totalStock },
      { title: 'Out of Stock', value: data.value.stockOutOf },
    ]
  } catch (e) {
    alert('Erreur de chargement des données')
  }
}

const fetchTopProducts = async () => {
  try {
    const response = await axios.get('http://localhost:8000/admin/top-products')
    topProducts.value = response.data
  } catch (e) {
    alert('Erreur de chargement des Top Products')
  }
}

onMounted(async () => {
  await fetchInfos()
  await fetchTopProducts()

  // Doughnut Inventory Chart
  const soldUnit = data.value.soldUnit
  const totalUnit = data.value.totalStock - data.value.soldUnit
  const ctxInventory = document.getElementById('inventoryChart') as HTMLCanvasElement
  if (ctxInventory) {
    new Chart(ctxInventory, {
      type: 'doughnut',
      data: {
        labels: ['Sold Units', 'Total Units'],
        datasets: [
          {
            data: [soldUnit, totalUnit],
            backgroundColor: ['#e48b08', '#198754'],
          },
        ],
      },
      options: {
        responsive: false,
        cutout: '60%',
        plugins: {
          legend: { display: true, position: 'right' },
        },
      },
    })
  }

  // Expense vs Profit Line Chart
  const ctxRevenue = document.getElementById('revenueChart') as HTMLCanvasElement
  if (ctxRevenue) {
    new Chart(ctxRevenue, {
      type: 'line',
      data: {
        labels: ['Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
          {
            label: 'Expense',
            data: [20000, 22000, 21000, 27000, 25000, 30000, 28000],
            borderColor: '#e48b08',
            backgroundColor: 'transparent',
            tension: 0.4,
          },
          {
            label: 'Profit',
            data: [10000, 18000, 18000, 24000, 29000, 34000, 38000],
            borderColor: '#198754',
            backgroundColor: 'transparent',
            tension: 0.4,
          },
        ],
      },
      options: {
        plugins: { legend: { display: true, position: 'bottom' } },
        responsive: true,
        scales: {
          y: { beginAtZero: true },
        },
      },
    })
  }
})
</script>

<style scoped>
.card {
  border: 1px solid #e48b08;
  max-width: 100%;
}

.card-body {
  background: #fff8e1;
  border-radius: 0.5rem;
  padding-top: 0.8rem !important;
  padding-bottom: 0.8rem !important;
}

h2 {
  font-weight: 700;
  font-size: 1.75rem;
  margin-bottom: 1rem;
}

.flex-wrap {
  flex-wrap: wrap;
}

.flex-fill {
  flex: 1 1 auto;
}

.min-w-200 {
  min-width: 200px;
}

.progress {
  background-color: #f0f0f0;
  border-radius: 0.3rem;
  overflow: hidden;
  height: 12px;
}

.progress-bar {
  transition: width 0.5s ease;
}
</style>
