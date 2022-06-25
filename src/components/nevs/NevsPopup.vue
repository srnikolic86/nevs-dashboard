<template>
  <div v-if="show" class="nevs-popup-container">
    <div class="nevs-popup">
      <div class="nevs-popup-text" v-html="text"/>
      <div v-if="type==='input'" class="nevs-popup-input">
        <NevsTextField v-model="input" :width="'100%'"></NevsTextField>
      </div>
      <div class="nevs-popup-buttons">
        <NevsButton class="error" v-if="type==='input'" @click="cancelClick">{{ $LANG.Get('buttons.cancel') }}</NevsButton>
        <NevsButton class="success" v-if="type==='alert' || type==='input'" @click="okClick">{{ $LANG.Get('buttons.ok') }}</NevsButton>
        <NevsButton class="error" v-if="type==='confirm'" @click="noClick">{{ $LANG.Get('buttons.no') }}</NevsButton>
        <NevsButton class="success" v-if="type==='confirm'" @click="yesClick">{{ $LANG.Get('buttons.yes') }}</NevsButton>
      </div>
    </div>
  </div>
</template>

<script>
import NevsButton from "@/components/nevs/NevsButton";
import NevsTextField from "@/components/nevs/NevsTextField"

export default {
  name: "NevsPopup",
  components: {NevsButton, NevsTextField},
  data() {
    return {
      text: '',
      show: false,
      type: null,
      callback: null,
      input: ''
    }
  },
  methods: {
    resetPopup() {
      this.text = '';
      this.input = '';
      this.show = false;
      this.type = null;
      this.callback = null;
    },
    okClick() {
      if (this.type !== 'alert' && this.type !== 'input') return;
      if (this.callback !== null) {
        switch (this.type) {
          case 'alert':
            this.callback(null);
            break;
          case 'input':
            this.callback(this.input);
            break;
        }
      }
      this.show = false;
      let vm = this;
      this.$nextTick(() => {
        vm.resetPopup();
      });
    },
    cancelClick() {
      if (this.type !== 'input') return;
      if (this.callback !== null) {
        this.callback(null);
      }
      this.show = false;
      let vm = this;
      this.$nextTick(() => {
        vm.resetPopup();
      });
    },
    yesClick() {
      if (this.type !== 'confirm') return;
      if (this.callback !== null) {
        this.callback(true);
      }
      this.show = false;
      let vm = this;
      this.$nextTick(() => {
        vm.resetPopup();
      });
    },
    noClick() {
      if (this.type !== 'confirm') return;
      if (this.callback !== null) {
        this.callback(false);
      }
      this.show = false;
      let vm = this;
      this.$nextTick(() => {
        vm.resetPopup();
      });
    }
  },
  mounted() {
    let vm = this;
    this.$LOCAL_BUS.ListenToEvent('popup', (data) => {
      if (data.type === undefined) return;
      if (data.text === undefined) return;
      vm.type = data.type;
      vm.text = data.text;
      vm.callback = (data.callback !== undefined) ? data.callback : null;
      vm.$nextTick(() => {
        vm.show = true;
      });
    });
  }
}
</script>

<style scoped>

</style>