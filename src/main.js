import '@fortawesome/fontawesome-free/js/all.min.js'
import 'moment/locale/hr'

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

// Keep the document title in sync with the current page. The app name comes from the initial
// <title> in index.html; the page-specific part is the last breadcrumb label, which modules and
// entities already commit to the store (module name, invoice number, person name, ...). Entities
// set their breadcrumbs asynchronously after loading, so the title is driven by the breadcrumb
// mutation rather than the route change. afterEach resets to the bare app name on navigation so a
// page that never sets breadcrumbs (or hasn't loaded yet) never shows the previous page's title.
const APP_NAME = document.title;
const applyTitle = (breadcrumbs) => {
    const last = Array.isArray(breadcrumbs) && breadcrumbs.length > 0
        ? breadcrumbs[breadcrumbs.length - 1].label : null;
    document.title = last ? APP_NAME + ' - ' + last : APP_NAME;
};
store.subscribe((mutation) => {
    if (mutation.type === 'setBreadcrumbs') applyTitle(mutation.payload);
});
router.afterEach(() => {
    document.title = APP_NAME;
});
app.use(router);

app.mount('#app')
