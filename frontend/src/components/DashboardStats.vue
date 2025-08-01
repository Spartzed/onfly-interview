<template>
  <div class="dashboard-stats">
    <!-- Cards de Métricas -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <el-icon><Document /></el-icon>
        </div>
        <div class="stat-content">
          <h3 class="stat-number">{{ totalOrders }}</h3>
          <p class="stat-label">Total de Pedidos</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon pending">
          <el-icon><Clock /></el-icon>
        </div>
        <div class="stat-content">
          <h3 class="stat-number">{{ statusCounts.requested || 0 }}</h3>
          <p class="stat-label">Solicitados</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon approved">
          <el-icon><Check /></el-icon>
        </div>
        <div class="stat-content">
          <h3 class="stat-number">{{ statusCounts.approved || 0 }}</h3>
          <p class="stat-label">Aprovados</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon cancelled">
          <el-icon><Close /></el-icon>
        </div>
        <div class="stat-content">
          <h3 class="stat-number">{{ statusCounts.cancelled || 0 }}</h3>
          <p class="stat-label">Cancelados</p>
        </div>
      </div>
    </div>

    <!-- Gráficos e Tabelas -->
    <div class="charts-section">
      <!-- Gráfico de Status -->
      <div class="chart-card">
        <h3 class="chart-title">Distribuição por Status</h3>
        <div class="chart-container">
          <div class="status-bars">
            <div 
              v-for="(count, status) in statusCounts" 
              :key="status"
              class="status-bar"
            >
              <div class="status-info">
                <span class="status-label">{{ getStatusLabel(status) }}</span>
                <span class="status-count">{{ count }}</span>
              </div>
              <div class="progress-bar">
                <div 
                  class="progress-fill"
                  :class="`status-${status}`"
                  :style="{ width: getPercentage(count) + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Usuários -->
      <div class="chart-card">
        <h3 class="chart-title">Top Solicitantes</h3>
        <div class="users-list">
          <div 
            v-for="(user, index) in topUsers" 
            :key="user.name"
            class="user-item"
          >
            <div class="user-rank">{{ index + 1 }}</div>
            <div class="user-info">
              <div class="user-name">{{ user.name }}</div>
              <div class="user-count">{{ user.count }} pedidos</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Meses Mais Requisitados -->
      <div class="chart-card">
        <h3 class="chart-title">Meses Mais Requisitados</h3>
        <div class="months-list">
          <div 
            v-for="(month, index) in topMonths" 
            :key="month.month"
            class="month-item"
          >
            <div class="month-rank">{{ index + 1 }}</div>
            <div class="month-info">
              <div class="month-value">{{ getMonthName(month.month) }}</div>
              <div class="month-count">{{ month.count }} pedidos</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { Document, Clock, Check, Close } from '@element-plus/icons-vue'
import dayjs from 'dayjs'

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
})

// Computed properties para estatísticas
const totalOrders = computed(() => props.orders.length)

const statusCounts = computed(() => {
  const counts = {}
  props.orders.forEach(order => {
    counts[order.status] = (counts[order.status] || 0) + 1
  })
  return counts
})

const topUsers = computed(() => {
  const userCounts = {}
  props.orders.forEach(order => {
    userCounts[order.requester_name] = (userCounts[order.requester_name] || 0) + 1
  })
  
  return Object.entries(userCounts)
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count)
    .slice(0, 5)
})

const topMonths = computed(() => {
  const monthCounts = {}
  props.orders.forEach(order => {
    const month = dayjs(order.departure_date).format('YYYY-MM')
    monthCounts[month] = (monthCounts[month] || 0) + 1
  })
  
  return Object.entries(monthCounts)
    .map(([month, count]) => ({ month, count }))
    .sort((a, b) => b.count - a.count)
    .slice(0, 5)
})

// Funções auxiliares
const getStatusLabel = (status) => {
  const labels = {
    requested: 'Solicitado',
    approved: 'Aprovado',
    cancelled: 'Cancelado'
  }
  return labels[status] || status
}

const getPercentage = (count) => {
  if (totalOrders.value === 0) return 0
  return Math.round((count / totalOrders.value) * 100)
}

const getMonthName = (monthStr) => {
  const [year, month] = monthStr.split('-')
  const monthNames = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
  ]
  return `${monthNames[parseInt(month) - 1]} ${year}`
}
</script>

<style scoped>
.dashboard-stats {
  padding: 24px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid rgba(102, 126, 234, 0.1);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(102, 126, 234, 0.15);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  color: white;
  font-size: 24px;
}

.stat-icon.pending {
  background: linear-gradient(135deg, #ffa726 0%, #ff7043 100%);
}

.stat-icon.approved {
  background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%);
}

.stat-icon.cancelled {
  background: linear-gradient(135deg, #ef5350 0%, #e53935 100%);
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 32px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 4px 0;
}

.stat-label {
  font-size: 14px;
  color: #7f8c8d;
  margin: 0;
  font-weight: 500;
}

.charts-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 24px;
}

.chart-card {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid rgba(102, 126, 234, 0.1);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.chart-title {
  font-size: 18px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 20px 0;
}

.status-bars {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.status-bar {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.status-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.status-label {
  font-weight: 600;
  color: #2c3e50;
  font-size: 14px;
}

.status-count {
  font-weight: 700;
  color: #667eea;
  font-size: 14px;
}

.progress-bar {
  height: 8px;
  background: rgba(102, 126, 234, 0.1);
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.6s ease;
}

.progress-fill.status-requested {
  background: linear-gradient(90deg, #ffa726 0%, #ff7043 100%);
}

.progress-fill.status-approved {
  background: linear-gradient(90deg, #66bb6a 0%, #43a047 100%);
}

.progress-fill.status-cancelled {
  background: linear-gradient(90deg, #ef5350 0%, #e53935 100%);
}

.users-list,
.months-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.user-item,
.month-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px;
  border-radius: 8px;
  background: rgba(102, 126, 234, 0.05);
  transition: all 0.3s ease;
}

.user-item:hover,
.month-item:hover {
  background: rgba(102, 126, 234, 0.1);
  transform: translateX(4px);
}

.user-rank,
.month-rank {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
}

.user-info,
.month-info {
  flex: 1;
}

.user-name,
.month-value {
  font-weight: 600;
  color: #2c3e50;
  font-size: 14px;
}

.user-count,
.month-count {
  font-size: 12px;
  color: #7f8c8d;
  margin-top: 2px;
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .charts-section {
    grid-template-columns: 1fr;
  }
  
  .dashboard-stats {
    padding: 16px;
  }
}
</style> 