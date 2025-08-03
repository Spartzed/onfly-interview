import { useAuthStore } from '@/stores/auth'
import { ElMessage } from 'element-plus'
import { User, Lock, Right } from '@element-plus/icons-vue'

export default {
  name: 'Login',
  components: {
    User,
    Lock,
    Right
  },
  setup() {
    const authStore = useAuthStore()
    return { authStore }
  },
  data() {
    return {
      loading: false,
      form: {
        email: '',
        password: ''
      },
      rules: {
        email: [
          { required: true, message: 'Email é obrigatório', trigger: 'blur' },
          { type: 'email', message: 'Email inválido', trigger: 'blur' }
        ],
        password: [
          { required: true, message: 'Senha é obrigatória', trigger: 'blur' },
          { min: 6, message: 'Senha deve ter pelo menos 6 caracteres', trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    async handleLogin() {
      const valid = await this.$refs.loginForm.validate()
      if (!valid) return

      this.loading = true
      const result = await this.authStore.login(this.form)
      this.loading = false

      if (result.success) {
        ElMessage.success('Login realizado com sucesso!')
        this.$router.push('/dashboard')
      } else {
        ElMessage.error(result.message)
      }
    }
  }
}
