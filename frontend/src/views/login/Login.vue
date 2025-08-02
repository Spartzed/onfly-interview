<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <div class="logo-section">
          <div class="logo-icon">
            <img src="/logo.png" alt="Onfly Logo" class="logo-img">
          </div>
          <h1>Onfly</h1>
        </div>
        <p>Faça login para acessar o sistema</p>
      </div>

      <el-form
        ref="loginForm"
        :model="form"
        :rules="rules"
        class="modern-form"
        @submit.prevent="handleLogin"
      >
        <div class="form-group">
          <label class="form-label">Email</label>
          <el-input
            v-model="form.email"
            placeholder="Digite seu email"
            type="email"
            size="large"
            class="modern-input"
          >
            <template #prefix>
              <el-icon><User /></el-icon>
            </template>
          </el-input>
        </div>

        <div class="form-group">
          <label class="form-label">Senha</label>
          <el-input
            v-model="form.password"
            placeholder="Digite sua senha"
            type="password"
            size="large"
            class="modern-input"
            show-password
          >
            <template #prefix>
              <el-icon><Lock /></el-icon>
            </template>
          </el-input>
        </div>

        <div class="form-group">
          <el-button
            type="primary"
            size="large"
            :loading="loading"
            @click="handleLogin"
            class="submit-btn"
          >
            <el-icon><Right /></el-icon>
            <span class="button-text">Entrar</span>
          </el-button>
        </div>

        <div class="register-link">
          <p>
            Não tem uma conta?
            <el-button link @click="$router.push('/register')" class="link-btn">
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
import { User, Lock, Right } from '@element-plus/icons-vue'

export default {
  name: 'Login',
  components: {
    User,
    Lock,
    Right
  },
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
@import './Login.css';
</style> 