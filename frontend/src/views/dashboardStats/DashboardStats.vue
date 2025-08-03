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

<script>
import { useRouter } from 'vue-router'
import { useTravelOrdersStore } from '@/stores/travelOrders'
import { Refresh, ArrowLeft, DataAnalysis } from '@element-plus/icons-vue'
import DashboardStats from '@/components/DashboardStats.vue'

export default {
    name: 'DashboardStatsPage',

    components: {
        DashboardStats
    },

    setup() {
        const router = useRouter()
        const travelOrdersStore = useTravelOrdersStore()
        return { router, travelOrdersStore }
    },

    data() {
        return {
            loading: false,
            orders: []
        }
    },

    mounted() {
        this.refreshStats()
    },

    methods: {
        async refreshStats() {
            this.loading = true
            try {
                await this.travelOrdersStore.fetchTravelOrders()
                this.orders = this.travelOrdersStore.travelOrders
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error)
            } finally {
                this.loading = false
            }
        },

        goBack() {
            this.router.push('/dashboard')
        }
    }
}
</script>

<style scoped>
@import './DashboardStats.css';
</style> 