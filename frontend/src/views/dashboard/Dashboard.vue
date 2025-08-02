<template>
  <div class="dashboard">
    <el-container>
      <el-header class="header">
        <div class="header-content">
          <div class="header-left">
            <div class="logo-section">
              <div class="logo-icon">
                <img src="/logo.png" alt="Onfly Logo" class="logo-img">
              </div>
              <h1>Onfly - Pedidos de Viagem</h1>
            </div>
          </div>
          <div class="header-right">
            <NotificationBell />
            <div class="user-info">
              <el-avatar :size="40" class="user-avatar">
                {{ authStore.user?.name?.charAt(0)?.toUpperCase() }}
              </el-avatar>
              <div class="user-details">
                <span class="user-name">{{ authStore.user?.name }}</span>
                <span class="user-role">{{ authStore.user?.role === 'admin' ? 'Administrador' : 'Usuário' }}</span>
              </div>
              <el-dropdown @command="handleUserAction">
                <el-button link class="user-menu-button">
                  <el-icon><ArrowDown /></el-icon>
                </el-button>
                <template #dropdown>
                  <el-dropdown-menu>
                    <el-dropdown-item command="profile">
                      <el-icon><User /></el-icon>
                      Perfil
                    </el-dropdown-item>
                    <el-dropdown-item command="settings">
                      <el-icon><Setting /></el-icon>
                      Configurações
                    </el-dropdown-item>
                    <el-dropdown-item divided command="logout">
                      <el-icon><SwitchButton /></el-icon>
                      Sair
                    </el-dropdown-item>
                  </el-dropdown-menu>
                </template>
              </el-dropdown>
            </div>
          </div>
        </div>
      </el-header>

      <el-main>
        <div class="container">
          <!-- Filtros -->
          <div class="filters card">
            <div class="filters-header">
              <div class="filters-title">
                <div class="title-icon">
                  <el-icon><Setting /></el-icon>
                </div>
                <h3>Filtros e Ações</h3>
              </div>
              <div class="filters-actions">
                <el-button 
                  type="primary" 
                  @click="refreshOrders"
                  class="action-button refresh-btn"
                  :loading="travelOrdersStore.loading"
                >
                  <el-icon><Refresh /></el-icon>
                  <span class="button-text">Atualizar</span>
                </el-button>
                <el-button 
                  type="success" 
                  @click="showCreateModal = true"
                  class="action-button create-btn"
                >
                  <el-icon><Plus /></el-icon>
                  <span class="button-text">Novo Pedido</span>
                </el-button>
                <el-button 
                  type="info" 
                  @click="goToStats"
                  class="action-button stats-btn"
                >
                  <el-icon><DataAnalysis /></el-icon>
                  <span class="button-text">Estatísticas</span>
                </el-button>
              </div>
            </div>
            
            <div class="filters-content">
              <div class="filter-group">
                <label class="filter-label">Status</label>
                <el-select 
                  v-model="filters.status" 
                  placeholder="Todos os status" 
                  clearable
                  @change="handleFilterChange"
                  class="filter-select"
                >
                  <el-option label="Solicitado" value="requested" />
                  <el-option label="Aprovado" value="approved" />
                  <el-option label="Cancelado" value="cancelled" />
                </el-select>
              </div>
              
              <div class="filter-group">
                <label class="filter-label">Destino</label>
                <el-input 
                  v-model="filters.destination" 
                  placeholder="Digite o destino" 
                  clearable
                  @input="handleFilterChange"
                  class="filter-input"
                >
                  <template #prefix>
                    <el-icon><Location /></el-icon>
                  </template>
                </el-input>
              </div>
              
              <div class="filter-group">
                <label class="filter-label">Período</label>
                <el-date-picker
                  v-model="filters.dateRange"
                  type="daterange"
                  range-separator="até"
                  start-placeholder="Data inicial"
                  end-placeholder="Data final"
                  class="filter-date-picker"
                  clearable
                  @change="handleFilterChange"
                  format="DD/MM/YYYY"
                  value-format="YYYY-MM-DD"
                  placement="bottom-start"
                />
              </div>
            </div>
          </div>

          <!-- Tabela de Pedidos -->
          <div class="orders-table card">
            <div class="table-header">
              <div class="table-title">
                <div class="title-icon">
                  <el-icon><Document /></el-icon>
                </div>
                <h3>Pedidos de Viagem</h3>
              </div>
              <span class="table-count">
                {{ travelOrdersStore.travelOrders.length }} pedido(s)
              </span>
            </div>
            <el-table
              :data="travelOrdersStore.travelOrders"
              v-loading="travelOrdersStore.loading"
              style="width: 100%"
              class="modern-table"
              empty-text="Sem resultados"
            >
              <el-table-column prop="order_id" label="ID" />
              <el-table-column prop="requester_name" label="Solicitante" />
              <el-table-column prop="destination" label="Destino" />
              <el-table-column label="Ida">
                <template #default="{ row }">
                  {{ formatDate(row.departure_date) }}
                </template>
              </el-table-column>
              <el-table-column label="Volta" width="120">
                <template #default="{ row }">
                  {{ formatDate(row.return_date) }}
                </template>
              </el-table-column>
              <el-table-column label="Status" width="120">
                <template #default="{ row }">
                  <el-tag :type="getStatusType(row.status)" class="status-tag">
                    {{ getStatusLabel(row.status).replace(/\.\./g, '').trim() }}
                  </el-tag>
                </template>
              </el-table-column>
              <el-table-column label="Ações" width="150">
                <template #default="{ row }">
                  <div class="action-buttons">
                    <!-- Botão Aprovar (apenas admin) -->
                    <el-button
                      v-if="authStore.isAdmin && row.status === 'requested'"
                      type="success"
                      size="small"
                      circle
                      @click="updateStatus(row.id, 'approved')"
                      title="Aprovar"
                      class="action-btn approve-btn"
                    >
                      <el-icon><Select /></el-icon>
                    </el-button>
                    
                    <!-- Botão Rejeitar (apenas admin) -->
                    <el-button
                      v-if="authStore.isAdmin && row.status === 'requested'"
                      type="danger"
                      size="small"
                      circle
                      @click="updateStatus(row.id, 'cancelled')"
                      title="Rejeitar"
                      class="action-btn reject-btn"
                    >
                      <el-icon><Close /></el-icon>
                    </el-button>
                    
                    <!-- Botão Cancelar (apenas usuário comum) -->
                    <el-button
                      v-if="!authStore.isAdmin && row.status === 'requested'"
                      type="danger"
                      size="small"
                      circle
                      @click="cancelOrder(row.id)"
                      title="Cancelar"
                      class="action-btn cancel-btn"
                    >
                      <el-icon><Close /></el-icon>
                    </el-button>
                  </div>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
      </el-main>
    </el-container>

    <!-- Modal de Criação de Pedido -->
    <el-dialog
      v-model="showCreateModal"
      title="Novo pedido de viagem"
      width="500px"
      :close-on-click-modal="false"
      class="modern-dialog"
    >
      <el-form
        ref="createForm"
        :model="createFormData"
        :rules="createRules"
        class="modern-form"
      >
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nome do Solicitante</label>
            <el-input 
              v-model="createFormData.requester_name" 
              placeholder="Digite o nome completo"
              class="modern-input"
            />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Destino</label>
            <el-input 
              v-model="createFormData.destination" 
              placeholder="Digite o destino da viagem"
              class="modern-input"
            />
          </div>
        </div>
        

        
        <div class="form-row dates-row">
          <div class="form-group">
            <label class="form-label">Data de Ida</label>
            <el-date-picker
              v-model="createFormData.departure_date"
              type="date"
              placeholder="Selecione a data de ida"
              class="modern-date-picker"
              :disabled-date="disablePastDates"
              format="DD/MM/YYYY"
              value-format="YYYY-MM-DD"
              placement="bottom-start"
            />
          </div>
          
          <div class="form-group">
            <label class="form-label">Data de Volta</label>
            <el-date-picker
              v-model="createFormData.return_date"
              type="date"
              placeholder="Selecione a data de volta"
              class="modern-date-picker"
              :disabled-date="disablePastDates"
              format="DD/MM/YYYY"
              value-format="YYYY-MM-DD"
              placement="bottom-start"
            />
          </div>
        </div>
      </el-form>

      <template #footer>
        <span class="dialog-footer">
          <el-button @click="showCreateModal = false" class="cancel-btn">Cancelar</el-button>
          <el-button
            type="primary"
            :loading="creating"
            @click="handleCreateOrder"
            class="submit-btn"
          >
            Criar Pedido
          </el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useTravelOrdersStore } from '@/stores/travelOrders'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Check, Close, Plus, Refresh, SwitchButton, ArrowDown, User, Setting, Location, Document, Select, Delete, DataAnalysis } from '@element-plus/icons-vue'
import NotificationBell from '@/components/NotificationBell.vue'
import dayjs from 'dayjs'

export default {
  name: 'Dashboard',
  components: {
    NotificationBell
  },
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    const travelOrdersStore = useTravelOrdersStore()

    const showCreateModal = ref(false)
    const creating = ref(false)
    const createForm = ref(null)

    const filters = reactive({
      status: '',
      destination: '',
      dateRange: null
    })

    const createFormData = reactive({
      requester_name: '',
      destination: '',
      departure_date: '',
      return_date: ''
    })

    const createRules = {
      requester_name: [
        { required: true, message: 'Nome do solicitante é obrigatório', trigger: 'blur' }
      ],
      destination: [
        { required: true, message: 'Destino é obrigatório', trigger: 'blur' }
      ],

      departure_date: [
        { required: true, message: 'Data de ida é obrigatória', trigger: 'change' }
      ],
      return_date: [
        { required: true, message: 'Data de volta é obrigatória', trigger: 'change' }
      ]
    }

    const formatDate = (date) => {
      return dayjs(date).format('DD/MM/YYYY')
    }

    const getStatusLabel = (status) => {
      const labels = {
        requested: 'Solicitado',
        approved: 'Aprovado',
        cancelled: 'Cancelado'
      }
      return (labels[status] || status).trim()
    }

    const getStatusType = (status) => {
      const types = {
        requested: 'warning',
        approved: 'success',
        cancelled: 'danger'
      }
      return types[status] || 'info'
    }

    const handleFilterChange = () => {
      travelOrdersStore.fetchTravelOrders(filters)
    }

    const refreshOrders = () => {
      travelOrdersStore.fetchTravelOrders(filters)
    }

    const updateStatus = async (id, status) => {
      try {
        const result = await travelOrdersStore.updateTravelOrderStatus(id, status)
        if (result.success) {
          ElMessage.success('Status atualizado com sucesso!')
        } else {
          ElMessage.error(result.message)
        }
      } catch (error) {
        ElMessage.error('Erro ao atualizar status')
      }
    }

    const cancelOrder = async (id) => {
      try {
        await ElMessageBox.confirm(
          'Tem certeza que deseja cancelar este pedido?',
          'Confirmar Cancelamento',
          {
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            type: 'warning'
          }
        )

        const result = await travelOrdersStore.cancelTravelOrder(id)
        if (result.success) {
          ElMessage.success('Pedido cancelado com sucesso!')
        } else {
          ElMessage.error(result.message)
        }
      } catch (error) {
        if (error !== 'cancel') {
          ElMessage.error('Erro ao cancelar pedido')
        }
      }
    }

    const handleCreateOrder = async () => {
      if (!createForm.value) return
      
      const valid = await createForm.value.validate()
      if (!valid) return

      creating.value = true
      const result = await travelOrdersStore.createTravelOrder(createFormData)
      creating.value = false

      if (result.success) {
        ElMessage.success('Pedido criado com sucesso!')
        showCreateModal.value = false
        Object.assign(createFormData, {
          requester_name: '',
          destination: '',
          departure_date: '',
          return_date: ''
        })
      } else {
        ElMessage.error(result.message)
      }
    }

    const handleUserAction = (command) => {
      switch (command) {
        case 'profile':
          ElMessage.info('Funcionalidade em desenvolvimento')
          break
        case 'settings':
          ElMessage.info('Funcionalidade em desenvolvimento')
          break
        case 'logout':
          handleLogout()
          break
      }
    }

    const disablePastDates = (time) => {
      return time.getTime() < Date.now() - 8.64e7
    }

    const handleLogout = () => {
      authStore.logout()
      router.push('/login')
    }

    const goToStats = () => {
      router.push('/stats')
    }

    onMounted(() => {
      travelOrdersStore.fetchTravelOrders()
    })

    return {
      authStore,
      travelOrdersStore,
      filters,
      createFormData,
      createForm,
      createRules,
      showCreateModal,
      creating,
      formatDate,
      getStatusLabel,
      getStatusType,
      handleFilterChange,
      refreshOrders,
      updateStatus,
      cancelOrder,
      handleCreateOrder,
      handleUserAction,
      handleLogout,
      goToStats
    }
  }
}
</script>

<style scoped>
@import './Dashboard.css';
</style> 