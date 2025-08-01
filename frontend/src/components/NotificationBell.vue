<template>
  <div class="notification-bell">
    <el-popover
      placement="bottom-end"
      :width="400"
      trigger="click"
      popper-class="notification-popover"
    >
      <template #reference>
        <el-badge :value="unreadCount" :hidden="unreadCount === 0" class="notification-badge">
          <el-button
            type="text"
            class="notification-button"
            :class="{ 'has-notifications': unreadCount > 0 }"
          >
            <el-icon size="20">
              <Bell />
            </el-icon>
          </el-button>
        </el-badge>
      </template>

      <div class="notification-header">
        <div class="header-content">
          <div class="header-icon">
            <el-icon size="20">
              <Bell />
            </el-icon>
          </div>
          <h3>Notificações</h3>
        </div>
        <el-button
          v-if="notifications.length > 0"
          type="text"
          size="small"
          @click="markAllAsRead"
          class="mark-all-read-btn"
        >
          Marcar todas como lidas
        </el-button>
      </div>

      <div class="notification-list">
        <div
          v-if="notifications.length === 0"
          class="empty-notifications"
        >
          <div class="empty-icon">
            <el-icon size="48" color="#909399">
              <Bell />
            </el-icon>
          </div>
          <p>Nenhuma notificação</p>
          <span class="empty-subtitle">Você está em dia com suas notificações</span>
        </div>

        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="notification-item"
          :class="{ 'unread': !notification.read_at }"
          @click="markAsRead(notification)"
        >
          <div class="notification-icon">
            <el-icon :color="getNotificationColor(notification.type)" size="18">
              <component :is="getNotificationIcon(notification.type)" />
            </el-icon>
          </div>
          <div class="notification-content">
            <div class="notification-title">
              {{ notification.data.title || 'Notificação' }}
            </div>
            <div class="notification-message">
              {{ notification.data.message }}
            </div>
            <div class="notification-time">
              {{ formatTime(notification.created_at) }}
            </div>
          </div>
          <div v-if="!notification.read_at" class="unread-indicator"></div>
        </div>
      </div>

      <div v-if="notifications.length > 0" class="notification-footer">
        <el-button type="text" size="small" @click="viewAllNotifications" class="view-all-btn">
          Ver todas
        </el-button>
      </div>
    </el-popover>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { Bell, Check, Close, InfoFilled, SuccessFilled, WarningFilled } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

dayjs.extend(relativeTime)

export default {
  name: 'NotificationBell',
  components: {
    Bell,
    Check,
    Close,
    InfoFilled,
    SuccessFilled,
    WarningFilled
  },
  setup() {
    const notifications = ref([])
    const loading = ref(false)

    const unreadCount = computed(() => {
      return notifications.value.filter(n => !n.read_at).length
    })

    const fetchNotifications = async () => {
      try {
        loading.value = true
        // Simular notificações por enquanto
        notifications.value = [
          {
            id: 1,
            type: 'success',
            data: {
              title: 'Pedido Aprovado',
              message: 'Seu pedido de viagem para São Paulo foi aprovado!'
            },
            created_at: new Date(Date.now() - 1000 * 60 * 30), // 30 minutos atrás
            read_at: null
          },
          {
            id: 2,
            type: 'info',
            data: {
              title: 'Novo Sistema',
              message: 'Bem-vindo ao novo sistema de pedidos de viagem!'
            },
            created_at: new Date(Date.now() - 1000 * 60 * 60 * 2), // 2 horas atrás
            read_at: new Date()
          }
        ]
      } catch (error) {
        ElMessage.error('Erro ao carregar notificações')
      } finally {
        loading.value = false
      }
    }

    const markAsRead = async (notification) => {
      if (!notification.read_at) {
        notification.read_at = new Date()
        // Aqui você faria uma chamada para a API para marcar como lida
      }
    }

    const markAllAsRead = async () => {
      notifications.value.forEach(notification => {
        if (!notification.read_at) {
          notification.read_at = new Date()
        }
      })
      // Aqui você faria uma chamada para a API para marcar todas como lidas
    }

    const viewAllNotifications = () => {
      // Implementar navegação para página de todas as notificações
      ElMessage.info('Funcionalidade em desenvolvimento')
    }

    const getNotificationIcon = (type) => {
      const icons = {
        success: 'SuccessFilled',
        error: 'Close',
        warning: 'WarningFilled',
        info: 'InfoFilled'
      }
      return icons[type] || 'InfoFilled'
    }

    const getNotificationColor = (type) => {
      const colors = {
        success: '#67C23A',
        error: '#F56C6C',
        warning: '#E6A23C',
        info: '#409EFF'
      }
      return colors[type] || '#409EFF'
    }

    const formatTime = (date) => {
      return dayjs(date).fromNow()
    }

    onMounted(() => {
      fetchNotifications()
    })

    return {
      notifications,
      loading,
      unreadCount,
      markAsRead,
      markAllAsRead,
      viewAllNotifications,
      getNotificationIcon,
      getNotificationColor,
      formatTime
    }
  }
}
</script>

<style scoped>
.notification-bell {
  position: relative;
}

.notification-badge {
  margin-right: 8px;
}

.notification-badge .el-badge__content {
  background: linear-gradient(135deg, #f56c6c 0%, #f78989 100%);
  border: 2px solid white;
  box-shadow: 0 4px 12px rgba(245, 108, 108, 0.3);
}

.notification-button {
  padding: 12px;
  border-radius: 12px;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #6c757d;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.notification-button:hover {
  background: rgba(255, 255, 255, 0.95);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  color: #667eea;
}

.notification-button.has-notifications {
  color: #667eea;
  background: rgba(102, 126, 234, 0.1);
  border-color: rgba(102, 126, 234, 0.2);
}

.notification-button.has-notifications:hover {
  background: rgba(102, 126, 234, 0.15);
  color: #5a6fd8;
}

/* Estilos globais para o popover */
:global(.notification-popover) {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.2);
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  padding: 0;
}

.notification-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 24px 20px;
  border-bottom: 1px solid rgba(102, 126, 234, 0.1);
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  color: white;
}

.header-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 10px;
  color: white;
}

.notification-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  color: white;
}

.mark-all-read-btn {
  color: rgba(255, 255, 255, 0.8);
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
}

.mark-all-read-btn:hover {
  color: white;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
}

.notification-list {
  max-height: 400px;
  overflow-y: auto;
  background: rgba(255, 255, 255, 0.95);
}

.empty-notifications {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 24px;
  color: #909399;
  text-align: center;
}

.empty-icon {
  margin-bottom: 16px;
  opacity: 0.6;
}

.empty-notifications p {
  margin: 0 0 8px 0;
  font-size: 16px;
  font-weight: 600;
  color: #2c3e50;
}

.empty-subtitle {
  font-size: 14px;
  color: #6c757d;
  opacity: 0.8;
}

.notification-item {
  display: flex;
  align-items: flex-start;
  padding: 20px 24px;
  border-bottom: 1px solid rgba(102, 126, 234, 0.1);
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  background: rgba(255, 255, 255, 0.8);
}

.notification-item:hover {
  background: rgba(102, 126, 234, 0.05);
  transform: translateX(4px);
}

.notification-item.unread {
  background: rgba(102, 126, 234, 0.08);
  border-left: 4px solid #667eea;
}

.notification-item.unread:hover {
  background: rgba(102, 126, 234, 0.12);
}

.notification-icon {
  margin-right: 16px;
  margin-top: 2px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: rgba(102, 126, 234, 0.1);
  border-radius: 8px;
  flex-shrink: 0;
}

.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-title {
  font-weight: 700;
  font-size: 14px;
  color: #2c3e50;
  margin-bottom: 6px;
  line-height: 1.4;
}

.notification-message {
  font-size: 13px;
  color: #606266;
  line-height: 1.5;
  margin-bottom: 8px;
}

.notification-time {
  font-size: 12px;
  color: #909399;
  font-weight: 500;
}

.unread-indicator {
  position: absolute;
  top: 20px;
  right: 24px;
  width: 10px;
  height: 10px;
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  border-radius: 50%;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.2);
    opacity: 0.7;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.notification-footer {
  padding: 16px 24px;
  border-top: 1px solid rgba(102, 126, 234, 0.1);
  text-align: center;
  background: rgba(248, 249, 250, 0.8);
  backdrop-filter: blur(10px);
}

.view-all-btn {
  color: #667eea;
  font-weight: 600;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  padding: 8px 16px;
  border-radius: 8px;
}

.view-all-btn:hover {
  background: rgba(102, 126, 234, 0.1);
  color: #5a6fd8;
  transform: translateY(-1px);
}

/* Scrollbar personalizada */
.notification-list::-webkit-scrollbar {
  width: 6px;
}

.notification-list::-webkit-scrollbar-track {
  background: rgba(102, 126, 234, 0.1);
  border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
  border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #5a6fd8 0%, #3d9bf0 100%);
}

/* Responsive Design */
@media (max-width: 768px) {
  :global(.notification-popover) {
    width: calc(100vw - 40px) !important;
    margin: 0 20px;
  }
  
  .notification-header {
    padding: 20px 20px 16px;
  }
  
  .notification-item {
    padding: 16px 20px;
  }
  
  .notification-footer {
    padding: 12px 20px;
  }
  
  .empty-notifications {
    padding: 32px 20px;
  }
}
</style> 