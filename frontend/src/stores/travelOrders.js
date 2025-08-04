import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useTravelOrdersStore = defineStore('travelOrders', () => {
  const travelOrders = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchTravelOrders = async (filters = {}) => {
    loading.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams()
      if (filters.status) params.append('status', filters.status)
      if (filters.destination) params.append('destination', filters.destination)

      if (filters.dateRange && Array.isArray(filters.dateRange) && filters.dateRange.length === 2) {
        params.append('date_range[start]', filters.dateRange[0])
        params.append('date_range[end]', filters.dateRange[1])
      }
      
      const response = await api.get(`/travel-orders?${params.toString()}`)
      travelOrders.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao carregar pedidos'
    } finally {
      loading.value = false
    }
  }

  const createTravelOrder = async (orderData) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.post('/travel-orders', orderData)
      travelOrders.value.unshift(response.data.data)
      return { success: true, data: response.data.data }
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao criar pedido'
      return { success: false, message: error.value }
    } finally {
      loading.value = false
    }
  }

  const updateTravelOrderStatus = async (id, status) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.patch(`/travel-orders/${id}/status`, { status })
      const updatedOrder = response.data.data
      
      const index = travelOrders.value.findIndex(order => order.id === id)
      if (index !== -1) {
        travelOrders.value[index] = updatedOrder
      }
      
      return { success: true, data: updatedOrder }
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao atualizar status'
      return { success: false, message: error.value }
    } finally {
      loading.value = false
    }
  }

  const cancelTravelOrder = async (id) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.delete(`/travel-orders/${id}`)
      const updatedOrder = response.data.data
      
      const index = travelOrders.value.findIndex(order => order.id === id)
      if (index !== -1) {
        travelOrders.value[index] = updatedOrder
      }
      
      return { success: true, data: updatedOrder }
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao cancelar pedido'
      return { success: false, message: error.value }
    } finally {
      loading.value = false
    }
  }

  const getTravelOrderById = (id) => {
    return travelOrders.value.find(order => order.id === id)
  }

  return {
    travelOrders,
    loading,
    error,
    fetchTravelOrders,
    createTravelOrder,
    updateTravelOrderStatus,
    cancelTravelOrder,
    getTravelOrderById,
    updateTravelOrder
  }
})

async function updateTravelOrder(id, orderData) {
    this.loading = true;
    this.error = null;
    
    try {
        const response = await api.put(`/travel-orders/${id}`, orderData);
        const updatedOrder = response.data.data;
        
        const index = this.travelOrders.findIndex(order => order.id === id);
        if (index !== -1) {
            this.travelOrders[index] = updatedOrder;
        }
        
        return { success: true, data: updatedOrder };
    } catch (err) {
        this.error = err.response?.data?.message || 'Erro ao atualizar pedido';
        return { success: false, message: this.error };
    } finally {
        this.loading = false;
    }
}
 