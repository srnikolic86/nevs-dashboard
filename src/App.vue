<template>
    <div v-if="maintenance" class="maintenance-screen">
        <i class="fa-solid fa-screwdriver-wrench maintenance-icon"></i>
        <h1>{{ $LANG.Get('labels.maintenanceTitle') }}</h1>
        <p>{{ $LANG.Get('labels.maintenanceMessage') }}</p>
    </div>
    <div v-else-if="sessionCheckDone" class="app-container">
        <NevsLoader v-if="$store.state.loaderCount > 0"></NevsLoader>
        <NevsNotification></NevsNotification>
        <NevsPopup></NevsPopup>
        <LoginForm :postLogin="setupMenu" v-if="$store.state.user === null"></LoginForm>
        <template v-if="$store.state.user !== null">
            <Transition name="main-menu">
                <NevsMainMenu :collapse="true" v-show="showMenu" :items="menu.items" :logo="menu.logo"
                              @toggleMenu="showMenu=!showMenu"></NevsMainMenu>
            </Transition>
            <NevsTopBar :breadcrumbs="$store.state.breadcrumbs" :buttons="topBarButtons"
                        @toggleMenu="showMenu=!showMenu"></NevsTopBar>
            <div class="nevs-main-content">
                <div v-if="maintenanceDate !== null" class="maintenance-planned-warning">
                    {{ maintenancePlannedText }}
                </div>
                <RouterView></RouterView>
            </div>
        </template>
    </div>
</template>

<script>

import NevsLoader from "@/components/nevs/NevsLoader.vue";
import NevsNotification from "@/components/nevs/NevsNotification.vue";
import NevsPopup from "@/components/nevs/NevsPopup.vue";
import LoginForm from "@/components/general/LoginForm.vue";
import NevsMainMenu from "@/components/nevs/NevsMainMenu.vue";
import NevsTopBar from "@/components/nevs/NevsTopBar.vue";

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
            maintenance: false,
            maintenanceDate: null,
            maintenanceTime: null,
            maintenanceHours: null,
            showMenu: true,
            sessionCheckDone: false,
            menu: {
                logo: 'https://placehold.co/250x100',
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
                                vm.$LOCAL_BUS.TriggerEvent('popup', {
                                    text: vm.$LANG.Get('alerts.serverError'),
                                    type: 'alert'
                                });
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
    computed: {
        maintenancePlannedText() {
            if (this.maintenanceDate === null) return '';
            const dateParts = this.maintenanceDate.split('-');
            const formattedDate = `${parseInt(dateParts[2])}.${parseInt(dateParts[1])}.${dateParts[0]}.`;
            const formattedTime = this.maintenanceTime ? this.maintenanceTime.substring(0, 5) : '';
            return this.$LANG.Get('labels.maintenancePlanned')
                .replace('%date%', formattedDate)
                .replace('%time%', formattedTime)
                .replace('%hours%', this.maintenanceHours);
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
            if (this.$store.state.user.permissions.includes('MANAGE_USERS')) {
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
        this.$API.APICall('get', 'public/version', {}, (data, success) => {
            if (!success) {
                if (data && data.error === 'maintenance in progress') {
                    vm.maintenance = true;
                }
                return;
            }
            vm.maintenanceDate = data.maintenance_date ?? null;
            vm.maintenanceTime = data.maintenance_time ?? null;
            vm.maintenanceHours = data.maintenance_hours ?? null;
            if (data.version !== this.$HELPERS.GetCookie('nevs_version')) {
                this.$HELPERS.SetCookie('nevs_version', data.version);
                window.location.reload(true);
            }
        });
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
        setInterval(() => {
            this.$API.APICall('get', 'public/heartbeat', {}, (data, success) => {
                if (!success) {
                    if (data && data.error === 'maintenance in progress') {
                        vm.maintenance = true;
                    }
                    return;
                }
                vm.maintenance = false;
                vm.maintenanceDate = data.maintenance_date ?? null;
                vm.maintenanceTime = data.maintenance_time ?? null;
                vm.maintenanceHours = data.maintenance_hours ?? null;
                if (vm.$store.state.user !== null) {
                    if (!data.logged_in) {
                        vm.$store.commit('setUser', null);
                    }
                    if (data.version !== this.$HELPERS.GetCookie('nevs_version')) {
                        this.$HELPERS.SetCookie('nevs_version', data.version);
                        window.location.reload(true);
                    }
                }
            }, false);
        }, 30000);
    }
}

</script>

<style scoped>
.maintenance-screen {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background: #f4f6f8;
    text-align: center;
    padding: 20px;
}

.maintenance-icon {
    font-size: 80px;
    color: #999;
    margin-bottom: 30px;
}

.maintenance-screen h1 {
    font-size: 28px;
    color: #444;
    margin-bottom: 15px;
    font-weight: 600;
}

.maintenance-screen p {
    font-size: 16px;
    color: #777;
    max-width: 480px;
    line-height: 1.6;
}

.maintenance-planned-warning {
    margin: 10px;
    background: #F88379;
    padding: 10px;
    color: black;
    border-radius: 10px;
}
</style>
