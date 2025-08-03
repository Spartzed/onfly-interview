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

<script src="./TravelOrderForm.js"></script>

<style scoped>
@import './TravelOrderForm.css';
</style>
