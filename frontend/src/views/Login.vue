<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>Sistema de Pedidos de Viagem</h1>
        <p>Faça login para acessar o sistema</p>
      </div>

      <el-form
        ref="loginForm"
        :model="form"
        :rules="rules"
        label-width="0"
        @submit.prevent="handleLogin"
      >
        <el-form-item prop="email">
          <el-input
            v-model="form.email"
            placeholder="Email"
            type="email"
            size="large"
            prefix-icon="User"
          />
        </el-form-item>

        <el-form-item prop="password">
          <el-input
            v-model="form.password"
            placeholder="Senha"
            type="password"
            size="large"
            prefix-icon="Lock"
            show-password
          />
        </el-form-item>

        <el-form-item>
          <el-button
            type="primary"
            size="large"
            :loading="loading"
            @click="handleLogin"
            style="width: 100%"
          >
            Entrar
          </el-button>
        </el-form-item>

        <div class="register-link">
          <p>
            Não tem uma conta?
            <el-button type="text" @click="$router.push('/register')">
              Registre-se
            </el-button>
          </p>
        </div>
      </el-form>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ElMessage } from 'element-plus'

export default {
  name: 'Login',
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    
    const loginForm = ref(null)
    const loading = ref(false)

    const form = reactive({
      email: '',
      password: ''
    })

    const rules = {
      email: [
        { required: true, message: 'Email é obrigatório', trigger: 'blur' },
        { type: 'email', message: 'Email inválido', trigger: 'blur' }
      ],
      password: [
        { required: true, message: 'Senha é obrigatória', trigger: 'blur' },
        { min: 6, message: 'Senha deve ter pelo menos 6 caracteres', trigger: 'blur' }
      ]
    }

    const handleLogin = async () => {
      if (!loginForm.value) return
      
      const valid = await loginForm.value.validate()
      if (!valid) return

      loading.value = true
      const result = await authStore.login(form)
      loading.value = false

      if (result.success) {
        ElMessage.success('Login realizado com sucesso!')
        router.push('/dashboard')
      } else {
        ElMessage.error(result.message)
      }
    }

    return {
      form,
      rules,
      loading,
      loginForm,
      handleLogin
    }
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  padding: 40px;
  width: 100%;
  max-width: 400px;
}

.login-header {
  text-align: center;
  margin-bottom: 30px;
}

.login-header h1 {
  color: #2c3e50;
  margin-bottom: 10px;
  font-size: 24px;
}

.login-header p {
  color: #7f8c8d;
  font-size: 14px;
}

.register-link {
  text-align: center;
  margin-top: 20px;
}

.register-link p {
  color: #7f8c8d;
  font-size: 14px;
}
</style> 