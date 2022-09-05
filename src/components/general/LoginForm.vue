<template>
  <NevsCard class="login-card">
    <template v-if="!passwordRecoveryMode">
      <NevsTextField :name="'email'" :label="$LANG.Get('fields.email')+':'" :width="'100%'"
                     v-model="loginData.email"></NevsTextField>
      <NevsTextField @keyup.enter="loginClick()" :name="'password'" :type="'password'" :label="$LANG.Get('fields.password')+':'" :width="'100%'"
                     v-model="loginData.password"></NevsTextField>
      <div @click="passwordRecoveryToggle" class="forgotten-password-button">{{ $LANG.Get('labels.forgottenPassword') }}</div>
      <NevsCheckbox :label="$LANG.Get('labels.rememberLogin')" v-model="loginData.remember"></NevsCheckbox>
      <div class="login-card-footer">
        <NevsButton @click="loginClick" class="nevs-float-right">{{ $LANG.Get('buttons.login') }}</NevsButton>
      </div>
    </template>
    <template v-if="passwordRecoveryMode">
      <div class="login-card-forgotten-password-explanation">{{ $LANG.Get('texts.forgottenPasswordExplanation') }}</div>
      <NevsTextField class="login-card-forgotten-password-email" :name="'email'" :label="$LANG.Get('fields.email')+':'" :width="'100%'"
                     v-model="passwordResetData.email"></NevsTextField>
      <div class="login-card-footer">
        <NevsButton @click="passwordRecoveryToggle">{{ $LANG.Get('buttons.cancel') }}</NevsButton>
        <NevsButton class="login-card-forgotten-password-recovery-button" @click="passwordRecoveryClick">{{ $LANG.Get('buttons.recoverPassword') }}</NevsButton>
      </div>
    </template>
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
      },
      passwordResetData: {
        email: ''
      },
      passwordRecoveryMode: false
    }
  },
  methods: {
    passwordRecoveryClick() {
      let vm = this;
      this.$API.APICall('post', 'public/password-reset', this.passwordResetData, (data, success) => {
        if (success) {
          vm.passwordRecoveryToggle();
          vm.$LOCAL_BUS.TriggerEvent('popup', {text: vm.$LANG.Get('alerts.passwordResetSuccessful'), type: 'alert'});
        } else {
          vm.$LOCAL_BUS.TriggerEvent('popup', {text: vm.$LANG.Get('alerts.serverError'), type: 'alert'});
        }
      });
    },
    passwordRecoveryToggle() {
      this.passwordRecoveryMode = !this.passwordRecoveryMode;
      this.passwordResetData = {
        email: ''
      };
      this.loginData = {
        email: '',
        password: '',
        remember: false
      };
    },
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