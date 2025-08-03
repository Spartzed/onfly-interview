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

<script src="./Register.js"></script>

<style scoped>
@import './Register.css';
</style>
