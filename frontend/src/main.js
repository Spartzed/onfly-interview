import { createApp } from 'vue'
import { createPinia } from 'pinia'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import zhCn from 'element-plus/dist/locale/zh-cn.mjs'
import ptBr from 'element-plus/dist/locale/pt-br.mjs'
import App from './App.vue'
import router from './router'
import './style.css'

const app = createApp(App)

// Registrar todos os ícones do Element Plus
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
  app.component(key, component)
}

app.use(createPinia())
app.use(router)
app.use(ElementPlus, {
  locale: ptBr,
})

app.mount('#app') 