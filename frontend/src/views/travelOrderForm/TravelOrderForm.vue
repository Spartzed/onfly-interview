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
                <el-input
                  v-model="form.destination"
                  placeholder="Digite o destino da viagem"
                  style="width: 100%"
                />
              </el-form-item>

              <el-form-item label="Status" prop="status">
                <el-select 
                  v-model="form.status" 
                  placeholder="Selecione o status"
                  style="width: 100%"
                >
                  <el-option label="Solicitado" value="requested" />
                  <el-option label="Aprovado" value="approved" />
                  <el-option label="Cancelado" value="cancelled" />
                </el-select>
              </el-form-item>

              <el-form-item label="Data de Ida" prop="departure_date">
                <el-date-picker
                  v-model="form.departure_date"
                  type="date"
                  placeholder="Selecione a data de ida"
                  style="width: 100%"
                  :disabled-date="disabledDate"
                  format="DD/MM/YYYY"
                  value-format="YYYY-MM-DD"
                  placement="bottom-start"
                />
              </el-form-item>

              <el-form-item label="Data de Volta" prop="return_date">
                <el-date-picker
                  v-model="form.return_date"
                  type="date"
                  placeholder="Selecione a data de volta"
                  style="width: 100%"
                  :disabled-date="disabledDate"
                  format="DD/MM/YYYY"
                  value-format="YYYY-MM-DD"
                  placement="bottom-start"
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
      status: 'requested',
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
      status: [
        { required: true, message: 'Status é obrigatório', trigger: 'change' }
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
@import './TravelOrderForm.css';
</style> 