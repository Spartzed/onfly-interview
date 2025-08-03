import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useNotificationsStore = defineStore('notifications', () => {
  const notifications = ref([])
  const loading = ref(false)

  const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.read_at).length
  })

  const fetchNotifications = async () => {
    try {
      loading.value = true
      const response = await fetch('/api/notifications', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        notifications.value = data.notifications || []
      } else {
        notifications.value = []
      }
    } catch (error) {
      console.error('Erro ao carregar notificações:', error)
      notifications.value = []
    } finally {
      loading.value = false
    }
  }

  const markAsRead = async (notificationId) => {
    try {
      const response = await fetch(`/api/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const notification = notifications.value.find(n => n.id === notificationId)
        if (notification) {
          notification.read_at = new Date()
        }
      }
    } catch (error) {
      console.error('Erro ao marcar notificação como lida:', error)
      const notification = notifications.value.find(n => n.id === notificationId)
      if (notification) {
        notification.read_at = new Date()
      }
    }
  }

  const markAllAsRead = async () => {
    try {
      const response = await fetch('/api/notifications/mark-all-read', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        notifications.value.forEach(notification => {
          if (!notification.read_at) {
            notification.read_at = new Date()
          }
        })
      }
    } catch (error) {
      console.error('Erro ao marcar todas como lidas:', error)
      notifications.value.forEach(notification => {
        if (!notification.read_at) {
          notification.read_at = new Date()
        }
      })
    }
  }

  const createNotification = async (notificationData) => {
    try {
      const response = await fetch('/api/notifications', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(notificationData)
      })

      if (response.ok) {
        const newNotification = await response.json()
        notifications.value.unshift(newNotification)
        return { success: true, notification: newNotification }
      } else {
        return { success: false, message: 'Erro ao criar notificação' }
      }
    } catch (error) {
      console.error('Erro ao criar notificação:', error)
      return { success: false, message: 'Erro de conexão' }
    }
  }

  return {
    notifications,
    loading,
    unreadCount,
    fetchNotifications,
    markAsRead,
    markAllAsRead,
    createNotification
  }
}) 