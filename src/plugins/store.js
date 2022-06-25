import {createStore} from "vuex";
import Settings from "@/config.json";

export default createStore({
    state() {
        return {
            user: null,
            locale: Settings.LOCALE,
            settings: Settings,
            loaderCount: 0,
            selectedMenu: null,
            selectedSubMenu: null,
            breadcrumbs: []
        }
    },
    mutations: {
        setLocale(state, value) {
          state.locale = value;
        },
        setUser(state, value) {
          state.user = value;
        },
        setBreadcrumbs(state, value) {
            state.breadcrumbs = value;
        },
        selectMenu(state, value) {
            state.selectedMenu = value;
        },
        selectSubMenu(state, value) {
            state.selectedSubMenu = value;
        },
        increaseLoaderCount(state) {
            setTimeout(() => {
                state.loaderCount++;
            }, 200);
        },
        decreaseLoaderCount(state) {
            state.loaderCount--;
        }
    }
})
