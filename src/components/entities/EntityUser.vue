<template>
  <div class="nevs-content" v-if="loaded">
    <div class="nevs-content-top-buttons">
      <NevsButton @click="saveClick()" class="success"><i class="fa-solid fa-floppy-disk"></i>
        {{ $LANG.Get('buttons.save') }}
      </NevsButton>&nbsp;
      <NevsButton @click="backClick" class="error"><i class="fa-solid fa-arrow-left"></i> {{
          $LANG.Get('buttons.back')
        }}
      </NevsButton>
    </div>
    <NevsCard class="user-form">
      <NevsTextField :error="userValidation.first_name" :label="'*' + $LANG.Get('fields.firstName') + ':'"
                     v-model="user.first_name"></NevsTextField>
      <NevsTextField :error="userValidation.last_name" :label="'*' + $LANG.Get('fields.lastName') + ':'"
                     v-model="user.last_name"></NevsTextField>
      <NevsTextField :error="userValidation.email" :label="'*' + $LANG.Get('fields.email') + ':'"
                     v-model="user.email"></NevsTextField>
      <template v-if="$store.state.user.permissions.includes('MANAGE_USERS')">
        <NevsMultipleSelect :label="$LANG.Get('fields.permissions') + ':'" :ajax="'select/permissions'"
                            v-model="user.permissions"></NevsMultipleSelect>
        <NevsCheckbox :label="$LANG.Get('fields.userActive')" v-model="user.active"></NevsCheckbox>
      </template>
      <NevsCheckbox v-if="mode === 'edit'" :label="$LANG.Get('labels.changePassword')"
                    v-model="changePassword"></NevsCheckbox>
      <NevsTextField :error="userValidation.password"
                     v-if="mode === 'add' || changePassword" :label="'*' + $LANG.Get('fields.password') + ':'"
                     v-model="user.password"></NevsTextField>
    </NevsCard>
  </div>
</template>

<script>
import User from '@/models/User';
import NevsCard from "@/components/nevs/NevsCard";
import NevsTextField from "@/components/nevs/NevsTextField";
import NevsCheckbox from "@/components/nevs/NevsCheckbox";
import NevsButton from "@/components/nevs/NevsButton";
import NevsMultipleSelect from "@/components/nevs/NevsMultipleSelect";

export default {
  name: "EntityUser",
  components: {NevsMultipleSelect, NevsButton, NevsCheckbox, NevsTextField, NevsCard},
  data() {
    return {
      loaded: false,
      mode: this.$route.params.id === '0' ? 'add' : 'edit',
      user: new User({id: this.$route.params.id}),
      userValidation: new User({}, true),
      changePassword: false
    }
  },
  methods: {
    backClick() {
      if (window.history.length === 1) {
        window.close();
      } else {
        this.$router.back();
      }
    },
    saveClick() {
      let valid = true;

      if (this.user.first_name === '') {
        valid = false;
        this.userValidation.first_name = this.$LANG.Get('labels.mandatoryField');
      } else {
        this.userValidation.first_name = '';
      }

      if (this.user.last_name === '') {
        valid = false;
        this.userValidation.last_name = this.$LANG.Get('labels.mandatoryField');
      } else {
        this.userValidation.last_name = '';
      }

      if (this.user.email === '') {
        valid = false;
        this.userValidation.email = this.$LANG.Get('labels.mandatoryField');
      } else {
        if (!this.$HELPERS.CheckEmail(this.user.email)) {
          valid = false;
          this.userValidation.email = this.$LANG.Get('labels.invalidEmailAddress');
        } else {
          this.userValidation.email = '';
        }
      }

      if (this.mode==='edit' && !this.changePassword) {
        this.user.password = '';
      }

      if (this.mode === 'add' || this.changePassword) {
        if (this.user.password === '') {
          valid = false;
          this.userValidation.password = this.$LANG.Get('labels.mandatoryField');
        } else {
          this.userValidation.password = '';
        }
      }

      if (valid) {
        let vm = this;
        let requestMethod = this.mode === 'add' ? 'post' : 'put';
        let requestService = this.mode === 'add' ? 'users' : 'users/' + this.user.id;
        this.$API.APICall(requestMethod, requestService, this.user, (data, success) => {
          if (success) {
            vm.$CROSS_TAB_BUS.TriggerEvent('reload-users', {});
            vm.$LOCAL_BUS.TriggerEvent('notification', {text: this.$LANG.Get('alerts.recordSaved')});
            vm.backClick();
          } else {
            vm.$LOCAL_BUS.TriggerEvent('popup', {text: vm.$LANG.Get('alerts.serverError'), type: 'alert'});
          }
        })
      }
    }
  },
  mounted() {
    this.$store.commit('selectMenu', 'users');
    this.$store.commit('selectSubMenu', null);

    if (this.mode === 'add') {
      if(!this.$store.state.user.permissions.includes('MANAGE_USERS')) {
        this.$LOCAL_BUS.TriggerEvent('popup', {text: this.$LANG.Get('alerts.unauthorized'), type: 'alert'});
        this.backClick();
      }
      this.$store.commit('setBreadcrumbs', [
        {
          label: this.$LANG.Get('modules.users'),
          link: '/users'
        },
        {
          label: this.$LANG.Get('modules.newUser'),
          link: null
        }
      ]);
      this.loaded = true;
    } else {
      let vm = this;
      this.$API.APICall('get', 'users/' + this.user.id, {}, (data, success) => {
        if (success) {
          if (data.user !== null) {
            vm.user.Fill(data.user);
            vm.$store.commit('setBreadcrumbs', [
              {
                label: vm.$LANG.Get('modules.users'),
                link: '/users'
              },
              {
                label: vm.user.first_name + ' ' + vm.user.last_name,
                link: null
              }
            ]);
          } else {
            vm.$LOCAL_BUS.TriggerEvent('popup', {text: vm.$LANG.Get('alerts.serverError'), type: 'alert'});
          }
        } else {
          vm.$LOCAL_BUS.TriggerEvent('popup', {text: vm.$LANG.Get('alerts.serverError'), type: 'alert'});
        }
        vm.$nextTick(()=>{
          vm.loaded = true;
        });
      });
    }
  }
}
</script>

<style scoped>
.user-form {
  max-width: 300px;
}
</style>