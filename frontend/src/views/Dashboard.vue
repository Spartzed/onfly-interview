<template>
  <div class="dashboard">
    <el-container>
      <el-header class="header">
        <div class="header-content">
          <h1>Sistema de Pedidos de Viagem</h1>
          <div class="user-info">
            <span>Olá, {{ authStore.user?.name }}</span>
            <el-button type="text" @click="handleLogout">
              <el-icon><SwitchButton /></el-icon>
              Sair
            </el-button>
          </div>
        </div>
      </el-header>

      <el-main>
        <div class="container">
          <!-- Filtros -->
          <div class="filters card">
            <el-row :gutter="20">
              <el-col :span="6">
                <el-select
                  v-model="filters.status"
                  placeholder="Filtrar por status"
                  clearable
                  @change="handleFilterChange"
                >
                  <el-option label="Solicitado" value="requested" />
                  <el-option label="Aprovado" value="approved" />
                  <el-option label="Cancelado" value="cancelled" />
                </el-select>
              </el-col>
              <el-col :span="6">
                <el-input
                  v-model="filters.destination"
                  placeholder="Filtrar por destino"
                  clearable
                  @input="handleFilterChange"
                />
              </el-col>
              <el-col :span="6">
                <el-button type="primary" @click="refreshOrders">
                  <el-icon><Refresh /></el-icon>
                  Atualizar
                </el-button>
              </el-col>
              <el-col :span="6">
                <el-button type="success" @click="showCreateModal = true">
                  <el-icon><Plus /></el-icon>
                  Novo Pedido
                </el-button>
              </el-col>
            </el-row>
          </div>

          <!-- Tabela de Pedidos -->
          <div class="orders-table card">
            <el-table
              :data="travelOrdersStore.travelOrders"
              v-loading="travelOrdersStore.loading"
              style="width: 100%"
            >
              <el-table-column prop="order_id" label="ID do Pedido" width="150" />
              <el-table-column prop="requester_name" label="Solicitante" width="150" />
              <el-table-column prop="destination" label="Destino" />
              <el-table-column label="Data de Ida" width="120">
                <template #default="{ row }">
                  {{ formatDate(row.departure_date) }}
                </template>
              </el-table-column>
              <el-table-column label="Data de Volta" width="120">
                <template #default="{ row }">
                  {{ formatDate(row.return_date) }}
                </template>
              </el-table-column>
              <el-table-column label="Status" width="120">
                <template #default="{ row }">
                  <el-tag :type="getStatusType(row.status)">
                    {{ getStatusLabel(row.status) }}
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
                    >
                      <el-icon><Check /></el-icon>
                    </el-button>
                    
                    <!-- Botão Rejeitar (apenas admin) -->
                    <el-button
                      v-if="authStore.isAdmin && row.status === 'requested'"
                      type="danger"
                      size="small"
                      circle
                      @click="updateStatus(row.id, 'cancelled')"
                      title="Rejeitar"
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
      title="Novo Pedido de Viagem"
      width="500px"
    >
      <el-form
        ref="createForm"
        :model="createFormData"
        :rules="createRules"
        label-width="120px"
      >
        <el-form-item label="Nome do Solicitante" prop="requester_name">
          <el-input v-model="createFormData.requester_name" />
        </el-form-item>
        <el-form-item label="Destino" prop="destination">
          <el-input v-model="createFormData.destination" />
        </el-form-item>
        <el-form-item label="Data de Ida" prop="departure_date">
          <el-date-picker
            v-model="createFormData.departure_date"
            type="date"
            placeholder="Selecione a data"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="Data de Volta" prop="return_date">
          <el-date-picker
            v-model="createFormData.return_date"
            type="date"
            placeholder="Selecione a data"
            style="width: 100%"
          />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="showCreateModal = false">Cancelar</el-button>
        <el-button
          type="primary"
          :loading="creating"
          @click="handleCreateOrder"
        >
          Criar Pedido
        </el-button>
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
import { Check, Close, Plus, Refresh, SwitchButton } from '@element-plus/icons-vue'
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
      destination: ''
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
      return labels[status] || status
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
      handleLogout
    }
  }
}
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
}

.header {
  background: white;
  border-bottom: 1px solid #e4e7ed;
  padding: 0 20px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 100%;
}

.header-content h1 {
  margin: 0;
  color: #2c3e50;
  font-size: 20px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-info span {
  color: #606266;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.filters {
  margin-bottom: 20px;
}

.orders-table {
  margin-top: 20px;
}

.action-buttons {
  display: flex;
  gap: 8px;
  justify-content: flex-start;
}

.action-buttons .el-button {
  margin: 0;
}
</style> 