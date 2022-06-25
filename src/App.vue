<template>
  <div v-if="sessionCheckDone" class="app-container">
    <NevsLoader v-if="$store.state.loaderCount > 0"></NevsLoader>
    <NevsNotification></NevsNotification>
    <NevsPopup></NevsPopup>
    <LoginForm :postLogin="setupMenu" v-if="$store.state.user === null"></LoginForm>
    <template v-if="$store.state.user !== null">
      <Transition name="main-menu">
        <NevsMainMenu v-show="showMenu" :items="menu.items" :logo="menu.logo"
                      @toggleMenu="showMenu=!showMenu"></NevsMainMenu>
      </Transition>
      <NevsTopBar :breadcrumbs="$store.state.breadcrumbs" :buttons="topBarButtons"
                  @toggleMenu="showMenu=!showMenu"></NevsTopBar>
      <div class="nevs-main-content">
        <RouterView></RouterView>
      </div>
    </template>
  </div>
</template>

<script>

import NevsLoader from "@/components/nevs/NevsLoader";
import NevsNotification from "@/components/nevs/NevsNotification";
import NevsPopup from "@/components/nevs/NevsPopup";
import LoginForm from "@/components/general/LoginForm";
import NevsMainMenu from "@/components/nevs/NevsMainMenu";
import NevsTopBar from "@/components/nevs/NevsTopBar";

export default {
  name: 'App',
  components: {
    NevsMainMenu,
    NevsTopBar,
    LoginForm,
    NevsPopup,
    NevsNotification,
    NevsLoader
  },
  data() {
    return {
      showMenu: true,
      sessionCheckDone: false,
      menu: {
        logo: 'https://via.placeholder.com/250x100',
        items: []
      },
      topBarButtons: [
        {
          icon: '<i class="fa-solid fa-right-from-bracket"></i>',
          tooltip: this.$LANG.Get('tooltips.logout'),
          action: () => {
            let vm = this;
            this.$API.APICall('post', 'logout', {}, (data, success) => {
              if (success) {
                vm.$store.commit('setUser', null);
              } else {
                vm.$LOCAL_BUS.TriggerEvent('popup', {text: vm.$LANG.Get('alerts.serverError'), type: 'alert'});
              }
            });
          }
        },
        {
          icon: '<i class="fa-solid fa-user"></i>',
          tooltip: this.$LANG.Get('tooltips.myProfile'),
          action: () => {
            this.$router.push('/users/' + this.$store.state.user.id);
          }
        }
      ]
    }
  },
  methods: {
    resolveWindowResize() {
      this.showMenu = window.innerWidth >= 800;
    },
    setupMenu() {
      this.menu.items = [];
      this.menu.items.push({
        id: 'home',
        label: this.$LANG.Get('modules.home'),
        link: '/',
        icon: '<i class="fa-solid fa-house"></i>',
        children: []
      });
      if(this.$store.state.user.permissions.includes('MANAGE_USERS')) {
        this.menu.items.push({
          id: 'users',
          label: this.$LANG.Get('modules.users'),
          link: '/users',
          icon: '<i class="fa-solid fa-users"></i>',
          children: []
        });
      }
    }
  },
  mounted() {
    window.addEventListener('resize', this.resolveWindowResize);
    this.resolveWindowResize();
    let vm = this;
    this.$API.APICall('get', 'session', {}, (data, success) => {
      if (success) {
        vm.$store.commit('setUser', data.user);
        vm.$store.commit('setLocale', data.user.locale);
        vm.$nextTick(() => {
          this.setupMenu();
        });
      } else {
        vm.$store.commit('setLocale', vm.$store.state.settings.LOCALE);
      }
      vm.$nextTick(() => {
        vm.sessionCheckDone = true;
      });
    }, false);
  }
}

</script>
