<template>
  <div class="login-screen">
    <NevsCard class="login-card">
      <template v-if="!passwordRecoveryMode">
        <h1 class="login-card-title">{{ $LANG.Get('labels.loginTitle') }}</h1>
        <p class="login-card-subtitle">{{ $LANG.Get('labels.loginSubtitle') }}</p>
        <NevsTextField @keyup.enter="loginClick()" :name="'email'" :type="'email'" :label="$LANG.Get('fields.email')"
                       :width="'100%'" v-model="loginData.email"></NevsTextField>
        <NevsTextField @keyup.enter="loginClick()" :name="'password'" :type="'password'"
                       :label="$LANG.Get('fields.password')" :width="'100%'"
                       v-model="loginData.password"></NevsTextField>
        <div class="login-card-options">
          <NevsCheckbox :label="$LANG.Get('labels.rememberLogin')" :width="'auto'" :size="'18px'"
                        v-model="loginData.remember"></NevsCheckbox>
          <a href="#" class="login-card-link" @click.prevent="passwordRecoveryToggle">
            {{ $LANG.Get('labels.forgottenPassword') }}
          </a>
        </div>
        <div class="login-card-footer">
          <NevsButton @click="loginClick" class="primary login-card-submit">{{ $LANG.Get('buttons.login') }}</NevsButton>
        </div>
      </template>
      <template v-if="passwordRecoveryMode">
        <h1 class="login-card-title">{{ $LANG.Get('labels.passwordRecoveryTitle') }}</h1>
        <p class="login-card-subtitle">{{ $LANG.Get('texts.forgottenPasswordExplanation') }}</p>
        <NevsTextField @keyup.enter="passwordRecoveryClick()" :name="'email'" :type="'email'"
                       :label="$LANG.Get('fields.email')" :width="'100%'"
                       v-model="passwordResetData.email"></NevsTextField>
        <div class="login-card-footer">
          <NevsButton class="primary login-card-submit" @click="passwordRecoveryClick">
            {{ $LANG.Get('buttons.recoverPassword') }}
          </NevsButton>
          <a href="#" class="login-card-link login-card-back" @click.prevent="passwordRecoveryToggle">
            <span v-html="backIcon"></span> {{ $LANG.Get('labels.backToLogin') }}
          </a>
        </div>
      </template>
    </NevsCard>
  </div>
</template>

<script>
import NevsTextField from "@/components/nevs/NevsTextField.vue";
import NevsCard from "@/components/nevs/NevsCard.vue";
import NevsButton from "@/components/nevs/NevsButton.vue";
import NevsCheckbox from "@/components/nevs/NevsCheckbox.vue";

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
  computed: {
    // Through v-html, like the other Font Awesome icons that are not static markup: it swaps an <i> for an <svg>.
    backIcon() {
      return '<i class="fa-solid fa-arrow-left"></i>';
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
          // Before setUser, which is what puts the menu on screen: it reads its stored state as it is created.
          vm.$store.commit('setUserData', data.user_data);
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