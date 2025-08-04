import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useTravelOrdersStore } from '@/stores/travelOrders'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Check, Close, Plus, Refresh, SwitchButton, ArrowDown, User, Setting, Location, Document, Select, Delete, DataAnalysis } from '@element-plus/icons-vue'
import NotificationBell from '@/components/NotificationBell.vue'
import dayjs from 'dayjs'

export default {
    name: 'Dashboard',

    components: {
        NotificationBell
    },

    setup() {
        const authStore = useAuthStore()
        const travelOrdersStore = useTravelOrdersStore()
        return { authStore, travelOrdersStore }
    },

    data() {
        return {
            showCreateModal: false,
            showEditModal: false,
            editing: false,
            currentOrder: null,
            searchTimeout: null,
            filters: {
                status: '',
                destination: '',
                dateRange: null
            },
            createFormData: {
                requester_name: '',
                destination: '',
                departure_date: '',
                return_date: ''
            },
            createRules: {
                requester_name: [
                    { required: true, message: 'Nome do solicitante é obrigatório', trigger: 'blur' }
                ],
                destination: [
                    { required: true, message: 'Destino é obrigatório', trigger: 'blur' }
                ],
                departure_date: [
                    { required: true, message: 'Data de ida é obrigatória', trigger: 'change' }
                ],
                return_date: [
                    { required: true, message: 'Data de volta é obrigatória', trigger: 'change' }
                ]
            }
        }
    },

    mounted() {
        this.travelOrdersStore.fetchTravelOrders()
    },

    watch: {
        'filters.destination'() {
            this.handleFilterChange()
        },
        'filters.status'() {
            this.handleFilterChange()
        },
        'filters.dateRange'() {
            this.handleFilterChange()
        }
    },

    computed: {
        hasActiveFilters() {
            return this.filters.status || this.filters.destination || this.filters.dateRange
        },
        
        ordersCount() {
            return this.travelOrdersStore.travelOrders.length
        }
    },

    methods: {
        formatDate(date) {
            return dayjs(date).format('DD/MM/YYYY')
        },

        getStatusLabel(status) {
            const labels = {
                requested: 'Solicitado',
                approved: 'Aprovado',
                cancelled: 'Cancelado'
            }
            return (labels[status] || status).trim()
        },

        getStatusType(status) {
            const types = {
                requested: 'warning',
                approved: 'success',
                cancelled: 'danger'
            }
            return types[status] || 'info'
        },

        handleFilterChange() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout)
            }
            
            this.searchTimeout = setTimeout(() => {
                this.travelOrdersStore.fetchTravelOrders(this.filters)
            }, 500)
        },

        refreshOrders() {
            this.travelOrdersStore.fetchTravelOrders(this.filters)
        },

        clearFilters() {
            this.filters.status = ''
            this.filters.destination = ''
            this.filters.dateRange = null
            this.travelOrdersStore.fetchTravelOrders()
        },

        async updateStatus(id, status) {
            try {
                if (status === 'approved') {
                    await ElMessageBox.confirm(
                        'Tem certeza que deseja aprovar este pedido?',
                        'Confirmar Aprovação',
                        {
                            confirmButtonText: 'Sim',
                            cancelButtonText: 'Não',
                            type: 'success'
                        }
                    )
                }
                
                if (status === 'cancelled') {
                    await ElMessageBox.confirm(
                        'Tem certeza que deseja rejeitar este pedido?',
                        'Confirmar Rejeição',
                        {
                            confirmButtonText: 'Sim',
                            cancelButtonText: 'Não',
                            type: 'warning'
                        }
                    )
                }

                const result = await this.travelOrdersStore.updateTravelOrderStatus(id, status)
                if (result.success) {
                    const action = status === 'approved' ? 'aprovado' : 'rejeitado'
                    ElMessage.success(`Pedido ${action} com sucesso!`)
                } else {
                    ElMessage.error(result.message)
                }
            } catch (error) {
                if (error !== 'cancel') {
                    ElMessage.error('Erro ao atualizar status')
                }
            }
        },

        async cancelOrder(id) {
            try {
                await ElMessageBox.confirm(
                    'Tem certeza que deseja cancelar este pedido?',
                    'Confirmar Cancelamento',
                    {
                        confirmButtonText: 'Sim',
                        cancelButtonText: 'Não',
                        type: 'warning'
                    }
                )

                const result = await this.travelOrdersStore.cancelTravelOrder(id)
                if (result.success) {
                    ElMessage.success('Pedido cancelado com sucesso!')
                } else {
                    ElMessage.error(result.message)
                }
            } catch (error) {
                if (error !== 'cancel') {
                    ElMessage.error('Erro ao cancelar pedido')
                }
            }
        },

        async handleCreateOrder() {
            if (!this.createFormData.requester_name || !this.createFormData.destination || 
                !this.createFormData.departure_date || !this.createFormData.return_date) {
                ElMessage.error('Por favor, preencha todos os campos obrigatórios')
                return
            }

            if (this.createFormData.departure_date >= this.createFormData.return_date) {
                ElMessage.error('A data de volta deve ser posterior à data de ida')
                return
            }

            try {
                this.creating = true
                
                const result = await this.travelOrdersStore.createTravelOrder(this.createFormData)
                
                this.creating = false

                if (result.success) {
                    ElMessage.success('Pedido criado com sucesso!')
                    this.showCreateModal = false
                    this.createFormData.requester_name = ''
                    this.createFormData.destination = ''
                    this.createFormData.departure_date = ''
                    this.createFormData.return_date = ''
                    
                    this.travelOrdersStore.fetchTravelOrders(this.filters)
                } else {
                    ElMessage.error(result.message || 'Erro ao criar pedido')
                }
            } catch (error) {
                console.error('Erro na criação:', error)
                this.creating = false
                ElMessage.error('Erro ao criar pedido: ' + (error.message || 'Erro desconhecido'))
            }
        },

        handleUserAction(command) {
            switch (command) {
                case 'profile':
                    ElMessage.info('Funcionalidade em desenvolvimento')
                    break
                case 'settings':
                    ElMessage.info('Funcionalidade em desenvolvimento')
                    break
                case 'logout':
                    this.handleLogout()
                    break
            }
        },

        disablePastDates(time) {
            return time.getTime() < Date.now() - 8.64e7
        },

        handleLogout() {
            this.authStore.logout()
            this.$router.push('/login')
        },

        goToStats() {
            this.$router.push('/stats')
        },

        openEditModal(order) {
            this.currentOrder = { ...order };
            this.showEditModal = true;
        },

        async handleUpdateOrder() {
            if (!this.currentOrder.requester_name || !this.currentOrder.destination ||
                !this.currentOrder.departure_date || !this.currentOrder.return_date) {
                ElMessage.error('Por favor, preencha todos os campos obrigatórios');
                return;
            }

            if (this.currentOrder.departure_date >= this.currentOrder.return_date) {
                ElMessage.error('A data de volta deve ser posterior à data de ida');
                return;
            }

            try {
                this.editing = true;
                const result = await this.travelOrdersStore.updateTravelOrder(this.currentOrder.id, this.currentOrder);
                this.editing = false;

                if (result.success) {
                    ElMessage.success('Pedido atualizado com sucesso!');
                    this.showEditModal = false;
                    this.travelOrdersStore.fetchTravelOrders(this.filters);
                } else {
                    ElMessage.error(result.message || 'Erro ao atualizar pedido');
                }
            } catch (error) {
                console.error('Erro na atualização:', error);
                this.editing = false;
                ElMessage.error('Erro ao atualizar pedido: ' + (error.message || 'Erro desconhecido'));
            }
        },
    }
} 