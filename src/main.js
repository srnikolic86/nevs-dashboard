import '@fortawesome/fontawesome-free/js/all.min.js'

import './scss/style.scss'

import {createApp} from 'vue'
import App from './App.vue'
const app = createApp(App)

import API from './plugins/api.js'
app.config.globalProperties.$API = API.generateApi(store);

import CROSS_TAB_BUS from './plugins/crossTabBus.js'
let CrossTabBus = CROSS_TAB_BUS.generateCrossTabBus();
CrossTabBus.Init();
app.config.globalProperties.$CROSS_TAB_BUS = CrossTabBus;

import LOCAL_BUS from './plugins/localBus.js'
app.config.globalProperties.$LOCAL_BUS = LOCAL_BUS.generateLocalBus();

import HELPERS from './plugins/helpers.js'
app.config.globalProperties.$HELPERS = HELPERS.generateHelpers();

import store from './plugins/store'
app.use(store);

import LANG from './plugins/translations.js'
let Lang = LANG.generateTranslations(store);
app.config.globalProperties.$LANG = Lang;

import { createWebHistory, createRouter } from 'vue-router'
import routes from './plugins/routes'
const router = createRouter({
    history: createWebHistory(),
    routes
});
app.use(router);

app.mount('#app')
