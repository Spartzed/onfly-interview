<template>
  <div class="dashboard-stats-page">
    <!-- Header -->
    <div class="stats-header">
      <div class="header-content">
        <div class="logo-section">
          <div class="logo-icon">
            <img src="/logo.png" class="logo-img" alt="Onfly Logo">
          </div>
          <div class="title-icon">
            <h1>Dashboard - Estatísticas</h1>
            <p class="subtitle">Análise detalhada dos pedidos de viagem</p>
          </div>
        </div>
        
        <div class="header-actions">
          <el-button 
            type="primary" 
            @click="refreshStats"
            :loading="loading"
            class="modern-button"
          >
            <el-icon><Refresh /></el-icon>
            <span>Atualizar</span>
          </el-button>
          
          <el-button 
            @click="goBack"
            class="modern-button secondary"
          >
            <el-icon><ArrowLeft /></el-icon>
            <span>Voltar</span>
          </el-button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <el-skeleton :rows="10" animated />
    </div>

    <!-- Stats Content -->
    <div v-else class="stats-content">
      <DashboardStats :orders="orders" />
    </div>

    <!-- Empty State -->
    <div v-if="!loading && orders.length === 0" class="empty-state">
      <div class="empty-icon">
        <el-icon><DataAnalysis /></el-icon>
      </div>
      <h3>Nenhum dado disponível</h3>
      <p>Não há pedidos para exibir estatísticas.</p>
      <el-button type="primary" @click="goBack" class="modern-button">
        <el-icon><ArrowLeft /></el-icon>
        <span>Voltar ao Dashboard</span>
      </el-button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useTravelOrdersStore } from '../stores/travelOrders'
import { Refresh, ArrowLeft, DataAnalysis } from '@element-plus/icons-vue'
import DashboardStats from '../components/DashboardStats.vue'

const router = useRouter()
const travelOrdersStore = useTravelOrdersStore()

const loading = ref(false)
const orders = ref([])

const refreshStats = async () => {
  loading.value = true
  try {
    await travelOrdersStore.fetchTravelOrders()
    orders.value = travelOrdersStore.travelOrders
  } catch (error) {
    console.error('Erro ao carregar estatísticas:', error)
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.push('/dashboard')
}

onMounted(async () => {
  await refreshStats()
})
</script>

<style scoped>
.dashboard-stats-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  padding: 24px;
}

.stats-header {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.logo-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
}

.logo-img {
  width: 40px;
  height: 40px;
  object-fit: contain;
}

.title-icon h1 {
  font-size: 28px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 4px 0;
}

.subtitle {
  font-size: 14px;
  color: #7f8c8d;
  margin: 0;
  font-weight: 500;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.modern-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.3s ease;
  border: none;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  color: white;
  box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
}

.modern-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
}

.modern-button.secondary {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
  color: #667eea;
  border: 2px solid rgba(102, 126, 234, 0.2);
}

.modern-button.secondary:hover {
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  color: white;
  border-color: transparent;
}

.stats-content {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.loading-container {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 24px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.empty-state {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 48px 24px;
  text-align: center;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 24px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 32px;
  box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
}

.empty-state h3 {
  font-size: 24px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 8px 0;
}

.empty-state p {
  font-size: 16px;
  color: #7f8c8d;
  margin: 0 0 24px 0;
}

@media (max-width: 768px) {
  .dashboard-stats-page {
    padding: 16px;
  }
  
  .header-content {
    flex-direction: column;
    gap: 16px;
  }
  
  .title-icon h1 {
    font-size: 24px;
  }
  
  .header-actions {
    width: 100%;
    justify-content: center;
  }
}
</style> 