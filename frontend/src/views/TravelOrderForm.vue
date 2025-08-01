<template>
  <div class="travel-order-form">
    <el-container>
      <el-header class="header">
        <div class="header-content">
          <h1>Formulário de Pedido de Viagem</h1>
          <el-button @click="$router.push('/dashboard')">
            <el-icon><ArrowLeft /></el-icon>
            Voltar ao Dashboard
          </el-button>
        </div>
      </el-header>

      <el-main>
        <div class="container">
          <div class="form-card card">
            <el-form
              ref="formRef"
              :model="form"
              :rules="rules"
              label-width="140px"
              @submit.prevent="handleSubmit"
            >
              <el-form-item label="Nome do Solicitante" prop="requester_name">
                <el-input v-model="form.requester_name" placeholder="Digite o nome completo" />
              </el-form-item>

              <el-form-item label="Destino" prop="destination">
                <el-input v-model="form.destination" placeholder="Ex: São Paulo, SP" />
              </el-form-item>

              <el-form-item label="Data de Ida" prop="departure_date">
                <el-date-picker
                  v-model="form.departure_date"
                  type="date"
                  placeholder="Selecione a data de ida"
                  style="width: 100%"
                  :disabled-date="disabledDate"
                />
              </el-form-item>

              <el-form-item label="Data de Volta" prop="return_date">
                <el-date-picker
                  v-model="form.return_date"
                  type="date"
                  placeholder="Selecione a data de volta"
                  style="width: 100%"
                  :disabled-date="disabledDate"
                />
              </el-form-item>

              <el-form-item>
                <el-button
                  type="primary"
                  :loading="loading"
                  @click="handleSubmit"
                >
                  {{ isEditing ? 'Atualizar Pedido' : 'Criar Pedido' }}
                </el-button>
                <el-button @click="$router.push('/dashboard')">
                  Cancelar
                </el-button>
              </el-form-item>
            </el-form>
          </div>
        </div>
      </el-main>
    </el-container>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTravelOrdersStore } from '@/stores/travelOrders'
import { ElMessage } from 'element-plus'
import dayjs from 'dayjs'

export default {
  name: 'TravelOrderForm',
  setup() {
    const route = useRoute()
    const router = useRouter()
    const travelOrdersStore = useTravelOrdersStore()
    
    const formRef = ref(null)
    const loading = ref(false)
    const isEditing = ref(false)

    const form = reactive({
      requester_name: '',
      destination: '',
      departure_date: '',
      return_date: ''
    })

    const rules = {
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
        { required: true, message: 'Data de volta é obrigatória', trigger: 'change' },
        {
          validator: (rule, value, callback) => {
            if (value && form.departure_date && dayjs(value).isBefore(dayjs(form.departure_date))) {
              callback(new Error('Data de volta deve ser posterior à data de ida'))
            } else {
              callback()
            }
          },
          trigger: 'change'
        }
      ]
    }

    const disabledDate = (time) => {
      return dayjs(time).isBefore(dayjs(), 'day')
    }

    const handleSubmit = async () => {
      if (!formRef.value) return
      
      const valid = await formRef.value.validate()
      if (!valid) return

      loading.value = true
      
      try {
        const result = await travelOrdersStore.createTravelOrder(form)
        
        if (result.success) {
          ElMessage.success('Pedido criado com sucesso!')
          router.push('/dashboard')
        } else {
          ElMessage.error(result.message || 'Erro ao criar pedido')
        }
      } catch (error) {
        ElMessage.error('Erro ao criar pedido')
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      // Se estiver editando, carregar dados do pedido
      if (route.params.id) {
        isEditing.value = true
        // Aqui você poderia carregar os dados do pedido para edição
      }
    })

    return {
      formRef,
      form,
      rules,
      loading,
      isEditing,
      disabledDate,
      handleSubmit
    }
  }
}
</script>

<style scoped>
.travel-order-form {
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

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.form-card {
  margin-top: 20px;
}
</style> 