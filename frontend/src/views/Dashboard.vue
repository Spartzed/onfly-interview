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
                <el-button type="text" class="user-menu-button">
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
                      <el-icon><Delete /></el-icon>
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
                      <el-icon><Delete /></el-icon>
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
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Status</label>
            <el-select 
              v-model="createFormData.status" 
              placeholder="Selecione o status"
              class="modern-select"
            >
              <el-option label="Solicitado" value="requested" />
              <el-option label="Aprovado" value="approved" />
              <el-option label="Cancelado" value="cancelled" />
            </el-select>
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
import { Check, Close, Plus, Refresh, SwitchButton, ArrowDown, User, Setting, Location, Document, Select, Delete } from '@element-plus/icons-vue'
import NotificationBell from '@/components/NotificationBell.vue'
import dayjs from 'dayjs'

export default {
  name: 'Dashboard',
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
      status: 'requested', // Default status
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
      status: [
        { required: true, message: 'Status é obrigatório', trigger: 'change' }
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
          status: 'requested', // Reset status
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
      handleLogout
    }
  }
}
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  position: relative;
}

.dashboard::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.02)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
  pointer-events: none;
}

.header {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  padding: 0 24px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  position: relative;
  z-index: 10;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 100%;
}

.header-left {
  display: flex;
  align-items: center;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.logo-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
  overflow: hidden;
  padding: 0;
}

.logo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
}

.header-left h1 {
  margin: 0;
  color: #2c3e50;
  font-size: 24px;
  font-weight: 700;
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 7px 20px;
  transition: all 0.3s ease;
}

.user-avatar {
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  color: white;
  font-weight: 700;
  font-size: 16px;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.user-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.user-name {
  font-weight: 700;
  color: #2c3e50;
  font-size: 14px;
}

.user-role {
  font-size: 12px;
  color: #6c757d;
  font-weight: 500;
}

.user-menu-button {
  padding: 8px;
  border-radius: 8px;
  transition: all 0.3s ease;
  color: #6c757d;
}

.user-menu-button:hover {
  background: rgba(102, 126, 234, 0.1);
  color: #667eea;
  transform: scale(1.1);
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px 24px;
  position: relative;
  z-index: 1;
}

.filters {
  margin-bottom: 32px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  position: relative;
  overflow: hidden;
}

.filters::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #667eea 0%, #4facfe 50%, #667eea 100%);
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 1px solid rgba(102, 126, 234, 0.1);
}

.filters-title {
  display: flex;
  align-items: center;
  gap: 16px;
}

.title-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  border-radius: 12px;
  color: white;
  font-size: 20px;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

.filters-header h3 {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  color: #2c3e50;
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.filters-actions {
  display: flex;
  gap: 16px;
}

.action-button {
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s ease;
  border: none;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 8px;
}

.action-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.action-button:hover::before {
  left: 100%;
}

.action-button .el-icon {
  font-size: 16px;
  flex-shrink: 0;
}

.action-button .button-text {
  font-weight: 600;
  white-space: nowrap;
}

.refresh-btn {
  background: linear-gradient(135deg, #409eff 0%, #36a3f7 100%);
  box-shadow: 0 8px 24px rgba(64, 158, 255, 0.3);
  color: white;
}

.refresh-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(64, 158, 255, 0.4);
}

.create-btn {
  background: linear-gradient(135deg, #67c23a 0%, #85ce61 100%);
  box-shadow: 0 8px 24px rgba(103, 194, 58, 0.3);
  color: white;
}

.create-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(103, 194, 58, 0.4);
}

.filters-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.filter-label {
  font-weight: 700;
  font-size: 14px;
  color: #2c3e50;
  margin-bottom: 4px;
  letter-spacing: 0.5px;
}

.filter-select,
.filter-input,
.filter-date-picker {
  width: 100%;
}

.filter-select .el-input__wrapper,
.filter-input .el-input__wrapper {
  border-radius: 12px;
  border: 2px solid rgba(102, 126, 234, 0.1);
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}

.filter-select .el-input__wrapper:hover,
.filter-input .el-input__wrapper:hover {
  border-color: #667eea;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
}

.filter-select .el-input__wrapper.is-focus,
.filter-input .el-input__wrapper.is-focus {
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.orders-table {
  margin-top: 32px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  position: relative;
}

.orders-table::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #667eea 0%, #4facfe 50%, #667eea 100%);
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 28px 0px;
  border-bottom: 1px solid rgba(102, 126, 234, 0.1);
  background: rgba(248, 249, 250, 0.8);
  backdrop-filter: blur(10px);
}

.table-title {
  display: flex;
  align-items: center;
  gap: 16px;
}

.table-header h3 {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  color: #2c3e50;
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.table-count {
  font-size: 14px;
  color: #6c757d;
  font-weight: 600;
  padding: 8px 16px;
  background: rgba(102, 126, 234, 0.1);
  border-radius: 20px;
  border: 1px solid rgba(102, 126, 234, 0.2);
}

.modern-table {
  border: none;
}

.modern-table .el-table__header {
  background: rgba(248, 249, 250, 0.8);
  backdrop-filter: blur(10px);
}

.modern-table .el-table__header th {
  background: rgba(248, 249, 250, 0.8);
  color: #2c3e50;
  font-weight: 700;
  border-bottom: 2px solid rgba(102, 126, 234, 0.1);
  padding: 20px 0;
  font-size: 14px;
  /* text-transform: uppercase; */
  letter-spacing: 0.5px;
}

.modern-table .el-table__body tr {
  transition: all 0.3s ease;
}

.modern-table .el-table__body tr:hover {
  background: rgba(102, 126, 234, 0.05);
  transform: scale(1.01);
}

.modern-table .el-table__body td {
  border-bottom: 1px solid rgba(102, 126, 234, 0.1);
  padding: 20px 0;
  font-weight: 500;
}

.status-tag {
  width: 80px;
  border-radius: 20px;
  font-weight: 600;
  padding: 6px 15px;
  font-size: 10px;
  /* text-transform: uppercase; */
  letter-spacing: 0.5px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: unset !important;
  text-align: center;
}

.action-buttons {
  display: flex;
  gap: 12px;
  justify-content: flex-start;
  align-items: center;
  padding: 4px 0;
}

.action-btn {
  margin: 0;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border: none;
  font-size: 14px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  position: relative;
  overflow: hidden;
  gap: 0;
}

.action-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.action-btn:hover::before {
  left: 100%;
}

.action-btn:hover {
  transform: scale(1.1) translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.action-btn .el-icon {
  font-size: 14px;
  z-index: 1;
  position: relative;
  flex-shrink: 0;
}

.approve-btn {
  background: linear-gradient(135deg, #67c23a 0%, #85ce61 100%);
  color: white;
}

.approve-btn:hover {
  background: linear-gradient(135deg, #5daf34 0%, #7bc23a 100%);
}

.reject-btn,
.cancel-btn {
  background: linear-gradient(135deg, #f56c6c 0%, #f78989 100%);
  color: white;
}

.reject-btn:hover,
.cancel-btn:hover {
  background: linear-gradient(135deg, #e64242 0%, #f56c6c 100%);
}

/* Modal Styles */
.modern-dialog .el-dialog {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.modern-dialog .el-dialog__header {
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  color: white;
  padding: 32px 0px 0px;
}

.modern-dialog .el-dialog__title {
  color: white;
  font-weight: 700;
  font-size: 20px;
}

.modern-dialog .el-dialog__body {
  padding: 32px 0px 0px;
}

.modern-form {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.form-row {
  display: flex;
  flex-direction: column;
  gap: 0px;
}

.dates-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  padding-bottom: 10px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0px;
}

.form-label {
  font-weight: 700;
  color: #2c3e50;
  font-size: 14px;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
  margin-left: 5px;
}

.modern-input .el-input__wrapper,
.modern-date-picker .el-input__wrapper,
.modern-select .el-input__wrapper {
  border-radius: 12px;
  border: 2px solid rgba(102, 126, 234, 0.1);
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}

.modern-input .el-input__wrapper:hover,
.modern-date-picker .el-input__wrapper:hover,
.modern-select .el-input__wrapper:hover {
  border-color: #667eea;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
}

.modern-input .el-input__wrapper.is-focus,
.modern-date-picker .el-input__wrapper.is-focus,
.modern-select .el-input__wrapper.is-focus {
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

/* Estilo específico para o select */
.modern-select .el-select .el-input__wrapper,
.modern-select .el-select__wrapper {
  border-radius: 12px;
  border: 2px solid rgba(102, 126, 234, 0.1);
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}

.modern-select .el-select .el-input__wrapper:hover,
.modern-select .el-select__wrapper:hover {
  border-color: #667eea;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
}

.modern-select .el-select .el-input__wrapper.is-focus,
.modern-select .el-select__wrapper.is-focus {
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.modern-date-picker {
  width: 100%;
}

.dialog-footer {
  display: flex;
  gap: 16px;
  justify-content: flex-end;
  padding-top: 24px;
  border-top: 1px solid rgba(102, 126, 234, 0.1);
}

.cancel-btn {
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.8);
  border: 2px solid rgba(102, 126, 234, 0.1);
  color: #6c757d;
}

.cancel-btn:hover {
  background: rgba(102, 126, 234, 0.1);
  border-color: #667eea;
  color: #667eea;
  transform: translateY(-2px);
}

.submit-btn {
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  border: none;
  transition: all 0.3s ease;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
  color: white;
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
}

/* Responsive Design */
@media (max-width: 768px) {
  .container {
    padding: 16px;
  }
  
  .filters {
    padding: 20px;
  }
  
  .filters-content {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .filters-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }
  
  .filters-actions {
    width: 100%;
    justify-content: space-between;
  }
  
  .table-header {
    flex-direction: column;
    gap: 12px;
    align-items: flex-start;
  }
  
  .header-left h1 {
    font-size: 18px;
  }
  
  .logo-section {
    gap: 12px;
  }
  
  .logo-icon {
    width: 40px;
    height: 40px;
  }
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.filters,
.orders-table {
  animation: fadeInUp 0.6s ease-out;
}

.filters {
  animation-delay: 0.1s;
}

.orders-table {
  animation-delay: 0.2s;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(102, 126, 234, 0.1);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #5a6fd8 0%, #3d9bf0 100%);
}

.modern-table .el-table__empty-block {
  background: rgba(248, 249, 250, 0.8);
  backdrop-filter: blur(10px);
}

.modern-table .el-table__empty-text {
  color: #6c757d;
  font-weight: 500;
  font-size: 14px;
  padding: 40px 0;
}
</style> 