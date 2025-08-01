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
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  position: relative;
}

.login-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.02)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
  pointer-events: none;
}

.login-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
  padding: 48px;
  width: 100%;
  max-width: 600px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  position: relative;
  overflow: hidden;
}

.login-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #667eea 0%, #4facfe 50%, #667eea 100%);
}

.login-header {
  text-align: center;
  margin-bottom: 40px;
}

.logo-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

.logo-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
  overflow: hidden;
  padding: 0;
}

.logo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 16px;
}

.login-header h1 {
  color: #2c3e50;
  margin: 0;
  font-size: 28px;
  font-weight: 700;
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.login-header p {
  color: #6c757d;
  font-size: 16px;
  font-weight: 500;
  margin: 0;
}

.modern-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  font-weight: 700;
  color: #2c3e50;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
  margin-left: 5px;
}

.modern-input .el-input__wrapper {
  border-radius: 12px;
  border: 2px solid rgba(102, 126, 234, 0.1);
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}

.modern-input .el-input__wrapper:hover {
  border-color: #667eea;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
}

.modern-input .el-input__wrapper.is-focus {
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.submit-btn {
  width: 100%;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  border: none;
  transition: all 0.3s ease;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  position: relative;
  overflow: hidden;
}

.submit-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.submit-btn:hover::before {
  left: 100%;
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
}

.submit-btn .el-icon {
  font-size: 16px;
  flex-shrink: 0;
}

.submit-btn .button-text {
  font-weight: 600;
  white-space: nowrap;
}

.register-link {
  text-align: center;
  margin-top: 8px;
}

.register-link p {
  color: #6c757d;
  font-size: 14px;
  margin: 0;
}

.link-btn {
  color: #667eea;
  font-weight: 600;
  transition: all 0.3s ease;
}

.link-btn:hover {
  color: #5a6fd8;
  transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
  .login-card {
    margin: 20px;
    padding: 32px;
  }
  
  .login-header h1 {
    font-size: 24px;
  }
  
  .logo-icon {
    width: 56px;
    height: 56px;
  }
}
</style> 