import { createApp } from 'vue'
import App from './vue/App.vue'
import '../css/vue.css'

document.documentElement.classList.add('js')

const root = document.querySelector('#app')
createApp(App, {
    user: Number(root.dataset.user) || null,
    cms: root.dataset.cms === '1',
    errorPage: root.dataset.error,
}).mount(root)

if ('serviceWorker' in navigator && import.meta.env.PROD) {
    window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js').catch(() => {}))
}
