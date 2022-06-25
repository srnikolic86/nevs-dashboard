<template>
  <NevsCard class="login-card">
    <NevsTextField :name="'email'" :label="$LANG.Get('fields.email')+':'" :width="'100%'"
                   v-model="loginData.email"></NevsTextField>
    <NevsTextField @keyup.enter="loginClick()" :name="'password'" :type="'password'" :label="$LANG.Get('fields.password')+':'" :width="'100%'"
                   v-model="loginData.password"></NevsTextField>
    <NevsCheckbox :label="$LANG.Get('labels.rememberLogin')" v-model="loginData.remember"></NevsCheckbox>
    <div class="login-card-footer">
      <NevsButton @click="loginClick" class="nevs-float-right">{{ $LANG.Get('buttons.login') }}</NevsButton>
    </div>
    <div class="nevs-clear-float"></div>
  </NevsCard>
</template>

<script>
import NevsTextField from "@/components/nevs/NevsTextField";
import NevsCard from "@/components/nevs/NevsCard";
import NevsButton from "@/components/nevs/NevsButton";
import NevsCheckbox from "@/components/nevs/NevsCheckbox";

export default {
  name: "LoginForm",
  components: {
    NevsCheckbox,
    NevsTextField,
    NevsCard,
    NevsButton
  },
  props: {
    postLogin: {
      type: Function
    }
  },
  data() {
    return {
      loginData: {
        email: '',
        password: '',
        remember: false
      }
    }
  },
  methods: {
    loginClick() {
      let vm = this;
      this.$API.APICall('post', 'public/login', this.loginData, (data, success) => {
        if (success) {
          vm.$store.commit('setUser', data.user);
          vm.$store.commit('setLocale', data.user.locale);
          vm.postLogin();
        } else {
          vm.$LOCAL_BUS.TriggerEvent('popup', {text: vm.$LANG.Get('alerts.loginFailed'), type: 'alert'});
        }
      });
    }
  }
}
</script>

<style scoped>

</style>