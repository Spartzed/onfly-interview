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
            <span>Não tem uma conta?</span>
            <el-button link @click="$router.push('/register')" class="link-btn">
             Registre-se
            </el-button>
          </p>
        </div>
      </el-form>
    </div>
  </div>
</template>

<script src="./Login.js"></script>
<style scoped src="./Login.css"></style>
