import {createStore} from "vuex";
import Settings from "@/config.json";

export default createStore({
    state() {
        return {
            user: null,
            // Per-user UI state from the user_data table, as a key => value map, delivered with the session
            // so components can render it on their first paint. Written back through the user-data endpoint.
            userData: {},
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
          // Stored state belongs to whoever is logged in, so logging out drops it instead of leaving it for
          // the next user. A login commits setUserData before setUser, so this never clears a fresh map.
          if (value === null) {
              state.userData = {};
          }
        },
        setUserData(state, value) {
            state.userData = value ?? {};
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
