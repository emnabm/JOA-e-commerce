<template>
  <div class="container-fluid p-3 min-vh-100">

    <!-- Over View -->
    <div class="card shadow-sm p-3 mb-4">
      <h2 class="mb-3" style="color: #C8854A;">Over View</h2>
      <div class="row g-3">
        <div v-for="(item, index) in overview" :key="index" class="col-6 col-md-3">
          <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-3">
              <p class="card-title fw-semibold text-secondary mb-1">{{ item.title }}</p>
              <p class="card-text h3 fw-bold" style="color: #2C2117;">{{ item.value }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex flex-wrap gap-3 justify-content-between">

      <div class="d-flex flex-column gap-4 flex-grow-1" style="min-width: 280px; max-width: 65%;">

        <div class="d-flex gap-4 flex-wrap">

          <!-- USERS -->
          <div class="flex-fill min-w-200">
            <div class="card p-3 shadow-sm text-center h-100">
              <h5 class="text-secondary">No of Users</h5>
              <h2 class="display-6 fw-bold mb-2" style="color: #C8854A;">{{ data.users }}</h2>
              <p class="text-muted">Total Customers</p>
            </div>
          </div>

          <!-- DONUT STATIC -->
          <div class="flex-fill min-w-200">
            <div class="card p-3 shadow-sm text-center h-100">
              <h5 class="text-secondary mb-3">Inventory Values</h5>
              <canvas id="inventoryChart"></canvas>
            </div>
          </div>

        </div>

        <!-- LOW STOCK ALERTS -->
        <div class="card p-3 shadow-sm">
          <div class="d-flex justify-content-between mb-3">
            <h5 class="text-secondary"> Low Stock Alerts</h5>
            <span v-if="lowStock.length > 0" class="badge" style="background: #f44336;">{{ lowStock.length }}</span>
          </div>

          <div v-if="lowStock.length === 0" class="text-success">
             All products are well stocked
          </div>

          <div v-for="p in lowStock" :key="p.id" class="d-flex justify-content-between align-items-center mb-2 p-2" style="border-radius: 8px; background: #F8F2E6;">
            <span style="font-size: 0.85rem; color: #2C2117;">{{ p.nom.length > 25 ? p.nom.substring(0, 25) + '...' : p.nom }}</span>
            <span class="badge" :class="p.stock === 0 ? 'bg-danger' : 'bg-warning text-dark'">
              {{ p.stock === 0 ? 'Out of stock' : p.stock + ' left' }}
            </span>
          </div>
        </div>

      </div>

      <!-- TOP PRODUCTS -->
      <div class="card p-3 shadow-sm" style="width: 350px; min-width: 300px;">
        <h5 class="text-secondary mb-3"> Top Products</h5>

        <table class="table table-borderless mb-0">
          <tbody>
            <tr v-for="(product, index) in topProducts" :key="index">

              <td style="min-width: 110px; color: #2C2117;">
                {{ product.name.length > 11 ? product.name.substring(0, 11) + '...' : product.name }}
              </td>

              <td style="width: 100%;">
                <div class="progress">
                  <div
                    class="progress-bar"
                    :style="{ width: product.popularity + '%', background: '#C8854A' }">
                  </div>
                </div>
              </td>

              <td class="text-end fw-bold" style="min-width: 40px; color: #2C2117;">
                {{ product.sales }}
              </td>

            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref, nextTick } from 'vue'
import Chart from 'chart.js/auto'

const data = ref({
  products: 0,
  orders: 0,
  users: 0,
  totalStock: 0,
  stockOutOf: 0,
  soldUnit: 0,
})

const overview = ref([])
const topProducts = ref([])
const lowStock = ref([])

// STATIC DATA pour le donut (pas besoin de données en base)
const staticChartData = {
  sold: 12450,      // Unités vendues (statique)
  remaining: 8750   // Unités restantes (statique)
}

const fetchInfos = async () => {
  try {
    const res = await axios.get('http://localhost:8000/admin/dashboard')
    data.value = res.data

    overview.value = [
      { title: 'Total Products', value: data.value.products },
      { title: 'Orders', value: data.value.orders },
      { title: 'Total Stock', value: data.value.totalStock },
      { title: 'Out of Stock', value: data.value.stockOutOf },
    ]
  } catch (error) {
    console.error('Dashboard error:', error)
    // Fallback data en cas d'erreur
    overview.value = [
      { title: 'Total Products', value: 0 },
      { title: 'Orders', value: 0 },
      { title: 'Total Stock', value: 0 },
      { title: 'Out of Stock', value: 0 },
    ]
  }
}

const fetchTopProducts = async () => {
  try {
    const res = await axios.get('http://localhost:8000/admin/top-products')
    topProducts.value = res.data
  } catch (error) {
    console.error('Top products error:', error)
    // Fallback data
    topProducts.value = []
  }
}

const fetchLowStock = async () => {
  try {
    const res = await axios.get('http://localhost:8000/admin/products/low-stock')
    lowStock.value = res.data.slice(0, 5)
  } catch (error) {
    console.error('Low stock error:', error)
    lowStock.value = []
  }
}

// CHART AVEC DONNÉES STATIQUES (pas besoin des données de la base)
const initChart = () => {
  const ctx = document.getElementById('inventoryChart')
  if (!ctx) return

  // Détruire le chart existant s'il y en a un
  const existingChart = Chart.getChart(ctx)
  if (existingChart) {
    existingChart.destroy()
  }

  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Sold', 'Remaining'],
      datasets: [{
        data: [staticChartData.sold, staticChartData.remaining],
        backgroundColor: ['#C8854A', '#2C2117'],
        borderWidth: 0,
        hoverOffset: 5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '65%',
      plugins: {
        legend: { 
            display: true,
            position: 'right',
                labels: {
                usePointStyle: true,
                boxWidth: 10,
                font: { size: 11 }
            }
        },
        tooltip: {
          backgroundColor: '#2C2117',
          titleColor: '#F8F2E6',
          bodyColor: 'rgba(248,242,230,0.7)',
          callbacks: {
            label: function(context) {
              const label = context.label || ''
              const value = context.raw || 0
              const total = staticChartData.sold + staticChartData.remaining
              const percentage = ((value / total) * 100).toFixed(1)
              return `${label}: ${value.toLocaleString()} units (${percentage}%)`
            }
          }
        }
      }
    }
  })
}

onMounted(async () => {
  await Promise.all([
    fetchInfos(),
    fetchTopProducts(),
    fetchLowStock()
  ])
  await nextTick()
  initChart()
})
</script>

<style scoped>
.container-fluid {
  background: #F8F2E6;
  min-height: 100vh;
}

.card {
  border: 1px solid #C8854A;
  border-radius: 16px;
  background: #fff;
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
}

.card-body {
  background: #fff8e1;
  border-radius: 12px;
}

h2 {
  font-family: 'Playfair Display', serif;
  font-weight: 600;
}

.progress {
  height: 8px;
  background-color: #F8F2E6;
  border-radius: 4px;
  overflow: hidden;
}

.progress-bar {
  transition: width 0.5s ease;
  border-radius: 4px;
}

#inventoryChart {
  width: 100% !important;
  height: 140px !important;
}

.text-secondary {
  color: #7a6555 !important;
}

.text-muted {
  color: #7a6555 !important;
}

.min-w-200 {
  min-width: 200px;
}

/* Low stock list scroll */
.d-flex.flex-column.gap-4 .card .d-flex.flex-column {
  max-height: 200px;
  overflow-y: auto;
}

.d-flex.flex-column.gap-4 .card .d-flex.flex-column::-webkit-scrollbar {
  width: 4px;
}

.d-flex.flex-column.gap-4 .card .d-flex.flex-column::-webkit-scrollbar-track {
  background: #F8F2E6;
  border-radius: 2px;
}

.d-flex.flex-column.gap-4 .card .d-flex.flex-column::-webkit-scrollbar-thumb {
  background: #C8854A;
  border-radius: 2px;
}

@media (max-width: 992px) {
  .d-flex.flex-wrap.gap-3.justify-content-between {
    flex-direction: column;
  }
  
  .d-flex.flex-column.gap-4.flex-grow-1 {
    max-width: 100% !important;
  }
  
  .card[style*="width: 350px"] {
    width: 100% !important;
  }
}

@media (max-width: 768px) {
  .container-fluid {
    padding: 1rem !important;
  }
  
  .d-flex.gap-4.flex-wrap {
    flex-direction: column;
  }
  
  .min-w-200 {
    min-width: auto;
  }
}
</style>