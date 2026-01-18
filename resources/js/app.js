import './bootstrap'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './utils/axios'
import '../css/app.css'
import 'boxicons/css/boxicons.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import '@fortawesome/fontawesome-free/css/all.min.css'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { authStore } from './stores/auth'

const pinia = createPinia()
const app = createApp(App)

app.use(pinia)  
app.use(router)
app.component('v-select', vSelect)

authStore.checkAuth().then(() => {
  //app.use(router).mount('#app')
  app.mount('#app')
})
//app.use(router).mount('#app')