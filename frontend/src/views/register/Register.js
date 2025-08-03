import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ElMessage } from 'element-plus'
import { User, Message, Lock, Plus } from '@element-plus/icons-vue'

export default {
  name: 'Register',
  components: {
    User,
    Message,
    Lock,
    Plus
  },
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    
    const registerForm = ref(null)
    const loading = ref(false)

    const form = reactive({
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    })

    const rules = {
      name: [
        { required: true, message: 'Nome é obrigatório', trigger: 'blur' },
        { min: 2, message: 'Nome deve ter pelo menos 2 caracteres', trigger: 'blur' }
      ],
      email: [
        { required: true, message: 'Email é obrigatório', trigger: 'blur' },
        { type: 'email', message: 'Email inválido', trigger: 'blur' }
      ],
      password: [
        { required: true, message: 'Senha é obrigatória', trigger: 'blur' },
        { min: 8, message: 'Senha deve ter pelo menos 8 caracteres', trigger: 'blur' }
      ],
      password_confirmation: [
        { required: true, message: 'Confirmação de senha é obrigatória', trigger: 'blur' },
        {
          validator: (rule, value, callback) => {
            if (value !== form.password) {
              callback(new Error('Senhas não coincidem'))
            } else {
              callback()
            }
          },
          trigger: 'blur'
        }
      ]
    }

    const handleRegister = async () => {
      if (!registerForm.value) return
      
      const valid = await registerForm.value.validate()
      if (!valid) return

      loading.value = true
      const result = await authStore.register(form)
      loading.value = false

      if (result.success) {
        ElMessage.success('Conta criada com sucesso!')
        router.push('/dashboard')
      } else {
        ElMessage.error(result.message)
      }
    }

    return {
      form,
      rules,
      loading,
      registerForm,
      handleRegister
    }
  }
}
