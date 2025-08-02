<template>
  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <div class="logo-section">
          <div class="logo-icon">
            <img src="/logo.png" alt="Onfly Logo" class="logo-img">
          </div>
          <h1>Onfly</h1>
        </div>
        <p>Crie sua conta para acessar o sistema</p>
      </div>

      <el-form
        ref="registerForm"
        :model="form"
        :rules="rules"
        class="modern-form"
        @submit.prevent="handleRegister"
      >
        <div class="form-group">
          <label class="form-label">Nome Completo</label>
          <el-input
            v-model="form.name"
            placeholder="Digite seu nome completo"
            size="large"
            class="modern-input"
          >
            <template #prefix>
              <el-icon><User /></el-icon>
            </template>
          </el-input>
        </div>

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
              <el-icon><Message /></el-icon>
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
          <label class="form-label">Confirmar Senha</label>
          <el-input
            v-model="form.password_confirmation"
            placeholder="Confirme sua senha"
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
            @click="handleRegister"
            class="submit-btn"
          >
            <el-icon><Plus /></el-icon>
            <span class="button-text">Criar Conta</span>
          </el-button>
        </div>

        <div class="login-link">
          <p>
            Já tem uma conta?
            <el-button link @click="$router.push('/login')" class="link-btn">
              Faça login
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
</script>

<style scoped>
@import './Register.css';
</style> 