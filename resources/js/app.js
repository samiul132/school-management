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

const pinia = createPinia()
const app = createApp(App)

app.use(pinia)  
app.use(router)
app.component('v-select', vSelect)

app.use(router).mount('#app')