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
      if (route.params.id) {
        isEditing.value = true
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
