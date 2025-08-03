import { useRouter } from 'vue-router'
import { useTravelOrdersStore } from '@/stores/travelOrders'
import { Refresh, ArrowLeft, DataAnalysis } from '@element-plus/icons-vue'

export default {
    name: 'DashboardStats',

    setup() {
        const router = useRouter()
        const travelOrdersStore = useTravelOrdersStore()
        return { router, travelOrdersStore }
    },

    data() {
        return {
            loading: false,
            orders: []
        }
    },

    mounted() {
        this.refreshStats()
    },

    methods: {
        async refreshStats() {
            this.loading = true
            try {
                await this.travelOrdersStore.fetchTravelOrders()
                this.orders = this.travelOrdersStore.travelOrders
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error)
            } finally {
                this.loading = false
            }
        },

        goBack() {
            this.router.push('/dashboard')
        }
    }
} 